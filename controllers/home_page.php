<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['username'])) {
    http_response_code(401);
    echo json_encode(['error' => 'not_authenticated']);
    exit();
}

$diretorio = "../uploads/";
$all = array_values(array_diff(scandir($diretorio), ['.', '..']));
$permitidos = ['jpg','jpeg','png','gif','webp'];
$images = [];
foreach ($all as $ficheiro) {
    $ext = strtolower(pathinfo($ficheiro, PATHINFO_EXTENSION));
    if (in_array($ext, $permitidos)) {
        $images[] = $diretorio . $ficheiro;
    }
}

echo json_encode($images);
