<?php
session_start();

if(!isset($_SESSION['username'])){
    header("location:../login.php");
    exit;
}

include '../koneksi.php';

$cari = isset($_GET['cari']) ? $_GET['cari'] : '';
$cari_safe = mysqli_real_escape_string($conn, $cari);

$data = mysqli_query($conn, "SELECT * FROM peminjaman
WHERE (status='Dikembalikan') 
AND (nama_peminjam LIKE '%$cari_safe%' OR judul_buku LIKE '%$cari_safe%') 
ORDER BY tanggal_kembali DESC, id DESC");

$total_kembali = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM peminjaman WHERE status='Dikembalikan'"));

$sum_denda = mysqli_query($conn, "SELECT SUM(denda) AS total_kas FROM peminjaman WHERE status='Dikembalikan'");
$row_denda = mysqli_fetch_assoc($sum_denda);
$total_kas_denda = $row_denda['total_kas'] ?? 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pengembalian & Denda - NusaBaca</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-bootstrap-4/bootstrap-4.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    body { font-family: 'Inter', sans-serif; background-color: #f8fafc; margin: 0; color: #334155; }
    .sidebar { width: 260px; height: 100vh; position: fixed; background: #ffffff; border-right: 1px solid #e2e8f0; padding: 30px 20px; display: flex; flex-direction: column; z-index: 1000; }
    .logo { font-size: 24px; font-weight: 700; color: #4f46e5; margin-bottom: 40px; display: flex; align-items: center; gap: 10px; }
    .sidebar-nav { flex-grow: 1; }
    .sidebar a { display: flex; align-items: center; gap: 12px; color: #64748b; text-decoration: none; padding: 12px 15px; border-radius: 12px; margin-bottom: 8px; transition: all 0.3s; font-weight: 500; }
    .sidebar a:hover, .sidebar a.active { background: #f1f5f9; color: #4f46e5; }
    .logout-btn { margin-top: auto; color: #ef4444 !important; border: 1px solid transparent; display: flex; align-items: center; gap: 12px; padding: 12px 15px; text-decoration: none; font-weight: 500; border-radius: 12px; }
    .logout-btn:hover { background: #fef2f2 !important; border-color: #fee2e2; }
    .main { margin-left: 260px; padding: 40px; }
    .table-card { background: white; border-radius: 20px; padding: 30px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }
    .stat-mini { background: white; border: 1px solid #e2e8f0; border-radius: 15px; padding: 15px 20px; display: flex; align-items: center; gap: 15px; }
    .stat-icon-circle { width: 45px; height: 45px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 18px; }
    .search-group { position: relative; max-width: 400px; }
    .search-box { background: #f1f5f9; border: 1px solid #e2e8f0; border-radius: 12px; padding: 12px 12px 12px 45px; transition: 0.3s; }
    .search-icon { position: absolute; left: 18px; top: 50%; transform: translateY(-50%); color: #94a3b8; }
    .table thead th { background-color: #f8fafc; color: #64748b; font-weight: 600; font-size: 11px; text-transform: uppercase; padding: 15px; border-bottom: 2px solid #f1f5f9; }
    .table tbody td { padding: 15px; vertical-align: middle; font-size: 14px; border-bottom: 1px solid #f1f5f9; }
    .badge-status { padding: 6px 12px; border-radius: 8px; font-size: 12px; font-weight: 600; }
    .footer { margin-top: 40px; color: #94a3b8; font-size: 14px; }
</style>
</head>
<body>

<div class="sidebar">
    <div class="logo"><i class="fas fa-book-open"></i> NusaBaca</div>
    <nav class="sidebar-nav">
        <a href="../dashboard.php"><i class="fas fa-columns"></i> Dashboard</a>
        <a href="../buku/tampil.php"><i class="fas fa-book"></i> Data Buku</a>
        <a href="../anggota/tampil.php"><i class="fas fa-users"></i> Anggota</a>
        <a href="../peminjaman/tampil.php"><i class="fas fa-exchange-alt"></i> Peminjaman</a>
        <a href="../pengembalian/tampil.php" class="active"><i class="fas fa-check-circle"></i> Pengembalian</a>
        <a href="../laporan/index.php"><i class="fas fa-file-alt"></i> Laporan</a>
    </nav>
    <a href="../logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Keluar</a>
</div>

<div class="main">

    <div class="row g-3 mb-4 align-items-center">
        <div class="col-md-5">
            <h3 class="fw-bold text-dark m-0">Riwayat Pengembalian Buku</h3>
            <small class="text-muted">Arsip data sirkulasi selesai beserta rekam denda keterlambatan</small>
        </div>
        <div class="col-md-7">
            <div class="d-flex justify-content-end gap-3">
                <div class="stat-mini">
                    <div class="stat-icon-circle bg-success text-white"><i class="fas fa-check-double"></i></div>
                    <div><small class="text-muted d-block">Total Selesai</small><strong><?= $total_kembali ?> Buku</strong></div>
                </div>
                <div class="stat-mini">
                    <div class="stat-icon-circle bg-danger text-white"><i class="fas fa-hand-holding-dollar"></i></div>
                    <div><small class="text-muted d-block">Total Kas Denda</small><strong class="text-danger">Rp <?= number_format($total_kas_denda, 0, ',', '.') ?></strong></div>
                </div>
            </div>
        </div>
    </div>

    <div class="table-card">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <form method="GET" class="flex-grow-1">
                <div class="search-group">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" name="cari" class="form-control search-box" placeholder="Cari data riwayat kembali..." value="<?= htmlspecialchars($cari) ?>">
                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th width="50">No</th>
                        <th>Nama Peminjam</th>
                        <th>Judul Buku</th>
                        <th>Tgl Pinjam</th>
                        <th>Tgl Kembali</th>
                        <th>Total Denda Final</th>
                        <th>Status</th>
                        <th class="text-center" width="100">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    if(mysqli_num_rows($data) > 0) {
                        while($d = mysqli_fetch_array($data)){ 
                    ?>
                    <tr>
                        <td><span class="text-muted"><?= $no++ ?></span></td>
                        <td class="fw-semibold text-dark"><?= htmlspecialchars($d['nama_peminjam']) ?></td>
                        <td><i class="fas fa-book me-2 text-muted"></i><?= htmlspecialchars($d['judul_buku']) ?></td>
                        <td><?= date('d/m/Y', strtotime($d['tanggal_pinjam'])) ?></td>
                        <td><span class="text-success fw-medium"><i class="fas fa-calendar-check me-1"></i> <?= date('d/m/Y', strtotime($d['tanggal_kembali'])) ?></span></td>
                        <td>
                            <?php if(isset($d['denda']) && $d['denda'] > 0) { ?>
                                <span class="badge bg-danger text-white px-2 py-1 rounded-3 fw-bold">
                                    <i class="fas fa-coins me-1"></i> Rp <?= number_format($d['denda'], 0, ',', '.') ?>
                                </span>
                            <?php } else { ?>
                                <span class="badge bg-light text-success border border-success-subtle px-2 py-1 rounded-3 fw-medium">
                                    <i class="fas fa-smile me-1"></i> Bebas Denda
                                </span>
                            <?php } ?>
                        </td>
                        <td><span class="badge bg-success badge-status"><i class="fas fa-check-circle me-1"></i> Arsip Selesai</span></td>
                        <td class="text-center">
                            <a href="../peminjaman/hapus.php?id=<?= $d['id'] ?>" class="btn btn-sm btn-outline-danger btn-hapus" style="border-radius: 8px; padding: 4px 10px;"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php } 
                    } else { ?>
                        <tr><td colspan="8" class="text-center py-4 text-muted"><i class="fas fa-folder-open me-1"></i> Belum ada rekaman riwayat pengembalian buku.</td></tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="footer text-center">&copy; <?= date('Y') ?> <b>NusaBaca</b> • Pusat Log Pengembalian & Denda</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    const urlParams = new URLSearchParams(window.location.search);
    
    // 1. Notifikasi Pop-up Pengembalian Sukses Beserta Informasi Denda
    if(urlParams.get('status_alert') === 'sukses') {
        const nominalDenda = parseInt(urlParams.get('nominal')) || 0;
        
        let titleText = 'Pengembalian Sukses!';
        let messageText = 'Buku telah berhasil dikembalikan ke dalam arsip.';
        let iconType = 'success';
        
        // Jika ada denda, buat peringatan pop-up bernuansa orange/warning agar pustakawan menagih denda
        if(nominalDenda > 0) {
            titleText = 'Buku Kembali (Ada Denda!)';
            messageText = 'Harap tagih denda keterlambatan sebesar: Rp ' + nominalDenda.toLocaleString('id-ID');
            iconType = 'warning';
        }

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

    // 2. Intersepsi Tombol Hapus Riwayat Beranimasi
    document.querySelectorAll('.btn-hapus').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const href = this.getAttribute('href');
            
            Swal.fire({
                title: 'Hapus Arsip?',
                text: "Data riwayat ini akan dihapus secara permanen dari database!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Hapus!',
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