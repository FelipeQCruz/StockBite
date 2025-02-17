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

// Fetch categories (id_pai IS NULL)
$query = "SELECT ID, nome FROM categoria WHERE id_pai IS NULL";
$result = $conn->query($query);
$categorias = [];
while ($row = $result->fetch_assoc()) {
    $categorias[$row['ID']] = $row['nome'];
}

// Fetch subcategories (id_pai IS NOT NULL)
$query = "SELECT ID, nome, id_pai FROM categoria WHERE id_pai IS NOT NULL";
$result = $conn->query($query);
$subcategorias = [];
while ($row = $result->fetch_assoc()) {
    $subcategorias[$row['id_pai']][] = ['ID' => $row['ID'], 'nome' => $row['nome']];
}

// Fetch unidades de medida
$query = "SELECT ID, nome FROM unidades_medida";
$result_medida = $conn->query($query);
$unidades_medida = [];

while ($row = $result_medida->fetch_assoc()) { // Corrigido: $result_medida em vez de $result
    $unidades_medida[$row['ID']] = $row['nome'];
}


// Processamento do formulário
if ($_SERVER["REQUEST_METHOD"] == "POST") 
{
    $nome = $_POST['nome'];
    $preco_unitario = $_POST['preco_unitario'];
    $quantidade_medida = $_POST['quantidade_medida'];
    $categoria = $_POST['categoria'];
    $id_fornecedor = $_POST['id_fornecedor'];
    $email_cadastro = $_POST['email_cadastro'];

    if (empty($nome) || empty($preco_unitario) || empty($quantidade_medida) || empty($categoria) || empty($id_fornecedor) || empty($email_cadastro)) 
    {
        $mensagem = "Todos os campos são obrigatórios!";
    } 
    else 
    {
        try 
        {
            $stmt = $pdo->prepare("INSERT INTO item (nome, preco_unitario, quantidade_medida, id_categoria, id_subcategoria, id_fornecedor, email_cadastro, id_medida) 
                VALUES (:nome, :preco_unitario, :quantidade_medida, :categoria, :subcategoria, :id_fornecedor, :email_cadastro, :id_medida)");

            $stmt->bindParam(":nome", $nome);
            $stmt->bindParam(":preco_unitario", $preco_unitario);
            $stmt->bindParam(":id_medida", $unidades_medida);
            $stmt->bindParam(":quantidade_medida", $quantidade_medida);
            $stmt->bindParam(":categoria", $categoria);
            $stmt->bindParam(":subcategoria", $subcategoria);
            $stmt->bindParam(":id_fornecedor", $id_fornecedor);
            $stmt->bindParam(":email_cadastro", $email_cadastro);
            $stmt->execute();

            $mensagem = "Item cadastrado com sucesso!";
        } 
        catch (PDOException $e) 
        {
            $mensagem = "Erro ao cadastrar item: " . $e->getMessage();
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
            <a class="nav-link active" href="cadastro_item.php">
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
                    <h1 class="h3 mb-4 text-gray-800">Cadastro de Produto</h1>
                    <?php if (isset($mensagem)) { echo "<div class='alert alert-info'>$mensagem</div>"; } ?>
                    <form action="cadastro_item.php" method="POST">
                        <div class="form-group">
                            <label for="nome">Nome do Produto</label>
                            <input type="text" class="form-control" id="nome" name="nome" required>
                        </div>
                        <div class="form-group">
                            <label for="preco_unitario">Preço Unitário</label>
                            <input type="number" step="0.01" class="form-control" id="preco_unitario" name="preco_unitario" required>
                        </div>

                        <label for="id_medida">Unidade de medida:</label>
                            <select id="id_medida" name="id_medida" class="form-control" required>
                                <option value="">Selecione uma opção</option>
                                <?php foreach ($unidades_medida as $id => $nome) { ?>                                    
                                    <option value="<?= $id ?>"><?= $nome ?></option>                                    
                                <?php } ?>
                            </select>
                            <script>
                                let unidades_medida = <?php echo json_encode($unidades_medida); ?>;
                                document.getElementById('id_medida').addEventListener('change', function() {
                                    let unidadeId = this.value;
                                });
                            </script>
                        <div class="form-group">
                            <label for="quantidade_medida">Unidade de Medida</label>
                            <input type="text" class="form-control" id="quantidade_medida" name="quantidade_medida" required>
                        </div>
                        <div class="form-group">
                        <label for="categoria">Categoria:</label>
                            <select id="categoria" name="categoria" class="form-control" required>
                                <option value="">Selecione uma categoria</option>
                                <?php foreach ($categorias as $id => $nome) { ?>                                    
                                    <option value="<?= $id ?>"><?= $nome ?></option>                                    
                                <?php } ?>
                            </select>

                            <label for="subcategoria">Subcategoria:</label>
                            <select id="subcategoria" name="subcategoria" class="form-control" required>
                                <option value="">Selecione uma subcategoria</option>
                            </select>

                            <script>
                                let subcategorias = <?php echo json_encode($subcategorias); ?>;
                                document.getElementById('categoria').addEventListener('change', function() {
                                    let categoriaId = this.value;
                                    let subcategoriaSelect = document.getElementById('subcategoria');
                                    subcategoriaSelect.innerHTML = '<option value="">Selecione uma subcategoria</option>';
                                    if (subcategorias[categoriaId]) {
                                        subcategorias[categoriaId].forEach(sub => {
                                            let option = document.createElement('option');
                                            option.value = sub.ID;
                                            option.textContent = sub.nome;
                                            subcategoriaSelect.appendChild(option);
                                        });
                                    }
                                });
                            </script>
                        </div>
                        <div class="form-group">
                            <label for="id_fornecedor">ID do Fornecedor</label>
                            <input type="text" class="form-control" id="id_fornecedor" name="id_fornecedor" required>
                        </div>
                        <div class="form-group">
                            <label for="email_cadastro">E-mail de Cadastro</label>
                            <input type="email" class="form-control" id="email_cadastro" name="email_cadastro" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Cadastrar Produto</button>                   

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

