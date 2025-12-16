<?php
session_start();
if (isset($_GET['confirm']) && $_GET['confirm'] === 'yes') {
    session_unset();
    session_destroy();
    header("Refresh: 2; url=owner_login.php");
    $logged_out = true;
} else {
    $logged_out = false;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Logout Owner</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            display: flex;
            height: 100vh;
            justify-content: center;
            align-items: center;
        }
        .logout-box {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        .spinner-border {
            width: 3rem;
            height: 3rem;
        }
    </style>
</head>
<body>

<div class="logout-box">
    <?php if ($logged_out): ?>
        <h4>Sedang logout...</h4>
        <div class="spinner-border text-danger mt-3" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <p class="mt-3">Anda akan diarahkan ke halaman login.</p>
    <?php else: ?>
        <h4>Apakah Anda yakin ingin logout?</h4>
        <a href="?confirm=yes" class="btn btn-danger mt-3">Ya, Logout</a>
        <a href="owner_dashboard.php" class="btn btn-secondary mt-3">Batal</a>
    <?php endif; ?>
</div>

</body>
</html>
