<?php
session_start();

if(!isset($_SESSION['username'])){
    header("location:../login.php");
    exit;
}

include '../koneksi.php';

$cari = isset($_GET['cari']) ? $_GET['cari'] : '';
$cari_safe = mysqli_real_escape_string($conn, $cari);

// MODIFIKASI QUERY: Hanya mengambil data yang statusnya 'Dipinjam'
$data = mysqli_query($conn, "SELECT * FROM peminjaman
WHERE (status='Dipinjam') 
AND (nama_peminjam LIKE '%$cari_safe%' OR judul_buku LIKE '%$cari_safe%') 
ORDER BY id DESC");

// Statistik ringkas untuk monitoring
$total_peminjaman = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM peminjaman"));
$total_dipinjam = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM peminjaman WHERE status='Dipinjam'"));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sirkulasi Aktif - NusaBaca</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

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
    .btn-custom { border-radius: 10px; font-weight: 600; }
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
        <a href="../peminjaman/tampil.php" class="active"><i class="fas fa-exchange-alt"></i> Peminjaman</a>
        <a href="../pengembalian/tampil.php"><i class="fas fa-check-circle"></i> Pengembalian</a>
        <a href="../laporan/index.php"><i class="fas fa-file-alt"></i> Laporan</a>
    </nav>
    <a href="../logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Keluar</a>
</div>

<div class="main">

    <div class="row g-3 mb-4 align-items-center">
        <div class="col-md-5">
            <h3 class="fw-bold text-dark m-0">Sirkulasi Peminjaman Aktif</h3>
            <small class="text-muted">Daftar buku yang saat ini sedang berada di tangan anggota</small>
        </div>
        <div class="col-md-7">
            <div class="d-flex justify-content-end gap-3">
                <div class="stat-mini">
                    <div class="stat-icon-circle bg-warning text-white"><i class="fas fa-hourglass-half"></i></div>
                    <div><small class="text-muted d-block">Sedang Dipinjam</small><strong><?= $total_dipinjam ?> Buku</strong></div>
                </div>
                <div class="stat-mini">
                    <div class="stat-icon-circle bg-secondary text-white"><i class="fas fa-history"></i></div>
                    <div><small class="text-muted d-block">Log Historis Global</small><strong><?= $total_peminjaman ?> Transaksi</strong></div>
                </div>
            </div>
        </div>
    </div>

    <div class="table-card">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <form method="GET" class="flex-grow-1 me-3">
                <div class="search-group">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" name="cari" class="form-control search-box" placeholder="Cari peminjam aktif atau judul buku..." value="<?= htmlspecialchars($cari) ?>">
                </div>
            </form>
            <a href="tambah.php" class="btn btn-primary btn-custom shadow-sm"><i class="fas fa-plus me-2"></i> Pinjamkan Buku</a>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th width="50">No</th>
                        <th>Nama Peminjam</th>
                        <th>Judul Buku</th>
                        <th>Tanggal Pinjam</th>
                        <th>Batas Waktu</th>
                        <th>Status Waktu</th>
                        <th class="text-center" width="220">Opsi Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    if(mysqli_num_rows($data) > 0) {
                        while($d = mysqli_fetch_array($data)){ 
                            // Hitung status keterlambatan secara real-time untuk warning pustakawan
                            $tgl_pinjam = new DateTime($d['tanggal_pinjam']);
                            $hari_ini = new DateTime(date('Y-m-d'));
                            $durasi = $hari_ini->diff($tgl_pinjam)->days;
                            
                            // Batas peminjaman standar 8 hari
                            $batas_pinjam = 8;
                    ?>
                    <tr>
                        <td><span class="text-muted"><?= $no++ ?></span></td>
                        <td class="fw-semibold text-primary"><?= htmlspecialchars($d['nama_peminjam']) ?></td>
                        <td><i class="fas fa-book me-2 text-muted"></i><?= htmlspecialchars($d['judul_buku']) ?></td>
                        <td><i class="far fa-calendar me-1 text-muted"></i> <?= date('d/m/Y', strtotime($d['tanggal_pinjam'])) ?></td>
                        
                        <td>
                            <i class="far fa-calendar-check me-1 text-muted"></i>
                            <?= date('d/m/Y', strtotime($d['tanggal_pinjam'] . ' + 8 days')) ?>
                        </td>

                        <td>
                            <?php if($durasi > $batas_pinjam) { 
                                $lewat = $durasi - $batas_pinjam;
                            ?>
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">
                                    <i class="fas fa-exclamation-circle me-1"></i> Terlambat <?= $lewat ?> Hari
                                </span>
                            <?php } else { ?>
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                    <i class="fas fa-check me-1"></i> Aman (<?= $batas_pinjam - $durasi ?> hari sisa)
                                </span>
                            <?php } ?>
                        </td>

                        <td class="text-center">
                            <div class="d-flex justify-content-center align-items-center gap-1">
                                <a href="proses_kembali.php?id=<?= $d['id'] ?>" class="btn btn-sm btn-success fw-bold px-3 py-1" style="border-radius: 8px; font-size:12px;" onclick="return confirm('Konfirmasi pengembalian buku ini? Sistem akan menghitung denda jika ada.')">
                                    <i class="fas fa-arrow-down me-1"></i> Kembalikan
                                </a>
                                
                                <a href="edit.php?id=<?= $d['id'] ?>" class="btn btn-sm btn-outline-warning" style="border-radius: 8px; padding: 4px 8px;" title="Ubah Data">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="hapus.php?id=<?= $d['id'] ?>" class="btn btn-sm btn-outline-danger" style="border-radius: 8px; padding: 4px 8px;" onclick="return confirm('Hapus transaksi ini?')" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php } 
                    } else { ?>
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <i class="fas fa-info-circle me-1"></i> Tidak ada buku yang sedang dipinjam saat ini.
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="footer text-center">
        &copy; <?= date('Y') ?> <b>NusaBaca</b> • Sistem Peminjaman Praktis
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>