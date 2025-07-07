<?php
session_start();

if (isset($_SESSION['login_message'])) {
    unset($_SESSION['login_message']);
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selamat Datang di Society Coffee</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/css_style.css">
</head>
<body>
    <div class="container landing-page">
        <h2>Selamat Datang di Society Coffee    </h2>
        <p>Pilih bagaimana Anda ingin masuk:</p>
        <div class="button-group">
            <a href="interface/login.php" class="big-button admin-button">
                <span class="icon">&#128100;</span> Masuk Sebagai Admin
            </a>
            <a href="Interface/home.php" class="big-button customer-button">
                <span class="icon">&#128101;</span> Masuk Sebagai Staff
            </a>
        </div>
    </div>
</body>
</html>