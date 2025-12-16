<?php
include "../koneksi/koneksi.php";
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}
// Hapus satu data
if (isset($_POST['hapus_satu']) && isset($_POST['order_id'])) {
    $order_id = intval($_POST['order_id']);
    // Hapus di tabel anak dulu
    mysqli_query($koneksi, "DELETE FROM payments WHERE order_id = $order_id");
    mysqli_query($koneksi, "DELETE FROM order_items WHERE order_id = $order_id");
    mysqli_query($koneksi, "DELETE FROM orders WHERE order_id = $order_id");
    header("Location: story.php");
    exit;
}
// Hapus semua
if (isset($_POST['hapus_semua'])) {
    // Ambil semua order_id yang status-nya sudah selesai atau dibatalkan
    $query = mysqli_query($koneksi, "SELECT order_id FROM orders WHERE status IN ('completed', 'dibatalkan')");
    while ($row = mysqli_fetch_assoc($query)) {
        $id = $row['order_id'];
        mysqli_query($koneksi, "DELETE FROM payments WHERE order_id = $id");
        mysqli_query($koneksi, "DELETE FROM order_items WHERE order_id = $id");
        mysqli_query($koneksi, "DELETE FROM orders WHERE order_id = $id");
    }
    header("Location: story.php");
    exit;
}
?>
