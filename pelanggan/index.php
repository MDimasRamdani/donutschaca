<?php 
session_start();
include "../koneksi/navbar.php"; 
include "../koneksi/koneksi.php";
$query = mysqli_query($koneksi, "SELECT * FROM product");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Beranda - Donuts Chaca</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css"/>
  <style>
    body {
      background: url('../img/cake.jpg') no-repeat center center fixed;
      background-size: cover;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      margin: 0;
      color: #fff;
      overflow: scroll;
      overflow-x: hidden;
    }

    .overlay {
      background-color: rgba(0, 0, 0, 0.5);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      align-items: center;
      padding: 10px 10px;
    }

    .hero {
      background-color: rgba(255, 255, 255, 0.1);
      text-align: center;
      margin-bottom: 0;
      max-width: 700px;
      padding: 20px;
      border-radius: 15px;
    }

    .hero h1 {
      font-size: 2.5rem;
      font-weight: bold;
      color: #ffdd57;
    }

    .hero p {
      font-size: 1.1rem;
      color: #f8f9fa;
    }

    .btn-donuts {
      background-color: #ff4081;
      border: none;
      transition: 0.3s ease;
    }

    .btn-donuts:hover {
      background-color: #e91e63;
      transform: scale(1.05);
    }

    .swiper {
      width: 100%;
      padding-top: 10px;
      padding-bottom: 10px;
      padding-right: 20px;
      padding-left: 35px;
      gap: 10px;
    }

    .swiper-slide {
      box-sizing: border-box;
      display: flex !important;
      justify-content: center;
    }

    .produk-card {
      width: 250px;
      background-color: rgba(255, 255, 255, 0.9);
      border-radius: 15px;
      overflow: hidden;
      transition: transform 0.3s ease;
      margin: 0 auto;
    }

    .anggota-card {
  width: 250px;
  background-color: rgba(255, 255, 255, 0.9);
  border-radius: 15px;
  overflow: hidden;
  transition: transform 0.3s ease;
  text-align: center;
  margin: 0 auto;
}

.anggota-card:hover {
  transform: translateY(-5px);
}

.anggota-card img {
  width: 100%;
  height: 190px;
  object-fit: cover;
  border-radius: 15px 15px 0 0;
}

.anggota-card .card-body {
  padding: 1rem;
}

.anggota-card .card-title {
  color: #e91e63;
  font-weight: bold;
  font-size: 1.1rem;
}

.anggota-card .card-text {
  color: #333;
  font-size: 0.95rem;
}

    .produk-card:hover {
      transform: translateY(-5px);
    }

    .produk-card img {
      width: 100%;
      height: 180px;
      object-fit: cover;
    }

    .produk-card .card-body {
      text-align: center;
      padding: 1rem;
      gap: 4px;
    }

    .produk-card .card-title {
      color: #ff4081;
      font-weight: bold;
    }

    .produk-card .card-text {
      color: #333;
    }

    .swiper-button-next,
    .swiper-button-prev {
      color: #ff4081;
    }

    .produk-card img {
  width: 100%;
  height: 200px;
  object-fit: cover;
  border-radius: 10px 10px 0 0;
}
.badge-promo {
  position: absolute;
  top: 10px;
  left: 10px;
  background: #ff3b3b;
  color: white;
  padding: 6px 10px;
  border-radius: 8px;
  font-size: 0.8rem;
  font-weight: bold;
  z-index: 10;
}
.harga-coret {
  text-decoration: line-through;
  color: #888;
  margin-top: 8px;
  font-size: 0.9rem;
}
.harga-promo {
  color: #d10000;
  font-weight: bold;
}

  </style>
</head>
<body>

<div class="overlay">
    <!-- Hero Section -->
<div class="hero">
  <h1>Selamat Datang di DonutsChaca 🍩</h1>
  <p class="lead">Nikmati kue premium buatan rumahan dengan rasa yang lezat dan harga bersahabat!</p>
  <a href="produk.php" class="btn btn-donuts text-white mt-2 px-4 py-2">Lihat Semua Produk</a>
</div>
<!-- Swiper Slider -->
  <div class="swiper mySwiper">
    <div class="swiper-wrapper">
      <?php while ($product = mysqli_fetch_assoc($query)) : ?>
        <div class="swiper-slide">
          <div class="produk-card position-relative">
    <!-- Promo Badge -->
    <?php if ($product['promo'] == 1): ?>
        <span class="badge-promo">Promo 10%</span>
    <?php endif; ?>
    <img src="../img/<?= $product['image'] ?>" alt="<?= $product['nama'] ?>">
    <div class="card-body">
        <h5 class="card-title"><?= $product['nama'] ?></h5>
        <?php if ($product['promo'] == 1): 
            $diskon = $product['price'] * 0.10;
            $harga_promo = $product['price'] - $diskon;
        ?>
            <!-- Harga promo -->
            <p class="harga-coret">Rp <?= number_format($product['price'], 0, ',', '.') ?></p>
            <p class="harga-promo">Rp <?= number_format($harga_promo, 0, ',', '.') ?></p>
        <?php else: ?>
            <!-- Harga normal -->
             <p class="harga-coret" style="visibility:hidden;">Rp 0</p>
            <p class="card-text">Rp <?= number_format($product['price'], 0, ',', '.') ?></p>
        <?php endif; ?>
        <a href="produk.php?id=<?= $product['product_id'] ?>" class="btn btn-donuts text-white mt-2">Detail</a>
    </div>
</div>
        </div>
      <?php endwhile; ?>
    </div>

    <!-- Tombol navigasi -->
    <div class="swiper-button-prev"></div>
    <div class="swiper-button-next"></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
<script>
  var swiper = new Swiper(".mySwiper", {
    slidesPerView: 1,
    spaceBetween: 10,
    navigation: {
      nextEl: ".swiper-button-next",
      prevEl: ".swiper-button-prev",
    },
    breakpoints: {
      576: { slidesPerView: 2 },
      768: { slidesPerView: 3 },
      992: { slidesPerView: 4 }
    }
  });
</script>
</body>
</html>