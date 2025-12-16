# 🍩 Donutschaca – Web Aplikasi Pemesanan Donat

Aplikasi **Donutschaca** adalah sistem pemesanan donat berbasis web yang dikembangkan menggunakan **PHP Native**, **MySQL**, serta teknologi frontend modern seperti **HTML, CSS, JavaScript, dan Bootstrap**. Aplikasi ini dirancang untuk membantu UMKM dalam mengelola produk, pemesanan, pembayaran, serta laporan penjualan secara terstruktur dan efisien.



![Dashboard Pelanggan](img/gambarweb/Pelanggan/Dashboard.png)

---

## 🚀 Fitur Utama

Aplikasi ini memiliki **3 aktor utama** dengan peran dan hak akses yang berbeda, yaitu **Admin**, **Owner**, dan **Pelanggan**.

### 👨‍💼 Admin

![Dashboard Admin](img/gambarweb/Admin/DashboardAdmin.png)

Admin bertugas mengelola operasional sistem harian, meliputi:

* Mengelola data produk
* Mengelola pemesanan pelanggan
* Mengelola pembayaran
* Melihat bukti pembayaran
* Melihat riwayat pemesanan

### 👑 Owner

Owner memiliki akses monitoring dan manajerial, meliputi:

* Melihat laporan produk
* Melihat laporan pemesanan
* Melihat laporan pembayaran
* Melihat pendapatan penjualan
* Mengelola akun admin

### 🧑‍💻 Pelanggan

Pelanggan menggunakan sistem untuk melakukan transaksi, meliputi:

* Melihat daftar produk
* Melakukan pemesanan produk

---

## 🛠️ Teknologi yang Digunakan

* **Backend** : PHP Native
* **Frontend** : HTML, CSS, JavaScript
* **Framework CSS** : Bootstrap
* **Database** : MySQL
* **Web Server** : Apache (XAMPP)

---

## 📁 Struktur Folder

Struktur direktori utama project adalah sebagai berikut:

```bash
htdocs/
├── admin/        # Halaman dan fitur khusus Admin
├── owner/        # Halaman dan laporan khusus Owner
├── pelanggan/    # Halaman pelanggan (frontend pemesanan)
├── koneksi/      # Konfigurasi dan koneksi database
├── bukti/        # Penyimpanan bukti pembayaran
├── img/          # Asset gambar aplikasi
├── donut.png     # Logo / gambar utama
└── index.php     # Entry point aplikasi
```

---

## ⚙️ Instalasi & Konfigurasi

1. **Clone repository**

```bash
git clone https://github.com/username/donutschaca.git
```

2. **Pindahkan project ke folder htdocs**

```bash
C:/xampp/htdocs/donutschaca
```

3. **Buat database MySQL**

* Nama database: `donutschaca`
* Import file SQL (jika tersedia)

4. **Konfigurasi koneksi database**
   Buka file:

```bash
/koneksi/koneksi.php
```

Sesuaikan dengan konfigurasi MySQL Anda:

```php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "donutschaca";
```

5. **Jalankan aplikasi**
   Buka browser dan akses:

```text
http://localhost/donutschaca
```

---

## 🔐 Hak Akses Pengguna

| Role      | Akses                            |
| --------- | -------------------------------- |
| Admin     | CRUD Produk, Pesanan, Pembayaran |
| Owner     | Laporan & Manajemen Admin        |
| Pelanggan | Melihat Produk & Pemesanan       |

---

## 📌 Catatan

* Pastikan folder `bukti/` memiliki permission write (read & write)
* Gunakan PHP versi 7.4 atau lebih baru untuk kompatibilitas optimal

---

## 📄 Lisensi

Project ini dikembangkan untuk kebutuhan akademik dan UMKM. Bebas digunakan dan dikembangkan lebih lanjut sesuai kebutuhan.

---

## 👨‍🎓 Developer

Dikembangkan sebagai bagian dari proyek skripsi dan pengembangan sistem informasi berbasis web menggunakan PHP Native dan MySQL.

---

✨ *Feel free to fork, improve, and give this repository a ⭐ if you find it useful!*
