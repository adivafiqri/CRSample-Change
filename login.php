<?php include 'functions.php'; ?>
<!DOCTYPE html>
<html lang="en">

<?php
//generate CSRF Token
session_start();
$_SESSION["token"] = bin2hex(random_bytes(32));
print_r($_SESSION["token"]);

$notif = null;
//check validate token
if (!isset($_SESSION["token"])) {
    exit("token not set!");
}
if (isset($_POST['username']) && isset($_POST['password']) && isset($_POST['token'])) {

    //melakukan sanitasi html special character
    $user = htmlspecialchars($_POST['username']);
    $pass = htmlspecialchars($_POST['password']);
    //melakukan validasi inputan

    $salt = "XDrBmrW9g2fb";
    $pdo = pdo_connect();
    $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ? AND password = ? LIMIT 1');
    //menggunakan bind param PDO
    $fungsi_hash = hash('sha256', $pass . $salt);
    $stmt->bindParam(1, $user);
    $stmt->bindParam(2, $fungsi_hash);
    $stmt->execute();
    $notif = $stmt->rowCount();
    //validasi form apabila ksong
    if (empty($user) || empty($pass)) {
        $notif = "Form harus diisi lengkap!";
    } elseif ($stmt->rowCount() > 0) {
        $_SESSION['user'] = $user;
        header("location: index.php");
    } else {
        $notif = "Username atau Password salah !";
    }
}
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <?= style_script() ?>
    <title>Login</title>
</head>

<body class="text-center">

    <form class="form-signin" method="POST">
        <h1 class="h3 mb-3 font-weight-normal">Please sign in</h1>
        <label for="inputUsername" class="sr-only">Username</label>
        <input type="username" id="inputUsername" name="username" class="form-control" placeholder="Username" required autofocus>
        <br>
        <label for="inputPassword" class="sr-only">Password</label>
        <input type="password" id="inputPassword" name="password" class="form-control" placeholder="Password" required>
        <input type="hidden" name="token" value="<?= $_SESSION["token"] ?>" />
        <div class="checkbox mb-3">
            <label>
                <?= $notif ?>
            </label>
        </div>
        <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
        <p class="mt-5 mb-3 text-muted">hk &copy; 2021</p>
    </form>
</body>

</html>