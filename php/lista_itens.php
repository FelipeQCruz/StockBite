<?php
session_start();

// Configuração do Banco de Dados
$host = "localhost";
$dbUsername = "root";
$dbPassword = "27H09g94B*";
$dbName = "stockbite";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbName;charset=utf8", $dbUsername, $dbPassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Atualizar preço, medida, quantidade e categoria via AJAX
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["id"])) {
        $id = intval($_POST["id"]);
        $updates = [];

        if (isset($_POST["novo_preco"])) {
            $updates[] = "preco_unitario = " . floatval($_POST["novo_preco"]);
        }
        if (isset($_POST["nova_medida"])) {
            $updates[] = "id_medida = " . intval($_POST["nova_medida"]);
        }
        if (isset($_POST["nova_categoria"])) {
            $nova_categoria = intval($_POST["nova_categoria"]);
            $updates[] = "id_categoria = $nova_categoria";

            // Resetar a subcategoria caso não pertença à nova categoria
            if (isset($_POST["nova_subcategoria"])) {
                $nova_subcategoria = intval($_POST["nova_subcategoria"]);

                // Verifica se a subcategoria pertence à categoria selecionada
                $stmt = $pdo->prepare("SELECT ID FROM categoria WHERE ID = :subcategoria AND id_pai = :categoria");
                $stmt->bindParam(":subcategoria", $nova_subcategoria);
                $stmt->bindParam(":categoria", $nova_categoria);
                $stmt->execute();

                if ($stmt->fetch()) {
                    $updates[] = "id_subcategoria = $nova_subcategoria";
                } else {
                    $updates[] = "id_subcategoria = NULL"; // Resetar caso inválido
                }
            }
        }

        if (isset($_POST["nova_quantidade"])) {
            $updates[] = "quantidade_medida = " . floatval($_POST["nova_quantidade"]);
        }

        if (!empty($updates)) {
            $query = "UPDATE item SET " . implode(", ", $updates) . " WHERE ID = :id";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(":id", $id);
            $stmt->execute();
        }

        echo json_encode(["success" => true, "message" => "Item atualizado com sucesso!"]);
        exit();
    }


    // Buscar unidades de medida
    $stmt = $pdo->query("SELECT * FROM unidades_medida");
    $medidas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Buscar categorias principais
    $stmt = $pdo->query("SELECT * FROM categoria WHERE id_pai IS NULL");
    $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Buscar todas as subcategorias organizadas por categoria
    $stmt = $pdo->query("SELECT * FROM categoria WHERE id_pai IS NOT NULL");
    $subcategorias = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $subcategorias[$row['id_pai']][] = ["ID" => $row["ID"], "nome" => $row["nome"]];
    }

    // Buscar os itens do banco de dados
    $stmt = $pdo->query("
        SELECT 
            i.ID, i.nome, i.preco_unitario, 
            f.empresa AS fornecedor, 
            u.nome AS cadastrado_por,
            c.ID AS categoria_id, c.nome AS categoria, 
            sc.nome AS subcategoria, 
            um.ID AS medida_id, um.nome AS medida, 
            i.quantidade_medida AS quantidade_atual
        FROM item i
        LEFT JOIN fornecedor f ON i.id_fornecedor = f.CNPJ
        LEFT JOIN usuario u ON i.email_cadastro = u.email
        LEFT JOIN categoria c ON i.id_categoria = c.ID
        LEFT JOIN categoria sc ON i.id_subcategoria = sc.ID
        LEFT JOIN unidades_medida um ON i.id_medida = um.ID
    ");

    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro ao conectar ao banco de dados: " . $e->getMessage());
}
