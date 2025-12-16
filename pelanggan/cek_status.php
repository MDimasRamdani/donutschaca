<?php
include "../koneksi/koneksi.php"; // sambungkan ke database

$status_pesanan = '';
$orders = null;
$show_cancel = false;

if (isset($_POST['cek_status'])) {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $nomor = mysqli_real_escape_string($koneksi, $_POST['nomor']);

    $queryGetData = "SELECT 
      o.order_id,
      o.nama,
      o.telepon,
      o.deskripsi,
      o.status,
      o.alasan_batal,
      o.total_price,
      o.order_date,
      p.nama AS nama_produk,
      oi.quantity,
      oi.price
    FROM orders o
    JOIN order_items oi ON o.order_id = oi.order_id
    JOIN product p ON oi.product_id = p.product_id
    WHERE o.nama = '$nama' AND o.telepon = '$nomor'
    ORDER BY o.order_date DESC
    LIMIT 1";

    $query = mysqli_query($koneksi, $queryGetData);
    $orders = mysqli_fetch_assoc($query);

    if ($orders) {
        $status = $orders['status'];

        $order_date = new DateTime($orders['order_date']);
        $today = new DateTime();
        $interval = $today->diff($order_date);
        $days_difference = $interval->days;

        if (($status == 'pending' || $status == 'down payment' || $status == 'dp') && $days_difference <= 2) {
            $show_cancel = true;
        } else {
            $show_cancel = false;
        }
    } else {
        echo "<div class='alert alert-danger mt-4'>Pesanan tidak ditemukan.</div>";
    }
}

if (isset($_POST['batal_pesanan'])) {
    $id_pesanan = mysqli_real_escape_string($koneksi, $_POST['id_pesanan']);
    $alasan_batal = mysqli_real_escape_string($koneksi, $_POST['alasan_batal']);

    $cekPesanan = mysqli_query($koneksi, "SELECT status, order_date FROM orders WHERE order_id = '$id_pesanan'");
    $dataPesanan = mysqli_fetch_assoc($cekPesanan);

    if ($dataPesanan) {
        $statusSekarang = $dataPesanan['status'];
        $order_date = new DateTime($dataPesanan['order_date']);
        $today = new DateTime();
        $interval = $today->diff($order_date);
        $days_difference = $interval->days;

        if (($statusSekarang == 'pending' || $statusSekarang == 'down payment' || $statusSekarang == 'dp') && $days_difference <= 2) {
            $update = mysqli_query($koneksi, "UPDATE orders SET status = 'dibatalkan', alasan_batal = '$alasan_batal' WHERE order_id = '$id_pesanan'");
            if ($update) {
                echo "<script>alert('Pesanan berhasil dibatalkan');window.location.href='cek_status.php';</script>";
            } else {
                echo "<script>alert('Gagal membatalkan pesanan');</script>";
            }
        } else {
            echo "<script>alert('Pesanan tidak dapat dibatalkan (status sudah berubah atau melewati batas waktu)');window.location.href='cek_status.php';</script>";
        }
    } else {
        echo "<script>alert('Pesanan tidak ditemukan');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cek Status Pesanan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    body {
        background: #f0f2f5;
        font-family: 'Segoe UI', sans-serif;
    }

    .card {
        border-radius: 1rem;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
    }

    h2, .card-title {
        font-weight: 700;
        color: #343a40;
    }

    .form-control {
        border-radius: 0.5rem;
    }

    .btn {
        border-radius: 0.75rem;
    }

    .alert {
        border-radius: 0.75rem;
    }

    .modal-content {
        border-radius: 1rem;
    }

    .text-muted {
        font-size: 0.875rem;
    }
</style>

</head>
<body class="bg-light">
<div class="container py-5">
    <h2 class="text-center mb-4">Cek Status Detail Pesanan</h2>
<form method="POST" action="" class="card p-4 shadow-sm">
    <div class="mb-3">
        <input type="text" name="nama" class="form-control" placeholder="Masukkan Nama" required>
    </div>
    <div class="mb-3">
        <input type="text" name="nomor" class="form-control" placeholder="Nomor WhatsApp" required>
    </div>
    <div class="d-grid">
        <button type="submit" name="cek_status" class="btn btn-primary">Cek Status</button>
    </div>
</form>

<?php if ($orders): ?>
    <div class="card mt-4 shadow-sm">
        <?php
$paymentData = null;
$order_id = $orders['order_id'];
$queryPayment = mysqli_query($koneksi, "SELECT method, payment_type FROM payments WHERE order_id = '$order_id' ORDER BY payment_date DESC LIMIT 1");

if ($queryPayment && mysqli_num_rows($queryPayment) > 0) {
    $paymentData = mysqli_fetch_assoc($queryPayment);
}
?>
<?php
$order_id = $orders['order_id'];
$items = [];
$queryItems = mysqli_query($koneksi, "
    SELECT p.nama AS nama_produk, oi.quantity 
    FROM order_items oi 
    JOIN product p ON oi.product_id = p.product_id 
    WHERE oi.order_id = '$order_id'
");
if ($queryItems && mysqli_num_rows($queryItems) > 0) {
    while ($item = mysqli_fetch_assoc($queryItems)) {
        $items[] = $item;
    }
}
?>
        <div class="card-body">
            <h4 class="card-title">Detail Pesanan</h4>
            <p><strong>Nama:</strong> <?= htmlspecialchars($orders['nama']) ?></p>
            <p><strong>Nomor WA:</strong> <?= htmlspecialchars($orders['telepon']) ?></p>
            <p><strong>Produk:</strong><br>
<?php foreach ($items as $item): ?>
    - <?= htmlspecialchars($item['nama_produk']) ?> (<?= htmlspecialchars($item['quantity']) ?>)<br>
<?php endforeach; ?>
</p>
            <p><strong>Harga:</strong> Rp <?= number_format($orders['total_price'], 0, ',', '.') ?></p>
            <p><strong>Deskripsi:</strong> <?= htmlspecialchars($orders['deskripsi']) ?></p>
            <p><strong>Tanggal:</strong> <?= htmlspecialchars($orders['order_date']) ?></p>
            <p><strong>Metode Pembayaran:</strong> <?= htmlspecialchars($paymentData['method']) ?></p>
            <p><strong>Jenis Pembayaran:</strong> <?= strtoupper(htmlspecialchars($paymentData['payment_type'])) ?></p>
            <p><strong>Status:</strong> <?= htmlspecialchars($orders['status']) ?></p>
            
            <?php if (in_array(strtolower($orders['status']), ['pending', 'dp', 'processing', 'down payment'])): ?>
                <div class="d-grid mt-2">
                    <a href="pembayaran.php?order_id=<?= $orders['order_id'] ?>" class="btn btn-success">
                        Lanjutkan Pembayaran
                    </a>
                    <small class="text-muted mt-1">Gunakan tombol ini untuk membayar DP atau melunasi pesanan Anda.</small>
                </div>
            <?php endif; ?>

            <?php if ($orders['status'] === 'dibatalkan'): ?>
                <?php
$order_id = $orders['order_id'];
$cek_payment = mysqli_query($koneksi, "SELECT * FROM payments WHERE order_id = '$order_id' AND payment_type = 'dp'");
$has_dp_payment = false;
if ($cek_payment) {
    $has_dp_payment = mysqli_num_rows($cek_payment) > 0;
} else {
    echo "<div class='alert alert-danger mt-3'>Query gagal: " . mysqli_error($koneksi) . "</div>";
}
?>
<?php if ($has_dp_payment): ?>
    <div class="d-grid mt-3">
        <a href="https://wa.me/6288210094521?text=Halo admin, saya ingin mengajukan refund untuk pesanan dengan Order ID <?= $orders['order_id'] ?>" 
           target="_blank" class="btn btn-warning">
            Minta Refund via WhatsApp
        </a>
        <small class="text-muted mt-1">Klik tombol ini untuk menghubungi admin dan meminta pengembalian uang DP.</small>
    </div>
<?php endif; ?>

<?php
    $alasan = $orders['alasan_batal'] ?? 'Tidak ada alasan yang diberikan.';
    if (str_starts_with($alasan, 'ADMIN:')) {
        $alasan = substr($alasan, 6); // Hapus "ADMIN: "
        echo "<p><strong>Pesanan Dibatalkan oleh Penjual:</strong> " . htmlspecialchars($alasan) . "</p>";
    } else {
        echo "<p><strong>Pesanan Dibatalkan oleh Anda (Pelanggan):</strong> " . htmlspecialchars($alasan) . "</p>";
    }
?>
            
            <?php endif; ?>
            <?php if ($show_cancel): ?>
                <div class="d-grid">
                    <button type="button" class="btn btn-danger mt-3" data-bs-toggle="modal" data-bs-target="#batalModal">
                        Batalkan Pesanan
                    </button>
                </div>
            <?php else: ?>
                <div class="alert alert-warning mt-3" role="alert">
                    Pembatalan tidak tersedia untuk status ini atau sudah melewati batas waktu 2 hari.
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal Batalkan -->
    <div class="modal fade" id="batalModal" tabindex="-1" aria-labelledby="batalModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <form method="POST" action="" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="batalModalLabel">Alasan Pembatalan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id_pesanan" value="<?= $orders['order_id'] ?>">
                <div class="mb-3">
                    <label for="alasan_batal" class="form-label">Tulis alasan mengapa Anda membatalkan pesanan ini:</label>
                    <textarea class="form-control" name="alasan_batal" rows="3" required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" name="batal_pesanan" class="btn btn-danger">Konfirmasi Pembatalan</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </form>
      </div>
    </div>
<?php endif; ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
