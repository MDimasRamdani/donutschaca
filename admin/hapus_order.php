<?php
include "../koneksi/koneksi.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $order_id = $_POST['order_id'];

    // Soft delete, hanya tandai sebagai dihapus
    $query = "UPDATE orders SET is_deleted = 1 WHERE order_id = ?";
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, "i", $order_id);
    mysqli_stmt_execute($stmt);

    header("Location: pemesanan_admin.php");
    exit;
}
?>
