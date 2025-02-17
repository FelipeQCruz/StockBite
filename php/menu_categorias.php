<?php
// Conexão com o banco de dados
$servername = "localhost";
$username = "root";
$password = "27H09g94B*";
$dbname = "estoque_db";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Função para buscar categorias e subcategorias organizadas
function getCategories($parent_id = null, $conn) {
    $sql = is_null($parent_id) ? 
        "SELECT ID, nome FROM categoria WHERE id_pai IS NULL ORDER BY nome" : 
        "SELECT ID, nome FROM categoria WHERE id_pai = $parent_id ORDER BY nome";

    $result = $conn->query($sql);
    $categories = [];

    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }

    return $categories;
}

// Buscar todas as categorias principais
$categorias = getCategories(null, $conn);

// Recuperar ID da categoria já cadastrada (se aplicável)
$id_categoria_selecionada = isset($_GET['categoria_id']) ? (int)$_GET['categoria_id'] : null;
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Item</title>
    <script>
        function carregarSubcategorias() {
            var categoriaId = document.getElementById("categoria").value;
            var subcategoriaSelect = document.getElementById("subcategoria");

            fetch('subcategorias.php?categoria_id=' + categoriaId)
                .then(response => response.json())
                .then(data => {
                    subcategoriaSelect.innerHTML = '<option value="">Selecione uma subcategoria</option>';
                    data.forEach(subcat => {
                        var option = document.createElement("option");
                        option.value = subcat.ID;
                        option.textContent = subcat.nome;
                        subcategoriaSelect.appendChild(option);
                    });
                });
        }
    </script>
</head>
<body>

    <h2>Cadastro de Item</h2>

    <form action="salvar_item.php" method="post">
        <label for="categoria">Escolha a categoria:</label>
        <select name="categoria" id="categoria" onchange="carregarSubcategorias()">
            <option value="">Selecione uma categoria</option>
            <?php foreach ($categorias as $cat): ?>
                <option value="<?= $cat['ID'] ?>" <?= ($id_categoria_selecionada == $cat['ID']) ? 'selected' : '' ?>>
                    <?= $cat['nome'] ?>
                </option>
            <?php endforeach; ?>
        </select>

        <br><br>

        <label for="subcategoria">Escolha a subcategoria:</label>
        <select name="subcategoria" id="subcategoria">
            <option value="">Selecione uma subcategoria</option>
        </select>

        <br><br>

        <label for="nome">Nome do Item:</label>
        <input type="text" name="nome" id="nome" required>

        <br><br>

        <button type="submit">Salvar</button>
    </form>

</body>
</html>

<?php
$conn->close();
?>
