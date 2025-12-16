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

// Jika form disubmit
if (isset($_POST['register'])) {
    $nama     = $_POST['nama'];
    $telepon  = $_POST['telepon'];
    $alamat   = $_POST['alamat'];
    $username = $_POST['username'];
    $password = $_POST['password']; // password tanpa hash

    // Cek apakah username sudah ada
    $cek = $koneksi->prepare("SELECT * FROM customers WHERE username = ?");
    $cek->bind_param("s", $username);
    $cek->execute();
    $cekResult = $cek->get_result();

    if ($cekResult->num_rows > 0) {
        echo "<script>alert('Username sudah digunakan!'); window.location='register_pelanggan.php';</script>";
    } else {
        // Insert data pelanggan baru
        $stmt = $koneksi->prepare("INSERT INTO customers (nama, telepon, alamat, username, password) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $nama, $telepon, $alamat, $username, $password);
        
        if ($stmt->execute()) {
            echo "<script>alert('Registrasi berhasil! Silakan login.'); window.location='login_pelanggan.php';</script>";
        } else {
            echo "Error saat registrasi: " . $stmt->error;
        }
        $stmt->close();
    }

    $cek->close();
}

$koneksi->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Register Pelanggan</title>

    <!-- TAILWIND CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-500 via-purple-500 to-pink-500">

<div class="w-full max-w-lg bg-white/90 backdrop-blur-md p-8 rounded-3xl shadow-2xl animate-fadeIn">
    <h2 class="text-3xl font-bold text-center text-purple-700 mb-6">Register Pelanggan</h2>

    <form method="POST" action="" autocomplete="off" class="space-y-4">

        <!-- Nama -->
        <div>
            <label class="block mb-1 font-semibold text-gray-700">Nama</label>
            <input type="text" name="nama"
                   class="w-full p-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-purple-500 outline-none"
                   required>
        </div>

        <!-- Telepon -->
        <div>
            <label class="block mb-1 font-semibold text-gray-700">No HP</label>
            <input type="text" name="telepon"
                   class="w-full p-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-purple-500 outline-none"
                   required>
        </div>

        <!-- Alamat -->
        <div>
            <label class="block mb-1 font-semibold text-gray-700">Alamat</label>
            <textarea name="alamat" rows="3"
                      class="w-full p-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-purple-500 outline-none"
                      required></textarea>
        </div>

        <!-- Username -->
        <div>
            <label class="block mb-1 font-semibold text-gray-700">Username</label>
            <input type="text" name="username"
                   class="w-full p-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-purple-500 outline-none"
                   autocomplete="off" required>
        </div>

        <!-- Password -->
        <div class="">
            <label class="block mb-1 font-semibold text-gray-700">Password</label>
            <input type="password" name="password"
                   class="w-full p-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-purple-500 outline-none"
                   autocomplete="new-password" required>
        </div>

        <!-- Button -->
        <button type="submit" name="register"
                class="w-full p-3 bg-purple-600 text-white font-semibold rounded-xl hover:bg-purple-700 transition">
            Register
        </button>
    </form>

    <p class="text-center mt-4 text-gray-700">
        Sudah punya akun?
        <a href="login_pelanggan.php" class="font-bold text-purple-700 hover:underline">
            Login di sini
        </a>
    </p>
</div>

</body>
</html>
