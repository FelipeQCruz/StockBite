<?php
include 'conexao.php';

// Check if the usuarios table is empty
$sql = "SELECT COUNT(*) as total FROM usuario";
$result = $conn->query($sql);
$row = $result->fetch_assoc();

// Get the current page name
$current_page = basename($_SERVER['PHP_SELF']);

if ($row['total'] == 0 && $current_page != 'primeiro_cadastro.php') {
    header("Location: primeiro_cadastro.php");
    exit(); // Ensure script execution stops after redirecting
}

if ($row['total'] > 0 && $current_page == 'primeiro_cadastro.php') {
    header("Location: login.html");
    exit(); // Ensure script execution stops after redirecting
}
?>

<?php
// Obtém o e-mail do usuário logado
$email = $_SESSION['email'];

// Consulta para obter o perfil do usuário com base no e-mail
$sql = "SELECT perfis FROM usuarios_restaurantes WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

// Obtém a página atual
$current_page = basename($_SERVER['PHP_SELF']);

// Se o usuário não for "Administrador" e estiver tentando acessar "cadastro_usuarios.php", redireciona
if ($row && $row['perfis'] != 'Administrador' && $current_page == 'cadastro_usuarios.php') {
    header("Location: index.php");
    exit(); // Garante que o script pare de rodar após o redirecionamento
}
?>