<?php
include "conexao.php";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbName;charset=utf8", $dbUsername, $dbPassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro ao conectar ao banco de dados: " . $e->getMessage());
}

$conn = new mysqli($host, $dbUsername, $dbPassword, $dbName);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

//Coleta os perfis
$query = "SELECT perfis FROM perfis";
$result = $conn->query($query);

$perfis = [];

if ($result && $result->num_rows > 0) {
    $dados = $result->fetch_all(MYSQLI_ASSOC);
    $perfis = array_combine(array_column($dados, 'perfis'), array_column($dados, 'perfis'));
}

// Fetch restaurantes
$query = "SELECT ID, nome as restaurante FROM restaurante";
$result = $conn->query($query);
$restaurantes = [];
while ($row = $result->fetch_assoc()) { // Corrigido: $result_restaurante em vez de $result
    $restaurantes[$row['ID']] = $row['restaurante'];
}

// Processamento do formulário
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $restaurante = $_POST['restaurante'];
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    $perfil = $_POST['perfil'];
    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

    if (empty($nome) || empty($senha) || empty($email) || empty($restaurante)) {
        $mensagem = "Todos os campos são obrigatórios!";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO usuario (nome, senha, email) 
                VALUES (:nome, :senhaHash, :email)");

            $stmt->bindParam(":nome", $nome);
            $stmt->bindParam(":senhaHash", $senhaHash);
            $stmt->bindParam(":email", $email);

            $stmt->execute();

            $stmt = $pdo->prepare("INSERT INTO usuarios_restaurantes (email, id_restaurante, perfis) 
            VALUES (:email, :id_restaurante, :perfil)");

            $stmt->bindParam(":id_restaurante", $restaurante);
            $stmt->bindParam(":perfil", $perfil);
            $stmt->bindParam(":email", $email);

            $stmt->execute();

            // Exibir popup antes do redirecionamento
            echo
            "<script>
                alert('usuario cadastrado com sucesso!');
                window.location.href = '" . $_SERVER['PHP_SELF'] . "';
            </script>";
            exit();
        } catch (PDOException $e) {
            $mensagem = "Erro ao cadastrar usuario: " . $e->getMessage();
        }
    }
}
