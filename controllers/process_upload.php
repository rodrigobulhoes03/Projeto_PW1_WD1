<?php

session_start();
if (!isset($_SESSION["username"])) {
    header("Location: ../views/login.html");
    exit();
}

if (!isset($_FILES["foto"])) { die("Nenhum ficheiro enviado."); }
$imagem = $_FILES["foto"];
$novoNome = time() . "_" . $imagem["name"];
$destino = "../uploads/" . $novoNome;
if (move_uploaded_file($imagem["tmp_name"], $destino)) {
    $file = "../data/imagens.txt";
    $linha = $_SESSION["username"] . "|" . $destino . "\n";
    file_put_contents($file, $linha, FILE_APPEND);
    header("Location: ../views/home_page.html");
    exit();
}
