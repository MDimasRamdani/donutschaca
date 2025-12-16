<?php
session_start();
$koneksi = new mysqli("localhost", "root", "", "donutschaca");
// Cek login owner
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'owner') {
    header("Location: owner_login.php");
    exit;
}
// Ambil keyword pencarian
$search = isset($_GET['search']) ? $koneksi->real_escape_string($_GET['search']) : '';
// Query data pembayaran dengan JOIN ke tabel payments
if (!empty($search)) {
    $sql = "
        SELECT orders.*, payments.method 
        FROM orders 
        LEFT JOIN payments ON orders.order_id = payments.order_id 
        WHERE orders.nama LIKE '%$search%'
        ORDER BY orders.order_id DESC
    ";
} else {
    $sql = "
        SELECT orders.*, payments.method 
        FROM orders 
        LEFT JOIN payments ON orders.order_id = payments.order_id 
        ORDER BY orders.order_id DESC
    ";
}
$query = $koneksi->query($sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Pembayaran</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap & Font Awesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
<?php include "../koneksi/navbar_owner.php"; ?>

<div class="container mt-4">
    <h3 class="mb-4"><i class="fas fa-money-check-alt me-2"></i>Laporan Pembayaran</h3>

    <form class="mb-4" method="GET">
        <div class="input-group">
            <input type="text" class="form-control" name="search" placeholder="Cari nama pelanggan..." value="<?= htmlspecialchars($search); ?>">
            <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
            <a href="laporan_pembayaran.php" class="btn btn-secondary"><i class="fas fa-undo"></i></a>
        </div>
    </form>
    <?php
    // Hitung jumlah pesanan completed dan dibatalkan
    $sqlStats = "
        SELECT status, COUNT(*) AS jumlah 
        FROM orders 
        GROUP BY status
    ";
    $resultStats = $koneksi->query($sqlStats);

    $completed = 0;
    $dibatalkan = 0;

    while ($row = $resultStats->fetch_assoc()) {
        if ($row['status'] === 'completed') {
            $completed = $row['jumlah'];
        } elseif ($row['status'] === 'dibatalkan') {
            $dibatalkan = $row['jumlah'];
        }
    }
    ?>

    <style>
        body {
        background: url('../img/donat1.png') no-repeat center center fixed;
        background-size: cover;
    }

    tbody td {
        text-align: center;
        vertical-align: middle !important;
    }

    /* Supaya teks tetap bisa dibaca */
    .table {
        background-color: white;
        border-radius: 10px;
        overflow: hidden;
    }

    .table th, .table td {
        vertical-align: middle;
    }
    
    .card-gradient-success {
        background: linear-gradient(135deg, #a8ff78, #78ffd6);
        color: #000;
        border: none;
        border-radius: 15px;
        box-shadow: 0 4px 12px rgba(0, 128, 0, 0.2);
    }
    .card-gradient-danger {
        background: linear-gradient(135deg, #ff6a6a, #ffa07a);
        color: #000;
        border: none;
        border-radius: 15px;
        box-shadow: 0 4px 12px rgba(255, 0, 0, 0.2);
    }
    .emoji-icon {
        font-size: 1.8rem;
        margin-right: 10px;
    }
</style>

<div class="row mb-4">
    <div class="col-md-6 mb-3">
        <div class="card card-gradient-success p-3 d-flex flex-row align-items-center">
            <div class="emoji-icon">✅🎉</div>
            <div>
                <h6 class="mb-1 fw-bold">Pesanan Completed</h6>
                <p class="mb-0 fs-5"><?= $completed; ?> pesanan</p>
            </div>
        </div>
    </div>
    <div class="col-md-6 mb-3">
        <div class="card card-gradient-danger p-3 d-flex flex-row align-items-center">
            <div class="emoji-icon">❌💔</div>
            <div>
                <h6 class="mb-1 fw-bold">Pesanan Dibatalkan</h6>
                <p class="mb-0 fs-5"><?= $dibatalkan; ?> pesanan</p>
            </div>
        </div>
    </div>
</div>

    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-dark text-center">
                <tr>
                    <th>Order ID</th>
                    <th>Nama</th>
                    <th>Produk</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Bukti Transfer</th>
                    <th>Tanggal</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($query->num_rows > 0): ?>
                    <?php while ($row = $query->fetch_assoc()): ?>
                        <tr>
                            <td>#<?= $row['order_id']; ?></td>
                            <td><?= htmlspecialchars($row['nama']); ?></td>
                            <td><?= htmlspecialchars($row['produk']); ?></td>
                            <td class="text-center">Rp <?= number_format($row['total_price'], 0, ',', '.'); ?></td>
                            <td class="text-center">
                                <span class="badge bg-<?= $row['status'] === 'completed' ? 'success' : 'warning'; ?>">
                                    <?= htmlspecialchars($row['status'] ?? 'Menunggu'); ?>
                                </span>
                            </td>
                            <td class="text-center">
                <?php 
                $isCOD = isset($row['method']) && $row['method'] === 'COD';
                $isCanceled = isset($row['status']) && $row['status'] === 'dibatalkan';

                if ($isCanceled) {
                    echo '<span class="text-danger fw-bold">Dibatalkan</span>';
                } elseif ($isCOD) {
                echo '<span class="text-danger fw-bold">COD</span>';
                } elseif (!empty($row['bukti_transfer'])) {
                 echo '<img src="../bukti/' . htmlspecialchars($row['bukti_transfer']) . '" alt="Bukti" style="max-width:100px; border-radius:8px;">';
                } else {
                echo '<span class="text-muted">-</span>';
                }
                ?>
                </td>
                            <td><?= date("d-m-Y", strtotime($row['created_at'] ?? 'now')); ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted">Tidak ada data pembayaran ditemukan.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
