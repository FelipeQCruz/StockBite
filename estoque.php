<?php
include "php/estoque.php";
include "header.php"
?>

<!DOCTYPE html>
<html lang="pt">
<!-- Tabela -->
<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <h1 class="h3 mb-2 text-gray-800">Itens cadastrados</h1>

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">O total em estoque é a multiplicação das unidades em estoque com a quantidade unitária</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive w-100">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Total em estoque</th>
                            <th>Medida</th>
                            <th>Unidades em estoque</th>
                            <th>Valor em estoque</th>
                            <th>Fornecedor</th>
                            <th>Categoria</th>
                            <th>Subcategoria</th>
                            <th>Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item): ?>
                            <tr id="row-<?= $item['ID'] ?>">
                                <td><?= htmlspecialchars($item['nome']) ?></td>
                                <td>
                                    <span id="quantidade_total_estoque-<?= $item['ID'] ?>"><?= number_format($item['quantidade_total_estoque'], 1, ',', '.') ?></span>
                                    <input type="number" id="input-quantidade_estoque-<?= $item['ID'] ?>" class="form-control d-none" value="<?= $item['quantidade_total_estoque'] ?>" step="0.1">
                                </td>
                                <td><?= htmlspecialchars($item['medida']) ?></td>
                                <td>
                                    <span id="unidades_estoque-<?= $item['ID'] ?>"><?= number_format($item['unidades_estoque'], 1, ',', '.') ?></span>
                                    <input type="number" id="input-unidades_estoque-<?= $item['ID'] ?>" class="form-control d-none" value="<?= $item['unidades_estoque'] ?>" step="0.1">
                                </td>
                                <td><span id="valor_total_estoque-<?= $item['ID'] ?>">R$ <?= number_format($item['valor_total_estoque'], 2, ',', '.') ?></span></td>
                                <td><?= htmlspecialchars($item['fornecedor']) ?></td>
                                <td><?= htmlspecialchars($item['categoria']) ?></td>
                                <td><?= htmlspecialchars($item['subcategoria']) ?></td>
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
        </div>
    </div>

    <script>
        function salvarAlteracoes(id) {
            let data = {
                id: id,
                nova_unidade_total_estoque: $("#input-unidades_estoque-" + id).val(),
                nova_quantidade_total_estoque: $("#input-quantidade_estoque-" + id).val(),
            };

            $.post("estoque.php", data, function(response) {
                alert(response.message);
                location.reload();
            }, "json");
        }

        function editarItem(id) {
            $("#unidades_estoque-" + id).addClass("d-none");
            $("#quantidade_total_estoque-" + id).addClass("d-none");
            $("#input-unidades_estoque-" + id).removeClass("d-none");
            $("#input-quantidade_estoque-" + id).removeClass("d-none");

            $("#editar-" + id).addClass("d-none");
            $("#salvar-" + id).removeClass("d-none");
            $("#cancelar-" + id).removeClass("d-none");
        }

        function cancelarEdicao(id) {
            location.reload();
        }



        $(".categoria-select").change(function() {
            let id = $(this).data("id");
            atualizarSubcategorias(id);
        });

        function salvarAlteracoes(id) {
            let data = {
                id: id,
                nova_unidade_total_estoque: $("#input-unidades_estoque-" + id).val(),
                nova_quantidade_total_estoque: $("#input-quantidade_estoque-" + id).val(),
            };

            console.log("Enviando dados:", data); // Verifique no console do navegador

            $.post("estoque.php", data, function(response) {
                console.log("Resposta do servidor:", response); // Verifique a resposta
                alert(response.message);
                location.reload();
            }, "json");
        }
    </script>

</div>

</body>

</html>

<?php
include "footer.php";
?>
