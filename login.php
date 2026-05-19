<?php
session_start();
include 'koneksi.php';

$error = '';

if(isset($_POST['login'])){

    $username = $_POST['username'];
    $password = $_POST['password'];

    $data = mysqli_query($conn,
    "SELECT * FROM user 
    WHERE username='$username' 
    AND password='$password'");

    $cek = mysqli_num_rows($data);

    if($cek > 0){

        $_SESSION['username'] = $username;

        header("location:dashboard.php");
        exit;

    } else {

        $error = "Username atau password salah!";

    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login Perpustakaan</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

    <style>

    body{
        font-family: 'Poppins', sans-serif;
        background: linear-gradient(135deg, #4e73df, #bfd3ef);
        height: 100vh;
        overflow: hidden;
    }

    .login-box{
        min-height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .card{
        border: none;
        border-radius: 20px;
        overflow: hidden;
    }

    .left-side{
        background: linear-gradient(135deg, #224abe, #4e73df);
        color: white;
        padding: 40px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }

    .left-side h1{
        font-size: 70px;
    }

    .right-side{
        padding: 40px;
        background: white;
    }

    .form-control{
        border-radius: 12px;
        height: 45px;
    }

    .btn-login{
        border-radius: 12px;
        height: 45px;
        font-weight: 500;
    }

    .small-text{
        font-size: 14px;
        color: gray;
    }

    </style>
</head>

<body>

<div class="container login-box">

<div class="card shadow-lg" style="max-width:900px; width:100%;">

    <div class="row g-0">

        <!-- LEFT -->
        <div class="col-md-5 left-side">

            <h1>📚</h1>

            <h3 class="mt-3">
                NusaBaca
            </h3>

            <p class="text-center mt-2">
                Sistem pengelolaan buku, anggota, peminjaman, dan pengembalian buku.
            </p>

        </div>

        <!-- RIGHT -->
        <div class="col-md-7 right-side">

            <h3 class="mb-2">
                Login Admin
            </h3>

            <p class="small-text mb-4">
                Silakan login untuk masuk ke dashboard perpustakaan.
            </p>

            <!-- ERROR -->
            <?php if($error != ''){ ?>

            <div class="alert alert-danger">
                <?= $error ?>
            </div>

            <?php } ?>

            <!-- FORM -->
            <form method="POST">

                <div class="mb-3">

                    <label class="form-label">
                        Username
                    </label>

                    <input type="text"
                           name="username"
                           class="form-control"
                           placeholder="Masukkan username"
                           required>

                </div>

                <div class="mb-4">

                    <label class="form-label">
                        Password
                    </label>

                    <input type="password"
                           name="password"
                           class="form-control"
                           placeholder="Masukkan password"
                           required>

                </div>

                <button type="submit"
                        name="login"
                        class="btn btn-primary w-100 btn-login">

                    Login

                </button>

            </form>

            <div class="text-center mt-4 small-text">
                © <?= date('Y') ?> Sistem Perpustakaan
            </div>

        </div>

    </div>

</div>

</div>

</body>
</html>