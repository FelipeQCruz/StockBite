<?php
session_start();

// Conexão com o banco de dados
include "conexao.php";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbName;charset=utf8", $dbUsername, $dbPassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro ao conectar ao banco de dados: " . $e->getMessage());
}

// Obtendo as categorias
$query = "SELECT ID, nome FROM categoria WHERE id_pai IS NULL";
$categorias = $pdo->query($query)->fetchAll(PDO::FETCH_ASSOC);

// Obtendo as subcategorias
$query = "SELECT ID, nome, id_pai FROM categoria WHERE id_pai IS NOT NULL";
$subcategorias = $pdo->query($query)->fetchAll(PDO::FETCH_ASSOC);

// Organizar subcategorias por categoria
$subcategoriasFormatadas = [];
foreach ($subcategorias as $sub) {
    $subcategoriasFormatadas[$sub['id_pai']][] = $sub;
}

// Obtendo as unidades de medida
$query = "SELECT ID, nome FROM unidades_medida";
$unidades_medida = $pdo->query($query)->fetchAll(PDO::FETCH_ASSOC);

// Processamento do formulário
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST['nome'];
    $preco_unitario = $_POST['preco_unitario'];
    $quantidade_medida = $_POST['quantidade_medida'];
    $categoria = $_POST['categoria'];
    $subcategoria = $_POST['subcategoria'];
    $medida = $_POST['medida'];
    $id_fornecedor = $_POST['fornecedor'];
    $email = $_POST['email'];

    if (empty($nome) || empty($preco_unitario) || empty($quantidade_medida) || empty($categoria) || empty($id_fornecedor) || empty($email)) {
        $_SESSION['mensagem'] = "Todos os campos são obrigatórios!";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO item (nome, preco_unitario, quantidade_medida, id_categoria, id_subcategoria, id_fornecedor, email_cadastro, id_medida) 
                VALUES (:nome, :preco_unitario, :quantidade_medida, :id_categoria, :id_subcategoria, :id_fornecedor, :email, :id_medida)");

            $stmt->execute([
                ":nome" => $nome,
                ":preco_unitario" => $preco_unitario,
                ":quantidade_medida" => $quantidade_medida,
                ":id_categoria" => $categoria,
                ":id_subcategoria" => $subcategoria,
                ":id_medida" => $medida,
                ":id_fornecedor" => $id_fornecedor,
                ":email" => $email
            ]);

            $_SESSION['mensagem'] = "Item cadastrado com sucesso!";
        } catch (PDOException $e) {
            $_SESSION['mensagem'] = "Erro ao cadastrar item: " . $e->getMessage();
        }
    }
    header("Location: ../cadastro_item.php");
    exit();
}

// Fetch suppliers from the database
$fornecedores = [];
$query = "SELECT cnpj AS ID, empresa FROM fornecedor";
$result = mysqli_query($conn, $query);

while ($row = mysqli_fetch_assoc($result)) {
    $fornecedores[] = $row;
}