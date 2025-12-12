<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['username'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Não autenticado.']);
    exit();
}

if (!isset($_POST['image_id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'ID da imagem em falta.']);
    exit();
}

$imageId = (int)$_POST['image_id'];
$username = $_SESSION['username'];
$file = "../data/imagens.txt";
$lines = file($file, FILE_IGNORE_NEW_LINES);
$newLines = [];
$found = false;
$isFavorite = false;
$newVotes = 0;

foreach ($lines as $index => $line) {
    // Novo formato: Username|Caminho|Descrição|Votos|Utilizadores_que_favoritaram
    $parts = explode("|", $line);
    
    // Ignorar linhas vazias ou mal formatadas
    if (count($parts) < 4) {
        $newLines[] = $line;
        continue;
    }

    // O ID da imagem é o índice da linha (começando em 0)
    if ($index === $imageId) {
        $found = true;
        $imageUsername = $parts[0];
        $imagePath = $parts[1];
        $imageDescription = $parts[2];
        $votes = (int)$parts[3];
        $favoritedUsers = isset($parts[4]) ? array_filter(explode(",", $parts[4])) : [];

        // Verificar se o utilizador já favoritou
        $userIndex = array_search($username, $favoritedUsers);

        if ($userIndex !== false) {
            // Remover favorito
            unset($favoritedUsers[$userIndex]);
            $newVotes = $votes - 1;
            $isFavorite = false;
        } else {
            // Adicionar favorito
            $favoritedUsers[] = $username;
            $newVotes = $votes + 1;
            $isFavorite = true;
        }
        
        // Reconstruir a linha
        $favoritedUsersString = implode(",", array_unique($favoritedUsers));
        $newLine = $imageUsername . "|" . $imagePath . "|" . $imageDescription . "|" . $newVotes . "|" . $favoritedUsersString;
        $newLines[] = $newLine;
    } else {
        $newLines[] = $line;
    }
}

if ($found) {
    // Reescrever o ficheiro com as linhas atualizadas
    file_put_contents($file, implode("\n", $newLines) . "\n");
    echo json_encode([
        'success' => true, 
        'is_favorite' => $isFavorite, 
        'new_votes' => $newVotes
    ]);
} else {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Imagem não encontrada.']);
}