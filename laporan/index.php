<?php
session_start();

if(!isset($_SESSION['username'])){
    header("location:../login.php");
    exit;
}

include '../koneksi.php';

// Ambil data anggota
$query = mysqli_query($conn, "SELECT * FROM anggota ORDER BY NO ASC");
$total_anggota = mysqli_num_rows($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Anggota - NusaBaca</title>

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

    /* SIDEBAR (Sama dengan halaman lain) */
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

    /* KERTAS LAPORAN DI DALAM DASHBOARD */
    .report-paper {
        background: white;
        border-radius: 20px;
        padding: 50px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    .kop-surat {
        border-bottom: 3px double #1e293b;
        padding-bottom: 20px;
        margin-bottom: 30px;
    }

    .table th {
        background-color: #f8fafc !important;
        color: #0f172a !important;
        font-weight: 600;
    }

    /* ATURAN CETAK (PRINT) */
    @media print {
        .sidebar, .no-print {
            display: none !important;
        }
        .main {
            margin-left: 0 !important;
            padding: 0 !important;
        }
        .report-paper {
            border: none !important;
            box-shadow: none !important;
            padding: 0 !important;
        }
    }
</style>
</head>
<body>

<div class="sidebar">
    <div class="logo">
        <i class="fas fa-book-open"></i> NusaBaca
    </div>
    <nav class="sidebar-nav">
        <a href="../dashboard.php"><i class="fas fa-columns"></i> Dashboard</a>
        <a href="../buku/tampil.php"><i class="fas fa-book"></i> Data Buku</a>
        <a href="../anggota/tampil.php"><i class="fas fa-users"></i> Anggota</a>
        <a href="../peminjaman/tampil.php"><i class="fas fa-exchange-alt"></i> Peminjaman</a>
        <a href="../pengembalian/tampil.php"><i class="fas fa-check-circle"></i> Pengembalian</a>
        <a href="index.php" class="active"><i class="fas fa-file-alt"></i> Laporan</a>
    </nav>
    <a href="../logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Keluar</a>
</div>

<div class="main">
    
    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <h4 class="fw-bold m-0 text-dark">Modul Cetak Laporan</h4>
        <button onclick="window.print()" class="btn btn-primary px-4" style="border-radius: 12px;">
            <i class="fas fa-print me-2"></i> Print Dokumen
        </button>
    </div>

    <div class="report-paper">
        <div class="text-center kop-surat">
            <h2 class="fw-bold text-uppercase m-0" style="color: #4f46e5;"><i class="fas fa-book-open me-2"></i> Perpustakaan NusaBaca</h2>
            <p class="text-muted m-0 small">Jl. Raya Nusantara No. 123, Sukaluyu, Jawa Barat • Telp: (021) 8888-9999</p>
            <p class="text-muted m-0 small font-monospace">Sistem Informasi Manajemen Perpustakaan Terpadu</p>
        </div>

        <div class="mb-4 text-center">
            <h4 class="fw-bold text-uppercase m-0">Laporan Data Anggota Terdaftar</h4>
            <p class="text-muted small">Dicetak otomatis pada: <b><?= date('d F Y') ?></b></p>
        </div>

        <p class="small font-monospace">Total Record: <b><?= $total_anggota ?> Anggota</b></p>

        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead>
                    <tr class="text-center">
                        <th width="80">No</th>
                        <th>Nama Lengkap</th>
                        <th>Alamat</th>
                        <th width="200">No. Telepon</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($total_anggota > 0) { ?>
                        <?php while($d = mysqli_fetch_array($query)){ ?>
                        <tr>
                            <td class="text-center font-monospace fw-bold">#<?= $d['NO'] ?></td>
                            <td class="fw-semibold"><?= htmlspecialchars($d['nama']) ?></td>
                            <td><?= htmlspecialchars($d['alamat']) ?></td>
                            <td class="text-center font-monospace"><?= htmlspecialchars($d['no_hp']) ?></td>
                        </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">Tidak ada data.</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <div class="row mt-5 pt-3">
            <div class="col-8"></div>
            <div class="col-4 text-center">
                <p class="m-0 small text-muted">Sukaluyu, <?= date('d F Y') ?></p>
                <p class="fw-semibold m-0">Kepala Perpustakaan NusaBaca</p>
                <div style="height: 70px;"></div>
                <p class="fw-bold m-0 text-decoration-underline">Admin Perpustakaan</p>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>