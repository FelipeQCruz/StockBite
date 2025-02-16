<?php

session_start();

// verifica se o usuario esta logado
if(!isset($_SESSION['usuario_id']))
{
    //se nao estiver logado, redireciona para a pagina de login
    header('Location: login.php');
    exit();
}

//caso o usuario esteja logado, ele vera o conteudo abaixo
?>

<!DOCTYPE html>
<head>
    <meta charset = "UFT-8">
    <meta name = "viewport" content = "width = device-width, initial-scale = 1.0">
    <title>painel do Usuário</title>
    <!-- Adicione aqui o seu css se necessario -->
</head>
<body>

    <h1>Bem-vindo ao painel, <?php echo $_SESSION['usuario_nome']; ?>!</h1>
    <p>Este é o seu painel protegido.</p>

    <a href = "logout.php">sair</a>
</body>
</html>