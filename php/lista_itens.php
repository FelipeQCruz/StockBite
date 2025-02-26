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

    // **Corrigido: Verifica se a requisição é AJAX corretamente**
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["id"]) && isset($_POST["novo_preco"])) {
        $id = intval($_POST["id"]);
        $novoPreco = floatval($_POST["novo_preco"]);

        // **Corrigido: Verifica se os valores não estão vazios**
        if ($id > 0 && $novoPreco > 0) {
            $stmt = $pdo->prepare("UPDATE item SET preco_unitario = :preco WHERE ID = :id");
            $stmt->bindParam(":preco", $novoPreco);
            $stmt->bindParam(":id", $id);

            if ($stmt->execute()) {
                echo json_encode(["success" => true, "novo_preco" => number_format($novoPreco, 2, ',', '.'), "message" => "Item alterado com sucesso!"]);
            } else {
                echo json_encode(["error" => "Erro ao atualizar no banco!"]);
            }
        } else {
            echo json_encode(["error" => "Dados inválidos enviados!"]);
        }
        exit();
    }
} catch (PDOException $e) {
    echo json_encode(["error" => "Erro ao conectar ao banco: " . $e->getMessage()]);
    exit();
}

// Buscar os itens do banco de dados
$stmt = $pdo->query("SELECT i.ID, i.nome, i.preco_unitario, c.nome as categoria 
                     FROM item i
                     LEFT JOIN categoria c ON i.id_categoria = c.ID");

$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Produto</title>
    <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/sb-admin-2.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body id="page-top">

    <div class="container mt-5">
        <h2 class="mb-4">Lista de Itens</h2>

        <!-- Alerta de sucesso -->
        <div id="alert-success" class="alert alert-success d-none" role="alert"></div>

        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Preço Unitário</th>
                    <th>Categoria</th>
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
                            <input type="number" id="input-<?= $item['ID'] ?>" class="form-control d-none" value="<?= $item['preco_unitario'] ?>" step="0.01">
                        </td>
                        <td><?= htmlspecialchars($item['categoria']) ?></td>
                        <td>
                            <button class="btn btn-primary btn-sm" id="editar-<?= $item['ID'] ?>" onclick="editarPreco(<?= $item['ID'] ?>)">Editar</button>
                            <button class="btn btn-success btn-sm d-none" id="salvar-<?= $item['ID'] ?>" onclick="salvarPreco(<?= $item['ID'] ?>)">Salvar</button>
                            <button class="btn btn-danger btn-sm d-none" id="cancelar-<?= $item['ID'] ?>" onclick="cancelarEdicao(<?= $item['ID'] ?>)">Cancelar</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script>
        function editarPreco(id) {
            $("#preco-" + id).addClass("d-none");
            $("#input-" + id).removeClass("d-none");
            $("#editar-" + id).addClass("d-none");
            $("#salvar-" + id).removeClass("d-none");
            $("#cancelar-" + id).removeClass("d-none");
        }

        function cancelarEdicao(id) {
            $("#preco-" + id).removeClass("d-none");
            $("#input-" + id).addClass("d-none");
            $("#editar-" + id).removeClass("d-none");
            $("#salvar-" + id).addClass("d-none");
            $("#cancelar-" + id).addClass("d-none");
        }

        function salvarPreco(id) {
            let novoPreco = $("#input-" + id).val();

            $.ajax({
                url: "lista_itens.php",
                type: "POST",
                data: { id: id, novo_preco: novoPreco },
                dataType: "json",
                success: function(response) {
                    if (response.success) {
                        $("#preco-" + id).text("R$ " + response.novo_preco);
                        cancelarEdicao(id);

                        $("#alert-success").text(response.message).removeClass("d-none");

                        setTimeout(() => {
                            $("#alert-success").addClass("d-none");
                        }, 3000);
                    } else {
                        console.error("Erro no PHP:", response.error);
                        alert("Erro ao atualizar preço!");
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Erro AJAX:", status, error);
                    alert("Erro na requisição! Veja o console para mais detalhes.");
                }
            });
        }
    </script>

</body>
</html>
