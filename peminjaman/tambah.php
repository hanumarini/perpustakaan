<?php
session_start();
if(!isset($_SESSION['username'])){
    header("location:../login.php");
    exit;
}
include '../koneksi.php';

// Ambil data master Anggota & Buku
$list_anggota = mysqli_query($conn, "SELECT nama FROM anggota ORDER BY nama ASC");
$list_buku = mysqli_query($conn, "SELECT judul, jumlah, category FROM buku WHERE jumlah > 0 ORDER BY judul ASC");
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
    body { font-family: 'Inter', sans-serif; background-color: #f4f6f9; color: #334155; }
    
    /* SIDEBAR */
    .sidebar { width: 260px; height: 100vh; position: fixed; background: #ffffff; border-right: 1px solid #e2e8f0; padding: 30px 20px; display: flex; flex-direction: column; z-index: 1000; }
    .logo { font-size: 24px; font-weight: 700; color: #4f46e5; margin-bottom: 40px; display: flex; align-items: center; gap: 10px; }
    .sidebar a { display: flex; align-items: center; gap: 12px; color: #64748b; text-decoration: none; padding: 12px 15px; border-radius: 12px; margin-bottom: 8px; font-weight: 500; transition: 0.2s; }
    .sidebar a:hover, .sidebar a.active { background: #f1f5f9; color: #4f46e5; }
    
    /* MAIN CONTENT */
    .main { margin-left: 260px; padding: 40px; }
    .form-card { background: white; border-radius: 16px; padding: 35px; border: 1px solid #e2e8f0; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05); max-width: 900px; }
    .form-label { font-weight: 600; color: #475569; font-size: 14px; margin-bottom: 8px; }

    /* MODERN MULTI-SELECT GRID ENGINE */
    .book-scroll-area { max-height: 280px; overflow-y: auto; padding-right: 5px; margin-bottom: 15px; }
    
    /* Custom Scrollbar */
    .book-scroll-area::-webkit-scrollbar { width: 6px; }
    .book-scroll-area::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 10px; }
    .book-scroll-area::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }

    /* Interactive Card Item */
    .book-selectable-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 14px;
        cursor: pointer;
        position: relative;
        transition: all 0.2s ease-in-out;
        user-select: none;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    
    .book-selectable-card:hover {
        border-color: #cbd5e1;
        transform: translateY(-2px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }

    /* Status Saat Kartu Dicentang Admin */
    .book-checkbox:checked + .book-selectable-card {
        border-color: #4f46e5;
        background-color: #f5f3ff;
        box-shadow: 0 0 0 1px #4f46e5;
    }

    .book-checkbox:checked + .book-selectable-card .check-badge {
        display: flex !important;
    }

    /* Ikon Centang Sudut Atas */
    .check-badge {
        position: absolute;
        top: -8px;
        right: -8px;
        background: #4f46e5;
        color: white;
        width: 22px;
        height: 22px;
        border-radius: 50%;
        display: none;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        border: 2px solid #ffffff;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .form-control, .form-select {
        border-radius: 10px; padding: 11px 14px; border: 1px solid #cbd5e1; font-size: 14px;
    }
    .form-control:focus, .form-select:focus {
        border-color: #4f46e5; box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.08);
    }
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
</div>

<div class="main">
    <div class="mb-4">
        <h4 class="fw-bold text-dark" style="font-size:22px;">Transaksi Peminjaman Baru</h4>
        <p class="text-muted small">Pilih anggota dan klik pada kartu buku untuk memilih lebih dari satu buku sekaligus</p>
    </div>

    <div class="form-card">
        <form action="tambah_proses.php" method="POST" id="formMultiPeminjaman">
            
            <div class="mb-4">
                <label class="form-label"><i class="fas fa-user-circle me-1 text-primary"></i> Nama Peminjam (Anggota)</label>
                <select name="nama_peminjam" class="form-select" required>
                    <option value="">-- Pilih Anggota Perpustakaan --</option>
                    <?php while($agt = mysqli_fetch_array($list_anggota)) { ?>
                        <option value="<?= htmlspecialchars($agt['nama']) ?>"><?= htmlspecialchars($agt['nama']) ?></option>
                    <?php } ?>
                </select>
            </div>

            <div class="mb-4">
                <label class="form-label d-block"><i class="fas fa-book-open me-1 text-primary"></i> Klik Buku yang Dipinjam</label>
                
                <div class="book-scroll-area">
                    <div class="row row-cols-1 row-cols-md-3 g-3">
                        <?php 
                        $i = 0;
                        if(mysqli_num_rows($list_buku) > 0) {
                            while($bk = mysqli_fetch_array($list_buku)) { 
                                $i++;
                                // Menentukan warna badge stok
                                $badge_color = ($bk['jumlah'] <= 2) ? 'bg-danger' : 'bg-success';
                        ?>
                            <div class="col">
                                <input type="checkbox" name="judul_buku[]" value="<?= htmlspecialchars($bk['judul']) ?>" id="buku_<?= $i ?>" class="book-checkbox d-none">
                                
                                <label for="buku_<?= $i ?>" class="book-selectable-card w-100">
                                    <div class="check-badge"><i class="fas fa-check"></i></div>
                                    
                                    <div>
                                        <span class="text-muted text-uppercase fw-bold" style="font-size: 10px; letter-spacing: 0.5px;">
                                            <?= htmlspecialchars($bk['category'] ?: 'Umum') ?>
                                        </span>
                                        <h6 class="fw-bold text-dark my-1 text-truncate-2" style="font-size: 13.5px; line-height: 1.4;">
                                            <?= htmlspecialchars($bk['judul']) ?>
                                        </h6>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top border-light">
                                        <span class="text-muted" style="font-size: 11px;">Tersedia</span>
                                        <span class="badge <?= $badge_color ?> rounded-pill" style="font-size: 10px; padding: 4px 8px;">
                                            <?= $bk['jumlah'] ?> Eks
                                        </span>
                                    </div>
                                </label>
                            </div>
                        <?php 
                            }
                        } else {
                            echo "<div class='col-12'><span class='text-muted small'>Tidak ada koleksi buku yang tersedia atau stok habis.</span></div>";
                        }
                        ?>
                    </div>
                </div>
                <small class="text-muted">Kartu yang berubah warna keunguan menandakan buku tersebut masuk ke daftar pinjam.</small>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Tanggal Pinjam</label>
                    <input type="date" name="tanggal_pinjam" class="form-control" value="<?= date('Y-m-d') ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Tanggal Kembali</label>
                    <input type="date" name="tanggal_kembali" class="form-control" value="<?= date('Y-m-d', strtotime('+7 days')) ?>" required>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4 pt-2 border-top border-light">
                <a href="tampil.php" class="btn btn-light border text-secondary px-4" style="border-radius:10px; font-size: 14px;">Batal</a>
                <button type="submit" name="simpan" class="btn btn-primary px-4 shadow-sm" style="background:#4f46e5; border-color:#4f46e5; border-radius:10px; font-size: 14px;">
                    <i class="fas fa-check-circle me-2"></i> Konfirmasi Peminjaman
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Validasi agar admin tidak mengosongkan pilihan buku saat submit
    document.getElementById('formMultiPeminjaman').addEventListener('submit', function(e) {
        const pilihanBuku = document.querySelectorAll('input[name="judul_buku[]"]:checked');
        if (pilihanBuku.length === 0) {
            e.preventDefault(); 
            Swal.fire({
                title: 'Buku Belum Dipilih!',
                text: 'Harap klik minimal satu atau beberapa kartu buku terlebih dahulu.',
                icon: 'warning',
                confirmButtonColor: '#0f57f1',
                customClass: { popup: 'rounded-4' }
            });
        }
    });
</script>
</body>
</html>