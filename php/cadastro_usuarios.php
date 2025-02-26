<?php
session_start();

// Conexão com o banco de dados 
$host = "localhost";
$dbUsername = "root";
$dbPassword = "27H09g94B*";
$dbName = "stockbite";

try 
{
    $pdo = new PDO("mysql:host=$host;dbname=$dbName;charset=utf8", $dbUsername, $dbPassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} 
catch (PDOException $e)
 {
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
if ($_SERVER["REQUEST_METHOD"] == "POST") 
{
    $restaurante = $_POST['restaurante'];
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    $perfil = $_POST['perfil'];
    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

    if (empty($nome) || empty($senha) || empty($email) || empty($restaurante)) 
    {
        $mensagem = "Todos os campos são obrigatórios!";
    } 
    else 
    {
        try 
        {
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
        }
        catch (PDOException $e) 
        {
            $mensagem = "Erro ao cadastrar usuario: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Produto</title>
    <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/sb-admin-2.min.css" rel="stylesheet">
</head>
<body id="page-top">
    <div id="wrapper">
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion">
            <a class="nav-link" href="index.php">
                <i class="fas fa-fw fa-tachometer-alt"></i>
                Dashboard
            </a>
            <a class="nav-link active" href="cadastro_usuarios.php">
                <i class="fas fa-box fa-sm fa-fw mr-2 text-gray-400"></i>
                Cadastro de Produto
            </a>
        </ul>
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">Bem-vindo, Usuário</span>
                                <i class="fas fa-user-circle fa-2x text-gray-400"></i>
                            </a>
                        </li>
                    </ul>
                </nav>
                <div class="container-fluid">
                    <h1 class="h3 mb-4 text-gray-800">Cadastro de usuario</h1>
                    <?php if (isset($mensagem)) { echo "<div class='alert alert-info'>$mensagem</div>"; } ?>
                    <form action="cadastro_usuarios.php" method="POST">
                    <label for="restaurante">Restaurante:</label>
                            <select id="restaurante" name="restaurante" class="form-control" required>
                                <option value="">Selecione um restaurante</option>
                                <?php foreach ($restaurantes as $id => $nome) { ?>                                    
                                    <option value="<?= $id ?>"><?= $nome ?></option>                                    
                                <?php } ?>
                            </select>
                        <div class="form-group">
                            <label for="nome">Nome</label>
                            <input type="nome" class="form-control" id="nome" name="nome" required>
                        </div>
                        <label for="perfil">Perfil:</label>
                            <select id="perfil" name="perfil" class="form-control" required>
                                <option value="">Selecione um perfil</option>
                                <?php foreach ($perfis as $id => $nome) { ?>                                    
                                    <option value="<?= $id ?>"><?= $nome ?></option>                                    
                                <?php } ?>
                            </select>
                        <div class="form-group">
                            <label for="email">E-mail</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>

                        <div class="form-group">
                            <label for="senha">Senha</label>
                            <input type="senha" class="form-control" id="senha" name="senha" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Cadastrar usuario</button>                   

</form>
                </div>
            </div>
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; Your Website 2021</span>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="js/sb-admin-2.min.js"></script>
</body>
</html>