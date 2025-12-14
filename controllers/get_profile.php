<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['username'])) {
    http_response_code(401);
    echo json_encode(['error' => 'not_authenticated']);
    exit();
}

$username = $_SESSION['username'];
$file = "../data/users.txt";
if (!file_exists($file)) {
    http_response_code(500);
    echo json_encode(['error' => 'users_file_missing']);
    exit();
}

$users = file($file, FILE_IGNORE_NEW_LINES);
foreach ($users as $line) {
    $parts = explode('|', $line);
    if (count($parts) < 4) { continue; }
    list($nome, $user, $email, $hash) = $parts;
    if ($user === $username) {
        echo json_encode(['nome' => $nome, 'username' => $user, 'email' => $email]);
        exit();
    }
}

http_response_code(404);
echo json_encode(['error' => 'user_not_found']);
