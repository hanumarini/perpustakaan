<?php
session_start();

if(!isset($_SESSION['username'])){
    header("location:login.php");
    exit;
}

// Karena dashboard.php ada di folder utama, panggil koneksi langsung tanpa ../
include 'koneksi.php';

// 1. QUERY UNTUK HITUNG TOTAL DATA (Untuk Kartu Statistik)
$total_anggota = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM anggota"));

// Cek apakah tabel buku, peminjaman, dll sudah ada (jika belum, nilainya set ke 0 dulu)
$total_buku = @mysqli_num_rows(mysqli_query($conn, "SELECT * FROM buku")) ?: 0;
$total_pinjam = @mysqli_num_rows(mysqli_query($conn, "SELECT * FROM peminjaman")) ?: 0;

// 2. QUERY UTAMA UNTUK TABEL LAPORAN (Menampilkan seluruh anggota)
$query_laporan = mysqli_query($conn, "SELECT * FROM anggota ORDER BY NO ASC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Utama - NusaBaca</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
    body {
        font-family: 'Inter', sans-serif;
        background-color: #f8fafc;
        margin: 0;
        color: #334155;
    }

    /* SIDEBAR (Karena di folder utama, href-nya mengarah ke dalam folder masing-masing) */
    .sidebar {
        width: 260px;
        height: 100vh;
        position: fixed;
        background: #ffffff;
        border-right: 1px solid #e2e8f0;
        padding: 30px 20px;
        display: flex;
        flex-direction: column;
        z-index: 1000;
    }

    .logo {
        font-size: 24px;
        font-weight: 700;
        color: #4f46e5;
        margin-bottom: 40px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .sidebar-nav {
        flex-grow: 1;
    }

    .sidebar a {
        display: flex;
        align-items: center;
        gap: 12px;
        color: #64748b;
        text-decoration: none;
        padding: 12px 15px;
        border-radius: 12px;
        margin-bottom: 8px;
        transition: all 0.3s;
        font-weight: 500;
    }

    .sidebar a:hover, .sidebar a.active {
        background: #f1f5f9;
        color: #4f46e5;
    }

    .logout-btn {
        margin-top: auto;
        color: #ef4444 !important;
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 15px;
        text-decoration: none;
        font-weight: 500;
        border-radius: 12px;
    }

    .logout-btn:hover {
        background: #fef2f2 !important;
    }

    /* MAIN CONTENT */
    .main {
        margin-left: 260px;
        padding: 40px;
    }

    /* STATISTIC CARDS */
    .stat-card {
        background: white;
        border-radius: 16px;
        padding: 24px;
        border: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: space-between;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }

    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }

    /* KERTAS LAPORAN DI DASHBOARD */
    .report-card {
        background: white;
        border-radius: 20px;
        padding: 35px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    .table thead th {
        background-color: #f8fafc;
        color: #64748b;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 12px;
        letter-spacing: 0.05em;
        padding: 15px;
        border-bottom: 2px solid #f1f5f9;
    }

    .table tbody td {
        padding: 15px;
        color: #1e293b;
        border-bottom: 1px solid #f1f5f9;
    }
</style>
</head>
<body>

<div class="sidebar">
    <div class="logo">
        <i class="fas fa-book-open"></i> NusaBaca
    </div>

    <nav class="sidebar-nav">
        <a href="dashboard.php" class="active">
            <i class="fas fa-columns"></i> Dashboard
        </a>
        <a href="buku/tampil.php">
            <i class="fas fa-book"></i> Data Buku
        </a>
        <a href="anggota/tampil.php">
            <i class="fas fa-users"></i> Anggota
        </a>
        <a href="peminjaman/tampil.php">
            <i class="fas fa-exchange-alt"></i> Peminjaman
        </a>
        <a href="pengembalian/tampil.php">
            <i class="fas fa-check-circle"></i> Pengembalian
        </a>
        <a href="laporan/index.php">
            <i class="fas fa-file-alt"></i> Laporan
        </a>
    </nav>

    <a href="logout.php" class="logout-btn">
        <i class="fas fa-sign-out-alt"></i> Keluar
    </a>
</div>

<div class="main">
    
    <div class="mb-4">
        <h3 class="fw-bold m-0 text-dark">Selamat Datang, <?= htmlspecialchars($_SESSION['username']); ?>!</h3>
        <p class="text-muted m-0">Berikut adalah ringkasan aktivitas sistem informasi perpustakaan hari ini.</p>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="stat-card">
                <div>
                    <p class="text-muted mb-1 small fw-semibold text-uppercase">Total Anggota</p>
                    <h3 class="fw-bold mb-0"><?= $total_anggota ?> Orang</h3>
                </div>
                <div class="stat-icon bg-primary-subtle text-primary">
                    <i class="fas fa-users"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div>
                    <p class="text-muted mb-1 small fw-semibold text-uppercase">Koleksi Buku</p>
                    <h3 class="fw-bold mb-0"><?= $total_buku ?> Judul</h3>
                </div>
                <div class="stat-icon bg-success-subtle text-success">
                    <i class="fas fa-book"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div>
                    <p class="text-muted mb-1 small fw-semibold text-uppercase">Peminjaman Aktif</p>
                    <h3 class="fw-bold mb-0"><?= $total_pinjam ?> Transaksi</h3>
                </div>
                <div class="stat-icon bg-warning-subtle text-warning">
                    <i class="fas fa-exchange-alt"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="report-card">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h5 class="fw-bold m-0 text-dark"><i class="fas fa-file-alt text-muted me-2"></i> Laporan Ringkas Data Anggota</h5>
                <p class="text-muted small m-0">Menampilkan seluruh data list anggota terdaftar di sistem</p>
            </div>
            <a href="laporan/index.php" target="_blank" class="btn btn-outline-primary btn-sm px-3" style="border-radius: 8px;">
                <i class="fas fa-print me-1"></i> Mode Cetak Penuh
            </a>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th width="80">NO</th>
                        <th>Nama Lengkap</th>
                        <th>Alamat</th>
                        <th>No. Telepon / HP</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($total_anggota > 0) { ?>
                        <?php while($d = mysqli_fetch_array($query_laporan)){ ?>
                        <tr>
                            <td class="fw-bold text-primary">#<?= $d['NO'] ?></td>
                            <td class="fw-semibold"><?= htmlspecialchars($d['nama']) ?></td>
                            <td><?= htmlspecialchars($d['alamat']) ?></td>
                            <td>
                                <span class="badge bg-light text-dark border font-monospace">
                                    <?= htmlspecialchars($d['no_hp']) ?>
                                </span>
                            </td>
                        </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">Belum ada data anggota.</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>