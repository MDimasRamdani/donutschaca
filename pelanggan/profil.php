<?php
session_start();
require_once '../koneksi/koneksi.php';

if (!isset($_SESSION['pelanggan'])) {
    echo "<script>alert('Silakan login terlebih dahulu!'); window.location='login_pelanggan.php';</script>";
    exit();
}

$customer_id = $_SESSION['pelanggan']['user_id'];
$query = "SELECT * FROM customers WHERE user_id = ?";
$stmt = $koneksi->prepare($query);
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result = $stmt->get_result();
$customer = $result->fetch_assoc();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Profil Pelanggan</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body, html {
    background: url('../img/donat1.png') no-repeat center center fixed;
    background-size: cover;
      height: 100%;
      margin: 0;
      padding: 0;
      position: relative;
    }

    .bg-video {
      position: fixed;
      top: 0; left: 0;
      min-width: 100%;
      min-height: 100%;
      z-index: -1;
      object-fit: cover;
      filter: brightness(0.7);
    }

    .content-wrapper {
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 2rem;
    }

    .card-profile {
      width: 100%;
      max-width: 600px;
      background-color: rgba(255, 255, 255, 0.95);
      border-radius: 20px;
    }
  </style>
</head>
<body>

<div class="container content-wrapper">
  <div class="card shadow-lg card-profile">
    <div class="card-header text-center bg-dark text-white rounded-top-4">
      <h4><i class="bi bi-person-circle me-2"></i>Profil Pelanggan</h4>
    </div>
    <div class="card-body">
      <ul class="list-group list-group-flush">
        <li class="list-group-item"><strong><i class="bi bi-person"></i> Nama :</strong> <?= htmlspecialchars($customer['nama']) ?></li>
        <li class="list-group-item"><strong><i class="bi bi-telephone"></i> No HP :</strong> <?= htmlspecialchars($customer['telepon']) ?></li>
        <li class="list-group-item"><strong><i class="bi bi-geo-alt"></i> Alamat :</strong> <?= nl2br(htmlspecialchars($customer['alamat'])) ?></li>
        <li class="list-group-item"><strong><i class="bi bi-person-badge"></i> Username :</strong> <?= htmlspecialchars($customer['username']) ?></li>
        <li class="list-group-item"><strong><i class="bi bi-person-badge"></i> Password :</strong> <?= htmlspecialchars($customer['password']) ?></li>
      </ul>
    </div>
    <div class="card-footer text-center">
      <a href="pemesanan.php" class="btn btn-primary"><i class="bi bi-cart4 me-1"></i>Mulai Pemesanan</a>
    </div>
  </div>
</div>

</body>
</html>
