<?php
session_start();

if(!isset($_SESSION['username'])){
    header("location:../login.php");
    exit;
}

include '../koneksi.php';

/* AMBIL ID */

$id = $_GET['id'];

/* HAPUS DATA */

mysqli_query($conn,
"DELETE FROM peminjaman
WHERE id='$id'");

/* KEMBALI KE HALAMAN TAMPIL */

header("location:tampil.php");
exit;
?>