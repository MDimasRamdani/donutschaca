<?php
session_start();
$koneksi = new mysqli("localhost", "root", "", "donutschaca");

// Cek login owner
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'owner') {
    header("Location: owner_login.php");
    exit;
}

// Total penjualan dari transaksi berstatus 'completed'
$q_total = $koneksi->query("SELECT SUM(total_price) as total FROM orders WHERE status = 'completed'");
$total_pendapatan = $q_total->fetch_assoc()['total'] ?? 0;

// Jumlah transaksi sukses
$q_jumlah = $koneksi->query("SELECT COUNT(*) as jumlah FROM orders WHERE status = 'completed'");
$jumlah_transaksi = $q_jumlah->fetch_assoc()['jumlah'] ?? 0;

// Ambil data total penjualan per bulan
$q_chart = $koneksi->query("
    SELECT DATE_FORMAT(order_date, '%Y-%m') AS bulan, SUM(total_price) AS total
    FROM orders
    WHERE status = 'completed'
    GROUP BY bulan
    ORDER BY bulan
");

$labels = [];
$data = [];

while ($row = $q_chart->fetch_assoc()) {
    $labels[] = $row['bulan'];
    $data[] = $row['total'];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Statistik Penjualan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap & Font Awesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        body {
  background: linear-gradient(to right, #e0f7fa, #fffde7); /* biru muda ke krem muda */
}
        .card-box {
            background: #ff914d;
            color: white;
            border-radius: 12px;
            padding: 10px;
            text-align: center;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }
        .card-box.bg-success {
            background-color: #28a745 !important;
        }
        canvas {
            background: #ffffff;
            border-radius: 12px;
            padding: 15px;
        }
    </style>
</head>
<body>

<?php include "../koneksi/navbar_owner.php"; ?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3><i class="fas fa-chart-line me-2"></i>Statistik Penjualan</h3>
        <a href="owner_dashboard.php" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="mb-5">
        <h5 class="mb-3">Grafik Penjualan per Pesanan</h5>
        <canvas id="salesChart" height="100"></canvas>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card-box">
                <h3>Rp <?= number_format($total_pendapatan, 0, ',', '.'); ?></h3>
                <p>Total Pendapatan</p>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card-box bg-success">
                <h3><?= $jumlah_transaksi; ?> Transaksi</h3>
                <p>Transaksi Sukses</p>
            </div>
        </div>
    </div>

    <div class="text-muted mb-3"><strong>Statistik hanya mencakup pesanan dengan status completed</strong>.</div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('salesChart').getContext('2d');
const salesChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: <?= json_encode($labels); ?>,
        datasets: [{
            label: 'Pendapatan Bulanan',
            data: <?= json_encode($data); ?>,
            fill: false,
            borderColor: '#007bff',
            backgroundColor: '#007bff',
            tension: 0.4,
            pointBorderColor: '#007bff',
            pointBackgroundColor: '#fff',
            pointRadius: 5,
            pointHoverRadius: 7
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'top'
            },
            tooltip: {
                callbacks: {
                    label: context => 'Rp ' + context.parsed.y.toLocaleString('id-ID')
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: value => 'Rp ' + value.toLocaleString('id-ID')
                }
            }
        }
    }
});

</script>

</body>
</html>
