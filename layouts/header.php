<!DOCTYPE html>
<html>
<head>
    <title>NusaBaca</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

    <style>

    body{
        font-family: 'Poppins', sans-serif;
        background-color: #ffffff;
    }

    .sidebar{
        width: 250px;
        height: 100vh;
        position: fixed;
        background: linear-gradient(180deg, #224abe, #4e73df);
        padding-top: 20px;
    }

    .sidebar a{
        color: white;
        text-decoration: none;
        display: block;
        padding: 15px 20px;
        transition: 0.3s;
    }

    .sidebar a:hover{
        background: rgba(255,255,255,0.1);
        padding-left: 25px;
    }

    .content{
        margin-left: 250px;
        padding: 30px;
    }

    .card{
        border: none;
        border-radius: 18px;
    }

    .btn{
        border-radius: 10px;
    }

    </style>
</head>

<body>