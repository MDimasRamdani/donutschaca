<?php
// Nyalakan error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Mulai session
session_start();

// Koneksi ke database
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'donutschaca';

$koneksi = new mysqli($host, $user, $pass, $db);

// Cek koneksi
if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}

// Jika form login disubmit
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Cek username dan password langsung
    $stmt = $koneksi->prepare("SELECT * FROM customers WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    // Kalau username & password cocok
    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();

        // Simpan data pelanggan ke session
        $_SESSION['pelanggan'] = [
            'user_id' => $row['user_id'],
            'nama'    => $row['nama'],
            'username'=> $row['username']
        ];
        
        echo "<script>alert('Login Berhasil!'); window.location='pemesanan.php';</script>";
    } else {
        echo "<script>alert('Username atau Password salah!'); window.location='login_pelanggan.php';</script>";
    }

    $stmt->close();
}

$koneksi->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login Pelanggan</title>

    <!-- TAILWIND CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="min-h-screen flex items-center justify-center bg-gradient-to-br from-purple-500 via-pink-500 to-yellow-400">

<div class="w-full max-w-md bg-white/90 backdrop-blur-sm p-8 rounded-3xl shadow-xl animate-fadeIn">
    <h2 class="text-3xl font-bold text-center text-purple-700 mb-6">Login Pelanggan</h2>

    <form method="POST" action="" autocomplete="new-password" class="space-y-4">

    <!-- Anti autofill -->
    <input type="text" name="fakeuser" autocomplete="off" style="display:none;">
    <input type="password" name="fakepass" autocomplete="off" style="display:none;">

    <!-- Username -->
    <div>
        <label class="block mb-1 font-semibold text-gray-700">Username</label>
        <input type="text" name="username" id="username" autocomplete="new-password"
               class="w-full p-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-purple-500 outline-none">
    </div>

    <!-- Password -->
    <div class="relative">
        <label class="block mb-1 font-semibold text-gray-700">Password</label>
        <input type="password" name="password" id="password" autocomplete="new-password"
               class="w-full p-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-purple-500 outline-none">

        <i class="fa-solid fa-eye absolute right-4 top-10 text-gray-600 cursor-pointer" id="togglePassword"></i>
    </div>

    <!-- Button -->
    <button type="submit" name="login"
            class="w-full p-3 bg-purple-600 text-white font-semibold rounded-xl hover:bg-purple-700 transition">
        Login
    </button>
</form>

    <p class="text-center mt-4 text-gray-700">
        Belum punya akun?
        <a href="register_pelanggan.php" class="font-bold text-purple-700 hover:underline">
            Daftar sekarang
        </a>
    </p>
</div>

<script>
    // Bersihkan input
    document.getElementById("username").value = "";
    document.getElementById("password").value = "";

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
