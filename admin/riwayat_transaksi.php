<?php
session_start();
$koneksi = new mysqli("localhost", "root", "", "donutschaca");
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$filter_bulan = $_GET['bulan'] ?? '';
$filter_tahun = $_GET['tahun'] ?? '';
$whereClause = '';

if ($filter_bulan && $filter_tahun) {
    $whereClause = "WHERE MONTH(o.created_at) = '$filter_bulan' AND YEAR(o.created_at) = '$filter_tahun'";
}

$data = $koneksi->query("SELECT o.*, p.method FROM orders o LEFT JOIN payments p ON o.order_id = p.order_id $whereClause ORDER BY o.order_id DESC");
?>
<?php include '../koneksi/navbar_admin.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Riwayat Transaksi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: url('../img/donat1.png') no-repeat center center fixed;
            background-size: cover;
            overflow: scroll;
            padding-top: 40px;
            overflow-x: hidden;
        }
        .container {
            background-color: #ffffff;
            padding: 30px;
            margin-top: 40px;
            margin-bottom: 10px;
            border-radius: 12px;
            box-shadow: 0 0 12px rgb(0, 117, 250);
        }
        h3 {
            font-weight: bold;
            margin-bottom: 30px;
            color: #343a40;
        }
        .table img {
            width: 100px;
            height: auto;
            border-radius: 8px;
            box-shadow: 0 0 4px rgba(0,0,0,0.1);
        }
        .table-responsive {
            border-radius: 8px;
            overflow-x: auto;
        }
        .table thead {
            background-color: #e9ecef;
        }
        .table th, .table td {
            vertical-align: middle;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <h3 class="text-center">Riwayat Transaksi & Bukti Transfer</h3>

        <form method="get" class="row g-3 mb-4">
            <div class="col-md-4">
                <select name="bulan" class="form-select">
                    <option value="">Pilih Bulan</option>
                    <?php
                    for ($i = 1; $i <= 12; $i++) {
                        $selected = ($filter_bulan == $i) ? 'selected' : '';
                        echo "<option value='$i' $selected>" . date('F', mktime(0, 0, 0, $i, 10)) . "</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="col-md-4">
                <select name="tahun" class="form-select">
                    <option value="">Pilih Tahun</option>
                    <?php
                    $currentYear = date('Y');
                    for ($y = $currentYear; $y >= $currentYear - 5; $y--) {
                        $selected = ($filter_tahun == $y) ? 'selected' : '';
                        echo "<option value='$y' $selected>$y</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="export_transaksi.php?bulan=<?= $filter_bulan ?>&tahun=<?= $filter_tahun ?>" class="btn btn-success">Export Excel</a>
                <a href="export_pdf.php?bulan=<?= $filter_bulan ?>&tahun=<?= $filter_tahun ?>" class="btn btn-danger">Export PDF</a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Order ID</th>
                        <th>Nama</th>
                        <th>No HP</th>
                        <th>Total</th>
                        <th>Alamat</th>
                        <th>Produk</th>
                        <th>Bukti Transfer</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $data->fetch_assoc()) : ?>
                        <tr>
                            <td>#<?= $row['order_id']; ?></td>
                            <td><?= htmlspecialchars($row['nama']); ?></td>
                            <td><?= htmlspecialchars($row['telepon']); ?></td>
                            <td>Rp <?= number_format($row['total_price'], 0, ',', '.'); ?></td>
                            <td><?= htmlspecialchars($row['alamat']); ?></td>
                            <td><?= htmlspecialchars($row['produk']); ?></td>
                            <td>
                                <?php 
                                $isCOD = isset($row['method']) && $row['method'] === 'COD';
                                $isCanceled = isset($row['status']) && $row['status'] === 'dibatalkan';

                                if ($isCanceled) {
                                    echo '<span class="text-danger fw-bold">Dibatalkan</span>';
                                } elseif ($isCOD) {
                                    echo '<span class="text-danger fw-bold">COD</span>';
                                } elseif (!empty($row['bukti_transfer'])) {
                                    echo '<img src="../bukti/' . htmlspecialchars($row['bukti_transfer']) . '" alt="Bukti">';
                                } else {
                                    echo '<span class="text-muted">-</span>';
                                }
                                ?>
                            </td>
                            <td><?= date("d-m-Y", strtotime($row['created_at'] ?? 'now')); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>