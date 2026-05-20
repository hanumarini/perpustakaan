<?php
include '../koneksi.php';

$NO = $_GET['NO'];

mysqli_query($conn, "DELETE FROM buku WHERE NO='$NO'");

header("location:tampil.php");
?>