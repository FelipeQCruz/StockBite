<?php
session_start();

// Obtém o nome da sessão antes de destruí-la
$session_name = session_name();

// Limpa todas as variáveis da sessão
$_SESSION = [];

// Remove o cookie da sessão
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie($session_name, '', time() - 42000, 
        $params["path"], $params["domain"], 
        $params["secure"], $params["httponly"]
    );
}

// Destrói a sessão no servidor
session_destroy();

// Força a remoção de todos os cookies
foreach ($_COOKIE as $key => $value) {
    setcookie($key, '', time() - 42000, "/");
}

// Confirma se a sessão foi realmente destruída
if (session_status() !== PHP_SESSION_ACTIVE) {
    header("Location: ../login.html");
    exit();
} else {
    echo "Erro: A sessão ainda está ativa!";
}
?>
