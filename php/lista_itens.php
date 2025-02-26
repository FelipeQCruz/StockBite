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

    // Atualizar preço, medida e quantidade via AJAX
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["id"])) {
        $id = intval($_POST["id"]);
        $updates = [];

        if (isset($_POST["novo_preco"])) {
            $updates[] = "preco_unitario = " . floatval($_POST["novo_preco"]);
        }
        if (isset($_POST["nova_medida"])) {
            $updates[] = "id_medida = " . intval($_POST["nova_medida"]);
        }
        if (isset($_POST["nova_quantidade"])) {
            $stmt = $pdo->prepare("UPDATE estoque SET quantidade = :quantidade WHERE id_item = :id");
            $stmt->bindParam(":quantidade", $_POST["nova_quantidade"]);
            $stmt->bindParam(":id", $id);
            $stmt->execute();
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

    // Buscar os itens do banco de dados
    $stmt = $pdo->query("
        SELECT 
            i.ID, i.nome, i.preco_unitario, 
            f.empresa AS fornecedor, 
            u.nome AS cadastrado_por,
            c.nome AS categoria, 
            sc.nome AS subcategoria, 
            um.ID AS medida_id, um.nome AS medida, 
            COALESCE(SUM(e.quantidade), 0) AS quantidade_atual
        FROM item i
        LEFT JOIN fornecedor f ON i.id_fornecedor = f.ID
        LEFT JOIN usuario u ON i.email_cadastro = u.email
        LEFT JOIN categoria c ON i.id_categoria = c.ID
        LEFT JOIN categoria sc ON i.id_subcategoria = sc.ID
        LEFT JOIN unidades_medida um ON i.id_medida = um.ID
        LEFT JOIN estoque e ON i.ID = e.id_item
        GROUP BY i.ID, i.nome, i.preco_unitario, f.empresa, u.nome, c.nome, sc.nome, um.ID, um.nome
    ");

    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro ao conectar ao banco de dados: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Itens</title>
    <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/sb-admin-2.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body id="page-top">

    <div class="container mt-5">
        <h2 class="mb-4">Lista de Itens</h2>

        <div id="alert-success" class="alert alert-success d-none" role="alert"></div>

        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Preço Unitário</th>
                    <th>Fornecedor</th>
                    <th>Cadastrado Por</th>
                    <th>Categoria</th>
                    <th>Subcategoria</th>
                    <th>Medida</th>
                    <th>Quantidade Atual</th>
                    <th>Ação</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                    <tr id="row-<?= $item['ID'] ?>">
                        <td><?= htmlspecialchars($item['ID']) ?></td>
                        <td><?= htmlspecialchars($item['nome']) ?></td>
                        <td>
                            <span id="preco-<?= $item['ID'] ?>">R$ <?= number_format($item['preco_unitario'], 2, ',', '.') ?></span>
                            <input type="number" id="input-preco-<?= $item['ID'] ?>" class="form-control d-none" value="<?= $item['preco_unitario'] ?>" step="0.01">
                        </td>
                        <td><?= htmlspecialchars($item['fornecedor']) ?></td>
                        <td><?= htmlspecialchars($item['cadastrado_por']) ?></td>
                        <td><?= htmlspecialchars($item['categoria']) ?></td>
                        <td><?= htmlspecialchars($item['subcategoria']) ?></td>
                        <td>
                            <span id="medida-<?= $item['ID'] ?>"><?= htmlspecialchars($item['medida']) ?></span>
                            <select id="input-medida-<?= $item['ID'] ?>" class="form-control d-none">
                                <?php foreach ($medidas as $medida): ?>
                                    <option value="<?= $medida['ID'] ?>" <?= $item['medida_id'] == $medida['ID'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($medida['nome']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td>
                            <span id="quantidade-<?= $item['ID'] ?>"><?= htmlspecialchars($item['quantidade_atual']) ?></span>
                            <input type="number" id="input-quantidade-<?= $item['ID'] ?>" class="form-control d-none" value="<?= $item['quantidade_atual'] ?>">
                        </td>
                        <td>
                            <button class="btn btn-primary btn-sm" id="editar-<?= $item['ID'] ?>" onclick="editarItem(<?= $item['ID'] ?>)">Editar</button>
                            <button class="btn btn-success btn-sm d-none" id="salvar-<?= $item['ID'] ?>" onclick="salvarAlteracoes(<?= $item['ID'] ?>)">Salvar</button>
                            <button class="btn btn-danger btn-sm d-none" id="cancelar-<?= $item['ID'] ?>" onclick="cancelarEdicao(<?= $item['ID'] ?>)">Cancelar</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script>
        function editarItem(id) {
            $("#preco-" + id).addClass("d-none");
            $("#input-preco-" + id).removeClass("d-none");

            $("#medida-" + id).addClass("d-none");
            $("#input-medida-" + id).removeClass("d-none");

            $("#quantidade-" + id).addClass("d-none");
            $("#input-quantidade-" + id).removeClass("d-none");

            $("#editar-" + id).addClass("d-none");
            $("#salvar-" + id).removeClass("d-none");
            $("#cancelar-" + id).removeClass("d-none");
        }

        function cancelarEdicao(id) {
            location.reload();
        }

        function salvarAlteracoes(id) {
            let data = {
                id: id,
                novo_preco: $("#input-preco-" + id).val(),
                nova_medida: $("#input-medida-" + id).val(),
                nova_quantidade: $("#input-quantidade-" + id).val()
            };

            $.post("lista_itens.php", data, function(response) {
                alert(response.message);
                location.reload();
            }, "json");
        }
    </script>

</body>
</html>
