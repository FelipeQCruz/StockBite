<?php
include 'php/cadastro_item.php'; // Inclui a lógica PHP
include 'header.php';

// Exibir mensagem de sucesso ou erro, se existir
$mensagem = $_SESSION['mensagem'] ?? null;
unset($_SESSION['mensagem']); // Remove a mensagem para evitar reexibição
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

<body>
    <div class="container mt-4">
        <h1 class="h3 mb-4">Cadastro de Produto</h1>

        <?php if ($mensagem): ?>
            <div class="alert alert-info"><?= $mensagem ?></div>
        <?php endif; ?>

        <form action="php/cadastro_item.php" method="POST">
            <div class="form-group">
                <label for="nome">Nome do Produto</label>
                <input type="text" class="form-control" id="nome" name="nome" required>
            </div>

            <div class="form-group">
                <label for="quantidade_medida">Quantidade</label>
                <input type="text" class="form-control" id="quantidade_medida" name="quantidade_medida" required>
            </div>

            <div class="form-group">
                <label for="medida">Unidade de Medida</label>
                <select id="medida" name="medida" class="form-control" required>
                    <option value="">Selecione uma opção</option>
                    <?php foreach ($unidades_medida as $medida) : ?>
                        <option value="<?= $medida['ID'] ?>"><?= $medida['nome'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="preco_unitario">Preço Unitário</label>
                <input type="number" step="0.01" class="form-control" id="preco_unitario" name="preco_unitario" required>
            </div>

            <div class="form-group">
                <label for="categoria">Categoria</label>
                <select id="categoria" name="categoria" class="form-control" required>
                    <option value="">Selecione uma categoria</option>
                    <?php foreach ($categorias as $categoria) : ?>
                        <option value="<?= $categoria['ID'] ?>"><?= $categoria['nome'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="subcategoria">Subcategoria</label>
                <select id="subcategoria" name="subcategoria" class="form-control" required disabled>
                    <option value="">Selecione uma subcategoria</option>
                </select>
            </div>

            <div class="form-group">
                <label for="id_fornecedor">ID do Fornecedor</label>
                <input type="text" class="form-control" id="id_fornecedor" name="id_fornecedor" required>
            </div>

            <div class="form-group">
                <label for="email">E-mail de Cadastro</label>
                <input type="email" class="form-control" id="email" name="email" readonly required>
            </div>

            <button type="submit" class="btn btn-primary">Cadastrar Produto</button>
        </form>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            fetch('php/session_data.php')
                .then(response => response.json())
                .then(data => document.getElementById("email").value = data.email)
                .catch(error => console.error('Erro ao obter e-mail:', error));
        });

        let subcategorias = <?= json_encode($subcategoriasFormatadas); ?>;
        document.getElementById('categoria').addEventListener('change', function() {
            let categoriaId = this.value;
            let subcategoriaSelect = document.getElementById('subcategoria');
            subcategoriaSelect.innerHTML = '<option value="">Selecione uma subcategoria</option>';
            subcategoriaSelect.disabled = this.value === "";
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
</body>

</html>

<?php include 'footer.php'; ?>
