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
    body { font-family: 'Inter', sans-serif; background-color: #f4f6f9; margin: 0; color: #334155; }

    /* SIDEBAR */
    .sidebar { width: 260px; height: 100vh; position: fixed; background: #ffffff; border-right: 1px solid #e2e8f0; padding: 30px 20px; display: flex; flex-direction: column; z-index: 1000; }
    .logo { font-size: 24px; font-weight: 700; color: #4f46e5; margin-bottom: 40px; display: flex; align-items: center; gap: 10px; }
    .sidebar-nav { flex-grow: 1; }
    .sidebar a { display: flex; align-items: center; gap: 12px; color: #64748b; text-decoration: none; padding: 12px 15px; border-radius: 12px; margin-bottom: 8px; transition: all 0.3s; font-weight: 500; }
    .sidebar a:hover, .sidebar a.active { background: #f1f5f9; color: #4f46e5; }
    .logout-btn { margin-top: auto; color: #ef4444 !important; border: 1px solid transparent; display: flex; align-items: center; gap: 12px; padding: 12px 15px; text-decoration: none; font-weight: 500; border-radius: 12px; }
    .logout-btn:hover { background: #fef2f2 !important; border-color: #fee2e2; }

    /* MAIN CONTENT */
    .main { margin-left: 260px; padding: 40px; }

    /* CARDS */
    .table-card { background: white; border-radius: 16px; padding: 30px; border: 1px solid #e2e8f0; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05); }

    /* SEARCH BOX */
    .search-group { position: relative; max-width: 380px; }
    .search-box { background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 10px; padding: 10px 12px 10px 42px; font-size: 14px; transition: 0.2s; }
    .search-box:focus { background: white; border-color: #4f46e5; box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.08); }
    .search-icon { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 14px; }

    /* TABLE STYLING */
    .table thead th { background-color: #f8fafc; color: #475569; font-weight: 600; text-transform: uppercase; font-size: 11.5px; letter-spacing: 0.05em; padding: 14px; border-bottom: 1px solid #e2e8f0; }
    .table tbody td { padding: 14px; vertical-align: middle; color: #334155; font-size: 14px; border-bottom: 1px solid #f1f5f9; }
    .table tbody tr:hover { background-color: #f8fafc; }

    /* AVATAR GENERATOR */
    .avatar-circle { width: 35px; height: 35px; background-color: #e0e7ff; color: #4f46e5; font-weight: 600; font-size: 13px; border-radius: 50%; display: flex; align-items: center; justify-content: center; text-transform: uppercase; }

    /* ACTION BUTTONS */
    .btn-action { width: 34px; height: 34px; display: inline-flex; align-items: center; justify-content: center; border-radius: 8px; font-size: 13px; transition: 0.2s; border: 1px solid transparent; }
    .btn-action-edit { background: #fffbeb; color: #d97706; }
    .btn-action-edit:hover { background: #fef3c7; color: #b45309; }
    .btn-action-delete { background: #fef2f2; color: #dc2626; }
    .btn-action-delete:hover { background: #fee2e2; color: #b91c1c; }

    .btn-custom { border-radius: 10px; font-weight: 600; padding: 10px 18px; font-size: 14px; }
    .footer { margin-top: 40px; color: #94a3b8; font-size: 13px; }
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
        <a href="../anggota/tampil.php" class="active"><i class="fas fa-users"></i> Anggota</a>
        <a href="../peminjaman/tampil.php"><i class="fas fa-exchange-alt"></i> Peminjaman</a>
        <a href="../pengembalian/tampil.php"><i class="fas fa-check-circle"></i> Pengembalian</a>
        <a href="../laporan/index.php"><i class="fas fa-file-alt"></i> Laporan</a>
    </nav>

    <a href="../logout.php" class="logout-btn">
        <i class="fas fa-sign-out-alt"></i> Keluar
    </a>
</div>

<div class="main">

    <div class="table-card">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold m-0 text-dark" style="font-size: 20px;">Data Anggota</h4>
                <p class="text-muted small m-0">Total: <span class="badge bg-light text-primary border px-2 py-1 fw-bold"><?= $total_anggota ?></span> anggota terdaftar</p>
            </div>
            <a href="tambah.php" class="btn btn-primary btn-custom shadow-sm" style="background:#4f46e5; border-color:#4f46e5;">
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
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th width="70">NO</th>
                        <th>Nama Lengkap</th>
                        <th>Alamat Rumah</th>
                        <th>No. Telepon</th>
                        <th width="120" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(mysqli_num_rows($data) > 0) { ?>
                        <?php 
                        $no = 1; // 1. Membuat variabel nomor awal di luar perulangan
                        while($d = mysqli_fetch_array($data)){ 
                            // Mengambil huruf pertama nama sebagai avatar placeholder
                            $initial = strtoupper(substr($d['nama'], 0, 1));
                        ?>
                        <tr>
                            <td><span class="badge bg-light text-secondary border fw-medium px-2 py-1"><?= $no++ ?></span></td>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="avatar-circle"><?= $initial ?></div>
                                    <div class="fw-semibold text-dark"><?= htmlspecialchars($d['nama']) ?></div>
                                </div>
                            </td>
                            <td class="text-secondary"><?= htmlspecialchars($d['alamat']) ?></td>
                            <td>
                                <span class="badge bg-white text-dark border fw-normal" style="padding: 6px 10px; border-radius: 8px;">
                                    <i class="fas fa-phone-alt me-1 text-muted" style="font-size: 11px;"></i> <?= htmlspecialchars($d['no_hp']) ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="d-inline-flex gap-2">
                                    <a href="edit.php?NO=<?= $d['NO'] ?>" class="btn-action btn-action-edit" title="Ubah Profil">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                    <a href="hapus.php?NO=<?= $d['NO'] ?>" class="btn-action btn-action-delete btn-hapus-anggota" title="Hapus Anggota">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="fas fa-folder-open d-block mb-2" style="font-size: 36px; color:#cbd5e1;"></i>
                                <span style="font-size: 14px;">Data anggota tidak ditemukan dalam sistem.</span>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="footer text-center">
        &copy; 2026 <b>NusaBaca</b> • Sistem Manajemen Perpustakaan <i class="fas fa-shield-alt ms-1"></i>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    const urlParams = new URLSearchParams(window.location.search);
    
    // Logika Menampilkan Pop-up Berhasil Sukses Operasi CRUD
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
                window.history.replaceState({}, document.title, window.location.pathname);
            });
        }
    }

    // Intersepsi Tombol Hapus: Mengubah Konfirmasi Klasik Menjadi SweetAlert2 Dialog
    document.querySelectorAll('.btn-hapus-anggota').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault(); 
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
                    window.location.href = href; 
                }
            });
        });
    });
</script>
</body>
</html>