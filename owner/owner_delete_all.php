<?php
session_start();
$koneksi = new mysqli("localhost", "root", "", "donutschaca");

// Cek login owner
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'owner') {
    header("Location: owner_login.php");
    exit;
}

// Hapus tabel pesanan
$koneksi->query("DELETE FROM orders");

// Hapus juga payments kalau ingin bersih total
$koneksi->query("DELETE FROM payments");

header("Location: laporan_pesanan.php?deleted=1");
exit;
?>
