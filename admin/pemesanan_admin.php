<?php
include "../koneksi/koneksi.php";
mysqli_query($koneksi, "UPDATE orders SET is_read = 1 WHERE is_read = 0");
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}
if (isset($_POST['clear_all'])) {
    mysqli_query($koneksi, "UPDATE orders SET is_deleted = 1");
    echo "<script>alert('Semua data pemesanan berhasil dihapus!'); location.href='pemesanan_admin.php';</script>";
    exit;
}
$orders = [];
$query = "SELECT * FROM orders WHERE is_deleted = 0 ORDER BY order_date DESC";
$result = mysqli_query($koneksi, $query);
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $orders[] = $row;
    }
}
?>
<?php include '../koneksi/navbar_admin.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin - Pemesanan</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
  <style>
    body {
      background: url('../img/donat1.png') no-repeat center center fixed;
      background-size: cover;
      font-family: 'Segoe UI', sans-serif;
      overflow: scroll;
      overflow-x: hidden;
      padding-top: 50px;
    }

    .dataTables_wrapper label,
    .dataTables_info {
    font-weight: bold;
    color: #000; /* Hitam untuk kontras maksimal */
    text-shadow: 1px 1px 2px rgba(255,255,255,0.6); /* Biar tidak nyaru di background */
    }

    .badge {
      text-transform: capitalize;
    }
    .action-btns button, .action-btns form {
      display: inline-block;
    }
    .toggle-darkmode {
      position: fixed;
      top: 20px;
      right: 20px;
    }
    .uniform-button {
  width: 140px;
  height: 40px;
  font-size: 14px;
  font-weight: 600;
  display: inline-flex;
  align-items: center;
  justify-content: center;
}
  </style>
</head>
<body>
<div class="container py-4">
  <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
    <h3 class="mb-3 mb-md-0">📦 Daftar Pemesanan</h3>
    <div class="d-flex gap-2">
  <button class="btn btn-success btn-sm uniform-button" onclick="exportTableToExcel('tabel-pemesanan', 'data-pemesanan')">📥 Export Excel</button>
  <button class="btn btn-danger btn-sm uniform-button" onclick="window.print()">🖨️ Cetak PDF</button>
  <form method="POST" style="display:inline;" onsubmit="return confirm('Yakin ingin menghapus semua pesanan? Data tidak bisa dikembalikan!');">
    <button type="submit" name="clear_all" class="btn btn-dark btn-sm uniform-button">🗑️ Clear All</button>
  </form>
</div>
  </div>

  <div class="table-responsive">
    <table id="tabel-pemesanan" class="table table-bordered table-hover align-middle text-center">
      <thead class="table-dark">
        <tr>
          <th>No. Order</th>
          <th>Nama</th>
          <th>No. HP</th>
          <th>Alamat</th>
          <th>Produk</th>
          <th>Deskripsi</th>
          <th>Total</th>
          <th>Status</th>
          <th>Alasan Batal</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach ($orders as $order): ?>
        <tr>
          <td>#<?= $order['order_id'] ?></td>
          <td><?= $order['nama'] ?></td>
          <td><?= $order['telepon'] ?></td>
          <td><?= $order['alamat'] ?></td>
          <td>
    <?php
    // Ambil daftar produk lengkap untuk order ini
    $order_id = $order['order_id'];
    $q_items = $koneksi->query("
        SELECT p.nama, p.promo, oi.quantity 
        FROM order_items oi
        JOIN product p ON oi.product_id = p.product_id
        WHERE oi.order_id = $order_id
    ");
    while ($it = $q_items->fetch_assoc()):
    ?>
        <div>
            <?= htmlspecialchars($it['nama']) ?> (<?= $it['quantity'] ?>x)
            
            <?php if ($it['promo'] == 1): ?>
                <span class="badge bg-danger ms-1">PROMO 10%</span>
            <?php endif; ?>
        </div>
    <?php endwhile; ?>
</td>
          <td><?= $order['deskripsi'] ?></td>
          <td>Rp<?= number_format($order['total_price']) ?></td>
          <td>
            <?php 
              $status = $order['status'];
              $color = match($status) {
                'pending' => 'secondary',
                // 'dibayar' => 'info',
                'down payment' => 'info',
                'processing' => 'warning',
                // 'shipped' => 'primary',
                'completed' => 'success',
                'dibatalkan' => 'danger',
                default => 'light',
              };
            ?>
            <span class="badge bg-<?= $color ?>"><?= $status ?></span>
          </td>
          <td>
          <?php
              $alasan = $order['alasan_batal'];
                if (!$alasan) {
                  echo '-';
              } elseif (stripos($alasan, 'CUSTOMER:') === 0) {
                  echo '<span class="text-danger">Pelanggan:</span> ' . htmlspecialchars(substr($alasan, 9));
              } elseif (stripos($alasan, 'ADMIN:') === 0) {
                  echo '<span class="text-primary">Admin:</span> ' . htmlspecialchars(substr($alasan, 6));
              } else {
                  echo htmlspecialchars($alasan);
              }
            ?>
          </td>     
          <td class="action-btns">
  <?php if ($status !== 'completed' && $status !== 'dibatalkan'): ?>
    <button class="btn btn-outline-primary btn-sm mb-1" onclick="openUpdateModal(<?= $order['order_id'] ?>, '<?= $status ?>')">✏️ Update</button>
    <button class="btn btn-outline-danger btn-sm mb-1" onclick="openCancelModal(<?= $order['order_id'] ?>)">❌ Batal</button>
  <?php endif; ?>
  <form action="hapus_order.php" method="post" onsubmit="return confirm('Yakin ingin menghapus?')">
    <input type="hidden" name="order_id" value="<?= $order['order_id'] ?>">
    <button type="submit" class="btn btn-outline-dark btn-sm">🗑️ Hapus</button>
  </form>
</td>

        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Modal Update -->
<div class="modal fade" id="updateModal" tabindex="-1">
  <div class="modal-dialog">
    <form action="update_status.php" method="post" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Update Status</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="order_id" id="update_order_id">
        <select class="form-select" name="status" required>
          <option value="pending">Pending</option>
          <option value="down payment">Down Payment</option>
          <option value="processing">Processing</option>
          <option value="completed">Completed</option>
        </select>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Simpan</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Batal -->
<div class="modal fade" id="cancelModal" tabindex="-1">
  <div class="modal-dialog">
    <form action="batalkan_admin.php" method="post" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Alasan Pembatalan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="order_id" id="cancel_order_id">
        <textarea class="form-control" name="alasan_batal" placeholder="Masukkan alasan..." required></textarea>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-danger">Batalkan</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
      </div>
    </form>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script>
  $(document).ready(function() {
    $('#tabel-pemesanan').DataTable({
      order: [[0, 'desc']],  // Ini biar urut No. Order DESC (terbaru di atas)
      language: {
        search: "Cari:",
        lengthMenu: "Tampilkan _MENU_ entri",
        info: "Menampilkan _START_ hingga _END_ dari _TOTAL_ entri",
        paginate: {
          first: "Awal",
          last: "Akhir",
          next: "→",
          previous: "←"
        }
      }
    });
  });

  function openUpdateModal(orderId, status) {
    $('#update_order_id').val(orderId);
    $('#updateModal select[name=status]').val(status);
    var modal = new bootstrap.Modal(document.getElementById('updateModal'));
    modal.show();
  }

  function openCancelModal(orderId) {
    $('#cancel_order_id').val(orderId);
    var modal = new bootstrap.Modal(document.getElementById('cancelModal'));
    modal.show();
  }

  function exportTableToExcel(tableID, filename = '') {
    var downloadLink;
    var dataType = 'application/vnd.ms-excel';
    var tableSelect = document.getElementById(tableID);
    var tableHTML = tableSelect.outerHTML.replace(/ /g, '%20');
    filename = filename ? filename + '.xls' : 'excel_data.xls';
    downloadLink = document.createElement("a");
    document.body.appendChild(downloadLink);
    if (navigator.msSaveOrOpenBlob) {
      var blob = new Blob(['\ufeff', tableHTML], { type: dataType });
      navigator.msSaveOrOpenBlob(blob, filename);
    } else {
      downloadLink.href = 'data:' + dataType + ', ' + tableHTML;
      downloadLink.download = filename;
      downloadLink.click();
    }
  }
</script>
</body>
</html>