<?php
include "php/cadastro_usuarios.php";
include "header.php"
?>

<!DOCTYPE html>
<html lang="pt-br">

<body id="page-top">

    <div class="container-fluid">
        <h1 class="h3 mb-4 text-gray-800">Cadastro de usuario</h1>
        <?php if (isset($mensagem)) {
            echo "<div class='alert alert-info'>$mensagem</div>";
        } ?>
        <form action="cadastro_usuarios.php" method="POST">
            <label for="restaurante">Restaurante:</label>
            <select id="restaurante" name="restaurante" class="form-control" required>
                <option value="">Selecione um restaurante</option>
                <?php foreach ($restaurantes as $id => $nome) { ?>
                    <option value="<?= $id ?>"><?= $nome ?></option>
                <?php } ?>
            </select>
            <div class="form-group">
                <label for="nome">Nome</label>
                <input type="nome" class="form-control" id="nome" name="nome" required>
            </div>
            <label for="perfil">Perfil:</label>
            <select id="perfil" name="perfil" class="form-control" required>
                <option value="">Selecione um perfil</option>
                <?php foreach ($perfis as $id => $nome) { ?>
                    <option value="<?= $id ?>"><?= $nome ?></option>
                <?php } ?>
            </select>
            <div class="form-group">
                <label for="email">E-mail</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="senha">Senha</label>
                <input type="senha" class="form-control" id="senha" name="senha" required>
            </div>
            <button type="submit" class="btn btn-primary">Cadastrar usuario</button>

        </form>
    </div>
</body>

</html>
<?php
include "footer.php"
?>