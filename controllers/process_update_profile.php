<?php
session_start();

if (!isset($_SESSION["username"])) {
    header("Location: ../views/login.html");
    exit();
}

$oldUsername = $_SESSION["username"];
$oldName = isset($_SESSION["nome"]) ? $_SESSION["nome"] : "";

// Validate required fields
$nome = isset($_POST["nome"]) ? trim($_POST["nome"]) : "";
$username = isset($_POST["username"]) ? trim($_POST["username"]) : "";
$email = isset($_POST["email"]) ? trim($_POST["email"]) : "";
$password = isset($_POST["password"]) ? $_POST["password"] : "";

if ($nome === "" || $username === "" || $email === "") {
    http_response_code(400);
    echo "Campos obrigatórios em falta.";
    exit();
}

$usersFile = "../data/users.txt";
if (!file_exists($usersFile)) {
    http_response_code(500);
    echo "Ficheiro de utilizadores não encontrado.";
    exit();
}

$users = file($usersFile, FILE_IGNORE_NEW_LINES);

// Check if new username already exists for a different user
foreach ($users as $line) {
    $parts = explode("|", $line);
    if (count($parts) < 4) { continue; }
    $existingUser = $parts[1];
    if ($existingUser === $username && $existingUser !== $oldUsername) {
        http_response_code(409);
        echo "Este username já está em uso.";
        exit();
    }
}

$newUsers = [];
$updated = false;

foreach ($users as $line) {
    $parts = explode("|", $line);
    if (count($parts) < 4) {
        $newUsers[] = $line;
        continue;
    }
    list($nomeAtual, $userAtual, $emailAtual, $hashAtual) = $parts;

    if ($userAtual === $oldUsername) {
        $newHash = ($password !== "") ? password_hash($password, PASSWORD_DEFAULT) : $hashAtual;
        $newLine = $nome . "|" . $username . "|" . $email . "|" . $newHash;
        $newUsers[] = $newLine;
        $updated = true;
    } else {
        $newUsers[] = $line;
    }
}

if (!$updated) {
    http_response_code(404);
    echo "Utilizador não encontrado.";
    exit();
}

file_put_contents($usersFile, implode("\n", $newUsers) . "\n");

// Update images file: change uploader username and favorites occurrences
$imagesFile = "../data/imagens.txt";
if (file_exists($imagesFile)) {
    $imageLines = file($imagesFile, FILE_IGNORE_NEW_LINES);
    $newImageLines = [];

    foreach ($imageLines as $line) {
        if (trim($line) === "") { $newImageLines[] = $line; continue; }
        $parts = explode("|", $line);
        // uploader|path|description|votes|favoritedUsers
        $uploader = isset($parts[0]) ? $parts[0] : "";
        $path = isset($parts[1]) ? $parts[1] : "";
        $desc = isset($parts[2]) ? $parts[2] : "";
        $votes = isset($parts[3]) ? $parts[3] : "0";
        $favUsersStr = isset($parts[4]) ? $parts[4] : "";
        $favUsers = $favUsersStr !== "" ? array_filter(explode(",", $favUsersStr)) : [];

        if ($uploader === $oldUsername) {
            $uploader = $username;
        }
        // Replace old username in favorites list
        $favUsers = array_map(function($u) use ($oldUsername, $username) {
            return ($u === $oldUsername) ? $username : $u;
        }, $favUsers);

        // Remove potential duplicates after rename
        $favUsers = array_values(array_unique($favUsers));
        $favUsersStr = implode(",", $favUsers);

        $newImageLines[] = $uploader . "|" . $path . "|" . $desc . "|" . $votes . "|" . $favUsersStr;
    }

    file_put_contents($imagesFile, implode("\n", $newImageLines) . "\n");
}

// Update session
$_SESSION["username"] = $username;
$_SESSION["nome"] = $nome;

header("Location: ../views/perfil.html");
exit();
