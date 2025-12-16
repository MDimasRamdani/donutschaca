<?php include '../koneksi/navbar_admin.php'; ?>
<?php
session_start();
require_once "../koneksi/koneksi.php";
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}
// Ambil semua orders yang selesai atau dibatalkan
$sql = "
SELECT order_id, nama, total_price, order_date, status, alasan_batal
FROM orders
WHERE status IN ('completed', 'dibatalkan')
ORDER BY order_date DESC
";
$result = $koneksi->query($sql);

if (!$result) {
    die("Query error: " . $koneksi->error);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pesanan</title>
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: url('../img/donat1.png') no-repeat center center fixed;
            background-size: cover;
            overflow: scroll;
            padding-top: 50px;
        }
        .container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 12px rgb(0, 117, 250);
        }
        h2 {
            font-weight: 600;
            color: #343a40;
        }
        .table thead th {
            vertical-align: middle;
            text-align: center;
        }
        .table td {
            vertical-align: middle;
        }
        .badge {
            font-size: 0.9rem;
            padding: 0.5em 0.75em;
        }
    </style>
</head>
<body>
<div class="container my-5">
    <h2 class="mb-4 text-center">Riwayat Pesanan</h2>
    <!-- Tombol Hapus Semua -->
<form method="post" action="hapus_riwayat.php" onsubmit="return confirm('Yakin ingin menghapus SEMUA riwayat secara permanen?');">
    <button type="submit" name="hapus_semua" class="btn btn-danger mb-3">🧹 Hapus Semua Riwayat</button>
</form>
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th>No</th>
                    <th>Order ID</th>
                    <th>Nama Pelanggan</th>
                    <th>Tanggal</th>
                    <th>Total Harga</th>
                    <th>Status</th>
                    <th>Alasan / Komentar</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
<?php 
$no = 1;
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>".$no++."</td>";
        echo "<td>".$row['order_id']."</td>";
        echo "<td>".$row['nama']."</td>";
        echo "<td>".date('d-m-Y', strtotime($row['order_date']))."</td>";
        echo "<td>Rp ".number_format($row['total_price'], 0, ',', '.')."</td>";
        if ($row['status'] === 'completed') {
            echo "<td><span class='badge bg-success'>Sukses</span></td>";
            echo "<td>-</td>";
        } else {
            echo "<td><span class='badge bg-danger'>Dibatalkan</span></td>";
            echo "<td>".$row['alasan_batal']."</td>";
        }
        // 🔽 Kolom Aksi (tombol hapus)
        echo "<td>
                <form action='hapus_riwayat.php' method='post' onsubmit=\"return confirm('Yakin ingin menghapus pesanan ini secara permanen?');\">
                    <input type='hidden' name='order_id' value='".$row['order_id']."'>
                    <button type='submit' name='hapus_satu' class='btn btn-sm btn-danger'>🗑️ Hapus</button>
                </form>
              </td>";
        echo "</tr>"; // ← tutup baris
    }
} else {
    echo "<tr><td colspan='8' class='text-center'>Belum ada riwayat pesanan.</td></tr>";
}
?>
</tbody>
        </table>
    </div>
</div>

<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php $koneksi->close(); ?>
