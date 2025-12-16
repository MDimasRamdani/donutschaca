<?php
session_start();
require_once "../koneksi/koneksi.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Hapus permanen semua yang is_deleted = 1
$query = "DELETE FROM orders WHERE is_deleted = 1";
if (mysqli_query($koneksi, $query)) {
    header("Location: story.php");
    exit;
} else {
    echo "Gagal menghapus data: " . mysqli_error($koneksi);
}
?>
