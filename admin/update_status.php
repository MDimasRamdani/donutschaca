<?php
include "../koneksi/koneksi.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];

    $query = "UPDATE orders SET status = ? WHERE order_id = ?";
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, "si", $status, $order_id);
    mysqli_stmt_execute($stmt);

    header("Location: pemesanan_admin.php");
    exit;
}
?>
