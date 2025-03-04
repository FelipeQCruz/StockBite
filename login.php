<?php
session_start(); // Deve ser a primeira linha

// Dados para conexão com o MySQL
$host = "localhost";
$dbUsername = "root";
$dbPassword = "27H09g94B*";
$dbName = "stockbite";

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
        $sql = "SELECT email, senha FROM usuario WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            if (password_verify($senha, $user['senha'])) {
                $_SESSION['email'] = $user['email'];
                header("Location: index.html");
                exit();
            } else {
                $erro = "Senha incorreta!";
            }
        } else {
            $erro = "Usuário não encontrado!";
        }

        $stmt->close();
    } else {
        $erro = "Preencha todos os campos!";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login de usuário</title>
    <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/sb-admin-2.min.css" rel="stylesheet">
</head>

<body id="page-top">
    <div id="wrapper">
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion">
        </ul>
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <div class="container-fluid">
                    <h1 class="h3 mb-4 text-gray-800">Login de usuário</h1>
                    <?php if (isset($erro)) {
                        echo "<div class='alert alert-info'>$erro</div>";
                    } ?>
                    <form action="login.php" method="POST">
                        <div class="form-group">
                            <label for="email">E-mail</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label for="senha">senha</label>
                            <input type="password" class="form-control" id="senha" name="senha" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Fazer login</button>

                    </form>
                </div>
            </div>
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                </div>
            </footer>
        </div>
    </div>
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="js/sb-admin-2.min.js"></script>
</body>

</html>