<?php
session_start();

include "conexao.php";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbName;charset=utf8", $dbUsername, $dbPassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Atualizar fornecedor via AJAX
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["id"])) {
        $id = intval($_POST["id"]);
        $updates = [];

        if (isset($_POST["nova_empresa"])) {
            $updates[] = "empresa = '" . htmlspecialchars($_POST["nova_empresa"], ENT_QUOTES) . "'";
        }
        if (isset($_POST["novo_cnpj"])) {
            $updates[] = "CNPJ = " . intval($_POST["novo_cnpj"]);
        }
        if (isset($_POST["nova_razao_social"])) {
            $updates[] = "razao_social = '" . htmlspecialchars($_POST["nova_razao_social"], ENT_QUOTES) . "'";
        }
        if (isset($_POST["novo_vendedor"])) {
            $updates[] = "nome_vendedor = '" . htmlspecialchars($_POST["novo_vendedor"], ENT_QUOTES) . "'";
        }
        if (isset($_POST["novo_telefone"])) {
            $updates[] = "telefone = '" . htmlspecialchars($_POST["novo_telefone"], ENT_QUOTES) . "'";
        }
        if (isset($_POST["novo_email"])) {
            $updates[] = "email = '" . htmlspecialchars($_POST["novo_email"], ENT_QUOTES) . "'";
        }

        if (!empty($updates)) {
            $query = "UPDATE fornecedor SET " . implode(", ", $updates) . " WHERE ID = :id";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(":id", $id);
            $stmt->execute();
        }

        echo json_encode(["success" => true, "message" => "Fornecedor atualizado com sucesso!"]);
        exit();
    }

    // Buscar fornecedores do banco de dados
    $stmt = $pdo->query("SELECT * FROM fornecedor");
    $fornecedores = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro ao conectar ao banco de dados: " . $e->getMessage());
}
?>