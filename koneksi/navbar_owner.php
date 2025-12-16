<!-- navbar_owner.php -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<style>
  .navbar-gradient {
    background-image: url('../img/hp.jpg'); /* ganti dengan nama file gambar kamu */
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
  color: white;
  }

  .offcanvas-gradient {
  background-image: url('../img/hp.jpg'); /* ganti dengan nama file gambar kamu */
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
  color: white;
}

.offcanvas-gradient::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.4); /* Lapisan gelap transparan */
  z-index: 0;
}

.offcanvas-body,
.offcanvas-header,
.navbar-nav .nav-link {
  position: relative;
  z-index: 1;
  color: white !important; /* Paksa putih agar kontras */
  text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.8); /* Efek bayangan */
}

.navbar-nav .nav-link:hover {
  background-color: rgba(255, 255, 255, 0.2);
  color: #fff !important;
}


  @keyframes gradientMove {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
  }

  @keyframes gradientShift {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
  }

  .navbar-brand::after {
    content: " ✨🌈🍩";
    font-size: 18px;
  }

  .nav-link {
    font-size: 16px;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .nav-link:hover {
    background-color: rgba(255, 255, 255, 0.2);
    border-radius: 8px;
    padding-left: 10px;
    color: #fff !important;
  }

  .nav-link i {
    min-width: 20px;
  }
</style>

<nav class="navbar navbar-dark navbar-gradient fixed-top">
  <div class="container-fluid">
    <button class="navbar-toggler me-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar">
      <span class="navbar-toggler-icon"></span>
    </button>
    <a class="navbar-brand text-white fw-bold" href="#">👑 Owner</a>

    <div class="offcanvas offcanvas-start offcanvas-gradient" tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel">
      <div class="offcanvas-header">
        <h5 class="offcanvas-title text-white" id="offcanvasNavbarLabel">Menu Navigasi</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
      </div>
      <div class="offcanvas-body">
        <ul class="navbar-nav flex-column">
          <li class="nav-item"><a class="nav-link text-dark" href="owner_dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
          <li class="nav-item"><a class="nav-link text-dark" href="laporan_produk.php"><i class="fas fa-box"></i> Laporan Produk</a></li>
          <li class="nav-item"><a class="nav-link text-dark" href="laporan_pesanan.php"><i class="fas fa-file-alt"></i> Laporan Pesanan</a></li>
          <li class="nav-item"><a class="nav-link text-dark" href="laporan_pembayaran.php"><i class="fas fa-credit-card"></i> Laporan Pembayaran</a></li>
          <li class="nav-item"><a class="nav-link text-dark" href="statistik_penjualan.php"><i class="fas fa-chart-line"></i> Statistik Penjualan</a></li>
          <li class="nav-item"><a class="nav-link text-dark" href="manajemen_admin.php"><i class="fas fa-user-cog"></i> Manajemen Admin</a></li>
          <li class="nav-item"><a class="nav-link text-danger" href="owner_logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
      </div>
    </div>
  </div>
</nav>
<div style="padding-top: 70px;"></div>
