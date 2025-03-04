<?php
session_start(); // Deve ser a primeira linha

include "php/conexao.php";
// Criando conexão
$conn = new mysqli($host, $dbUsername, $dbPassword, $dbName);

// Verificando erro na conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $senha = trim($_POST['senha']);

    if (!empty($email) && !empty($senha)) {
        $sql = "SELECT email, senha, nome FROM usuario WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            if (password_verify($senha, $user['senha'])) {
                $_SESSION['email'] = $user['email'];
                $_SESSION['nome'] = $user['nome']; // Armazena o nome na sessão
                header("Location: index.html");
                exit();
            } else {
                echo "Senha incorreta!";
            }
        } else {
            echo "Usuário não encontrado!";
        }

        $stmt->close();
    } else {
        echo "Preencha todos os campos!";
    }
}

$conn->close();
?>