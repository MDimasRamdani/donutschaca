<?php
session_start();
// Cek login owner
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'owner') {
  header("Location: owner_login.php");
  exit;
}
require_once '../koneksi/koneksi.php';
// Ambil data dashboard seperti sebelumnya
$total_produk = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM product"))['total'];
$total_pesanan = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM orders"))['total'];
$pesanan_sukses = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as sukses FROM orders WHERE status = 'completed'"))['sukses'];
$total_pendapatan = mysqli_fetch_assoc(mysqli_query($koneksi, 
    "SELECT SUM(total_price) as total FROM orders WHERE status = 'completed'"
))['total'];
$admin_terdaftar = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM users WHERE role IN ('admin')"))['total'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Owner</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <style>
    body {
      background: url('../img/donat1.png') no-repeat center center fixed;
      background-size: cover;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      position: relative;
    }

    body::before {
      content: '';
      position: fixed;
      top: 0;
      left: 0;
      height: 100%;
      width: 100%;
      background-color: rgba(0, 0, 0, 0.6); /* Lapisan gelap */
      z-index: -1;
    }

    .container {
      position: relative;
      z-index: 1;
    }

    .card-hover {
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      cursor: pointer;
      text-decoration: none;
    }

    .card-hover:hover {
      transform: scale(1.03);
      box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
    }

    .icon-circle {
      width: 60px;
      height: 60px;
      line-height: 60px;
      border-radius: 50%;
      background-color: rgba(255, 255, 255, 0.3);
      margin: 0 auto 10px;
      font-size: 24px;
    }

    h2 {
      color: white;
      text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.8);
    }

    .card-title, .card-text {
      color: white !important;
    }

    .text-secondary {
      color: #cccccc !important;
    }
  </style>
</head>
<body>
<?php include "../koneksi/navbar_owner.php"; ?>
<div class="container py-4">
  <h2 class="mb-4 fw-bold text-center" style="color: white;">Selamat Datang, Owner! 🎉</h2>
  <div class="row g-4">
    <div class="col-md-4">
      <a href="laporan_produk.php" class="card-hover text-white">
        <div class="card bg-primary h-100">
          <div class="card-body text-center text-white">
            <div class="icon-circle"><i class="fas fa-box"></i></div>
            <h5 class="card-title"><?= $total_produk ?></h5>
            <p class="card-text">Total Produk</p>
          </div>
        </div>
      </a>
    </div>
    <div class="col-md-4">
      <a href="laporan_pesanan.php?status=masuk" class="card-hover text-white">
        <div class="card bg-success h-100">
          <div class="card-body text-center text-white">
            <div class="icon-circle"><i class="fas fa-shopping-cart"></i></div>
            <h5 class="card-title"><?= $total_pesanan ?></h5>
            <p class="card-text">Pesanan Masuk</p>
          </div>
        </div>
      </a>
    </div>
    <div class="col-md-4">
      <a href="laporan_pembayaran.php?status=sukses" class="card-hover text-white">
        <div class="card bg-warning h-100">
          <div class="card-body text-center text-white">
            <div class="icon-circle"><i class="fas fa-check-circle"></i></div>
            <h5 class="card-title"><?= $pesanan_sukses ?></h5>
            <p class="card-text">Pembayaran Completed</p>
          </div>
        </div>
      </a>
    </div>
    <div class="col-md-6">
      <a href="statistik_penjualan.php" class="card-hover text-white">
        <div class="card bg-danger h-100">
          <div class="card-body text-center text-white">
            <div class="icon-circle"><i class="fas fa-money-bill-wave"></i></div>
            <h5 class="card-title">Rp <?= number_format($total_pendapatan, 0, ',', '.') ?></h5>
            <p class="card-text">Total Pendapatan</p>
          </div>
        </div>
      </a>
    </div>

    <div class="col-md-6">
      <a href="manajemen_admin.php" class="card-hover text-white">
        <div class="card bg-dark h-100">
          <div class="card-body text-center text-white">
            <div class="icon-circle"><i class="fas fa-user-shield"></i></div>
            <h5 class="card-title"><?= $admin_terdaftar ?></h5>
            <p class="card-text">Admin Terdaftar</p>
          </div>
        </div>
      </a>
    </div>
  </div>

  <div class="text-center mt-5 text-white small">&copy; 2025 DonutsChaca | Tetap Semangat!! 🍩</div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
</body>
</html>
