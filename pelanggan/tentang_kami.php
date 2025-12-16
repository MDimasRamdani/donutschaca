<?php
include "../koneksi/navbar.php";
$koneksi = new mysqli("localhost", "root", "", "donutschaca");
if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Tentang Kami - Donuts Chaca</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    /* Menghilangkan scrollbar horizontal */
    body {
      margin: 0;
      overflow-x: hidden;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    /* Background dengan overlay gelap */
    .overlay-background {
      background: url('../img/cake.jpg') no-repeat center center fixed;
      background-size: cover;
      position: relative;
      min-height: 100vh;
    }

    .overlay-background::before {
      content: "";
      position: absolute;
      top: 0; left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.5); /* Mengatur gelapnya background */
      z-index: 0;
    }

    .about-section {
      position: relative;
      z-index: 1; /* pastikan isi tampil di atas overlay */
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 85px 20px;
    }

    .about-img {
      width: 100%;
      max-height: 400px;
      object-fit: cover;
      border-radius: 20px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.15);
      transition: transform 0.3s ease;
    }

    .about-img:hover {
      transform: scale(1.02);
    }

    .about-text h2 {
      font-weight: 700;
      color: #ffff;
    }

    .about-text p {
      font-size: 1.05rem;
      line-height: 1.8;
      color: #fff;
    }

    @media (max-width: 768px) {
      .about-text {
        margin-top: 30px;
      }
    }
    .floating-map-btn {
  position: fixed;
  bottom: 25px;
  right: 25px;
  z-index: 9999;
  background: #ffffff;
  padding: 14px;
  border-radius: 50%;
  box-shadow: 0 4px 12px rgba(0,0,0,0.25);
  border: 2px solid #f7d9e4; /* warna pink pastel */
  transition: 0.3s;
}

.floating-map-btn:hover {
  transform: scale(1.12);
  box-shadow: 0 6px 16px rgba(0,0,0,0.35);
}

.floating-map-btn img {
  width: 32px;
  height: 32px;
}

  </style>
</head>
<body>
<div class="overlay-background">
  <div class="container about-section">
    <div class="row align-items-center justify-content-center">
      <div class="col-md-6">
        <img src="../img/donuts.jpg" alt="Tentang Kami" class="about-img">
      </div>
      <div class="col-md-6 about-text">
        <h2 class="mb-4">Tentang Kami 🍩</h2>
        <p>
          DonutsChaca adalah UMKM rumahan yang berdiri sejak tahun 2022. Berawal dari hobi membuat donat,
          kami mencoba memproduksi aneka kue rumahan dengan cita rasa premium dan harga yang tetap terjangkau.
        </p>
        <p>
          Dengan bahan-bahan berkualitas dan tanpa bahan pengawet, kami berkomitmen untuk memberikan produk
          terbaik kami kepada pelanggan. DonutsChaca terus berkembang dan kini melayani berbagai pesanan untuk acara,
          kantor, dan pelanggan retail.
        </p>
        <p>
          Terima kasih telah mendukung usaha lokal UMKM kami 💕
        </p>
      </div>
    </div>
  </div>
</div>
<a href="https://maps.app.goo.gl/UJo4kY9LsMKPG5gL8?g_st=ac" 
   target="_blank" 
   class="floating-map-btn">
    <img src="../donut.png" alt="Lokasi DonutsChaca">
</a>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
