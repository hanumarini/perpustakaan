<?php
session_start();

if(!isset($_SESSION['username'])){
    header("location:../login.php");
    exit;
}

include '../koneksi.php';

if(isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    
    // 1. Ambil data tanggal_pinjam terlebih dahulu berdasarkan ID
    $query_tampil = mysqli_query($conn, "SELECT tanggal_pinjam FROM peminjaman WHERE id = '$id'");
    $data = mysqli_fetch_array($query_tampil);
    
    if($data) {
        $tanggal_pinjam = $data['tanggal_pinjam'];
        $tanggal_kembali = date('Y-m-m'); // Tanggal pengembalian adalah hari ini
        
        // 2. Hitung Selisih Hari
        $pinjam = new DateTime($tanggal_pinjam);
        $kembali = new DateTime($tanggal_kembali);
        $selisih = $kembali->diff($pinjam)->days;
        
        // 3. Logika Denda (> 8 Hari)
        $batas_aman = 8;
        $tarif_denda = 1000; // Rp 1.000 per hari keterlambatan
        $denda = 0;
        
        if($selisih > $batas_aman) {
            $terlambat = $selisih - $batas_aman;
            $denda = $terlambat * $tarif_denda;
        }
        
        // 4. Update data peminjaman (Ubah status, tanggal kembali real, dan masukkan nilai denda)
        // Catatan: Pastikan di tabel `peminjaman` kamu sudah ada kolom `denda` (Type: INT).
        // Jika belum ada, jalankan perintah ini di PHPMyAdmin: ALTER TABLE peminjaman ADD denda INT DEFAULT 0;
        $sql_update = "UPDATE peminjaman SET 
                       tanggal_kembali = '$tanggal_kembali', 
                       status = 'Dikembalikan', 
                       denda = '$denda' 
                       WHERE id = '$id'";
                       
        if(mysqli_query($conn, $sql_update)) {
           // Modifikasi baris redirect sukses di dalam file proses_kembali.php
      header("location:../pengembalian/tampil.php?status_alert=sukses&nominal=" . $denda);
   exit;
        } else {
            echo "Gagal memperbarui data pengembalian: " . mysqli_error($conn);
        }
    } else {
        echo "Data peminjaman tidak ditemukan.";
    }
} else {
    header("location:tampil.php");
    exit;
}
?>