<?php
session_start();
header('Content-Type: application/json');

echo json_encode([
    'nome' => $_SESSION['nome'] ?? 'Usuário',
    'email' => $_SESSION['email'] ?? 'Não logado'
]);
?>
