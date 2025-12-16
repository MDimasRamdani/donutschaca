<?php
session_start();
$koneksi = new mysqli("localhost", "root", "", "donutschaca");
// Cek login owner
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'owner') {
    header("Location: owner_login.php");
    exit;
}
$data = $koneksi->query("
    SELECT orders.*, payments.method 
    FROM orders 
    LEFT JOIN payments ON orders.order_id = payments.order_id 
    ORDER BY orders.order_id DESC
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Pesanan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Bootstrap & Font Awesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        body { background-color: #f8f9fa; overflow: scroll; }
        table img { width: 80px; height: auto; border-radius: 6px; }
        body {
        background: url('../img/donat1.png') no-repeat center center fixed;
        background-size: cover;
        overflow-x: hidden;
    }
    </style>
</head>
<body>
<?php include "../koneksi/navbar_owner.php"; ?>
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
    <h3><i class="fas fa-receipt me-2"></i>Laporan Pesanan</h3>
    <div class="d-flex gap-2">
        <a href="owner_dashboard.php" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
        <!-- Tombol Hapus Semua -->
        <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#hapusSemuaModal">
            <i class="fas fa-trash"></i> Hapus Semua
        </button>
    </div>
</div>

    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle text-center">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nama</th>
                    <th>Produk</th>
                    <th>Alamat</th>
                    <th>No HP</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Bukti</th>
                    <th>Tanggal</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $data->fetch_assoc()) : ?>
                    <tr>
                        <td>#<?= $row['order_id']; ?></td>
                        <td><?= htmlspecialchars($row['nama']); ?></td>
                        <td><?= htmlspecialchars($row['produk']); ?></td>
                        <td><?= htmlspecialchars($row['alamat']); ?></td>
                        <td><?= htmlspecialchars($row['telepon']); ?></td>
                        <td class="text-end">Rp <?= number_format($row['total_price'], 0, ',', '.'); ?></td>
                        <td>
                            <span class="badge bg-<?= $row['status'] === 'completed' ? 'success' : ($row['status'] === 'pending' ? 'warning' : 'secondary'); ?>">
                                <?= htmlspecialchars(ucfirst($row['status'] ?? 'Menunggu')); ?>
                            </span>
                        </td>
                        <td>
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
            </tbody>
        </table>
    </div>
</div>
<!-- Modal Konfirmasi Hapus Semua -->
<div class="modal fade" id="hapusSemuaModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">

      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title">Hapus Semua Pesanan?</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <p class="mb-1">Apakah kamu yakin ingin menghapus <b>SEMUA pesanan</b>?</p>
        <p class="text-danger fw-bold">Data yang terhapus tidak bisa dikembalikan!</p>
      </div>

      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>

        <form method="post" action="owner_delete_all.php">
            <button type="submit" class="btn btn-danger">Ya, Hapus Semua</button>
        </form>
      </div>

    </div>
  </div>
</div>
</body>
</html>
