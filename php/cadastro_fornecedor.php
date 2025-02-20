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

// Processamento do formulário
if ($_SERVER["REQUEST_METHOD"] == "POST") 
{
    $empresa = $_POST['empresa'];
    $CNPJ = $_POST['CNPJ'];
    $razao_social = $_POST['razao_social'];
    $nome_vendedor = $_POST['nome_vendedor'];
    $telefone = $_POST['telefone'];
    $email = $_POST['email'];

    if (empty($empresa) || empty($CNPJ) || empty($razao_social) || empty($nome_vendedor) || empty($telefone) || empty($email)) 
    {
        $mensagem = "Todos os campos são obrigatórios!";
    } 
    else 
    {
        try 
        {
            $stmt = $pdo->prepare("INSERT INTO fornecedor (empresa, CNPJ, razao_social, nome_vendedor, telefone, email) 
                VALUES (:empresa, :CNPJ, :razao_social, :nome_vendedor, :telefone, :email)");

            $stmt->bindParam(":empresa", $empresa);
            $stmt->bindParam(":CNPJ", $CNPJ);
            $stmt->bindParam(":razao_social", $razao_social);
            $stmt->bindParam(":nome_vendedor", $nome_vendedor);
            $stmt->bindParam(":telefone", $telefone);
            $stmt->bindParam(":email", $email);
            $stmt->execute();

            
            // Exibir popup antes do redirecionamento
            echo 
                "<script>
                alert('fornecedor cadastrado com sucesso!');
                window.location.href = '" . $_SERVER['PHP_SELF'] . "';
            </script>";
            exit();
        }
        catch (PDOException $e) 
        {
            $mensagem = "Erro ao cadastrar fornecedor: " . $e->getMessage();
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
            <a class="nav-link active" href="cadastro_fornecedor.php">
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
                    <h1 class="h3 mb-4 text-gray-800">Cadastro de Fornecedor</h1>
                    <?php if (isset($mensagem)) { echo "<div class='alert alert-info'>$mensagem</div>"; } ?>
                    <form action="cadastro_fornecedor.php" method="POST">
                        <div class="form-group">
                            <label for="empresa">Empresa</label>
                            <input type="text" class="form-control" id="empresa" name="empresa" required>
                        </div>
                        <div class="form-group">
                            <label for="CNPJ">CNPJ</label>
                            <input type="number" step="0.01" class="form-control" id="CNPJ" name="CNPJ" required>
                        </div>
                        <div class="form-group">
                            <label for="razao_social">Razão social</label>
                            <input type="text" class="form-control" id="razao_social" name="razao_social" required>
                        </div>
                        <div class="form-group">
                            <label for="nome_vendedor">Nome vendedor</label>
                            <input type="nome_vendedor" class="form-control" id="nome_vendedor" name="nome_vendedor" required>
                        </div>
                        <div class="form-group">
                            <label for="telefone">Telefone vendedor</label>
                            <input type="text" class="form-control" id="telefone" name="telefone" required>
                        </div>
                        <div class="form-group">
                            <label for="email">E-mail vendedor</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Cadastrar Fornecedor</button>                   

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