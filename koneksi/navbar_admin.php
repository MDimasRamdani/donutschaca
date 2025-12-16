<!-- koneksi/navbar_admin.php -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<style>
  #notif-count {
      padding: 3px 3px;
      font-weight: bold;
  }
</style>
<nav class="navbar navbar-expand-lg navbar-dark fixed-top" style="background: linear-gradient(90deg, #0d47a1, #1976d2);">
  <div class="container-fluid">
  <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="dashboard.php">
  <i class="bi bi-speedometer2 text-white"></i>
  <span style="color: white;">Admin Panel</span>
</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAdmin"
      aria-controls="navbarNavAdmin" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNavAdmin">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <a class="nav-link text-white" href="dashboard.php"><i class="bi bi-house-door"></i> Dashboard</a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-white" href="produk_admin.php"><i class="bi bi-box-seam"></i> Produk</a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-white" href="pemesanan_admin.php"><i class="bi bi-bag-check"></i> Pemesanan</a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-white" href="payment_admin.php"><i class="bi bi-credit-card"></i> Payment</a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-white" href="story.php"><i class="bi bi-clock-history"></i> History</a>
        </li>
        <li class="nav-item position-relative">
  <a class="nav-link text-white" href="pemesanan_admin.php">
    <i class="bi bi-bell" style="font-size: 20px;"></i>
    <span id="notif-count" 
          class="badge bg-danger rounded-pill position-absolute top-0 start-100 translate-middle" 
          style="display:none; font-size: 12px;">
    </span>
  </a>
</li>
        <li class="nav-item">
          <a class="nav-link text-warning" href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function checkNewOrders() {
    fetch("../admin/check_new_orders.php")
        .then(response => response.json())
        .then(data => {
            const badge = document.getElementById("notif-count");

            if (data.new_orders > 0) {
                badge.style.display = "inline-block";
                badge.textContent = data.new_orders;

                // Bunyi notifikasi
                if (!window.notifPlayed) {
                    const audio = new Audio("/Donutschaca/koneksi/notif.mp3");
                    audio.play();
                    window.notifPlayed = true;
                }
            } else {
                badge.style.display = "none";
                window.notifPlayed = false;
            }
        })
        .catch(error => console.log("Error:", error));
}

// Cek setiap 3 detik
setInterval(checkNewOrders, 3000);
</script>
