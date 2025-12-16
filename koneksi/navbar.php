<!-- navbar.php -->
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm sticky-top">
  <div class="container">
    
    <!-- LOGO: tetap di kiri -->
    <a class="navbar-brand fw-bold fs-4" href="index.php">
      DonutsChaca <span class="text-warning">🍩</span>
    </a>

    <!-- TOGGLER -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- MENU NAVIGASI -->
    <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <a class="nav-link px-3 active" href="index.php">Beranda</a>
        </li>
        <li class="nav-item">
          <a class="nav-link px-3 active" href="produk.php">Produk</a>
        </li>
        <li class="nav-item">
          <a class="nav-link px-3 active" href="tentang_kami.php">Tentang Kami</a>
        </li>
        <li class="nav-item">
          <a class="nav-link px-3 active" href="pemesanan.php">Pemesanan</a>
        </li>

        <?php if (isset($_SESSION['pelanggan'])): ?>
  <!-- Tampilkan nama pelanggan -->
  <li class="nav-item d-flex align-items-center">
    <a href="profil.php" class="nav-link text-white">
  <i class="bi bi-person-circle"></i>
  <?= htmlspecialchars($_SESSION['pelanggan']['nama']) ?>
</a>
  </li>
  <!-- Tombol Logout -->
  <li class="nav-item">
    <a class="nav-link px-3 text-warning fw-semibold" href="logout_pelanggan.php">
      <i class="bi bi-box-arrow-right"></i> Logout
    </a>
  </li>
<?php endif; ?>

      </ul>
    </div>

  </div>
</nav>

<!-- Styling -->
<style>
  .nav-link {
    transition: all 0.3s ease-in-out;
  }

  .nav-link:hover {
    color: #ffc107 !important;
    transform: scale(1.05);
  }

  .navbar-brand span {
    animation: float 1.5s ease-in-out infinite;
  }

  @keyframes float {
    0%, 100% {
      transform: translateY(0);
    }
    50% {
      transform: translateY(-3px);
    }
  }
</style>

<!-- Tambahkan ini di <head> kalau belum ada Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
