<?php
session_start();

if(!isset($_SESSION['username'])){
    header("location:../login.php");
    exit;
}

include '../koneksi.php';

// 1. Validasi parameter ID yang dikirim dari halaman tampil
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("location:tampil.php");
    exit;
}

$id = mysqli_real_escape_string($conn, $_GET['id']);

// 2. Ambil data transaksi peminjaman lama berdasarkan ID
$query_peminjaman = mysqli_query($conn, "SELECT * FROM peminjaman WHERE id = '$id'");
$data_pinjam = mysqli_fetch_assoc($query_peminjaman);

// Jika data tidak ditemukan, tendang kembali ke halaman utama
if (!$data_pinjam) {
    header("location:tampil.php");
    exit;
}

// 3. Ambil data master Anggota & Buku untuk keperluan opsi Dropdown (Select)
$list_anggota = mysqli_query($conn, "SELECT nama FROM anggota ORDER BY nama ASC");
$list_buku = mysqli_query($conn, "SELECT judul FROM buku ORDER BY judul ASC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Peminjaman - NusaBaca</title>

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

    .form-card {
        background: white;
        border-radius: 20px;
        padding: 35px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        max-width: 700px;
    }

    .form-label {
        font-weight: 600;
        color: #475569;
        font-size: 14px;
        margin-bottom: 8px;
    }

    .form-control, .form-select {
        border-radius: 10px;
        padding: 10px 15px;
        border: 1px solid #cbd5e1;
        background-color: #f8fafc;
    }

    .form-control:focus, .form-select:focus {
        background-color: #ffffff;
        border-color: #4f46e5;
        box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
    }

    .btn-custom {
        border-radius: 10px;
        font-weight: 600;
        padding: 10px 25px;
    }

    .footer {
        margin-top: 40px;
        color: #94a3b8;
        font-size: 14px;
        max-width: 700px;
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
        <a href="../peminjaman/tampil.php" class="active"><i class="fas fa-exchange-alt"></i> Peminjaman</a>
        <a href="../pengembalian/tampil.php"><i class="fas fa-check-circle"></i> Pengembalian</a>
        <a href="../laporan/index.php"><i class="fas fa-file-alt"></i> Laporan</a>
    </nav>

    <a href="../logout.php" class="logout-btn">
        <i class="fas fa-sign-out-alt"></i> Keluar
    </a>
</div>

<div class="main">

    <div class="mb-4">
        <h4 class="fw-bold m-0 text-dark">Form Edit Transaksi Peminjaman</h4>
        <p class="text-muted small m-0">Perbarui informasi sirkulasi buku perpustakaan</p>
    </div>

    <div class="form-card">
        <form action="edit_proses.php" method="POST" id="formEditPeminjaman">
            
            <input type="hidden" name="id" value="<?= $data_pinjam['id'] ?>">

            <div class="mb-3">
                <label class="form-label">Nama Peminjam (Anggota)</label>
                <select name="nama_peminjam" class="form-select" required>
                    <option value="">-- Pilih Anggota --</option>
                    <?php while($agt = mysqli_fetch_array($list_anggota)) { ?>
                        <option value="<?= htmlspecialchars($agt['nama']) ?>" <?= ($data_pinjam['nama_peminjam'] == $agt['nama']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($agt['nama']) ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Judul Buku</label>
                <select name="judul_buku" class="form-select" required>
                    <option value="">-- Pilih Buku --</option>
                    <?php while($bk = mysqli_fetch_array($list_buku)) { ?>
                        <option value="<?= htmlspecialchars($bk['judul']) ?>" <?= ($data_pinjam['judul_buku'] == $bk['judul']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($bk['judul']) ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Tanggal Pinjam</label>
                    <input type="date" name="tanggal_pinjam" class="form-control" value="<?= $data_pinjam['tanggal_pinjam'] ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Tanggal Kembali</label>
                    <input type="date" name="tanggal_kembali" class="form-control" value="<?= $data_pinjam['tanggal_kembali'] ?>" required>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Status Transaksi</label>
                    <select name="status" class="form-select" required>
                        <option value="Dipinjam" <?= ($data_pinjam['status'] == 'Dipinjam') ? 'selected' : '' ?>>Dipinjam (Aktif)</option>
                        <option value="Dikembalikan" <?= ($data_pinjam['status'] == 'Dikembalikan') ? 'selected' : '' ?>>Dikembalikan</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nominal Denda (Rp)</label>
                    <input type="number" name="denda" class="form-control" value="<?= $data_pinjam['denda'] ?>" min="0">
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="tampil.php" class="btn btn-light btn-custom border text-secondary">
                    <i class="fas fa-arrow-left me-2"></i> Kembali
                </a>
                <button type="submit" name="submit" class="btn btn-primary btn-custom shadow-sm">
                    <i class="fas fa-save me-2"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

    <div class="footer text-center">
        &copy; <?= date('Y') ?> <b>NusaBaca</b> • Sirkulasi Engine <i class="fas fa-code-branch ms-1"></i>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    document.getElementById('formEditPeminjaman').addEventListener('submit', function(e) {
        e.preventDefault(); // Tahan pengiriman form default
        
        Swal.fire({
            title: 'Simpan Perubahan?',
            text: "Pastikan data peminjaman buku yang diubah sudah benar!",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#4f46e5',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Update Data!',
            cancelButtonText: 'Batal',
            customClass: { popup: 'rounded-4' }
        }).then((result) => {
            if (result.isConfirmed) {
                this.submit(); // Jalankan pengiriman form asli ke edit_proses.php jika disetujui
            }
        });
    });
</script>

</body>
</html>