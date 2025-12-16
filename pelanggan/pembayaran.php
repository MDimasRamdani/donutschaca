<?php
session_start();
$koneksi = new mysqli("localhost", "root", "", "donutschaca");
if (isset($_GET['order_id'])) {
    $order_id = intval($_GET['order_id']);
} else {
    die("Order ID tidak ditemukan. Silakan lakukan pemesanan terlebih dahulu.");
}
// Ambil data pesanan
$query = $koneksi->query("SELECT * FROM orders WHERE order_id = $order_id") or die("Query error: " . $koneksi->error);
if ($query->num_rows == 0) {
    die("Pesanan tidak ditemukan.");
}
$order = $query->fetch_assoc();
// Ambil item dari tabel order_items
$items_query = $koneksi->query("
    SELECT p.nama, oi.quantity 
    FROM order_items oi
    JOIN product p ON oi.product_id = p.product_id
    WHERE oi.order_id = $order_id
");
$detail_produk = "";
while ($item = $items_query->fetch_assoc()) {
    $detail_produk .= "- " . $item['nama'] . " (" . $item['quantity'] . " pcs)%0A";
}
// Default untuk WA
$payment_type_display = "COD (Bayar di Tempat)"; // Diatur setelah upload berhasil
// ====== HITUNG ULANG TOTAL DENGAN PROMO 10% ======
$items_result = $koneksi->query("
    SELECT p.price, p.promo, oi.quantity
    FROM order_items oi
    JOIN product p ON oi.product_id = p.product_id
    WHERE oi.order_id = $order_id
");
$total_baru = 0;
while ($row = $items_result->fetch_assoc()) {
    $harga = $row['price'];

    // Jika produk promo, diskon 10%
    if ($row['promo'] == 1) {
        $harga = $harga * 0.9;
    }

    $total_baru += $harga * $row['quantity'];
}
// Update total yang dipakai halaman pembayaran
$order['total_price'] = $total_baru;
?>

<!DOCTYPE html>

<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pembayaran - Donuts Chaca</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #fff6f9; }
        .container { max-width: 600px; background: white; padding: 20px; border-radius: 10px; margin-top: 30px; }
        .rekening { font-size: 18px; font-weight: bold; color: #d9534f; }
        .btn-wa { background-color: #25d366; color: white; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="text-center">Pembayaran</h2>
        <p>Order ID: <strong>#<?= $order['order_id']; ?></strong></p>
        <p>Nama: <strong><?= htmlspecialchars($order['nama']); ?></strong></p>
        <p>No HP: <strong><?= htmlspecialchars($order['telepon']); ?></strong></p>
        <p>Alamat: <strong><?= htmlspecialchars($order['alamat']); ?></strong></p>
        <p>Total Bayar: <strong>Rp <?= number_format($order['total_price'], 0, ',', '.'); ?></strong></p>
        <hr>
        <h4>Instruksi Pembayaran</h4>
        <?php
        // Ambil metode pembayaran dari tabel payments
        $payment_query = $koneksi->prepare("SELECT method FROM payments WHERE order_id = ?");
        $payment_query->bind_param("i", $order_id);
        $payment_query->execute();
        $payment_result = $payment_query->get_result();
        if ($payment_result->num_rows > 0) {
            $payment = $payment_result->fetch_assoc();
            $metode_pembayaran = $payment['method'];
            $detail_metode = '';
            if ($metode_pembayaran == "Transfer Bank") {
                $detail_metode = "Transfer Via Bank BCA";
                echo "<p>Silakan transfer ke rekening berikut:</p>";
                echo "<p class='rekening'>BCA - 3450069069 a/n Wulandhari</p>";
            } elseif ($metode_pembayaran == "E-Wallet") {
                $detail_metode = "ShopeePay";
                echo "<p>Silakan transfer ke akun ShopeePay berikut:</p>";
                echo "<p class='rekening'>ShopeePay - 081381298618 a/n Wulandhari</p>";
            } elseif ($metode_pembayaran == "COD") {
                $detail_metode = "COD (Bayar di Tempat)";
                $payment_type_display = "COD (Bayar di Tempat)";
                echo "<p>Anda memilih metode pembayaran <strong>COD (Bayar di Tempat)</strong>.</p>";
                echo "<p>Mohon siapkan uang tunai sesuai total pesanan saat pesanan sudah selesai.</p>";
            } else {
                echo "<p>Metode pembayaran tidak dikenali.</p>";
            }
        } else {
            echo "<p>Data pembayaran tidak ditemukan.</p>";
        }
        ?>
        <hr>
        <?php if ($metode_pembayaran != "COD"): ?>
        <h5>Upload Bukti Transfer</h5>
        <form action="" method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="bukti" class="form-label">Upload Bukti Transfer</label>
                <input type="file" name="bukti" accept="image/*" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="payment_type" class="form-label">Jenis Pembayaran</label>
                <select name="payment_type" class="form-select" required>
                    <option value="">-- Pilih Jenis Pembayaran --</option>
                    <option value="dp">DP (Uang Muka)</option>
                    <option value="full">Pelunasan</option>
                </select>
                <div id="dp-input" class="mt-3" style="display: none;">
    <label for="nominal_dp" class="form-label">Masukkan Nominal DP (Rp)</label>
    <input type="number" class="form-control" name="nominal_dp" id="nominal_dp" placeholder="" min="1">
    <div class="form-text text-danger" id="sisa_pembayaran"></div>
</div>
            </div>
            <button type="submit" name="upload" class="btn btn-primary w-100">Upload Bukti ke Sistem</button>
        </form>
        <?php
        if (isset($_POST['upload'])) {
            $nama_file = $_FILES['bukti']['name'];
            $tmp = $_FILES['bukti']['tmp_name'];
            $payment_type = $_POST['payment_type'];
            $ext = pathinfo($nama_file, PATHINFO_EXTENSION);
            $ext_boleh = ['jpg', 'jpeg', 'png'];
            if (!in_array(strtolower($ext), $ext_boleh)) {
                echo "<div class='alert alert-danger mt-3'>Format file harus JPG/PNG.</div>";
            } elseif (empty($payment_type)) {
                echo "<div class='alert alert-danger mt-3'>Silakan pilih jenis pembayaran.</div>";
            } else {
                $nama_baru = "bukti_" . time() . "_" . rand(100,999) . "." . $ext;
                $path_simpan = "../bukti/" . $nama_baru;
                if (move_uploaded_file($tmp, $path_simpan)) {
    $koneksi->query("UPDATE orders SET bukti_transfer='$nama_baru' WHERE order_id=$order_id");
    $payment_date = date("Y-m-d H:i:s");
    $amount = ($payment_type == 'dp') ? ($order['total_price'] * 0.5) : $order['total_price'];
    // Cek metode pembayaran
if (isset($payment['method'])) {
    $method = $payment['method'];
} else {
    // Ambil metode pembayaran dari database kalau belum diambil
    $metode_query = $koneksi->query("SELECT method FROM payments WHERE order_id = $order_id");
    if ($metode_query->num_rows > 0) {
        $metode_row = $metode_query->fetch_assoc();
        $method = $metode_row['method'];
    } else {
        // Jika belum ada, ambil dari pilihan terakhir user atau set default
        $method = "Transfer Bank"; // default fallback jika diperlukan
    }
}

    // ✅ Cek apakah sudah ada data payment untuk order ini
    $cek_payment = $koneksi->query("SELECT * FROM payments WHERE order_id = $order_id");
    $metode_result = $koneksi->query("SELECT method FROM payments WHERE order_id = $order_id");
if ($metode_result->num_rows > 0) {
    $row_method = $metode_result->fetch_assoc();
    $method = $row_method['method'];
} else {
    $method = "Transfer Bank"; // atau bisa ambil dari session/user input jika ingin dinamis
}   

    if ($cek_payment->num_rows > 0) {
    $row = $cek_payment->fetch_assoc();
    if ($row['status'] != 'paid') {
        $koneksi->query("UPDATE payments SET payment_date='$payment_date', amount=$amount, method='$method', payment_type='$payment_type', status='unpaid' WHERE order_id=$order_id");
    } else {
        // Kalau sudah paid, jangan ubah status
        $koneksi->query("UPDATE payments SET payment_date='$payment_date', amount=$amount, method='$method', payment_type='$payment_type' WHERE order_id=$order_id");
    }
}
    echo "<div class='alert alert-success mt-3'>Bukti transfer berhasil diupload sebagai <strong>$payment_type</strong>!</div>";
    $payment_type_display = ($payment_type == 'dp') ? "Pembayaran DP (Uang Muka)" : "Pembayaran Pelunasan";
}
else {
                    echo "<div class='alert alert-danger mt-3'>Gagal upload file. Coba lagi.</div>";
                }
            }
        }
        ?>
        <?php endif; ?>
        <h5>Konfirmasi Pembayaran</h5>
        <p>Setelah transfer, silakan kirim bukti pembayaran ke WhatsApp admin:</p>
        <?php
        $sisa_pembayaran_text = "";
if (isset($_POST['payment_type']) && $_POST['payment_type'] === 'dp' && isset($_POST['nominal_dp'])) {
    $dp = intval($_POST['nominal_dp']);
    $sisa = $order['total_price'] - $dp;
    if ($sisa > 0) {
        $sisa_pembayaran_text = "DP yang dibayarkan: Rp " . number_format($dp, 0, ',', '.') . "%0A"
            . "Sisa pembayaran: Rp " . number_format($sisa, 0, ',', '.') . "%0A";
    }
}
        $pesan_wa = "Halo Admin,%0A"
        . "Saya telah melakukan pembayaran.%0A"
        . "Detail pesanan saya:%0A"
        . "--------------------%0A"
        . "Nama: " . urlencode($order['nama']) . "%0A"
        . "Metode Pembayaran: " . urlencode($detail_metode) . "%0A"
        . "Jenis Pembayaran: " . urlencode($payment_type_display) . "%0A"
    . $sisa_pembayaran_text
        . "Produk:%0A" . $detail_produk
        . "Alamat: " . urlencode($order['alamat']) . "%0A"
        . "Total: Rp " . number_format($order['total_price'], 0, ',', '.') . "%0A"
        . "--------------------%0A"
        . "Saya juga lampirkan bukti transfer di chat ini ya Admin.";
        $nomor_wa_admin = "6288210094521";
        ?>
        <a href="https://wa.me/<?= $nomor_wa_admin; ?>?text=<?= $pesan_wa; ?>" target="_blank" class="btn btn-wa w-100">
            Kirim Bukti Pembayaran via WhatsApp
        </a>
    </div>
    <script>
document.addEventListener('DOMContentLoaded', function () {
    const selectPayment = document.querySelector('select[name="payment_type"]');
    const dpInputDiv = document.getElementById('dp-input');
    const nominalInput = document.getElementById('nominal_dp');
    const sisaDiv = document.getElementById('sisa_pembayaran');
    const totalBayar = <?= $order['total_price']; ?>;

    selectPayment.addEventListener('change', function () {
        if (this.value === 'dp') {
            dpInputDiv.style.display = 'block';
        } else {
            dpInputDiv.style.display = 'none';
            nominalInput.value = '';
            sisaDiv.textContent = '';
        }
    });

    nominalInput.addEventListener('input', function () {
        const dpValue = parseInt(this.value) || 0;
        const sisa = totalBayar - dpValue;
        if (sisa >= 0) {
            sisaDiv.textContent = 'Sisa pembayaran: Rp ' + sisa.toLocaleString('id-ID');
        } else {
            sisaDiv.textContent = 'Nominal DP melebihi total pembayaran!';
        }
    });

    // Cek apakah ada alert sukses dan jenis pembayaran adalah DP
const alertSuccess = document.querySelector('.alert-success');
if (alertSuccess && selectPayment.value === 'dp') {
    const dpValue = parseInt(nominalInput.value) || 0;
    const sisa = totalBayar - dpValue;
    if (sisa > 0) {
        sisaDiv.textContent = 'Sisa pembayaran: Rp ' + sisa.toLocaleString('id-ID');
    } else {
        sisaDiv.textContent = '';
    }
}
});
</script>
</body>
</html>
