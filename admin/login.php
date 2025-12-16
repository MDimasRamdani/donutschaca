<?php
session_start();
include "../koneksi/koneksi.php"; // file koneksi ke database

// Cek jika form dikirim
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Cek di database
    $stmt = $koneksi->prepare("SELECT * FROM users WHERE username = ? AND role = 'admin'");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    // Jika user ditemukan
    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        
        if ($password == $row['password']) { // kalau belum di-hash
            $_SESSION['admin'] = $row['username'];
            $_SESSION['role'] = 'admin'; 
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Password salah!";
        }
    } else {
        $error = "Username tidak ditemukan!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Login Admin</title>

  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- FontAwesome untuk icon mata -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-500 via-purple-500 to-pink-500">

  <div class="w-full max-w-md bg-white/90 backdrop-blur-lg p-8 rounded-3xl shadow-2xl animate-[fadeIn_0.6s_ease]">

    <h2 class="text-3xl font-bold text-center text-purple-700 mb-6">Login Admin</h2>

    <!-- Tampilkan error jika ada -->
    <?php if (isset($error)): ?>
      <div class="mb-4 p-3 text-red-700 bg-red-100 border border-red-300 rounded-lg">
        <?= $error ?>
      </div>
    <?php endif; ?>

    <form method="post" class="space-y-4">

      <!-- Username -->
      <div>
        <label class="block mb-1 font-semibold text-gray-700">Username</label>
        <input type="text" name="username" id="username"
               class="w-full p-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-purple-500 outline-none" required>
      </div>

      <!-- Password + Icon Mata -->
      <div class="relative">
        <label class="block mb-1 font-semibold text-gray-700">Password</label>
        <input type="password" name="password" id="password"
               class="w-full p-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-purple-500 outline-none" required>

        <i class="fa-solid fa-eye absolute right-4 top-11 text-gray-600 cursor-pointer" id="togglePassword"></i>
      </div>

      <!-- Tombol Login -->
      <button type="submit"
              class="w-full p-3 bg-purple-600 text-white font-semibold rounded-xl hover:bg-purple-700 transition-all duration-200">
        Login
      </button>

    </form>
  </div>

  <script>
    // Bersihkan input saat halaman dibuka
    window.addEventListener('DOMContentLoaded', () => {
      document.getElementById('username').value = '';
      document.getElementById('password').value = '';
    });

    // Show/hide password
    const passwordField = document.getElementById("password");
    const toggleEye = document.getElementById("togglePassword");

    toggleEye.addEventListener("click", function () {
      const type = passwordField.type === "password" ? "text" : "password";
      passwordField.type = type;

      this.classList.toggle("fa-eye");
      this.classList.toggle("fa-eye-slash");
    });
  </script>

</body>
</html>
