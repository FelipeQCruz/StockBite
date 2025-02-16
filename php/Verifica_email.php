<?php
// processa a inserção php

// recebe os dados enviados pelo formulario via metodo POST
$email = $_POST['email'];
$senha = $_POST['senha'];
$nome = $_POST['nome'];
$tipo = $_POST['tipo'];
$empresa = $_POST['empresa'];

// Dados para conexão com o MySQL
$host = "localhosto";
$dbUsername = "root";
$dbPassword = "27H09g94B*";
$dbName = "stockbite";

// cria a conexão
$conn = new mysqli($host, $dbUsername, $dbPassword, $dbName);

// verifica a conexão
if ($conn->connect_error)
{
    die("Falha na conexão: " . $conn->connect_error);
}

// verifica se o email ja esta cadastrado
$sqlVerifica = "SELECT id FROM usuario WHERE email = ?";
$stmtVerifica = $conn->prepare($sqlVerifica);
$stmtVerifica->bind_param("s", $email);
$stmtverifica->execute();
$resultVerifica = $stmtVerifica->get_result();

if($resultVerifica->num_rowa > 0)
{
    echo "Erro: este email já está cadastrado!";
    $stmtVerifica->close();
    $conn->close();
    exit();
}

// fecha a verificação
$stmtVerifica->close(); 

// Aplica hash na senha para maio segurança
$senhaHash = password_hash($senha, PASSWORD_DEFAULT);

//prepara o comando SQL usando prepared statements
$sql = "INSERT INTO usuario (email, senha, nome, tipo, empresa) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);

if($stmt === false)
{
    die("Erro na preparação da query: " . $conn->error);
}

// vincula os parametros (todos os campos sao strings)
$stmt=>bind_param("sssss", $email, $senhaHash, $nome, $tipo, $empresa );

//executa a query
if($stmt->execute())
{
    echo "Usuário inserido com sucesso!";
}
else 
{
    echo "Erro ao inserir usuário: " . $stmt->error;
}

// fecha o statmente e a conexão
$stmt->close();
$conn->close();

?>