<?php
session_start();
$error = "";
if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']); // agar hilang setelah ditampilkan sekali
}
include "../koneksi/koneksi.php";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["login_user"];
    $password = $_POST["login_pass"];

    $stmt = $koneksi->prepare("SELECT * FROM users WHERE username = ? AND role = 'owner'");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();

        if ($password == $row['password']) {
            $_SESSION['owner'] = $row['username'];
            $_SESSION['role'] = 'owner';
            header("Location: owner_dashboard.php");
            exit;
        } else {
            $_SESSION['error'] = "Password salah!";
header("Location: owner_login.php");
exit;
        }
    } else {
        $_SESSION['error'] = "Username tidak ditemukan!";
header("Location: owner_login.php");
exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Login Owner</title>

  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- FontAwesome for eye icon -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-500 via-purple-600 to-pink-500">

  <div class="w-full max-w-md bg-white/90 backdrop-blur-md p-8 rounded-3xl shadow-2xl animate-[fadeIn_0.6s_ease]">

    <h2 class="text-3xl font-bold text-center text-purple-700 mb-6">Login Owner</h2>

    <!-- Tampilkan Error Jika Ada -->
    <?php if (!empty($error)): ?>
      <div class="mb-4 p-3 bg-red-100 text-red-700 border border-red-300 rounded-lg">
        <?= $error ?>
      </div>
    <?php endif; ?>

    <form method="post" autocomplete="off" class="space-y-5">

      <!-- Username -->
      <div>
        <label class="block mb-1 font-semibold text-gray-700">Username</label>
        <input type="text" name="login_user" id="username"
               class="w-full p-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-purple-500 outline-none"
               required autocomplete="off">
      </div>

      <!-- Password -->
      <div class="relative">
        <label class="block mb-1 font-semibold text-gray-700">Password</label>
        <input type="password" name="login_pass" id="password"
               class="w-full p-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-purple-500 outline-none"
               required autocomplete="new-password">

        <!-- Icon Mata -->
        <i class="fa-solid fa-eye absolute right-4 top-11 text-gray-600 cursor-pointer" id="togglePassword"></i>
      </div>

      <!-- Tombol Login -->
      <button type="submit"
              class="w-full p-3 bg-purple-600 text-white font-semibold rounded-xl hover:bg-purple-700 transition duration-200">
        Login
      </button>

    </form>
  </div>

  <script>
    // Bersihkan input saat halaman dibuka
    document.addEventListener("DOMContentLoaded", () => {
      document.getElementById("username").value = "";
      document.getElementById("password").value = "";
    });

    // Show / Hide Password
    const passwordField = document.getElementById("password");
    const toggleEye = document.getElementById("togglePassword");

    toggleEye.addEventListener("click", () => {
      const type = passwordField.type === "password" ? "text" : "password";
      passwordField.type = type;
      toggleEye.classList.toggle("fa-eye");
      toggleEye.classList.toggle("fa-eye-slash");
    });
  </script>

</body>
</html>