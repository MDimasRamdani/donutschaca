<?php
include "../koneksi/navbar.php";
$koneksi = new mysqli("localhost", "root", "", "donutschaca");
if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}

// Ambil semua produk dari database dan urutkan berdasarkan nama (ASC)
$query = "SELECT * FROM product ORDER BY nama ASC";
$result = $koneksi->query($query);
$products = $result->fetch_all(MYSQLI_ASSOC);

// ------------------------------
// 🔍 Fungsi Algoritma Pencarian
// ------------------------------
function linearSearch($data, $target) {
    $start = microtime(true);
    foreach ($data as $item) {
        if (strtolower($item['nama']) == strtolower($target)) {
            $end = microtime(true);
            return [
                'result' => $item,
                'time' => ($end - $start) * 1000,
                'start' => $start,
                'end' => $end
            ];
        }
    }
    $end = microtime(true);
    return [
        'result' => null,
        'time' => ($end - $start) * 1000,
        'start' => $start,
        'end' => $end
    ];
}

function binarySearch($data, $target) {
    // usort($data, function($a, $b) {
    //     return strcasecmp($a['nama'], $b['nama']);
    // });

    $start = microtime(true);
    $left = 0;
    $right = count($data) - 1;

    while ($left <= $right) {
        $mid = floor(($left + $right) / 2);
        $cmp = strcmp(strtolower($data[$mid]['nama']), strtolower($target));
        if ($cmp == 0) {
            $end = microtime(true);
            return [
                'result' => $data[$mid],
                'time' => ($end - $start) * 1000,
                'start' => $start,
                'end' => $end
            ];
        } elseif ($cmp < 0) {
            $left = $mid + 1;
        } else {
            $right = $mid - 1;
        }
    }

    $end = microtime(true);
    return [
        'result' => null,
        'time' => ($end - $start) * 1000,
        'start' => $start,
        'end' => $end
    ];
}

// ------------------------------
// 🚀 Proses Jika User Melakukan Pencarian
// ------------------------------
$searchResult = null;
$linear = null;
$binary = null;

if (isset($_GET['keyword']) && $_GET['keyword'] != '') {
    $keyword = trim($_GET['keyword']);
    $keywordLower = strtolower($keyword); // konversi ke huruf kecil
    $linear = linearSearch($products, $keywordLower);
    $binary = binarySearch($products, $keywordLower);
    $searchResult = $linear['result'];
}
?>
<?php if (isset($_GET['keyword']) && $_GET['keyword'] != ''): ?>
    <div class="alert alert-info text-center w-75 mx-auto">
        <h5>Hasil Pencarian untuk: <b><?= htmlspecialchars($keyword) ?></b></h5>
        <p>
            <b>Linear Search:</b> <?= number_format($linear['time'], 4) ?> ms |
            <b>Binary Search:</b> <?= number_format($binary['time'], 4) ?> ms
        </p>
        <!-- Tambahan informasi debugging -->
        <hr>
        <small>
            <b>Waktu Awal Linear:</b> <?= $linear['start'] ?? '-' ?><br>
            <b>Waktu Akhir Linear:</b> <?= $linear['end'] ?? '-' ?><br><br>
            <b>Waktu Awal Binary:</b> <?= $binary['start'] ?? '-' ?><br>
            <b>Waktu Akhir Binary:</b> <?= $binary['end'] ?? '-' ?><br>
        </small>
    </div>
<?php endif; ?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Produk - Donuts Chaca</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: url('../img/cake.jpg') no-repeat center center fixed;
      background-size: cover;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      margin: 0;
    }

    .container-produk {
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      justify-content: center;
      padding-top: 30px;
      padding-bottom: 40px;
    }

    .product-card {
      border: none;
      border-radius: 16px;
      overflow: hidden;
      background-color: #ffffff;
      box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
      transition: all 0.3s ease-in-out;
      display: flex;
      flex-direction: column;
      height: 100%;
    }

    .product-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    }

    .product-img {
      height: 180px;
      object-fit: cover;
      width: 100%;
    }

    .card-body {
      text-align: center;
      flex-grow: 1; /* biar semua card sama tinggi */
      display: flex;
      flex-direction: column;
      justify-content: space-between; /* tombol rata bawah */
    }

    .card-title {
      font-weight: 600;
      color: #ff4081;
    }

    .price {
      color: #e91e63;
      font-size: 1.1rem;
      font-weight: bold;
    }

    .stok {
      font-size: 0.85rem;
      color: #6c757d;
    }

    @media (max-width: 576px) {
      .product-img {
        height: 150px;
      }

      .card-title {
        font-size: 1rem;
      }

      .price {
        font-size: 1rem;
      }
    }
    .badge-promo {
  position: absolute;
  top: 10px;
  left: 10px;
  background: #ff3b3b;
  padding: 6px 10px;
  color: white;
  font-weight: bold;
  border-radius: 8px;
  font-size: 0.8rem;
  z-index: 10;
}
.harga-coret {
  text-decoration: line-through;
  color: #777;
  font-size: 0.9rem;
  margin-bottom: 0;
}
.harga-promo {
  color: #d10000;
  font-weight: bold;
  font-size: 1.1rem;
}
  </style>
</head>
<body>

<div class="container container-produk">
  <h2 class="text-center mb-4 fw-bold text-white" style="
    text-shadow: 
        -1px -1px 0 #000,  
         1px -1px 0 #000,
        -1px  1px 0 #000,
         1px  1px 0 #000;
">
    🍩 Daftar Produk DonutsChaca
  </h2>

  <!-- 🔍 FORM PENCARIAN -->
  <form method="GET" class="mb-4 text-center">
    <div class="d-flex justify-content-center">
      <input type="text" name="keyword" class="form-control w-50" 
        placeholder="Cari produk" 
        value="<?= isset($_GET['keyword']) ? htmlspecialchars($_GET['keyword']) : '' ?>">
      <button type="submit" class="btn btn-success ms-2">Cari</button>
    </div>
  </form>

  <!-- 💡 HASIL PENGUJIAN WAKTU -->
  <?php if(isset($_GET['keyword']) && $_GET['keyword'] != ''): ?>
    <div class="alert alert-info text-center w-75 mx-auto">
      <h5>Hasil Pencarian untuk: <b><?= htmlspecialchars($keyword) ?></b></h5>
      <p>
        <b>Linear Search:</b> <?= number_format($linear['time'], 4) ?> ms |
        <b>Binary Search:</b> <?= number_format($binary['time'], 4) ?> ms
      </p>
    </div>

    <?php if($searchResult): ?>
      <div class="row justify-content-center mb-5">
        <div class="col-md-4">
          <div class="card product-card">
            <img src="../img/<?= htmlspecialchars($searchResult['image']) ?>" class="product-img" alt="<?= htmlspecialchars($searchResult['nama']) ?>">
            <div class="card-body">
              <div>
                <h5 class="card-title"><?= htmlspecialchars($searchResult['nama']) ?></h5>
                <?php if ($searchResult['promo'] == 1): ?>
    <?php 
      $diskon = $searchResult['price'] * 0.10;
      $hargaPromo = $searchResult['price'] - $diskon;
    ?>
    <p class="harga-coret">Rp <?= number_format($searchResult['price'], 0, ',', '.') ?></p>
    <p class="harga-promo">Rp <?= number_format($hargaPromo, 0, ',', '.') ?></p>
<?php else: ?>
    <p class="price">Rp <?= number_format($searchResult['price'], 0, ',', '.') ?></p>
<?php endif; ?>
                <p class="stok">Stok: <?= htmlspecialchars($searchResult['stok']) ?></p>
                <p class="keterangan text-muted small"><?= htmlspecialchars($searchResult['keterangan']) ?></p>
              </div>
              <a href="pemesanan.php" class="btn btn-sm btn-primary mt-3">Pesan Sekarang</a>
            </div>
          </div>
        </div>
      </div>
    <?php else: ?>
      <p class="text-center text-white fw-bold">Produk tidak ditemukan.</p>
    <?php endif; ?>

  <?php endif; ?>

  <!-- 🧁 DAFTAR PRODUK DEFAULT (SUDAH TERURUT NAMA) -->
  <div class="row g-4 justify-content-center">
    <?php foreach($products as $row): ?>
      <div class="col-12 col-sm-6 col-md-4 col-lg-3 d-flex align-items-stretch">
        <div class="card product-card w-100">
          <?php if ($row['promo'] == 1): ?>
  <span class="badge-promo">Promo 10%</span>
<?php endif; ?>
          <img src="../img/<?= htmlspecialchars($row['image']) ?>" class="product-img" alt="<?= htmlspecialchars($row['nama']) ?>">
          <div class="card-body">
            <div>
              <h5 class="card-title"><?= htmlspecialchars($row['nama']) ?></h5>
              <?php if ($row['promo'] == 1): ?>
    <?php 
      $diskon = $row['price'] * 0.10;
      $hargaPromo = $row['price'] - $diskon;
    ?>
    <p class="harga-coret">Rp <?= number_format($row['price'], 0, ',', '.') ?></p>
    <p class="harga-promo">Rp <?= number_format($hargaPromo, 0, ',', '.') ?></p>
<?php else: ?>
    <p class="price">Rp <?= number_format($row['price'], 0, ',', '.') ?></p>
<?php endif; ?>
              <p class="keterangan text-muted small"><?= htmlspecialchars($row['keterangan']) ?></p>
              <p class="stok">Stok : <?= htmlspecialchars($row['stok']) ?></p>
            </div>
            <a href="pemesanan.php" class="btn btn-sm btn-primary mt-3">Pesan Sekarang</a>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
  const searchInput = document.querySelector("input[name='keyword']");
  searchInput.addEventListener("input", function() {
    if (this.value.trim() === "") {
      // Jika kolom pencarian kosong, reload halaman tanpa parameter keyword
      window.location.href = "produk.php";
    }
  });
});
</script>
</body>
</html>

<?php $koneksi->close(); ?>
