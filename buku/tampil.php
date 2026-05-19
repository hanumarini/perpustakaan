<?php
session_start();

if(!isset($_SESSION['username'])){
    header("location:../login.php");
    exit;
}

include '../koneksi.php';

$cari = isset($_GET['cari']) ? $_GET['cari'] : '';

// Sanitasi input pencarian sederhana
$cari_safe = mysqli_real_escape_string($conn, $cari);

$data = mysqli_query($conn, "SELECT * FROM buku
WHERE judul LIKE '%$cari_safe%'
OR penulis LIKE '%$cari_safe%'
OR category LIKE '%$cari_safe%'");
?>

<!DOCTYPE html>
<html lang="NO">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Buku - NusaBaca</title>

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

    /* SIDEBAR */
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
        border: 1px solid transparent;
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
        border-color: #fee2e2;
    }

    /* MAIN CONTENT */
    .main {
        margin-left: 260px;
        padding: 40px;
    }

    /* CARDS */
    .table-card {
        background: white;
        border-radius: 20px;
        padding: 30px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    /* SEARCH BOX */
    .search-group {
        position: relative;
        max-width: 400px;
    }

    .search-box {
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 12px 12px 12px 45px;
        transition: 0.3s;
    }

    .search-box:focus {
        background: white;
        border-color: #4f46e5;
        box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
    }

    .search-icon {
        position: absolute;
        left: 18px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
    }

    /* TABLE STYLING */
    .table thead th {
        background-color: #f8fafc;
        color: #64748b;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 11px;
        letter-spacing: 0.05em;
        padding: 15px;
        border-bottom: 2px solid #f1f5f9;
    }

    .table tbody td {
        padding: 15px;
        vertical-align: middle;
        color: #1e293b;
        font-size: 14px;
        border-bottom: 1px solid #f1f5f9;
    }

    .btn-custom {
        border-radius: 10px;
        font-weight: 600;
        padding: 10px 20px;
    }

    .badge-category {
        background: #e0e7ff;
        color: #4338ca;
        padding: 5px 10px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
    }

    .footer {
        margin-top: 40px;
        color: #94a3b8;
        font-size: 14px;
    }
</style>
</head>
<body>

<div class="sidebar">
    <div class="logo">
        <i class="fas fa-book-open"></i> NusaBaca
    </div>

    <nav class="sidebar-nav">
        <a href="../dashboard.php">
            <i class="fas fa-columns"></i> Dashboard
        </a>
        <a href="../buku/tampil.php" class="active">
            <i class="fas fa-book"></i> Data Buku
        </a>
        <a href="../anggota/tampil.php">
            <i class="fas fa-users"></i> Anggota
        </a>
        <a href="../peminjaman/tampil.php">
            <i class="fas fa-exchange-alt"></i> Peminjaman
        </a>
        <a href="../pengembalian/tampil.php">
            <i class="fas fa-check-circle"></i> Pengembalian
        </a>

        <a href="../laporan/index.php"><i class="fas fa-file-alt"></i> Laporan</a>
    </nav>

    <a href="../logout.php" class="logout-btn">
        <i class="fas fa-sign-out-alt"></i> Keluar
    </a>
</div>

<div class="main">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-dark m-0">Pengelolaan Buku</h3>
        <div class="bg-white border px-3 py-2 rounded-pill shadow-sm">
            <i class="fas fa-user-circle text-primary me-2"></i> 
            <span class="fw-semibold"><?= $_SESSION['username']; ?></span>
        </div>
    </div>

    <div class="table-card">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="fw-bold m-0">Katalog Buku</h5>
            <a href="tambah.php" class="btn btn-primary btn-custom shadow-sm">
                <i class="fas fa-plus me-2"></i> Tambah Buku
            </a>
            
        </div>

        <form method="GET" class="mb-4">
            <div class="search-group">
                <i class="fas fa-search search-icon"></i>
                <input type="text" 
                       name="cari" 
                       class="form-control search-box" 
                       placeholder="Cari judul, penulis, atau kategori..." 
                       value="<?= htmlspecialchars($cari) ?>">
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>NO</th>
                        <th>ISBN</th>
                        <th>Judul Buku</th>
                        <th>Kategori</th>
                        <th>Penulis</th>
                        <th>Penerbit</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(mysqli_num_rows($data) > 0) { ?>
                        <?php while($d = mysqli_fetch_array($data)){ ?>
                        <tr>
                            <td class="text-muted">#<?= $d['NO'] ?></td>
                            <td class="small fw-mono"><?= $d['ISBN'] ?></td>
                            <td><span class="fw-bold"><?= $d['judul'] ?></span></td>
                            <td><span class="badge-category"><?= $d['category'] ?></span></td>
                            <td><?= $d['penulis'] ?></td>
                            <td><?= $d['penerbit'] ?></td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <a href="edit.php?NO=<?= $d['NO'] ?>" class="btn btn-sm btn-outline-warning me-2" style="border-radius: 8px;">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                    <a href="hapus.php?NO=<?= $d['NO'] ?>" class="btn btn-sm btn-outline-danger" style="border-radius: 8px;" onclick="return confirm('Hapus buku ini dari database?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="fas fa-book-open d-block mb-3" style="font-size: 40px; opacity: 0.3;"></i>
                                Buku tidak ditemukan dalam database.
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="footer text-center">
        &copy; <?= date('Y') ?> <b>NusaBaca</b> • <i class="fas fa-code"></i> v2.0
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>