<?php
include "php/cadastro_fornecedor.php";
include "header.php"
?>

<!DOCTYPE html>
<html lang="pt-br">

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Cadastro de Fornecedor</h1>
    <?php if (isset($mensagem)) {
        echo "<div class='alert alert-info'>$mensagem</div>";
    } ?>
    <form action="cadastro_fornecedor.php" method="POST">
        <div class="form-group">
            <label for="empresa">Empresa</label>
            <input type="text" class="form-control" id="empresa" name="empresa" required>
        </div>
        <div class="form-group">
            <label for="CNPJ">CNPJ</label>
            <input type="number" step="0.01" class="form-control" id="CNPJ" name="CNPJ" required>
        </div>
        <div class="form-group">
            <label for="razao_social">Razão social</label>
            <input type="text" class="form-control" id="razao_social" name="razao_social" required>
        </div>
        <div class="form-group">
            <label for="nome_vendedor">Nome vendedor</label>
            <input type="nome_vendedor" class="form-control" id="nome_vendedor" name="nome_vendedor" required>
        </div>
        <div class="form-group">
            <label for="telefone">Telefone vendedor</label>
            <input type="text" class="form-control" id="telefone" name="telefone" required>
        </div>
        <div class="form-group">
            <label for="email">E-mail vendedor</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <button type="submit" class="btn btn-primary">Cadastrar Fornecedor</button>

    </form>
</div>

</html>
<?php
include "footer.php"
?>