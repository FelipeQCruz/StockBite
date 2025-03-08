<?php
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: ../login.html");
    exit();
}

header('Content-Type: application/json');

// Se a sessão não existir, retorna "not_logged_in"
if (!isset($_SESSION['email'])) {
    echo json_encode(["status" => "not_logged_in"]);
    exit();
}

// Se estiver logado, retorna os dados do usuário
echo json_encode([
    "status" => "logged_in",
    "nome" => $_SESSION['nome'],
    "email" => $_SESSION['email']
]);
