<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['username'])) {
    http_response_code(401);
    echo json_encode(['error' => 'not_authenticated']);
    exit();
}

$username = $_SESSION['username'];
$file = "../data/imagens.txt";
$lines = file($file, FILE_IGNORE_NEW_LINES);
$images = [];

foreach ($lines as $index => $line) {
    // Novo formato: Username|Caminho|Descrição|Votos|Utilizadores_que_favoritaram
    $parts = explode("|", $line);
    
    if (count($parts) < 4) {
        continue; // Ignorar linhas mal formatadas
    }

    $imageUsername = $parts[0];
    $imagePath = $parts[1];
    $imageDescription = $parts[2];
    $votes = (int)$parts[3];
    $favoritedUsers = isset($parts[4]) ? array_filter(explode(",", $parts[4])) : [];
    
    // Verificar se o utilizador atual favoritou esta imagem
    $isFavorite = in_array($username, $favoritedUsers);

    $images[] = [
        'id' => $index, // O ID da imagem é o índice da linha
        'username' => $imageUsername,
        'path' => $imagePath,
        'description' => $imageDescription,
        'votes' => $votes,
        'is_favorite' => $isFavorite
    ];
}

// Requisito: Galeria organizada por maior número de votos (favoritos)
usort($images, function($a, $b) {
    return $b['votes'] <=> $a['votes'];
});

echo json_encode($images);