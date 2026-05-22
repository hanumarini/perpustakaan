<?php
session_start();

if(!isset($_SESSION['username'])){
    header("location:../login.php");
    exit;
}

include '../koneksi.php';

if(!isset($_GET['id']) || empty($_GET['id'])){
    header("location:tampil.php");
    exit;
}

$id = mysqli_real_escape_string($conn, $_GET['id']);

// Deteksi nama kolom primary key di tabel anggota (id atau NO)
$cek_pk = mysqli_query($conn, "SELECT * FROM anggota WHERE id='$id'");
if(!$cek_pk){
    $kolom_pk = 'NO';
} else {
    $kolom_pk = 'id';
}

// Eksekusi Hapus
$query_hapus = mysqli_query($conn, "DELETE FROM anggota WHERE $kolom_pk='$id'");

if($query_hapus){
    // Melempar tanda pesan sukses terhapus ke tampil.php anggota
    header("location:tampil.php?pesan=terhapus");
    exit;
} else {
    echo "Gagal menghapus data anggota: " . mysqli_error($conn);
    exit;
}
?>