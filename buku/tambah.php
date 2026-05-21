<?php
session_start();

if(!isset($_SESSION['username'])){
    header("location:../login.php");
    exit;
}

include '../koneksi.php';

$sukses = false;

if(isset($_POST['simpan'])){
    
    $isbn     = mysqli_real_escape_string($conn, $_POST['isbn']);
    $judul    = mysqli_real_escape_string($conn, $_POST['judul']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $penulis  = mysqli_real_escape_string($conn, $_POST['penulis']);
    $penerbit = mysqli_real_escape_string($conn, $_POST['penerbit']);
    $jumlah   = mysqli_real_escape_string($conn, $_POST['jumlah']);

    $insert = mysqli_query($conn, "INSERT INTO buku (ISBN, judul, category, penulis, penerbit, jumlah) 
                                   VALUES ('$isbn', '$judul', '$category', '$penulis', '$penerbit', '$jumlah')");

    if($insert){
        $sukses = true;
    } else {
        echo "Gagal menyimpan: " . mysqli_error($conn);
    }
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
    body { font-family: 'Inter', sans-serif; background-color: #f4f6f9; margin: 0; color: #334155; }
    
    /* SIDEBAR */
    .sidebar { width: 260px; height: 100vh; position: fixed; background: #ffffff; border-right: 1px solid #e2e8f0; padding: 30px 20px; display: flex; flex-direction: column; z-index: 1000; }
    .logo { font-size: 24px; font-weight: 700; color: #4f46e5; margin-bottom: 40px; display: flex; align-items: center; gap: 10px; }
    .sidebar-nav { flex-grow: 1; }
    .sidebar a { display: flex; align-items: center; gap: 12px; color: #64748b; text-decoration: none; padding: 12px 15px; border-radius: 12px; margin-bottom: 8px; transition: all 0.3s; font-weight: 500; }
    .sidebar a:hover, .sidebar a.active { background: #f1f5f9; color: #4f46e5; }
    .logout-btn { margin-top: auto; color: #ef4444 !important; display: flex; align-items: center; gap: 12px; padding: 12px 15px; text-decoration: none; font-weight: 500; border-radius: 12px; }
    .logout-btn:hover { background: #fef2f2 !important; }
    
    /* MAIN CONTENT */
    .main { margin-left: 260px; padding: 40px; }
    
    /* NEW GRID & FORM CARD STYLE */
    .form-card { background: white; border-radius: 16px; padding: 30px; border: 1px solid #e2e8f0; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05); max-width: 950px; }
    .form-label { font-weight: 600; color: #475569; font-size: 13.5px; margin-bottom: 6px; display: flex; align-items: center; gap: 6px; }
    .form-label i { color: #4f46e5; font-size: 14px; }
    
    /* Input Group Styling */
    .input-group-text { background-color: #f8fafc; border-color: #cbd5e1; color: #64748b; border-radius: 10px 0 0 10px; }
    .form-control { background: #ffffff; border: 1px solid #cbd5e1; border-radius: 10px; padding: 10px 14px; font-size: 14px; transition: 0.2s; }
    .form-control:focus { border-color: #4f46e5; box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.08); }
    .has-icon .form-control { border-radius: 0 10px 10px 0; }
    
    .btn-custom { border-radius: 10px; padding: 10px 20px; font-weight: 600; font-size: 14px; transition: 0.2s; }
    .btn-back { color: #64748b; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; margin-bottom: 16px; font-size: 14px; font-weight: 500; }
    .btn-back:hover { color: #4f46e5; }
</style>
</head>
<body>

<?php if($sukses): ?>
<script>
    Swal.fire({
        title: 'Berhasil Disimpan!',
        text: 'Buku baru telah berhasil terdaftar ke sistem perpustakaan.',
        icon: 'success',
        confirmButtonColor: '#4f46e5'
    }).then(() => {
        window.location.href = 'tampil.php';
    });
</script>
<?php endif; ?>

<div class="sidebar">
    <div class="logo"><i class="fas fa-book-open"></i> NusaBaca</div>
    <nav class="sidebar-nav">
        <a href="../dashboard.php"><i class="fas fa-columns"></i> Dashboard</a>
        <a href="tampil.php" class="active"><i class="fas fa-book"></i> Data Buku</a>
        <a href="../anggota/tampil.php"><i class="fas fa-users"></i> Anggota</a>
        <a href="../peminjaman/tampil.php"><i class="fas fa-exchange-alt"></i> Peminjaman</a>
        <a href="../pengembalian/tampil.php"><i class="fas fa-check-circle"></i> Pengembalian</a>
        <a href="../laporan/index.php"><i class="fas fa-file-alt"></i> Laporan</a>
    </nav>
    <a href="../logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Keluar</a>
</div>

<div class="main">
    <a href="tampil.php" class="btn-back">
        <i class="fas fa-arrow-left"></i> Kembali ke Katalog Utama
    </a>
    <h3 class="fw-bold text-dark mb-3" style="font-size: 22px;">Registrasi Buku Baru</h3>

    <div class="form-card">
        <form action="" method="POST">
            
            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <label class="form-label"><i class="fas fa-barcode"></i> Kode ISBN</label>
                    <input type="text" name="isbn" class="form-control" placeholder="Contoh: 978-623-..." required>
                </div>
                <div class="col-md-5">
                    <label class="form-label"><i class="fas fa-bookmark"></i> Judul Lengkap Buku</label>
                    <input type="text" name="judul" class="form-control" placeholder="Masukkan judul buku" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label"><i class="fas fa-tags"></i> Kategori / Genre</label>
                    <input type="text" name="category" class="form-control" placeholder="Fiksi, Romance, Komik" required>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label"><i class="fas fa-pen-nib"></i> Nama Penulis</label>
                    <input type="text" name="penulis" class="form-control" placeholder="Nama pengarang buku" required>
                </div>
                <div class="col-md-5">
                    <label class="form-label"><i class="fas fa-building"></i> Perusahaan Penerbit</label>
                    <input type="text" name="penerbit" class="form-control" placeholder="Nama penerbit/pustaka" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label"><i class="fas fa-layer-group"></i> Jumlah Stok (Buku)</label>
                    <input type="number" name="jumlah" class="form-control" placeholder="Isi angka stok awal" min="1" required>
                </div>
            </div>

            <hr class="mb-4" style="opacity: 0.08;">

            <div class="d-flex justify-content-end gap-2">
                <button type="reset" class="btn btn-light btn-custom border text-secondary px-4">
                    Reset Form
                </button>
                <button type="submit" name="simpan" class="btn btn-primary btn-custom px-4 shadow-sm" style="background: #4f46e5; border-color: #4f46e5;">
                    <i class="fas fa-cloud-upload-alt me-2"></i> Daftarkan Buku
                </button>
            </div>

        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>