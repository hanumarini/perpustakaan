<?php
session_start();

if(!isset($_SESSION['username'])){
    header("location:../login.php");
    exit;
}

include '../koneksi.php';

/* SIMPAN DATA */

if(isset($_POST['submit'])){

    mysqli_query($conn,
    "INSERT INTO peminjaman
    (
        nama_peminjam,
        judul_buku,
        tanggal_pinjam,
        tanggal_kembali,
        status
    )

    VALUES
    (
        '$_POST[nama_peminjam]',
        '$_POST[judul_buku]',
        '$_POST[tanggal_pinjam]',
        '$_POST[tanggal_kembali]',
        'Dipinjam'
    )");

    header("location:tampil.php");
}
?>

<!DOCTYPE html>
<html>
<head>

    <title>Tambah Peminjaman</title>

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

    height:55px;
}

.form-control:focus,
.form-select:focus{

    background: rgba(255,255,255,0.18);

    color:white;

    box-shadow:none;
}

.form-control::placeholder{
    color:#ddd;
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

/* INFO */

.alert-custom{

    background: rgba(255,255,255,0.12);

    border:1px solid rgba(255,255,255,0.1);

    color:white;

    border-radius:20px;
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

    <a href="tampil.php">
        🔄 Peminjaman
    </a>

    <a href="../pengembalian/tampil.php">
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
                ➕ Tambah Peminjaman
            </h3>

            <a href="tampil.php"
               class="btn btn-light btn-custom">

               ⬅ Kembali

            </a>

        </div>

        <!-- INFO -->

        <div class="alert alert-custom mb-4">

            📌 Pilih anggota dan buku yang ingin dipinjam.
            Status otomatis menjadi
            <b>Dipinjam</b>.

        </div>

        <!-- FORM -->

        <form method="POST">

            <!-- NAMA PEMINJAM -->

            <div class="mb-4">

                <label class="mb-2 fw-semibold">
                    Nama Peminjam
                </label>

                <select name="nama_peminjam"
                        class="form-select"
                        required>

                    <option value="">
                        -- Pilih Anggota --
                    </option>

                    <?php
                    $anggota = mysqli_query($conn,
                    "SELECT * FROM anggota");

                    while($a = mysqli_fetch_array($anggota)){
                    ?>

                    <option value="<?= $a['nama'] ?>">

                        <?= $a['nama'] ?>

                    </option>

                    <?php } ?>

                </select>

            </div>

            <!-- JUDUL BUKU -->

            <div class="mb-4">

                <label class="mb-2 fw-semibold">
                    Judul Buku
                </label>

                <select name="judul_buku"
                        class="form-select"
                        required>

                    <option value="">
                        -- Pilih Buku --
                    </option>

                    <?php
                    $buku = mysqli_query($conn,
                    "SELECT * FROM buku");

                    while($b = mysqli_fetch_array($buku)){
                    ?>

                    <option value="<?= $b['judul'] ?>">

                        <?= $b['judul'] ?>

                    </option>

                    <?php } ?>

                </select>

            </div>

            <!-- TANGGAL PINJAM -->

            <div class="mb-4">

                <label class="mb-2 fw-semibold">
                    Tanggal Pinjam
                </label>

                <input type="date"
                       name="tanggal_pinjam"
                       class="form-control"
                       required>

            </div>

            <!-- TANGGAL KEMBALI -->

            <div class="mb-4">

                <label class="mb-2 fw-semibold">
                    Tanggal Kembali
                </label>

                <input type="date"
                       name="tanggal_kembali"
                       class="form-control"
                       required>

            </div>

            <!-- BUTTON -->

            <div class="d-flex gap-3">

                <button type="submit"
                        name="submit"
                        class="btn btn-success btn-custom">

                    ✅ Simpan Peminjaman

                </button>

                <button type="reset"
                        class="btn btn-secondary btn-custom">

                    🔄 Reset

                </button>

            </div>

        </form>

    </div>

    <!-- FOOTER -->

    <div class="footer">

        © <?= date('Y') ?> NusaBaca Premium ✨

    </div>

</div>

</body>
</html>

