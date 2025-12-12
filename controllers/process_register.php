<?php
$nome = $_POST["nome"];
$username = $_POST["username"];
$email = $_POST["email"];
$password = $_POST["password"];

$file = "../data/users.txt";
$hash = password_hash($password, PASSWORD_DEFAULT);
$linha = "$nome|$username|$email|$hash\n";
file_put_contents($file, $linha, FILE_APPEND);
header("Location: ../views/login.html");
exit();