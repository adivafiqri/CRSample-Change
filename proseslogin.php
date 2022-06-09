<?php
include 'functions.php';
//generate CSRF Token
session_start();
$notif = null;
//check validate token
if (!isset($_SESSION["token"]) || !isset($_POST["token"])) {
    exit("token belum di setting!");
}
if ($_POST["token"] == $_SESSION["token"]) {

    if (isset($_POST['username']) && isset($_POST['password'])) {
        //melakukan sanitasi html special character
        $user = htmlspecialchars($_POST['username']);
        $pass = htmlspecialchars($_POST['password']);

        //mengecek salt dan mengambil objeknya
        $pdo_salt = pdo_connect();
        $stmt_salt = $pdo_salt->prepare('SELECT * FROM users WHERE username = ? LIMIT 1');
        $stmt_salt->bindParam(1, $user);
        $stmt_salt->execute();
        $kolom = $stmt_salt->fetch(PDO::FETCH_ASSOC);
        //query untuk mengecek username,password, dan salt pada database
        $pdo = pdo_connect();
        $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ? AND password = ? AND salt = ? LIMIT 1');
        //menggunakan bind param PDO
        $fungsi_hash = hash('sha256', $pass . $kolom['salt']);
        $stmt->bindParam(1, $user);
        $stmt->bindParam(2, $fungsi_hash);
        $stmt->bindParam(3, $kolom['salt']);
        $stmt->execute();
        $notif = $stmt->rowCount();
        //validasi form apabila ksong
        if (empty($user) || empty($pass)) {
            $notif = "Form harus diisi lengkap!";
        } elseif ($stmt->rowCount() > 0) {
            $_SESSION['user'] = $user;
            header("location: index.php");
        } else {
            echo "<Script>document.location='login.php?error';</script>";
        }
    }
} else {
    echo "<Script>document.location='login.php';alert('Token salah!'); </script>";
}
