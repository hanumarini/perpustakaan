<?php
session_start();

if(!isset($_SESSION['username'])){
    header("location:../login.php");
    exit;
}

include '../koneksi.php';

// Menangkap parameter pesan sukses dari URL jika ada
$notif_hapus = isset($_GET['pesan']) && $_GET['pesan'] == 'terhapus' ? true : false;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Buku - NusaBaca</title>

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

    /* DATA CARD & TABLE */
    .data-card {
        background: white;
        border-radius: 20px;
        padding: 30px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    .table th {
        background-color: #f1f5f9;
        color: #1e293b;
        font-weight: 600;
        border-bottom: 2px solid #e2e8f0;
    }

    .btn-action {
        border-radius: 8px;
        padding: 6px 12px;
        font-size: 14px;
        font-weight: 500;
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
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-dark m-0">Koleksi Data Buku</h3>
        <a href="tambah.php" class="btn btn-primary" style="background:#4f46e5; border-color:#4f46e5; border-radius:12px; padding:10px 20px; font-weight:600;">
            <i class="fas fa-plus me-2"></i> Tambah Buku Baru
        </a>
    </div>

    <div class="data-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle m-0">
                <thead>
                    <tr>
                        <th width="60">No</th>
                        <th>ISBN</th>
                        <th>Judul Buku</th>
                        <th>Kategori</th>
                        <th>Penulis</th>
                        <th>Penerbit</th> <th width="100">Stok</th>
                        <th width="180" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    $query = mysqli_query($conn, "SELECT * FROM buku ORDER BY NO DESC");
                    if(mysqli_num_rows($query) > 0) {
                        while($d = mysqli_fetch_array($query)){
                            // Deteksi variabel dinamis dari database kamu
                            $stok = isset($d['jumlah']) ? $d['jumlah'] : (isset($d['stok']) ? $d['stok'] : 0);
                            $judul = isset($d['judul']) ? $d['judul'] : (isset($d['JUDUL']) ? $d['JUDUL'] : 'Tidak ada judul');
                            $isbn = isset($d['ISBN']) ? $d['ISBN'] : (isset($d['isbn']) ? $d['isbn'] : '-');
                            $category = isset($d['category']) ? $d['category'] : (isset($d['CATEGORY']) ? $d['CATEGORY'] : (isset($d['kategori']) ? $d['kategori'] : '-'));
                            $penulis = isset($d['penulis']) ? $d['penulis'] : (isset($d['PENULIS']) ? $d['PENULIS'] : '-');
                            
                            // Logika Deteksi Kolom Penerbit (Besar / Kecil)
                            $penerbit = isset($d['penerbit']) ? $d['penerbit'] : (isset($d['PENERBIT']) ? $d['PENERBIT'] : '-');
                    ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><span class="badge bg-light text-secondary border"><?= htmlspecialchars($isbn); ?></span></td>
                            <td class="fw-semibold text-dark"><?= htmlspecialchars($judul); ?></td>
                            <td><?= htmlspecialchars($category); ?></td>
                            <td><?= htmlspecialchars($penulis); ?></td>
                            <td><?= htmlspecialchars($penerbit); ?></td> <td>
                                <span class="badge <?= ($stok <= 2) ? 'bg-danger' : 'bg-success'; ?> rounded-pill">
                                    <?= $stok; ?> Eks
                                </span>
                            </td>
                            <td class="text-center">
                                <a href="edit.php?NO=<?= $d['NO']; ?>" class="btn btn-outline-warning btn-action text-dark me-1">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <a href="hapus.php?NO=<?= $d['NO']; ?>" class="btn btn-outline-danger btn-action btn-hapus">
                                    <i class="fas fa-trash-alt"></i> Hapus
                                </a>
                            </td>
                        </tr>
                    <?php 
                        }
                    } else {
                        echo "<tr><td colspan='8' class='text-center text-muted py-4'>Belum ada data buku di dalam database.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // 1. ENGINE KONFIRMASI SEBELUM HAPUS DATA
    const tombolHapus = document.querySelectorAll('.btn-hapus');
    
    tombolHapus.forEach(tombol => {
        tombol.addEventListener('click', function(e) {
            e.preventDefault(); 
            
            const urlTujuan = this.getAttribute('href'); 
            
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data buku yang dihapus akan hilang permanen dari database!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444', 
                cancelButtonColor: '#64748b',  
                confirmButtonText: 'Ya, Hapus Data!',
                cancelButtonText: 'Batal',
                customClass: { popup: 'rounded-4' }
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = urlTujuan; 
                }
            });
        });
    });

    // 2. ALERT SUKSES SETELAH REDIRECT DARI FILE HAPUS.PHP
    <?php if($notif_hapus) { ?>
        Swal.fire({
            title: 'Berhasil Dihapus!',
            text: 'Data buku tersebut telah sepenuhnya dibersihkan dari sistem.',
            icon: 'success',
            confirmButtonColor: '#4f46e5',
            customClass: { popup: 'rounded-4' }
        }).then(() => {
            window.history.replaceState({}, document.title, window.location.pathname);
        });
    <?php } ?>
</script>
</body>
</html>