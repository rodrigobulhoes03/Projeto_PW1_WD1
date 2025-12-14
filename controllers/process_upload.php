<?php

session_start();
if (!isset($_SESSION["username"])) {
    header("Location: ../views/login.html");
    exit();
}

if (!isset($_FILES["foto"]) || !isset($_POST["descricao"])) { 
    die("Nenhum ficheiro ou descrição enviado."); 
}

$descricao = $_POST["descricao"];
$username = $_SESSION["username"];
$imagem = $_FILES["foto"];
$novoNome = time() . "_" . $imagem["name"];
$destino = "../uploads/" . $novoNome;

if (move_uploaded_file($imagem["tmp_name"], $destino)) {
    $file = "../data/imagens.txt";
    $linha = $username . "|" . $destino . "|" . $descricao . "|0|" . "\n";
    file_put_contents($file, $linha, FILE_APPEND);
    header("Location: ../views/home_page.html");
    exit();
}