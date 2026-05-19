<?php
session_start();

if(!isset($_SESSION['username'])){
    header("location:../login.php");
    exit;
}

include '../koneksi.php';

if(isset($_POST['simpan'])){
    
    $isbn     = mysqli_real_escape_string($conn, $_POST['isbn']);
    $judul    = mysqli_real_escape_string($conn, $_POST['judul']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $penulis  = mysqli_real_escape_string($conn, $_POST['penulis']);
    $penerbit = mysqli_real_escape_string($conn, $_POST['penerbit']);

    mysqli_query($conn, "INSERT INTO buku (ISBN, judul, category, penulis, penerbit) 
                         VALUES ('$isbn', '$judul', '$category', '$penulis', '$penerbit')");

    header("location:tampil.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Buku - NusaBaca</title>

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

    /* FORM CARD */
    .form-card {
        background: white;
        border-radius: 20px;
        padding: 40px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        max-width: 800px;
    }

    .form-label {
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 8px;
    }

    .form-control {
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 12px 15px;
        transition: 0.3s;
    }

    .form-control:focus {
        background: white;
        border-color: #4f46e5;
        box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
    }

    .btn-custom {
        border-radius: 12px;
        padding: 12px;
        font-weight: 600;
        transition: 0.3s;
    }

    .btn-back {
        color: #64748b;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 20px;
        font-weight: 500;
        transition: 0.3s;
    }

    .btn-back:hover {
        color: #4f46e5;
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

        <a href="tampil.php" class="active">
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
        <a href="laporan.php" target="_blank" class="btn btn-outline-secondary btn-custom me-2">
    <i class="fas fa-print me-2"></i> Cetak Laporan
</a>
    </nav>

    <a href="../logout.php" class="logout-btn">
        <i class="fas fa-sign-out-alt"></i> Keluar
    </a>
</div>

<div class="main">

    <a href="tampil.php" class="btn-back">
        <i class="fas fa-arrow-left"></i> Kembali ke Daftar Buku
    </a>

    <h3 class="fw-bold text-dark mb-4">Input Data Buku Baru</h3>

    <div class="form-card">

        <form method="POST">

            <div class="row">

                <div class="col-md-6 mb-4">
                    <label class="form-label">ISBN</label>
                    <input type="text" name="isbn" class="form-control" placeholder="Contoh: 978-602-..." required>
                </div>

                <div class="col-md-6 mb-4">
                    <label class="form-label">Kategori</label>
                    <input type="text" name="category" class="form-control" placeholder="Fiksi, Edukasi, dll" required>
                </div>

            </div>

            <div class="mb-4">
                <label class="form-label">Judul Buku</label>
                <input type="text" name="judul" class="form-control" placeholder="Masukkan judul lengkap buku" required>
            </div>

            <div class="row">

                <div class="col-md-6 mb-4">
                    <label class="form-label">Penulis</label>
                    <input type="text" name="penulis" class="form-control" placeholder="Nama penulis" required>
                </div>

                <div class="col-md-6 mb-4">
                    <label class="form-label">Penerbit</label>
                    <input type="text" name="penerbit" class="form-control" placeholder="Nama penerbit" required>
                </div>

            </div>

            <hr class="my-4" style="opacity: 0.1;">

            <div class="d-flex gap-3">

                <button type="submit" name="simpan" class="btn btn-primary btn-custom flex-grow-1">
                    <i class="fas fa-save me-2"></i> Simpan Data Buku
                </button>

                <button type="reset" class="btn btn-light btn-custom px-4">
                    Reset
                </button>

            </div>

        </form>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>