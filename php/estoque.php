<?php
session_start();

include "conexao.php";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbName;charset=utf8", $dbUsername, $dbPassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["id"])) {
        $id_item = intval($_POST["id"]);

        // Verifica quais campos foram enviados na requisição POST
        $nova_quantidade_total = isset($_POST["nova_quantidade_total_estoque"]) ? floatval($_POST["nova_quantidade_total_estoque"]) : NULL;
        $nova_unidade = isset($_POST["nova_unidade_total_estoque"]) ? floatval($_POST["nova_unidade_total_estoque"]) : NULL;

        // Buscar quantidade_medida do item
        $stmt = $pdo->prepare("SELECT quantidade_medida FROM item WHERE ID = :id_item");
        $stmt->bindParam(":id_item", $id_item);
        $stmt->execute();
        $item = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($item && $item['quantidade_medida'] > 0) {
            $quantidade_medida = floatval($item['quantidade_medida']);
            try {
                // Buscar a quantidade atual no estoque
                $stmt = $pdo->prepare("SELECT quantidade FROM estoque WHERE id_item = :id_item");
                $stmt->bindParam(":id_item", $id_item);
                $stmt->execute();
                $item = $stmt->fetch(PDO::FETCH_ASSOC);
                $quantidade_atual = floatval($item['quantidade']);


                // Verifica qual campo foi modificado e calcula a nova quantidade
                if ($nova_unidade === $quantidade_atual) {
                    $nova_quantidade = $nova_quantidade_total / $quantidade_medida;
                } else {
                    $nova_quantidade = $nova_unidade;
                }
            } catch (PDOException $e) {
                if ($nova_quantidade_total > 0) {
                    $nova_quantidade = $nova_quantidade_total / $quantidade_medida;
                } elseif ($nova_unidade > 0) {
                    $nova_quantidade = $nova_unidade;
                } else {
                    echo json_encode(["success" => false, "message" => "Nenhum valor foi alterado."]);
                    exit();
                }
            }

            // Verifica se o item já existe no estoque
            $stmt = $pdo->prepare("SELECT quantidade FROM estoque WHERE id_item = :id_item LIMIT 1");
            $stmt->bindParam(":id_item", $id_item);
            $stmt->execute();
            $estoque_existente = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($estoque_existente) {
                // Atualiza a quantidade mantendo a data_hora_entrada original
                $query = "UPDATE estoque SET quantidade = :nova_quantidade WHERE id_item = :id_item";
            } else {
                // Insere um novo registro no estoque
                $query = "INSERT INTO estoque (id_item, data_hora_entrada, quantidade) VALUES (:id_item, NOW(), :nova_quantidade)";
            }

            $stmt = $pdo->prepare($query);
            $stmt->bindParam(":id_item", $id_item);
            $stmt->bindParam(":nova_quantidade", $nova_quantidade);
            $stmt->execute();

            echo json_encode(["success" => true, "message" => "Estoque atualizado com sucesso!"]);
        } else {
            echo json_encode(["success" => false, "message" => "Erro: quantidade_medida inválida ou item não encontrado."]);
        }
        exit();
    }


    // Buscar os itens do banco de dados
    $stmt = $pdo->query("
        SELECT 
            i.ID, i.nome, i.preco_unitario, 
            f.empresa AS fornecedor, 
            c.ID AS categoria_id, c.nome AS categoria, 
            sc.nome AS subcategoria, 
            um.ID AS medida_id, um.nome AS medida, 
            COALESCE(SUM(i.quantidade_medida), 0) AS quantidade_unitaria,
            COALESCE(SUM(e.quantidade), 0) AS unidades_estoque,
            (COALESCE(SUM(e.quantidade), 0) * COALESCE(SUM(i.quantidade_medida), 0)) AS quantidade_total_estoque,
            (COALESCE(SUM(e.quantidade), 0) * COALESCE(SUM(i.preco_unitario), 0)) AS valor_total_estoque
            FROM item i
            LEFT JOIN fornecedor f ON i.id_fornecedor = f.CNPJ
            LEFT JOIN categoria c ON i.id_categoria = c.ID
            LEFT JOIN categoria sc ON i.id_subcategoria = sc.ID
            LEFT JOIN unidades_medida um ON i.id_medida = um.ID
            LEFT JOIN estoque e ON i.id = e.id_item
            GROUP BY i.ID, i.nome, i.preco_unitario, f.empresa, c.ID, c.nome, sc.nome, um.ID, um.nome;
    ");

    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro ao conectar ao banco de dados: " . $e->getMessage());
}

// Consulta para obter o faturamento atual (soma dos valores)
$sql = "SELECT sum(valor) AS faturamento_atual , data_faturamento
        FROM faturamento 
        WHERE data_faturamento = (SELECT MAX(data_faturamento) FROM faturamento)
        group by data_faturamento";
$result = $conn->query($sql);
$row = $result->fetch_assoc();

// Se houver resultado, armazena o faturamento, senão define como 0
$faturamento_atual = $row['faturamento_atual'] ?? 0;
$data_faturamento = $row['data_faturamento'] ?? null;

// Converte a data para o formato brasileiro (dd/mm/yyyy)
if ($data_faturamento) {
    $data_faturamento = date("d/m/Y", strtotime($data_faturamento));
}

?>
