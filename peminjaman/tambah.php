<?php
session_start();

if(!isset($_SESSION['username'])){
    header("location:../login.php");
    exit;
}

include '../koneksi.php';

$notif = '';
if(isset($_POST['submit'])){
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $tgl_pinjam = mysqli_real_escape_string($conn, $_POST['tgl_pinjam']);
    $tgl_kembali = mysqli_real_escape_string($conn, $_POST['tgl_kembali']);
    $status = "Dipinjam";
    
    // Menangkap array dari dropdown buku-buku yang dipilih
    $judul_buku_array = isset($_POST['judul']) ? $_POST['judul'] : [];

    // Filter array agar tidak ada value kosong
    $judul_buku_array = array_filter($judul_buku_array);

    if(!empty($judul_buku_array)){
        
        // 1. GABUNGKAN BANYAK BUKU MENJADI SATU STRING (Dipisahkan oleh koma)
        $kumpulan_judul = implode(", ", $judul_buku_array);
        $judul_safe = mysqli_real_escape_string($conn, $kumpulan_judul);
        
        // 2. INSERT HANYA SATU BARIS KE TABEL PEMINJAMAN
        $insert = mysqli_query($conn, "INSERT INTO peminjaman 
        (nama_peminjam, judul_buku, tanggal_pinjam, tanggal_kembali, status) 
        VALUES ('$nama', '$judul_safe', '$tgl_pinjam', '$tgl_kembali', '$status')");
        
        // 3. SEKALIGUS POTONG STOK MASING-MASING BUKU YANG DIPILIH
        foreach($judul_buku_array as $judul_tunggal){
            $judul_tunggal_safe = mysqli_real_escape_string($conn, $judul_tunggal);
            mysqli_query($conn, "UPDATE buku SET jumlah = jumlah - 1 WHERE judul = '$judul_tunggal_safe'");
        }
        
        if($insert){
            $notif = 'sukses';
        } else {
            $notif = 'gagal';
        }
    } else {
        $notif = 'kosong';
    }
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    body { font-family: 'Inter', sans-serif; background-color: #f8fafc; margin: 0; color: #334155; }
    .sidebar { width: 260px; height: 100vh; position: fixed; background: #ffffff; border-right: 1px solid #e2e8f0; padding: 30px 20px; display: flex; flex-direction: column; z-index: 1000; }
    .logo { font-size: 24px; font-weight: 700; color: #4f46e5; margin-bottom: 40px; display: flex; align-items: center; gap: 10px; }
    .sidebar-nav { flex-grow: 1; }
    .sidebar a { display: flex; align-items: center; gap: 12px; color: #64748b; text-decoration: none; padding: 12px 15px; border-radius: 12px; margin-bottom: 8px; transition: all 0.3s; font-weight: 500; }
    .sidebar a:hover, .sidebar a.active { background: #f1f5f9; color: #4f46e5; }
    .logout-btn { margin-top: auto; color: #ef4444 !important; display: flex; align-items: center; gap: 12px; padding: 12px 15px; text-decoration: none; font-weight: 500; border-radius: 12px; }
    .logout-btn:hover { background: #fef2f2 !important; }
    
    .main { margin-left: 260px; padding: 40px; }
    .form-card { background: white; border-radius: 20px; padding: 40px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); max-width: 750px; }
    .form-label { font-weight: 600; color: #1e293b; margin-bottom: 8px; }
    .form-control, .form-select { background: #f1f5f9; border: 1px solid #e2e8f0; border-radius: 12px; padding: 12px 15px; transition: 0.3s; }
    .form-control:focus, .form-select:focus { background: white; border-color: #4f46e5; box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1); }
    .btn-custom { border-radius: 12px; padding: 12px; font-weight: 600; transition: 0.3s; }
    .btn-back { color: #64748b; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; margin-bottom: 20px; font-weight: 500; }
    .btn-back:hover { color: #4f46e5; }
    
    .book-row { animation: fadeIn 0.3s ease-in-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(-5px); } to { opacity: 1; transform: translateY(0); } }
</style>
</head>
<body>

<div class="sidebar">
    <div class="logo"><i class="fas fa-book-open"></i> NusaBaca</div>
    <nav class="sidebar-nav">
        <a href="../dashboard.php"><i class="fas fa-columns"></i> Dashboard</a>
        <a href="../buku/tampil.php"><i class="fas fa-book"></i> Data Buku</a>
        <a href="../anggota/tampil.php"><i class="fas fa-users"></i> Anggota</a>
        <a href="tampil.php" class="active"><i class="fas fa-exchange-alt"></i> Peminjaman</a>
        <a href="../pengembalian/tampil.php"><i class="fas fa-check-circle"></i> Pengembalian</a>
        <a href="../laporan/index.php"><i class="fas fa-file-alt"></i> Laporan</a>
    </nav>
    <a href="../logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Keluar</a>
</div>

<div class="main">
    <a href="tampil.php" class="btn-back"><i class="fas fa-arrow-left"></i> Kembali ke Daftar</a>
    <h3 class="fw-bold text-dark mb-4">Catat Peminjaman Baru</h3>

    <div class="form-card">
        <form method="POST" id="formPeminjaman">
            
            <div class="mb-4">
                <label class="form-label">Nama Peminjam (Anggota)</label>
                <select name="nama" class="form-select" required>
                    <option value="">-- Pilih Anggota --</option>
                    <?php
                    $anggota = mysqli_query($conn, "SELECT * FROM anggota ORDER BY nama ASC");
                    while($a = mysqli_fetch_array($anggota)){
                        echo "<option value='".htmlspecialchars($a['nama'])."'>".htmlspecialchars($a['nama'])."</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="mb-4">
                <label class="form-label d-flex justify-content-between align-items-center">
                    <span>Buku yang Dipinjam</span>
                    <button type="button" id="btn-tambah-buku" class="btn btn-sm btn-outline-primary style-btn px-3" style="border-radius: 8px;">
                        <i class="fas fa-plus me-1"></i> Tambah Buku
                    </button>
                </label>
                
                <div id="container-buku">
                    <div class="d-flex gap-2 mb-2 book-row">
                        <select name="judul[]" class="form-select" required>
                            <option value="">-- Pilih Buku --</option>
                            <?php
                            $buku = mysqli_query($conn, "SELECT * FROM buku WHERE jumlah > 0 ORDER BY judul ASC");
                            while($b = mysqli_fetch_array($buku)){
                                echo "<option value='".htmlspecialchars($b['judul'])."'>".htmlspecialchars($b['judul'])." (Stok: ".$b['jumlah'].")</option>";
                            }
                            ?>
                        </select>
                        <button type="button" class="btn btn-outline-danger btn-hapus-buku" style="border-radius:12px; width:50px;" disabled>
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                </div>
                <small class="text-muted small d-block mt-1">Sistem Otomatis: Denda dikenakan sebesar <b>Rp 1.000 / hari</b> jika durasi pinjam melewati batas 7 hari.</small>
            </div>

            <div class="row">
                <div class="col-md-6 mb-4">
                    <label class="form-label">Tanggal Pinjam</label>
                    <input type="date" name="tgl_pinjam" class="form-control" value="<?= date('Y-m-d') ?>" required>
                </div>
                <div class="col-md-6 mb-4">
                    <label class="form-label">Estimasi Tanggal Kembali (7 Hari)</label>
                    <input type="date" name="tgl_kembali" class="form-control" value="<?= date('Y-m-d', strtotime('+7 days')) ?>" required>
                </div>
            </div>

            <hr class="my-4" style="opacity: 0.1;">

            <div class="d-grid gap-2">
                <button type="submit" name="submit" class="btn btn-primary btn-custom" style="background:#4f46e5; border-color:#4f46e5;">
                    <i class="fas fa-plus-circle me-2"></i> Proses Peminjaman
                </button>
                <a href="tampil.php" class="btn btn-light btn-custom">Batal</a>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // ENGINE JAVASCRIPT: Tambah / Hapus Dropdown Pilihan Buku secara Dinamis
    const containerBuku = document.getElementById('container-buku');
    const btnTambahBuku = document.getElementById('btn-tambah-buku');

    const templateBarisBuku = () => {
        const selectPertama = containerBuku.querySelector('select').innerHTML;
        const div = document.createElement('div');
        div.className = 'd-flex gap-2 mb-2 book-row';
        div.innerHTML = `
            <select name="judul[]" class="form-select" required>${selectPertama}</select>
            <button type="button" class="btn btn-outline-danger btn-hapus-buku" style="border-radius:12px; width:50px;">
                <i class="fas fa-trash-alt"></i>
            </button>
        `;
        return div;
    };

    btnTambahBuku.addEventListener('click', () => {
        containerBuku.appendChild(templateBarisBuku());
        cekStatusTombolHapus();
    });

    containerBuku.addEventListener('click', (e) => {
        if(e.target.classList.contains('btn-hapus-buku') || e.target.parentElement.classList.contains('btn-hapus-buku')){
            const row = e.target.closest('.book-row');
            row.remove();
            cekStatusTombolHapus();
        }
    });

    function cekStatusTombolHapus() {
        const semuaRow = containerBuku.querySelectorAll('.book-row');
        if(semuaRow.length === 1) {
            semuaRow[0].querySelector('.btn-hapus-buku').setAttribute('disabled', 'true');
        } else {
            semuaRow.forEach(row => row.querySelector('.btn-hapus-buku').removeAttribute('disabled'));
        }
    }

    // NOTIFIKASI SWEETALERT JIKA SUBMIT BERHASIL / GAGAL
    <?php if($notif === 'sukses') { ?>
        Swal.fire({
            title: 'Berhasil Disimpan!',
            text: 'Data peminjaman kelompok buku berhasil dicatat dalam satu baris.',
            icon: 'success',
            confirmButtonColor: '#4f46e5',
            customClass: { popup: 'rounded-4' }
        }).then(() => { window.location.href = 'tampil.php'; });
    <?php } elseif($notif === 'gagal') { ?>
        Swal.fire({
            title: 'Gagal Menyimpan!',
            text: 'Terjadi kesalahan pada struktur query database.',
            icon: 'error',
            confirmButtonColor: '#ef4444',
            customClass: { popup: 'rounded-4' }
        });
    <?php } ?>
</script>
</body>
</html> 