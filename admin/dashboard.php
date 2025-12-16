<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}
?>
<?php include '../koneksi/navbar_admin.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Dashboard Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    body {
      background: url('../img/donat1.png') no-repeat center center fixed;
      background-size: cover;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      padding-top: 70px;
    }

    .overlay {
      background-color: rgba(0, 0, 0, 0.6);
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      z-index: -1;
    }

    .navbar {
      background: rgba(255, 255, 255, 0.9);
    }

    .navbar-brand {
      font-weight: bold;
      color: #0d6efd !important;
    }

    .card-menu {
      border: none;
      border-radius: 16px;
      padding: 25px;
      color: white;
      transition: transform 0.3s, box-shadow 0.3s;
    }

    .card-menu:hover {
      transform: translateY(-8px);
      box-shadow: 0 12px 25px rgba(0, 0, 0, 0.15);
    }

    .bg-produk     { background: linear-gradient(135deg, #36d1dc, #5b86e5); }
    .bg-pemesanan  { background: linear-gradient(135deg, #ff6a00, #ee0979); }
    .bg-payment    { background: linear-gradient(135deg, #00c9ff, #92fe9d); }
    .bg-transaksi  { background: linear-gradient(135deg, #f7971e, #ffd200); }

    h2 {
      font-weight: bold;
      color: #fff;
      text-shadow: 1px 1px 4px rgba(0, 0, 0, 0.8);
    }

    p {
      margin-bottom: 0;
      font-size: 0.9rem;
    }

    .container {
      position: relative;
      z-index: 1;
    }

    body::before {
      content: '';
      background-color: rgba(0, 0, 0, 0.4); /* Lapisan gelap */
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      z-index: -1;
    }
  </style>
</head>
<body>

<!-- Main Content -->
<div class="container py-5">
  <h2 class="mb-5 text-center">Selamat Datang, <?= htmlspecialchars($_SESSION['admin']); ?> 👋</h2>

  <div class="row row-cols-1 row-cols-sm-2 row-cols-md-2 row-cols-lg-2 g-4 justify-content-center">

    <div class="col">
      <a href="produk_admin.php" class="text-decoration-none">
        <div class="card-menu bg-produk text-center">
          <h4>Kelola Produk</h4>
          <p>Tambah, edit, atau hapus produk</p>
        </div>
      </a>
    </div>

    <div class="col">
      <a href="pemesanan_admin.php" class="text-decoration-none">
        <div class="card-menu bg-pemesanan text-center">
          <h4>Data Pemesanan</h4>
          <p>Lihat pesanan dari pelanggan</p>
        </div>
      </a>
    </div>

    <div class="col">
      <a href="payment_admin.php" class="text-decoration-none">
        <div class="card-menu bg-payment text-center">
          <h4>Data Pembayaran</h4>
          <p>Lihat bukti & status pembayaran</p>
        </div>
      </a>
    </div>

    <div class="col">
      <a href="riwayat_transaksi.php" class="text-decoration-none">
        <div class="card-menu bg-transaksi text-center">
          <h4>Riwayat Transaksi</h4>
          <p>Lihat riwayat pembelian pelanggan</p>
        </div>
      </a>
    </div>

  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
