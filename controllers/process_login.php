<?php
session_start();
$username = $_POST["username"];
$password = $_POST["password"];
$file = "../data/users.txt";
$users = file($file, FILE_IGNORE_NEW_LINES);
foreach ($users as $linha) {
    list($nome, $user, $email, $hash) = explode("|", $linha);
    if ($user === $username && password_verify($password, $hash)) {
        $_SESSION["username"] = $username;
        $_SESSION["nome"] = $nome;
        header("Location: ../views/home_page.html");
        exit();
    }
}

// Fall back to login page on failed authentication
header("Location: ../views/login.html?error=1");
exit();