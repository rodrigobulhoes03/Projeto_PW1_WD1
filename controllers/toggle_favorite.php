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
    $parts = explode("|", $line);
    
    if (count($parts) < 4) {
        $newLines[] = $line;
        continue;
    }

    if ($index === $imageId) {
        $found = true;
        $imageUsername = $parts[0];
        $imagePath = $parts[1];
        $imageDescription = $parts[2];
        $votes = (int)$parts[3];
        $favoritedUsers = isset($parts[4]) ? array_filter(explode(",", $parts[4])) : [];

        $userIndex = array_search($username, $favoritedUsers);

        if ($userIndex !== false) {
            unset($favoritedUsers[$userIndex]);
            $newVotes = $votes - 1;
            $isFavorite = false;
        } else {
            $favoritedUsers[] = $username;
            $newVotes = $votes + 1;
            $isFavorite = true;
        }
        
        $favoritedUsersString = implode(",", array_unique($favoritedUsers));
        $newLine = $imageUsername . "|" . $imagePath . "|" . $imageDescription . "|" . $newVotes . "|" . $favoritedUsersString;
        $newLines[] = $newLine;
    } else {
        $newLines[] = $line;
    }
}

if ($found) {
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