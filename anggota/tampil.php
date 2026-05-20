<?php
session_start();

if(!isset($_SESSION['username'])){
    header("location:../login.php");
    exit;
}

include '../koneksi.php';

$cari = isset($_GET['cari']) ? $_GET['cari'] : '';

// Menggunakan real_escape_string untuk keamanan filter pencarian
$cari_safe = mysqli_real_escape_string($conn, $cari);

$query = "SELECT * FROM anggota 
          WHERE nama LIKE '%$cari_safe%' 
          OR alamat LIKE '%$cari_safe%' 
          OR no_hp LIKE '%$cari_safe%'
          ORDER BY NO DESC";

$data = mysqli_query($conn, $query);
$total_anggota = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM anggota"));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Anggota - NusaBaca</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-bootstrap-4/bootstrap-4.css">
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
        font-size: 12px;
        letter-spacing: 0.05em;
        padding: 15px;
        border-bottom: 2px solid #f1f5f9;
    }

    .table tbody td {
        padding: 15px;
        vertical-align: middle;
        color: #1e293b;
        border-bottom: 1px solid #f1f5f9;
    }

    .btn-custom {
        border-radius: 10px;
        font-weight: 600;
        padding: 10px 20px;
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
        <a href="../buku/tampil.php">
            <i class="fas fa-book"></i> Data Buku
        </a>
        <a href="../anggota/tampil.php" class="active">
            <i class="fas fa-users"></i> Anggota
        </a>
        <a href="../peminjaman/tampil.php">
            <i class="fas fa-exchange-alt"></i> Peminjaman
        </a>
        <a href="../pengembalian/tampil.php">
            <i class="fas fa-check-circle"></i> Pengembalian
        </a>
        <a href="../laporan/index.php">
            <i class="fas fa-file-alt"></i> Laporan
        </a>
    </nav>

    <a href="../logout.php" class="logout-btn">
        <i class="fas fa-sign-out-alt"></i> Keluar
    </a>
</div>

<div class="main">

    <div class="table-card">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold m-0 text-dark">Data Anggota</h4>
                <p class="text-muted small m-0">Total: <?= $total_anggota ?> anggota terdaftar</p>
            </div>
            <a href="tambah.php" class="btn btn-primary btn-custom shadow-sm">
                <i class="fas fa-user-plus me-2"></i> Tambah Anggota
            </a>
        </div>

        <form method="GET" class="mb-4">
            <div class="search-group">
                <i class="fas fa-search search-icon"></i>
                <input type="text" 
                       name="cari" 
                       class="form-control search-box" 
                       placeholder="Cari berdasarkan nama, alamat..." 
                       value="<?= htmlspecialchars($cari) ?>">
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th width="80">NO</th>
                        <th>Nama Lengkap</th>
                        <th>Alamat</th>
                        <th>No. Telepon</th>
                        <th width="180" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(mysqli_num_rows($data) > 0) { ?>
                        <?php while($d = mysqli_fetch_array($data)){ ?>
                        <tr>
                            <td class="fw-bold text-primary">#<?= $d['NO'] ?></td>
                            <td>
                                <div class="fw-semibold"><?= htmlspecialchars($d['nama']) ?></div>
                            </td>
                            <td><?= htmlspecialchars($d['alamat']) ?></td>
                            <td>
                                <span class="badge bg-light text-dark border">
                                    <i class="fas fa-phone-alt me-1 text-muted"></i> <?= htmlspecialchars($d['no_hp']) ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <a href="edit.php?NO=<?= $d['NO'] ?>" class="btn btn-outline-warning btn-sm shadow-sm me-2" style="border-radius: 8px;">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <a href="hapus.php?NO=<?= $d['NO'] ?>" class="btn btn-outline-danger btn-sm shadow-sm btn-hapus-anggota" style="border-radius: 8px;">
                                        <i class="fas fa-trash"></i> Hapus
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="fas fa-folder-open d-block mb-2" style="font-size: 40px;"></i>
                                Data tidak ditemukan.
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="footer text-center">
        &copy; <?= date('Y') ?> <b>NusaBaca</b> • Sistem Manajemen Perpustakaan <i class="fas fa-shield-alt ms-1"></i>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    const urlParams = new URLSearchParams(window.location.search);
    
    // 1. Logika Menampilkan Pop-up Berhasil Sukses Operasi CRUD
    if(urlParams.has('pesan')) {
        const pesan = urlParams.get('pesan');
        let titleText = '';
        let messageText = '';
        let iconType = 'success';

        if(pesan === 'berhasil_tambah') {
            titleText = 'Anggota Terdaftar!';
            messageText = 'Data profil anggota baru berhasil dimasukkan ke sistem.';
        } else if(pesan === 'berhasil_edit') {
            titleText = 'Perubahan Disimpan!';
            messageText = 'Pembaruan data identitas anggota telah berhasil diperbarui.';
        } else if(pesan === 'berhasil_hapus') {
            titleText = 'Anggota Dihapus!';
            messageText = 'Data keanggotaan telah dibersihkan secara permanen.';
            iconType = 'info';
        }

        if(titleText !== '') {
            Swal.fire({
                title: titleText,
                text: messageText,
                icon: iconType,
                confirmButtonColor: '#4f46e5',
                background: '#ffffff',
                customClass: { popup: 'rounded-4' }
            }).then(() => {
                // Menghilangkan parameter '?pesan=...' di URL agar pop-up tidak muncul lagi saat di-refresh
                window.history.replaceState({}, document.title, window.location.pathname);
            });
        }
    }

    // 2. Intersepsi Tombol Hapus: Mengubah Konfirmasi Klasik Menjadi SweetAlert2 Dialog
    document.querySelectorAll('.btn-hapus-anggota').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault(); // Menahan link asli agar tidak langsung pindah ke hapus.php
            const href = this.getAttribute('href');
            
            Swal.fire({
                title: 'Hapus Data Anggota?',
                text: "Tindakan ini tidak bisa dibatalkan dan dapat memengaruhi data sirkulasi buku terkait!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Hapus Saja!',
                cancelButtonText: 'Batal',
                background: '#ffffff',
                customClass: { popup: 'rounded-4' }
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = href; // Eksekusi pindah halaman jika menekan tombol konfirmasi
                }
            });
        });
    });
</script>
</body>
</html>