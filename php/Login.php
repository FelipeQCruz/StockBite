<?php
session_start();

$host = "localhost";
$dbUsername = "root";
$dbPassword = "27H09g94B*";
$dbName = "stockbite";


//concetar o banco de dados usando PDO
try
{
    $pdo = new PDO("mysql:host=$host;dbname=$dbName;charset=utf8", $dbUsername, $dbPassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
        die("Erro ao conectar ao banco de dados:" . $e->getMessage());
    }
//Verificando se os dados foram enviados pelo formulario
if($_SERVER["REQUEST_METHOD"]=="POST")
{
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    //consulta para buscar o usuario pelo email
    $stmt =$pdo->prepare("SELECT id, senha FROM usuario WHERE email = :email ");
    $stmt->bindParam(":email", $email);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if($user && password_verify($senha, $user['senha']))
    {
        $_SESSION['user_id'] = $user['id']; // armazena o id do usuario na sessão
        header("Location: painel.php"); //redireciona para o painel apos o login
        exit();
    }
    else
    {
        echo "Email ou senha incorretos!";
    }
}