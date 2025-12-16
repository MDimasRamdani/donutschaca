<?php
session_start();
$koneksi = new mysqli("localhost", "root", "", "donutschaca");

// Cek login owner
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'owner') {
    header("Location: owner_login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $koneksi->real_escape_string($_POST['username']);
    $password = $koneksi->real_escape_string($_POST['password']);
    if (!empty($username) && !empty($password)) {
        $koneksi->query("INSERT INTO users (username, password, role) VALUES ('$username', '$password', 'admin')");
    }
}

if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    $koneksi->query("DELETE FROM users WHERE user_id = $id AND role = 'admin'");
    header("Location: manajemen_admin.php");
    exit;
}

$admins = $koneksi->query("SELECT * FROM users WHERE role = 'admin'");
?>
<?php include "../koneksi/navbar_owner.php"; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #ffecd2, #fcb69f);
            padding: 20px;
            font-family: 'Segoe UI', sans-serif;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        .form-label {
            font-weight: bold;
        }
        .btn-primary {
            background-color: #ff914d;
            border: none;
        }
        .btn-primary:hover {
            background-color: #ff7a26;
        }
        .table th, .table td {
            vertical-align: middle;
        }
        @media (max-width: 576px) {
            .form-group {
                margin-bottom: 1rem;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <h3 class="mb-4 text-center text-uppercase">👤 Manajemen Admin</h3>

    <form method="POST" class="row g-3 mb-4">
        <div class="col-sm-6">
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-control" required>
        </div>
        <div class="col-sm-6">
            <label class="form-label">Password</label>
            <input type="text" name="password" class="form-control" required>
        </div>
        <div class="col-12 d-grid mt-2">
            <button type="submit" class="btn btn-primary">➕ Tambah Admin</button>
        </div>
    </form>

    <h5 class="text-center mb-3">📋 Daftar Admin Terdaftar</h5>
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-warning text-center">
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Password</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $admins->fetch_assoc()): ?>
                <tr>
                    <td class="text-center"><?= $row['user_id']; ?></td>
                    <td><?= htmlspecialchars($row['username']); ?></td>
                    <td><?= htmlspecialchars($row['password']); ?></td>
                    <td class="text-center">
                        <a href="?hapus=<?= $row['user_id']; ?>" class="btn btn-danger btn-sm"
                           onclick="return confirm('Yakin ingin menghapus admin ini?')">🗑️ Hapus</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
