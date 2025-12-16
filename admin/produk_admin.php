<?php
include '../koneksi/navbar_admin.php';
include "../koneksi/koneksi.php";
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Hapus produk
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($koneksi, "DELETE FROM product WHERE product_id = '$id'");
    header("Location: produk_admin.php");
    exit;
}

// Tambah/Edit produk
if (isset($_POST['simpan'])) {
    $id = $_POST['product_id'];
    $nama = $_POST['nama'];
    $harga = $_POST['price'];
    $stok = $_POST['stok'];
    $keterangan = $_POST['keterangan'];

    // checkbox promo (jika dicentang → 1, jika tidak → 0)
    $promo = isset($_POST['promo']) ? 1 : 0;

    // upload gambar
    $gambar = $_FILES['image']['name'];
    $tmp = $_FILES['image']['tmp_name'];

    if ($gambar) {
        move_uploaded_file($tmp, "../img/$gambar");
    }

    // Jika tambah produk
    if ($id == '') {
        $gambar_final = $gambar ?: ''; // default kosong

        $query = "INSERT INTO product (nama, price, stok, image, keterangan, promo)
                  VALUES ('$nama', '$harga', '$stok', '$gambar_final', '$keterangan', $promo)";
    } 
    
    // Jika edit produk
    else {
        if ($gambar) {
            $query = "UPDATE product 
                      SET nama='$nama', price='$harga', stok='$stok', image='$gambar', 
                          keterangan='$keterangan', promo=$promo
                      WHERE product_id='$id'";
        } else {
            $query = "UPDATE product 
                      SET nama='$nama', price='$harga', stok='$stok', 
                          keterangan='$keterangan', promo=$promo
                      WHERE product_id='$id'";
        }
    }

    mysqli_query($koneksi, $query);
    header("Location: produk_admin.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Produk</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
      body {
        background: url('../img/donat1.png') no-repeat center center fixed;
        background-size: cover;
      }
      .table th, .table td {
        vertical-align: middle;
      }
      .badge-promo {
        background-color: #ff3b3b;
        color: white;
        font-size: 0.75rem;
        padding: 5px 10px;
        border-radius: 8px;
      }
    </style>
</head>
<body>

<div class="container py-5">
    <h2 class="text-center mt-4">Kelola Produk</h2>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="dashboard.php" class="btn btn-secondary">← Kembali</a>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#produkModal" onclick="tambahProduk()">+ Tambah Produk</button>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-hover bg-white align-middle text-center">
            <thead class="table-secondary">
                <tr>
                    <th>Gambar</th>
                    <th>Nama</th>
                    <th>Harga</th>
                    <th>Stok</th>
                    <th>Promo</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $data = mysqli_query($koneksi, "SELECT * FROM product");
                while ($d = mysqli_fetch_assoc($data)) {
                    echo "<tr>
                        <td><img src='../img/" . htmlspecialchars($d['image']) . "' width='60' class='img-thumbnail'></td>
                        <td>" . htmlspecialchars($d['nama']) . "</td>
                        <td>Rp " . number_format($d['price'], 0, ',', '.') . "</td>
                        <td>" . htmlspecialchars($d['stok']) . "</td>

                        <td>";
                    if ($d['promo'] == 1) {
                        echo "<span class='badge-promo'>Promo 10%</span>";
                    } else {
                        echo "-";
                    }
                    echo "</td>

                        <td>
                            <div class='d-flex justify-content-center gap-2'>
                                <button class='btn btn-sm btn-warning' 
                                        data-bs-toggle='modal' 
                                        data-bs-target='#produkModal' 
                                        onclick='editProduk(" . json_encode($d) . ")'>Edit</button>

                                <a href='produk_admin.php?hapus={$d['product_id']}' 
                                   class='btn btn-sm btn-danger' 
                                   onclick='return confirm(\"Yakin ingin menghapus produk ini?\")'>Hapus</a>
                            </div>
                        </td>
                    </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Produk -->
<div class="modal fade" id="produkModal" tabindex="-1">
  <div class="modal-dialog">
    <form method="post" enctype="multipart/form-data" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Form Produk</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <input type="hidden" name="product_id" id="product_id">

        <div class="mb-3">
          <label class="form-label">Nama Produk</label>
          <input type="text" name="nama" id="nama" class="form-control" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Harga</label>
          <input type="number" name="price" id="price" class="form-control" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Keterangan</label>
          <input type="text" name="keterangan" id="keterangan" class="form-control">
        </div>

        <div class="mb-3">
          <label class="form-label">Stok</label>
          <select name="stok" id="stok" class="form-select" required>
            <option value="">-- Pilih --</option>
            <option value="Tersedia">Tersedia</option>
            <option value="Kosong">Kosong</option>
          </select>
        </div>

        <!-- Checkbox Promo -->
        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" name="promo" id="promo" value="1">
            <label class="form-check-label" for="promo">
                Aktifkan Promo 10%
            </label>
        </div>

        <div class="mb-3">
          <label class="form-label">Gambar</label>
          <input type="file" name="image" class="form-control">
        </div>

      </div>

      <div class="modal-footer">
        <button type="submit" name="simpan" class="btn btn-success">Simpan</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
      </div>
    </form>
  </div>
</div>

<script>
function tambahProduk() {
    document.getElementById('product_id').value = '';
    document.getElementById('nama').value = '';
    document.getElementById('price').value = '';
    document.getElementById('stok').value = '';
    document.getElementById('keterangan').value = '';
    document.getElementById('promo').checked = false; // reset checkbox
}

function editProduk(data) {
    document.getElementById('product_id').value = data.product_id;
    document.getElementById('nama').value = data.nama;
    document.getElementById('price').value = data.price;
    document.getElementById('stok').value = data.stok;
    document.getElementById('keterangan').value = data.keterangan;

    // load status promo
    document.getElementById('promo').checked = (data.promo == 1);
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
