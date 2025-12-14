<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['username'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Utilizador não autenticado.']);
    exit();
}

$diretorio = "../uploads/";
$images = [];

foreach (glob($diretorio . "*") as $ficheiro) {
    $ext = strtolower(pathinfo($ficheiro, PATHINFO_EXTENSION));
    if (in_array($ext, $permitidos)) {
        $images[] = $ficheiro;
    }
}

echo json_encode($images);