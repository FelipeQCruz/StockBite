<?php
// processa a inserção php
 
// recebe os dados enviados pelo formulario via metodo POST
$email = isset($_POST['email']) ? $_POST['email'] : '';
$senha = isset($_POST['senha']) ? $_POST['senha'] : '';
$nome = isset($_POST['nome']) ? $_POST['nome'] : '';

// Dados para conexão com o MySQL
$host = "localhost";
$dbUsername = "root";
$dbPassword = "27H09g94B*";
$dbName = "stockbite";
 
// cria a conexão
$conn = new mysqli($host, $dbUsername, $dbPassword, $dbName);
 
// verifica a conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}
 
// Aplica hash na senha para maior segurança
$senhaHash = password_hash($senha, PASSWORD_DEFAULT);
 
// prepara o comando SQL usando prepared statements
$sql = "INSERT INTO Usuario (Email, senha, nome) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
 
if ($stmt === false) {
    die("Erro na preparação da query: " . $conn->error);
}
 
// vincula os parâmetros (todos os campos são strings)
$stmt->bind_param("sss", $email, $senhaHash, $nome);
 
// captura erros de e-mail duplicado
try {
    $stmt->execute();
    // Redireciona para index.html após o sucesso
    header("Location: index.html");
    exit(); // Garante que o script pare após o redirecionamento
} catch (mysqli_sql_exception $e) {
    if ($e->getCode() == 1062) {
        header("Location: index.html");
    } else {
        echo "Erro ao inserir usuário: " . $e->getMessage();
    }
}
 
 
// fecha o statement e a conexão
$stmt->close();
$conn->close();
?>

tem menu de contexto

