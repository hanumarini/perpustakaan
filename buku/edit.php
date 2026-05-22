<?php
session_start();

if(!isset($_SESSION['username'])){
    header("location:../login.php");
    exit;
}

include '../koneksi.php';

// Pastikan parameter NO ada di URL
if (!isset($_GET['NO']) || empty($_GET['NO'])) {
    header("location:tampil.php");
    exit;
}

$NO = mysqli_real_escape_string($conn, $_GET['NO']);
$query = mysqli_query($conn, "SELECT * FROM buku WHERE NO='$NO'");
$d = mysqli_fetch_array($query);

if(!$d){
    header("location:tampil.php");
    exit;
}

// ====================================================================
// TRIK PENDETEKSI OTOMATIS (Mencegah data kosong akibat salah huruf besar/kecil)
// ====================================================================
$val_isbn      = isset($d['ISBN']) ? $d['ISBN'] : (isset($d['isbn']) ? $d['isbn'] : '');
$val_judul     = isset($d['judul']) ? $d['judul'] : (isset($d['JUDUL']) ? $d['JUDUL'] : '');
$val_category  = isset($d['category']) ? $d['category'] : (isset($d['CATEGORY']) ? $d['CATEGORY'] : (isset($d['kategori']) ? $d['kategori'] : ''));
$val_penulis   = isset($d['penulis']) ? $d['penulis'] : (isset($d['PENULIS']) ? $d['PENULIS'] : '');
$val_penerbit  = isset($d['penerbit']) ? $d['penerbit'] : (isset($d['PENERBIT']) ? $d['PENERBIT'] : '');
// Deteksi otomatis kolom stok/jumlah buku
$val_jumlah    = isset($d['jumlah']) ? $d['jumlah'] : (isset($d['JUMLAH']) ? $d['JUMLAH'] : (isset($d['stok']) ? $d['stok'] : (isset($d['STOK']) ? $d['STOK'] : 0)));

// ====================================================================
// PROSES SIMPAN HASIL EDIT
// ====================================================================
$notif = '';
if(isset($_POST['update'])){
    $ISBN     = mysqli_real_escape_string($conn, $_POST['ISBN']);
    $judul    = mysqli_real_escape_string($conn, $_POST['JUDUL']);
    $category = mysqli_real_escape_string($conn, $_POST['CATEGORY']);
    $penulis  = mysqli_real_escape_string($conn, $_POST['PENULIS']);
    $penerbit = mysqli_real_escape_string($conn, $_POST['PENERBIT']);
    $jumlah   = (int)$_POST['JUMLAH']; // Ambil nilai stok baru

    // Menggunakan pengecekan dinamis untuk kolom jumlah/stok saat update
    $kolom_jumlah = isset($d['stok']) || isset($d['STOK']) ? 'stok' : 'jumlah';

    $update_query = mysqli_query($conn, "UPDATE buku SET 
        ISBN='$ISBN', 
        judul='$judul', 
        category='$category', 
        penulis='$penulis', 
        penerbit='$penerbit',
        $kolom_jumlah='$jumlah' 
        WHERE NO='$NO'");

    if($update_query){
        $notif = 'sukses';
    } else {
        $notif = 'gagal';
        $error_msg = mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Buku - NusaBaca</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
        <a href="../dashboard.php"><i class="fas fa-columns"></i> Dashboard</a>
        <a href="tampil.php" class="active"><i class="fas fa-book"></i> Data Buku</a>
        <a href="../anggota/tampil.php"><i class="fas fa-users"></i> Anggota</a>
        <a href="../peminjaman/tampil.php"><i class="fas fa-exchange-alt"></i> Peminjaman</a>
        <a href="../pengembalian/tampil.php"><i class="fas fa-check-circle"></i> Pengembalian</a>
        <a href="../laporan/index.php"><i class="fas fa-file-alt"></i> Laporan</a>
    </nav>

    <a href="../logout.php" class="logout-btn">
        <i class="fas fa-sign-out-alt"></i> Keluar
    </a>
</div>

<div class="main">
    <a href="tampil.php" class="btn-back">
        <i class="fas fa-arrow-left"></i> Kembali ke Daftar
    </a>

    <h3 class="fw-bold text-dark mb-4">Edit Informasi Buku</h3>

    <div class="form-card">
        <form method="POST">
            <div class="row">
                <div class="col-md-6 mb-4">
                    <label class="form-label">ISBN</label>
                    <input type="text" name="ISBN" class="form-control" value="<?= htmlspecialchars($val_isbn) ?>" required>
                </div>
                <div class="col-md-6 mb-4">
                    <label class="form-label">Kategori</label>
                    <input type="text" name="CATEGORY" class="form-control" value="<?= htmlspecialchars($val_category) ?>" required>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label">Judul Buku</label>
                <input type="text" name="JUDUL" class="form-control" value="<?= htmlspecialchars($val_judul) ?>" required>
            </div>

            <div class="row">
                <div class="col-md-6 mb-4">
                    <label class="form-label">Penulis</label>
                    <input type="text" name="PENULIS" class="form-control" value="<?= htmlspecialchars($val_penulis) ?>" required>
                </div>
                <div class="col-md-6 mb-4">
                    <label class="form-label">Penerbit</label>
                    <input type="text" name="PENERBIT" class="form-control" value="<?= htmlspecialchars($val_penerbit) ?>" required>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label">Jumlah / Stok Buku (Ekspl)</label>
                <input type="number" name="JUMLAH" class="form-control" min="0" value="<?= htmlspecialchars($val_jumlah) ?>" required>
            </div>

            <hr class="my-4" style="opacity: 0.1;">

            <div class="d-flex gap-2 mt-2">
                <button type="submit" name="update" class="btn btn-primary btn-custom flex-grow-1" style="background:#4f46e5; border-color:#4f46e5;">
                    <i class="fas fa-save me-2"></i> Perbarui Data Buku
                </button>
                <a href="tampil.php" class="btn btn-light btn-custom px-4">Batal</a>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // NOTIFIKASI SWEETALERT JIKA UPDATE BERHASIL / GAGAL
    <?php if($notif === 'sukses') { ?>
        Swal.fire({
            title: 'Berhasil Diperbarui!',
            text: 'Informasi dan stok buku telah diperbarui di sistem.',
            icon: 'success',
            confirmButtonColor: '#4f46e5',
            customClass: { popup: 'rounded-4' }
        }).then(() => { window.location.href = 'tampil.php'; });
    <?php } elseif($notif === 'gagal') { ?>
        Swal.fire({
            title: 'Gagal Memperbarui!',
            text: '<?= isset($error_msg) ? $error_msg : "Terjadi kesalahan database." ?>',
            icon: 'error',
            confirmButtonColor: '#ef4444',
            customClass: { popup: 'rounded-4' }
        });
    <?php } ?>
</script>
</body>
</html>