<?php
session_start();

if(!isset($_SESSION['username'])){
   header("location:tampil.php?pesan=berhasil_hapus");
exit;
}

include '../koneksi.php';

// 1. Ambil data 'NO' dari URL petunjuk tombol hapus
if (isset($_GET['NO'])) {
    
    // Simpan ke variabel $NO (samakan namanya agar tidak bingung)
    $NO = mysqli_real_escape_string($conn, $_GET['NO']);

    // 2. Eksekusi query menggunakan variabel $NO yang sudah ada isinya
    $delete = mysqli_query($conn, "DELETE FROM anggota WHERE NO='$NO'");

    if(!$delete){
        die("Gagal menghapus data dari database: " . mysqli_error($conn));
    }
}

header("location:tampil.php");
exit;