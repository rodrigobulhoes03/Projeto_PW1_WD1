<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['username'])) {
    http_response_code(401);
    echo json_encode(['error' => 'not_authenticated']);
    exit();
}

if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'missing_id']);
    exit();
}

$id = (int)$_GET['id'];
$username = $_SESSION['username'];
$file = "../data/imagens.txt";
if (!file_exists($file)) {
    http_response_code(500);
    echo json_encode(['error' => 'images_file_missing']);
    exit();
}

$lines = file($file, FILE_IGNORE_NEW_LINES);
if ($id < 0 || $id >= count($lines)) {
    http_response_code(404);
    echo json_encode(['error' => 'image_not_found']);
    exit();
}

$line = $lines[$id];
$parts = explode('|', $line);
if (count($parts) < 4) {
    http_response_code(500);
    echo json_encode(['error' => 'invalid_image_format']);
    exit();
}

$imageUsername = $parts[0];
$imagePath = $parts[1];
$imageDescription = $parts[2];
$votes = (int)$parts[3];
$favoritedUsers = isset($parts[4]) ? array_filter(explode(',', $parts[4])) : [];
$isFavorite = in_array($username, $favoritedUsers);

echo json_encode([
    'id' => $id,
    'username' => $imageUsername,
    'path' => $imagePath,
    'description' => $imageDescription,
    'votes' => $votes,
    'is_favorite' => $isFavorite
]);
