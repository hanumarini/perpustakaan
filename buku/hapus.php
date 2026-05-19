<?php
include '../koneksi.php';

$id = $_GET['id'];

mysqli_query($conn, "DELETE FROM buku WHERE id='$id'");

header("location:tampil.php");
?>