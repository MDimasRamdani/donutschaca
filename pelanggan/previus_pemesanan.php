<?php
include "../koneksi/navbar.php";
$isLoggedIn = isset($_SESSION['pelanggan']); // Cek apakah user sudah login
$koneksi = new mysqli("localhost", "root", "", "donutschaca");
// Ambil data pelanggan yang sedang login
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
    $metode = $_POST['metode_pembayaran']; // NEW
    $total = 0;


if (!preg_match("/^[0-9]+$/", $hp)) {
    echo "<script>alert('Nomor HP harus dalam bentuk angka');window.location='pemesanan.php';</script>";
    exit();
}

    foreach ($produk as $p) {
        $qty = $p['qty'];
        $harga = $p['harga'];
        $subtotal = $qty * $harga;
        $total += $subtotal;
    }

    $tanggal_order = date("Y-m-d H:i:s");
    $status = 'pending';
    $user_id = $_SESSION['pelanggan']['user_id'];

    // Cek anti double insert berdasarkan nama, hp, tanggal
    $cek = $koneksi->prepare("SELECT order_id FROM orders WHERE nama=? AND telepon=? AND order_date>=DATE_SUB(NOW(), INTERVAL 5 MINUTE)");
    $cek->bind_param("ss", $nama, $hp);
    $cek->execute();
    $cek->store_result();
    if ($cek->num_rows > 0) {
        echo "<script>alert('Anda sudah membuat pesanan baru-baru ini.');window.location='pemesanan.php';</script>";
        exit();
    }
    $cek->close();

    // Simpan pesanan/orderan ke database
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
    $_SESSION['order_id'] = $order_id; // Simpan order_id ke session
    $stmt->close();

    // Simpan order item
    foreach ($produk as $item) {
        $stmt = $koneksi->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiid", $order_id, $item['id'], $item['qty'], $item['harga']);      
        $stmt->execute();
        $stmt->close();
    }

    // Simpan pembayaran ke tabel payments
    $stmt = $koneksi->prepare("INSERT INTO payments (order_id, payment_date, amount, method, status)
                            VALUES (?, NOW(), ?, ?, 'unpaid')");
    $stmt->bind_param("ids", $order_id, $total, $metode);
    $stmt->execute();
    $stmt->close();

    // Redirect ke pembayaran.php
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

  h2, h5 {
    color: #d63384;
    font-weight: bold;
  }

  .product-card {
    border: 1px solid #e4dcdc;
    border-radius: 12px;
    padding: 10px;
    text-align: center;
    background-color: #fff;
    transition: 0.3s;
    cursor: pointer;
  }

  .product-card:hover {
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
    transform: scale(1.02);
  }

  .product-img {
    height: 120px;
    object-fit: cover;
    width: 100%;
    border-radius: 8px;
  }

  .scroll-produk {
    max-height: 500px;
    overflow-y: auto;
    padding-right: 5px;
  }

  .scroll-pilihan {
    max-height: 130px;
    overflow-y: auto;
    margin-bottom: 10px;
    border: 1px solid #ddd;
    border-radius: 6px;
    background: #fff;
  }

  .btn-success {
    background-color: #28a745;
    border: none;
  }

  .btn-success:hover {
    background-color: #218838;
  }

  .scroll-produk .col-6,
.scroll-produk .col-md-4 {
  display: flex;
}

.scroll-produk .product-card {
  flex: 1;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}

  @media (max-width: 768px) {
    .product-img {
      height: 100px;
    }
    .scroll-produk {
      max-height: 300px;
    }
    .btn {
      font-size: 1rem;
    }
  }
</style>
</head>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<body>
<div class="container-fluid py-4 d-flex justify-content-center align-items-center" style="min-height: 100vh;">
  <div class="w-100">
    <h2 class="text-center mb-4">Pemesanan Produk</h2>
    <div class="row flex-column flex-lg-row justify-content-center align-items-start">

      <!-- Produk -->
      <div class="col-12 col-lg-6 mb-4">
        <h5 class="text-center text-lg-start">Produk Kami 🍩</h5>
        <div class="mb-3">
  <input type="text" id="searchInput" class="form-control" placeholder="🔍 Cari produk..." 
         onkeyup="filterProduk()" style="border-radius: 10px;">
</div>
        <div class="scroll-produk row g-2 justify-content-center">
          <?php while($row = $result->fetch_assoc()): ?>
            <div class="col-6 col-sm-4 col-md-4">
              <div class="product-card text-center" onclick="tambahProduk(<?= $row['product_id'] ?>, '<?= $row['nama'] ?>', <?= $row['price'] ?>)">
                <img src="../img/<?= htmlspecialchars($row['image']) ?>" class="product-img" alt="<?= htmlspecialchars($row['nama']) ?>">
                <h6 class="mt-2"><?= htmlspecialchars($row['nama']) ?></h6>
                <p class="text-danger">Rp <?= number_format($row['price'], 0, ',', '.') ?></p>
                <small class="text-muted">Stok: <?= $row['stok'] ?></small>
              </div>
            </div>
          <?php endwhile; ?>
        </div>
      </div>

      <!-- Form -->
      <div class="col-12 col-lg-6">
        <h5 class="text-center text-lg-start">Form Pemesanan 📝</h5>
        <form method="POST" onsubmit="return validateForm();">
          <div class="mb-2">
  <label>Nama Pelanggan</label>
  <input type="text" name="nama" class="form-control" 
         value="<?= htmlspecialchars($customer['nama'] ?? '') ?>">
</div>
<div class="mb-2">
  <label>No HP</label>
  <input type="text" name="hp" class="form-control" 
         value="<?= htmlspecialchars($customer['telepon'] ?? '') ?>">
</div>
<div class="mb-2">
  <label>Alamat</label>
  <textarea name="alamat" class="form-control" rows="3"><?= htmlspecialchars($customer['alamat'] ?? '') ?></textarea>
</div>

          <div class="mb-2">
            <label>Deskripsi / Permintaan Khusus</label>
            <textarea name="deskripsi" class="form-control" rows="3" placeholder="Contoh: Request warna pink, tanpa topping..."></textarea>
          </div>

          <div class="mb-2">
            <label>Metode Pembayaran</label>
            <select name="metode_pembayaran" class="form-control" required>
              <option value="">Pilih Metode</option>
              <option value="COD">COD</option>
              <option value="Transfer Bank">Transfer via Bank BCA</option>
              <option value="E-Wallet">ShopeePay</option>
            </select>
          </div>

          <h6 class="mt-3">Produk yang dipilih:</h6>
          <div class="scroll-pilihan mb-2">
            <ul id="daftar-produk" class="list-group mb-0"></ul>
          </div>
          <input type="hidden" name="produk" id="produk-hidden">
          <p><strong>Total: Rp <span id="total-harga">0</span></strong></p>
          <?php if ($isLoggedIn): ?>
          <button type="button" class="btn btn-success w-100" onclick="konfirmasiPesanan()">Pesan Sekarang</button>
          <?php else: ?>
          <button type="button" class="btn btn-secondary w-100" onclick="redirectToLogin()">SILAHKAN REGISTER/LOGIN DULU SEBELUM MEMBUAT PESANAN</button>
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
    const index = produkDipilih.findIndex(item => item.nama === nama);
    if (index !== -1) {
      produkDipilih[index].qty += 1;
    } else {
      produkDipilih.push({id, nama, harga, qty: 1});
    }
    updateDaftar();
  }

  function tambahQty(index) {
    produkDipilih[index].qty += 1;
    updateDaftar();
  }

  function kurangQty(index) {
    produkDipilih[index].qty -= 1;
    if (produkDipilih[index].qty <= 0) {
      produkDipilih.splice(index, 1);
    }
    updateDaftar();
  }

  function updateDaftar() {
    let list = document.getElementById('daftar-produk');
    list.innerHTML = '';
    let total = 0;

    produkDipilih.forEach((item, index) => {
      total += item.harga * item.qty;
      let li = document.createElement('li');
      li.className = 'list-group-item d-flex justify-content-between align-items-center';
      li.innerHTML = `
        <div>
          ${item.nama} <span class="badge bg-secondary ms-2">${item.qty}x</span>
        </div>
        <div>
          Rp ${(item.harga * item.qty).toLocaleString('id-ID')}
          <button type="button" class="btn btn-sm btn-success ms-2" onclick="tambahQty(${index})">+</button>
          <button type="button" class="btn btn-sm btn-danger ms-1" onclick="kurangQty(${index})">-</button>
        </div>
      `;
      list.appendChild(li);
    });

    document.getElementById('total-harga').textContent = total.toLocaleString('id-ID');
    document.getElementById('produk-hidden').value = JSON.stringify(produkDipilih);
  }

  function konfirmasiPesanan() {
    if (produkDipilih.length === 0) {
      alert("Silakan pilih minimal 1 produk.");
      return;
    }
    const konfirmasi = confirm("Apakah Anda yakin ingin membuat pesanan?");
    if (konfirmasi) {
      document.querySelector("form").submit();
    }
  }
  // Cek untuk login pelanggan
  function redirectToLogin() {
  alert("Anda harus login terlebih dahulu untuk melakukan pemesanan.");
  window.location.href = "register_pelanggan.php"; // Ganti sesuai lokasi file login kamu
}
// === FITUR PENCARIAN PRODUK ===
function filterProduk() {
  const input = document.getElementById('searchInput').value.toLowerCase().trim();
  const cards = document.querySelectorAll('.product-card');
  let ditemukan = false;

  cards.forEach(card => {
    const namaProduk = card.querySelector('h6').textContent.toLowerCase();
    if (namaProduk.includes(input)) {
      card.parentElement.style.display = 'block';
      ditemukan = true;
    } else {
      card.parentElement.style.display = 'none';
    }
  });

  // Jika input dikosongkan → reset semua
  if (input === "") {
    produkDipilih = []; // Reset daftar produk yang dipilih
    updateDaftar(); // Hapus semua tampilan produk di daftar
    document.getElementById('total-harga').textContent = "0"; // Reset total harga
    document.getElementById('produk-hidden').value = ""; // Reset hidden input
  }
}
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php $koneksi->close(); ?>