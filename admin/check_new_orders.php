<?php
// check_new_orders.php
header('Content-Type: application/json');
include '../koneksi/koneksi.php';

// Hitung pesanan yang belum dibaca admin
$query = "SELECT COUNT(*) AS jumlah FROM orders WHERE is_read = 0 AND is_deleted = 0";
$result = mysqli_query($koneksi, $query);

$data = mysqli_fetch_assoc($result);

// Return ke AJAX
echo json_encode([
    "new_orders" => intval($data['jumlah'])
]);
?>
