
<?php
// Database connection
$servername = "localhost"; // Change if needed
$username = "root"; // Change if needed
$password = "27H09g94B*"; // Change if needed
$database = "stockbite"; // Change to your actual database name

$conn = new mysqli($servername, $username, $password, $database);
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
?>

<form method="post" action="processar_item.php">
    <label for="categoria">Categoria:</label>
    <select id="categoria" name="categoria" required>
        <option value="">Selecione uma categoria</option>
        <?php foreach ($categorias as $id => $nome) { ?>
            <option value="<?= $id ?>"><?= $nome ?></option>
        <?php } ?>
    </select>

    <label for="subcategoria">Subcategoria:</label>
    <select id="subcategoria" name="subcategoria" required>
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

    <input type="submit" value="Cadastrar Item">
</form>
