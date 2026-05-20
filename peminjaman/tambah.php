<?php
session_start();

if(!isset($_SESSION['username'])){
    header("location:../login.php");
    exit;
}

include '../koneksi.php';

if(isset($_POST['submit'])){
    // Menggunakan real_escape_string untuk keamanan
    $nama        = mysqli_real_escape_string($conn, $_POST['nama']);
    $judul       = mysqli_real_escape_string($conn, $_POST['judul']);
    $tgl_pinjam  = mysqli_real_escape_string($conn, $_POST['tgl_pinjam']);
    $tgl_kembali = mysqli_real_escape_string($conn, $_POST['tgl_kembali']);
    $status      = "Dipinjam";

    mysqli_query($conn, "INSERT INTO peminjaman 
    (nama_peminjam, judul_buku, tanggal_pinjam, tanggal_kembali, status) 
    VALUES ('$nama', '$judul', '$tgl_pinjam', '$tgl_kembali', '$status')");

    header("location:tampil.php");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Peminjaman - NusaBaca</title>

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

    /* FORM CARD */
    .form-card {
        background: white;
        border-radius: 20px;
        padding: 40px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        max-width: 700px;
    }

    .form-label {
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 8px;
    }

    .form-control, .form-select {
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 12px 15px;
        transition: 0.3s;
    }

    .form-control:focus, .form-select:focus {
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
        <a href="../buku/tampil.php">
            <i class="fas fa-book"></i> Data Buku
        </a>
        <a href="../anggota/tampil.php">
            <i class="fas fa-users"></i> Anggota
        </a>
        <a href="tampil.php" class="active">
            <i class="fas fa-exchange-alt"></i> Peminjaman
        </a>
        <a href="../pengembalian/tampil.php">
            <i class="fas fa-check-circle"></i> Pengembalian
        </a>

        <a href="../laporan/index.php"><i class="fas fa-file-alt"></i> Laporan</a>
    </nav>
    </nav>

    <a href="../logout.php" class="logout-btn">
        <i class="fas fa-sign-out-alt"></i> Keluar
    </a>
</div>

<div class="main">
    <a href="tampil.php" class="btn-back">
        <i class="fas fa-arrow-left"></i> Kembali ke Daftar
    </a>

    <h3 class="fw-bold text-dark mb-4">Catat Peminjaman Baru</h3>

    <div class="form-card">
        <form method="POST">
            <div class="mb-4">
                <label class="form-label">Nama Peminjam (Anggota)</label>
                <select name="nama" class="form-select" required>
                    <option value="">-- Pilih Anggota --</option>
                    <?php
                    $anggota = mysqli_query($conn, "SELECT * FROM anggota ORDER BY nama ASC");
                    while($a = mysqli_fetch_array($anggota)){
                        echo "<option value='".$a['nama']."'>".$a['nama']."</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="mb-4">
                <label class="form-label">Judul Buku</label>
                <select name="judul" class="form-select" required>
                    <option value="">-- Pilih Buku --</option>
                    <?php
                    $buku = mysqli_query($conn, "SELECT * FROM buku ORDER BY judul ASC");
                    while($b = mysqli_fetch_array($buku)){
                        echo "<option value='".$b['judul']."'>".$b['judul']."</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="row">
                <div class="col-md-6 mb-4">
                    <label class="form-label">Tanggal Pinjam</label>
                    <input type="date" name="tgl_pinjam" class="form-control" value="<?= date('Y-m-d') ?>" required>
                </div>
                <div class="col-md-6 mb-4">
                    <label class="form-label">Estimasi Tanggal Kembali</label>
                    <input type="date" name="tgl_kembali" class="form-control" required>
                </div>
            </div>

            <hr class="my-4" style="opacity: 0.1;">

            <div class="d-grid gap-2">
                <button type="submit" name="submit" class="btn btn-primary btn-custom">
                    <i class="fas fa-plus-circle me-2"></i> Proses Peminjaman
                </button>
                <a href="tampil.php" class="btn btn-light btn-custom">Batal</a>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>