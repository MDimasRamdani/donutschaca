<?php
session_start();
session_unset();
session_destroy();

// Simpan pesan logout ke session sementara
session_start(); // Mulai ulang session agar bisa pakai session lagi
$_SESSION['logout_message'] = "Anda telah berhasil logout.";

header("Location: dashboard.php");
exit;
