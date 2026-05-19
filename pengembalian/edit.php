<?php
session_start();

if(!isset($_SESSION['username'])){
    header("location:../login.php");
    exit;
}

include '../koneksi.php';

/* AMBIL ID */

$id = $_GET['id'];

/* AMBIL DATA */

$data = mysqli_query($conn,
"SELECT * FROM pengembalian
WHERE id='$id'");

$d = mysqli_fetch_array($data);

/* UPDATE DATA */

if(isset($_POST['update'])){

    mysqli_query($conn,
    "UPDATE pengembalian SET

    nama_peminjam='$_POST[nama_peminjam]',
    judul_buku='$_POST[judul_buku]',
    tanggal_pinjam='$_POST[tanggal_pinjam]',
    tanggal_kembali='$_POST[tanggal_kembali]',
    denda='$_POST[denda]',
    status='$_POST[status]'

    WHERE id='$id'");

    header("location:tampil.php");
}
?>

<!DOCTYPE html>
<html>
<head>

    <title>Edit Pengembalian</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>

*{
    font-family:'Poppins', sans-serif;
}

body{

    margin:0;

    background:
    linear-gradient(
        135deg,
        #0f172a,
        #1e1b4b,
        #312e81,
        #6d28d9
    );

    min-height:100vh;
}

/* SIDEBAR */

.sidebar{

    width:270px;
    height:100vh;

    position:fixed;

    background: rgba(255,255,255,0.08);

    backdrop-filter: blur(25px);

    border-right:1px solid rgba(255,255,255,0.1);

    padding:30px 20px;
}

.logo{

    color:white;

    font-size:30px;

    font-weight:700;

    margin-bottom:45px;
}

.sidebar a{

    display:block;

    text-decoration:none;

    color:white;

    padding:15px 18px;

    border-radius:18px;

    margin-bottom:14px;

    transition:0.3s;
}

.sidebar a:hover{

    background: rgba(255,255,255,0.15);

    transform:translateX(5px);
}

/* MAIN */

.main{

    margin-left:270px;

    padding:35px;
}

/* CARD */

.card-custom{

    background: rgba(255,255,255,0.12);

    backdrop-filter: blur(20px);

    border-radius:30px;

    padding:35px;

    color:white;

    box-shadow:
    0 8px 32px rgba(0,0,0,0.2);
}

/* INPUT */

.form-control,
.form-select{

    background: rgba(255,255,255,0.12);

    border:none;

    color:white;

    border-radius:15px;

    padding:14px;
}

.form-control:focus,
.form-select:focus{

    background: rgba(255,255,255,0.18);

    color:white;

    box-shadow:none;
}

option{
    color:black;
}

/* BUTTON */

.btn-custom{

    border-radius:15px;

    padding:12px 18px;

    font-weight:600;
}

/* FOOTER */

.footer{

    text-align:center;

    color:rgba(255,255,255,0.7);

    margin-top:40px;
}

</style>

</head>

<body>

<!-- SIDEBAR -->

<div class="sidebar">

    <div class="logo">
        📚 NusaBaca
    </div>

    <a href="../dashboard.php">
        🏠 Dashboard
    </a>

    <a href="../buku/tampil.php">
        📖 Data Buku
    </a>

    <a href="../anggota/tampil.php">
        👤 Anggota
    </a>

    <a href="../peminjaman/tampil.php">
        🔄 Peminjaman
    </a>

    <a href="tampil.php">
        ✅ Pengembalian
    </a>

    <a href="../logout.php">
        🚪 Logout
    </a>

</div>

<!-- MAIN -->

<div class="main">

    <div class="card-custom">

        <div class="d-flex justify-content-between align-items-center mb-4">

            <h3 class="fw-bold">
                ✏️ Edit Pengembalian
            </h3>

            <a href="tampil.php"
               class="btn btn-light btn-custom">

               Kembali

            </a>

        </div>

        <form method="POST">

            <!-- NAMA -->

            <div class="mb-4">

                <label class="mb-2">
                    Nama Peminjam
                </label>

                <input type="text"
                       name="nama_peminjam"
                       class="form-control"
                       value="<?= $d['nama_peminjam'] ?>"
                       required>

            </div>

            <!-- JUDUL -->

            <div class="mb-4">

                <label class="mb-2">
                    Judul Buku
                </label>

                <input type="text"
                       name="judul_buku"
                       class="form-control"
                       value="<?= $d['judul_buku'] ?>"
                       required>

            </div>

            <!-- TANGGAL PINJAM -->

            <div class="mb-4">

                <label class="mb-2">
                    Tanggal Pinjam
                </label>

                <input type="date"
                       name="tanggal_pinjam"
                       class="form-control"
                       value="<?= $d['tanggal_pinjam'] ?>"
                       required>

            </div>

            <!-- TANGGAL KEMBALI -->

            <div class="mb-4">

                <label class="mb-2">
                    Tanggal Kembali
                </label>

                <input type="date"
                       name="tanggal_kembali"
                       class="form-control"
                       value="<?= $d['tanggal_kembali'] ?>"
                       required>

            </div>

            <!-- DENDA -->

            <div class="mb-4">

                <label class="mb-2">
                    Denda
                </label>

                <input type="number"
                       name="denda"
                       class="form-control"
                       value="<?= $d['denda'] ?>"
                       required>

            </div>

            <!-- STATUS -->

            <div class="mb-4">

                <label class="mb-2">
                    Status
                </label>

                <select name="status"
                        class="form-select">

                    <option value="Dikembalikan"
                    <?php if($d['status']=="Dikembalikan"){ echo "selected"; } ?>>

                        Dikembalikan

                    </option>

                    <option value="Terlambat"
                    <?php if($d['status']=="Terlambat"){ echo "selected"; } ?>>

                        Terlambat

                    </option>

                </select>

            </div>

            <!-- BUTTON -->

            <button type="submit"
                    name="update"
                    class="btn btn-success btn-custom">

                Update Data

            </button>

        </form>

    </div>

    <!-- FOOTER -->

    <div class="footer">

        © <?= date('Y') ?> NusaBaca Premium ✨

    </div>

</div>

</body>
</html>