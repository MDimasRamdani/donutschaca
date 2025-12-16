<?php
$koneksi = new mysqli("localhost", "root", "", "donutschaca");

// Ambil order_id dari URL
$order_id = $_GET['id'];

// Ambil data order
$stmt = $koneksi->prepare("SELECT status FROM orders WHERE order_id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();

if (!$order) {
    echo "Pesanan tidak ditemukan.";
    exit;
}

// Cek apakah sudah dibatalkan
if ($order['status'] === 'dibatalkan') {
    echo "Pesanan ini sudah dibatalkan.";
    exit;
}

// Jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $alasan = $_POST['alasan'];

    if (empty($alasan)) {
        echo "Alasan pembatalan harus diisi.";
        exit;
    }

    // Update status menjadi dibatalkan
    $stmt2 = $koneksi->prepare("
        UPDATE orders 
        SET status = 'dibatalkan', alasan_batal = ?
        WHERE order_id = ?
    ");
    $stmt2->bind_param("si", $alasan, $order_id);
    $stmt2->execute();

    echo "Pesanan berhasil dibatalkan. Alasan: " . htmlspecialchars($alasan);
}

$stmt->close();
$koneksi->close();
?>
