<?php
session_start();
session_unset();
session_destroy();
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="refresh" content="3;url=index.php">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Logout...</title>
  <style>
    body {
      margin: 0;
      height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      background: #fef6f9;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    .logout-box {
      text-align: center;
    }
    .loader {
      border: 6px solid #f3f3f3;
      border-top: 6px solid #ff69b4;
      border-radius: 50%;
      width: 50px;
      height: 50px;
      animation: spin 1s linear infinite;
      margin: 0 auto 20px;
    }
    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }
    h2 {
      color: #d63384;
    }
    p {
      color: #555;
    }
  </style>
</head>
<body>
  <div class="logout-box">
    <div class="loader"></div>
    <h2>Anda telah logout.</h2>
    <p>Mengarahkan kembali ke halaman utama...</p>
  </div>
</body>
</html>
