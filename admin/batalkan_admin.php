<?php
session_start();
require_once "../koneksi/koneksi.php"; // Koneksi database

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $order_id = $_POST['order_id'];
        $alasan_batal = "ADMIN: " . trim($_POST['alasan_batal']);

        if (empty($order_id) || empty(trim($_POST['alasan_batal']))) {
            throw new Exception("Semua field harus diisi!");
        }

        $koneksi->begin_transaction();

        // Update status orders jadi dibatalkan + simpan alasan
        $stmt_update_order = $koneksi->prepare("
            UPDATE orders 
            SET status = 'dibatalkan', alasan_batal = ?
            WHERE order_id = ?
        ");
        $stmt_update_order->bind_param("si", $alasan_batal, $order_id);
        $stmt_update_order->execute();

        $koneksi->commit();

        $_SESSION['success'] = "Pesanan berhasil dibatalkan oleh Admin!";
    } catch (Exception $e) {
        $koneksi->rollback();
        $_SESSION['error'] = "Gagal membatalkan pesanan: " . $e->getMessage();
    } finally {
        $koneksi->close();
        header("Location: pemesanan_admin.php");
        exit();
    }
}
?>
