<?php
// Koneksi database
$koneksi = new mysqli("localhost", "root", "", "donutschaca");
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}
// Cek koneksi
if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}
// Proses hapus pembayaran
if (isset($_POST['hapus'])) {
    $hapus_id = intval($_POST['hapus_id']);
    $query = "DELETE FROM payments WHERE payments_id = $hapus_id";
    // Cari order_id berdasarkan payment_id
    $result = $koneksi->query("SELECT order_id FROM payments WHERE payments_id = $hapus_id");
    if ($result && $result->num_rows > 0) {
        $data = $result->fetch_assoc();
        $order_id = $data['order_id'];

        // Soft delete di tabel orders
        $koneksi->query("UPDATE orders SET is_deleted = 1 WHERE order_id = $order_id");

        echo "<script>alert('Pembayaran dihapus dan dipindahkan ke arsip.'); window.location.href='payment_admin.php';</script>";
        exit;
    }
}
// Proses verifikasi pembayaran
if (isset($_GET['verifikasi'])) {
    $order_id = intval($_GET['verifikasi']);
    // Ambil payment_type dari database
    $cek_query = $koneksi->query("SELECT payment_type FROM payments WHERE order_id = $order_id");
    if ($cek_query->num_rows > 0) {
        $data = $cek_query->fetch_assoc();
        $payment_type = $data['payment_type'];

        // Hanya proses verifikasi jika pembayaran FULL
        if ($payment_type === 'full') {
            $koneksi->query("UPDATE payments SET status = 'paid' WHERE order_id = $order_id");
            $koneksi->query("UPDATE orders SET status = 'completed' WHERE order_id = $order_id");
        } else {
            echo "<script>alert('Pembayaran belum FULL, tidak dapat diverifikasi sebagai lunas.'); window.location.href='payment_admin.php';</script>";
            exit;
        }
    }
    header("Location: payment_admin.php");
    exit;
}
// Proses hapus pembayaran
if (isset($_GET['hapus'])) {
    $payment_id = intval($_GET['hapus']);
    $koneksi->query("DELETE FROM payments WHERE payments_id = $payment_id");
    header("Location: payment_admin.php");
    exit;
}
// Filter bulan & tahun
$filter_query = "WHERE o.is_deleted = 0";
if (isset($_GET['bulan']) && isset($_GET['tahun'])) {
    $bulan = intval($_GET['bulan']);
    $tahun = intval($_GET['tahun']);
    $filter_query = "WHERE MONTH(p.payment_date) = $bulan AND YEAR(p.payment_date) = $tahun";
}
// Query ambil data
$query = "
    SELECT 
        p.payments_id,
        o.order_id,
        o.nama,
        o.order_date,
        o.total_price,
        p.method,
        p.status AS payment_status,
        p.payment_type, -- ini yang ditambahkan
        o.status AS order_status,
        p.payment_date,
        p.amount
    FROM orders o
    JOIN payments p ON o.order_id = p.order_id
    $filter_query
    ORDER BY o.order_id DESC
";
$result = $koneksi->query($query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Payment Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap & FontAwesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" crossorigin="anonymous" />
    <script src="https://cdn.jsdelivr.net/npm/jspdf"></script>
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.17.5/dist/xlsx.full.min.js"></script>
    <style>
        body {
            background: url('../img/donat1.png') no-repeat center center fixed;
            background-size: cover;
            overflow: scroll;
            padding-top: 20px;
            overflow-x: hidden;
        }
        h2 {
            font-weight: bold;
        }
        .table th, .table td {
            vertical-align: middle;
        }
        .form-select, .form-control {
            min-width: 150px;
        }
    </style>
</head>
<body>
    <?php include '../koneksi/navbar_admin.php'; ?>
    <div class="container">
<h2 class="mb-4 mt-5 text-center text-md-start">📑 Dashboard Payment Admin</h2>
    <!-- Filter Bulan, Tahun dan Tanggal -->
<form class="row g-3 mb-4 align-items-end" method="get">
    <div class="col-md-3 col-6">
        <strong class="form-label">Bulan</strong>
        <select name="bulan" class="form-select" required>
            <option value="">Pilih Bulan</option>
            <?php 
            for($i=1;$i<=12;$i++) {
                echo "<option value='$i' ".(isset($_GET['bulan']) && $_GET['bulan']==$i ? 'selected' : '').">".date('F', mktime(0,0,0,$i,1))."</option>";
            }
            ?>
        </select>
    </div>
    <div class="col-md-3 col-6">
        <strong class="form-label">Tahun</strong>
        <input type="number" name="tahun" class="form-control" placeholder="Tahun" value="<?= isset($_GET['tahun']) ? $_GET['tahun'] : date('Y'); ?>" required>
    </div>
    <div class="col-md-3 col-6">
        <strong class="form-label">Tanggal Order</strong>
        <input type="date" name="tanggal" class="form-control" value="<?= isset($_GET['tanggal']) ? $_GET['tanggal'] : '' ?>">
    </div>
    <div class="col-md-3 d-flex gap-2">
        <button type="submit" class="btn btn-primary">🔍 Filter</button>
        <a href="payment_admin.php" class="btn btn-secondary">🔄 Reset</a>
    </div>
</form>
    
    <!-- Tabel Pembayaran -->
    <div class="table-responsive">
        <table class="table table-bordered table-hover table-striped align-middle text-center" id="paymentTable">
            <thead class="table-dark">
                <tr>
                    <th>No</th>
                    <th>Order ID</th>
                    <th>Nama</th>
                    <th>Tanggal Order</th>
                    <th>Metode</th>
                    <th>Jenis Pembayaran</th>
                    <th>Total Harga</th>
                    <th>Status Pembayaran</th>
                    <th>Status Order</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = 1; 
                $total_pembayaran = 0;
                if ($result->num_rows > 0): 
                    while($row = $result->fetch_assoc()): 
                        if ($row['payment_status'] == 'paid') {
                            $total_pembayaran += $row['amount'];
                        }
                ?>
                <tr>
                    <td><?= $no++; ?></td>
                    <td>#<?= $row['order_id']; ?></td>
                    <td><?= htmlspecialchars($row['nama']); ?></td>
                    <td><?= date('d-m-Y', strtotime($row['order_date'])); ?></td>
                    <td>
                    <?php 
                        if ($row['method'] == "Transfer Bank") {
                        echo "Transfer via Bank BCA";
                    } elseif ($row['method'] == "E-Wallet") {
                        echo "ShopeePay";
                    } elseif ($row['method'] == "COD") {
                        echo "COD (Bayar di Tempat)";
                    } else {
                        echo htmlspecialchars($row['method']);
                    }
                    ?>
                    </td>
                    <td>
    <?php
        if ($row['payment_type'] == 'dp') {
            echo '<span class="badge bg-info text-dark">DP</span>';
        } elseif ($row['payment_type'] == 'full') {
            echo '<span class="badge bg-success">FULL</span>';
        } else {
            echo '<span class="badge bg-secondary">-</span>';
        }
    ?>
        </td>
                    <td>Rp <?= number_format($row['total_price'], 0, ',', '.'); ?></td>
                    <td>
                        <?php if($row['order_status'] == 'dibatalkan'): ?>
                    <span class="badge bg-danger">Batal</span>
                    <?php elseif($row['payment_status'] == 'unpaid'): ?>
                    <span class="badge bg-warning text-dark">Lunas/DP?</span>
                        <?php else: ?>
                    <span class="badge bg-success">Lunas</span>
                            <?php endif; ?>
                    </td>
                    <td><?= ucfirst($row['order_status']); ?></td>
                    <td>
                        <div class="d-flex flex-column gap-1">
                            <?php if(
    $row['payment_status'] == 'unpaid' && 
    $row['order_status'] != 'dibatalkan' &&
    $row['payment_type'] == 'full'
): ?>
    <a href="?verifikasi=<?= $row['order_id']; ?>" class="btn btn-success btn-sm" onclick="return confirm('Verifikasi pembayaran ini?')">
        ✔️ Verifikasi
    </a>
                    <?php endif; ?>
                            <form method="post" action="">
                                <input type="hidden" name="hapus_id" value="<?= $row['payments_id']; ?>">
                                <button type="submit" name="hapus" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus data pembayaran ini?')">
                                    🗑️ Hapus
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endwhile; else: ?>
                <tr>
                    <td colspan="9" class="text-center">Tidak ada data pembayaran</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <!-- Total Pembayaran -->
    <div class="mt-1">
        <h5>Total Pembayaran Lunas: <span class="text-success">Rp <?= number_format($total_pembayaran, 0, ',', '.'); ?></span></h5>
    </div>
    <!-- Export Buttons -->
    <div class="mt-1">
        <button class="btn btn-outline-success" onclick="exportExcel()">📥 Export Excel</button>
    </div>
    <script>
    function exportExcel() {
        var table = document.getElementById('paymentTable');
        var workbook = XLSX.utils.table_to_book(table, {sheet:"Sheet 1"});
        XLSX.writeFile(workbook, 'pembayaran.xlsx');
    }
    </script>
</body>
</html>
<?php $koneksi->close(); ?>