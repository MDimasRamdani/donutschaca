<?php
include "../koneksi/navbar.php";
$isLoggedIn = isset($_SESSION['pelanggan']);
$koneksi = new mysqli("localhost", "root", "", "donutschaca");
// Ambil data pelanggan yang login
$customer = null;
if ($isLoggedIn) {
    $user_id = $_SESSION['pelanggan']['user_id'];
    $stmt = $koneksi->prepare("SELECT * FROM customers WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result_customer = $stmt->get_result();
    $customer = $result_customer->fetch_assoc();
    $stmt->close();
}
$result = mysqli_query($koneksi, "SELECT * FROM product ORDER BY nama ASC");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['produk'])) {
    if (!$isLoggedIn) {
        echo "<script>alert('Anda harus register/login untuk memesan.'); window.location='register_pelanggan.php';</script>";
        exit();
    }
    $nama = $_POST['nama'];
    $hp = $_POST['hp'];
    $alamat = $_POST['alamat'];
    $deskripsi = $_POST['deskripsi'];
    $produkJSON = $_POST['produk'];
    $produk = json_decode($produkJSON, true);
    $metode = $_POST['metode_pembayaran'];

    if (!preg_match("/^[0-9]+$/", $hp)) {
        echo "<script>alert('Nomor HP harus dalam bentuk angka');window.location='pemesanan.php';</script>";
        exit();
    }

    $total = 0;

    // ============================
    // HITUNG TOTAL (dengan promo)
    // ============================
    foreach ($produk as $p) {
        $qty = $p['qty'];
        $harga = $p['harga']; // sudah harga promo
        $subtotal = $qty * $harga;
        $total += $subtotal;
    }

    $tanggal_order = date("Y-m-d H:i:s");
    $status = 'pending';
    $user_id = $_SESSION['pelanggan']['user_id'];

    // Anti double order 5 menit
    $cek = $koneksi->prepare("SELECT order_id FROM orders WHERE nama=? AND telepon=? AND order_date>=DATE_SUB(NOW(), INTERVAL 5 MINUTE)");
    $cek->bind_param("ss", $nama, $hp);
    $cek->execute();
    $cek->store_result();
    if ($cek->num_rows > 0) {
        echo "<script>alert('Anda baru saja membuat pesanan, tunggu beberapa menit.');window.location='pemesanan.php';</script>";
        exit();
    }
    $cek->close();

    // Tambah ke tabel orders
    $product_names = [];
    foreach ($produk as $item) {
        $product_names[] = $item['nama'];
    }
    $produkText = implode(", ", $product_names);

    $stmt = $koneksi->prepare("INSERT INTO orders (user_id, order_date, total_price, status, nama, telepon, alamat, deskripsi, produk)
                               VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isdssssss", $user_id, $tanggal_order, $total, $status, $nama, $hp, $alamat, $deskripsi, $produkText);
    $stmt->execute();
    $order_id = $stmt->insert_id;
    $_SESSION['order_id'] = $order_id;
    $stmt->close();

    // Tambah ke order_items
    foreach ($produk as $item) {
        $stmt = $koneksi->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiid", $order_id, $item['id'], $item['qty'], $item['harga']);
        $stmt->execute();
        $stmt->close();
    }

    // Tambah ke payments
    $stmt = $koneksi->prepare("INSERT INTO payments (order_id, payment_date, amount, method, status)
                               VALUES (?, NOW(), ?, ?, 'unpaid')");
    $stmt->bind_param("ids", $order_id, $total, $metode);
    $stmt->execute();
    $stmt->close();

    echo "<script>window.location='pembayaran.php?order_id={$order_id}';</script>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Pemesanan - Donuts Chaca</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
  body {
    background: linear-gradient(to right, #fff6f9, #ffeaf1);
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  }
  h2, h5 { color: #d63384; font-weight: bold; }

  .product-card {
    border: 1px solid #e4dcdc;
    border-radius: 12px;
    padding: 10px;
    text-align: center;
    background-color: #fff;
    transition: 0.3s;
    cursor: pointer;
    position: relative;
  }
  .product-card:hover { transform: scale(1.02); box-shadow: 0 6px 12px rgba(0,0,0,0.1); }

  .promo-badge {
    position: absolute;
    top: 8px;
    right: 8px;
    background: #ff4081;
    color: white;
    padding: 4px 8px;
    border-radius: 8px;
    font-size: 0.75rem;
    font-weight: bold;
  }

  .product-img {
    height: 120px;
    object-fit: cover;
    width: 100%;
    border-radius: 8px;
  }
  /* === FIX SCROLL PRODUK HILANG === */
.scroll-produk {
    max-height: 500px;
    overflow-y: auto;
    padding-right: 5px;
}

/* Biar grid tidak rusak */
.scroll-produk .col-6,
.scroll-produk .col-sm-4,
.scroll-produk .col-md-4 {
    display: flex;
}

/* Agar card tidak meloncat & layout stabil */
.scroll-produk .product-card {
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}
  </style>
</head>
<body>

<div class="container-fluid py-4 d-flex justify-content-center align-items-center" style="min-height: 100vh;">
  <div class="w-100">
    <h2 class="text-center mb-4">Pemesanan Produk</h2>

    <div class="row flex-column flex-lg-row">

      <!-- LIST PRODUK -->
      <div class="col-12 col-lg-6 mb-4">
        <h5>Produk Kami 🍩</h5>
<div class="mb-3">
  <input type="text" id="searchInput" class="form-control" placeholder="🔍 Cari produk..." 
         onkeyup="filterProduk()" style="border-radius: 10px;">
</div>
        <div class="scroll-produk row g-2">
          <?php 
          $result = mysqli_query($koneksi, "SELECT * FROM product ORDER BY nama ASC");
          while($row = $result->fetch_assoc()):
            
            // harga promo
            $promo = $row['promo'] == 1;
            $hargaNormal = $row['price'];
            $hargaPromo = $promo ? ($hargaNormal * 0.9) : $hargaNormal;
          ?>
          
          <div class="col-6 col-sm-4 col-md-4">
            <div class="product-card" onclick="tambahProduk(<?= $row['product_id'] ?>, '<?= $row['nama'] ?>', <?= $hargaPromo ?>)">
              
              <?php if($promo): ?>
                <span class="promo-badge">PROMO 10%</span>
              <?php endif; ?>

              <img src="../img/<?= htmlspecialchars($row['image']) ?>" class="product-img" alt="<?= htmlspecialchars($row['nama']) ?>">

              <h6 class="mt-2"><?= htmlspecialchars($row['nama']) ?></h6>

              <?php if($promo): ?>
                <p class="text-danger mb-0">
                  <b>Rp <?= number_format($hargaPromo,0,',','.') ?></b>
                </p>
                <small class="text-muted"><s>Rp <?= number_format($hargaNormal,0,',','.') ?></s></small>
              <?php else: ?>
                <p class="text-danger">Rp <?= number_format($hargaNormal,0,',','.') ?></p>
              <?php endif; ?>

            </div>
          </div>

          <?php endwhile; ?>
        </div>
      </div>

      <!-- FORM PESANAN -->
      <div class="col-12 col-lg-6">

        <h5>Form Pemesanan 📝</h5>

        <form method="POST">

          <div class="mb-2"><label>Nama</label>
            <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($customer['nama'] ?? '') ?>">
          </div>

          <div class="mb-2"><label>No HP</label>
            <input type="text" name="hp" class="form-control" value="<?= htmlspecialchars($customer['telepon'] ?? '') ?>">
          </div>

          <div class="mb-2"><label>Alamat</label>
            <textarea name="alamat" class="form-control"><?= htmlspecialchars($customer['alamat'] ?? '') ?></textarea>
          </div>

          <div class="mb-2">
            <label>Deskripsi</label>
            <textarea name="deskripsi" class="form-control"></textarea>
          </div>

          <div class="mb-2">
            <label>Metode Pembayaran</label>
            <select name="metode_pembayaran" class="form-control" required>
              <option value="">Pilih Metode</option>
              <option value="COD">COD</option>
              <option value="Transfer Bank">Transfer Bank</option>
              <option value="E-Wallet">ShopeePay</option>
            </select>
          </div>

          <h6 class="mt-3">Produk dipilih:</h6>
          <div class="scroll-pilihan mb-2">
            <ul id="daftar-produk" class="list-group"></ul>
          </div>

          <input type="hidden" name="produk" id="produk-hidden">
          <p><strong>Total: Rp <span id="total-harga">0</span></strong></p>

          <?php if ($isLoggedIn): ?>
            <button type="button" class="btn btn-success w-100" onclick="konfirmasiPesanan()">Pesan Sekarang</button>
          <?php else: ?>
            <button type="button" class="btn btn-secondary w-100" onclick="redirectToLogin()">Login dulu untuk memesan</button>
          <?php endif; ?>
        </form>
<a href="cek_status.php" target="_blank"
   class="btn btn-primary btn-sm d-flex align-items-center justify-content-center"
   style="position: absolute; top: 80px; right: 20px; z-index: 1000; width: 40px; height: 40px; border-radius: 50%;"
   data-bs-toggle="tooltip" data-bs-placement="left" title="Detail Pesanan">
    <i class="bi bi-receipt-cutoff"></i>
</a>
      </div>

    </div>
  </div>
</div>

<script>
let produkDipilih = [];

function tambahProduk(id, nama, harga) {
  const index = produkDipilih.findIndex(item => item.id === id);
  if (index !== -1) {
    produkDipilih[index].qty++;
  } else {
    produkDipilih.push({id, nama, harga, qty: 1});
  }
  updateDaftar();
}

function tambahQty(i) {
  produkDipilih[i].qty++;
  updateDaftar();
}

function kurangQty(i) {
  produkDipilih[i].qty--;
  if (produkDipilih[i].qty <= 0) produkDipilih.splice(i, 1);
  updateDaftar();
}

function updateDaftar() {
  let list = document.getElementById('daftar-produk');
  list.innerHTML = '';
  let total = 0;

  produkDipilih.forEach((item, i) => {
    total += item.harga * item.qty;

    list.innerHTML += `
      <li class="list-group-item d-flex justify-content-between align-items-center">
        <div>${item.nama} <span class="badge bg-secondary ms-2">${item.qty}x</span></div>
        <div>
          Rp ${(item.harga * item.qty).toLocaleString('id-ID')}
          <button type="button" class="btn btn-sm btn-success ms-2" onclick="tambahQty(${i})">+</button>
          <button type="button" class="btn btn-sm btn-danger ms-1" onclick="kurangQty(${i})">-</button>
        </div>
      </li>`;
  });

  document.getElementById('total-harga').textContent = total.toLocaleString('id-ID');
  document.getElementById('produk-hidden').value = JSON.stringify(produkDipilih);
}

function konfirmasiPesanan() {
  if (produkDipilih.length === 0) {
    alert("Pilih minimal 1 produk.");
    return;
  }
  if (confirm("Yakin membuat pesanan?")) {
    document.querySelector("form").submit();
  }
}

function redirectToLogin() {
  alert("Anda harus login dulu.");
  window.location.href = "register_pelanggan.php";
}
</script>

</body>
</html>

<?php $koneksi->close(); ?>
