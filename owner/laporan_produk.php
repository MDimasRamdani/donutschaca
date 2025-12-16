<?php
session_start();

// Cek login owner
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'owner') {
    header("Location: owner_login.php");
    exit;
}
require_once '../koneksi/koneksi.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Produk</title>
    <!-- Bootstrap & Font Awesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
<?php include "../koneksi/navbar_owner.php"; ?>
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3><i class="fas fa-box-open me-2"></i>Laporan Produk</h3>
        <a href="owner_dashboard.php" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <style>
         body {
        background: url('../img/donat1.png') no-repeat center center fixed;
        background-size: cover;
    }
    </style>

    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-dark text-center">
                <tr>
                    <th>No</th>
                    <th>Nama Produk</th>
                    <th>Harga</th>
                    <th>Stok</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT * FROM product ORDER BY nama ASC";
                $result = $koneksi->query($sql);
                $no = 1;

                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td class='text-center'>{$no}</td>";
                        echo "<td>" . htmlspecialchars($row['nama']) . "</td>";
                        echo "<td>";
if ($row['promo'] == 1) {
    // Harga setelah diskon 10%
    $hargaPromo = $row['price'] * 0.9;
    echo "
        <span class='badge bg-danger mb-1'>Promo 10%</span><br>
        <span class='text-success fw-bold'>Rp " . number_format($hargaPromo, 0, ',', '.') . "</span><br>
        <small class='text-muted text-decoration-line-through'>
            Rp " . number_format($row['price'], 0, ',', '.') . "
        </small>
    ";
} else {
    // Harga normal
    echo "Rp " . number_format($row['price'], 0, ',', '.');
}
echo "</td>";
                        echo "<td class='text-center'>" . $row['stok'] . "</td>";
                        echo "</tr>";
                        $no++;
                    }
                } else {
                    echo "<tr><td colspan='4' class='text-center text-muted'>Tidak ada data produk.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
