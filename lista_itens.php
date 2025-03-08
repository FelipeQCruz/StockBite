<?php
include "php/lista_itens.php";
include "header.php";
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
            <h6 class="m-0 font-weight-bold text-primary">DataTables Example</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive w-100">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Preço Unitário</th>
                            <th>Fornecedor</th>
                            <th>Cadastrado Por</th>
                            <th>Categoria</th>
                            <th>Subcategoria</th>
                            <th>Quantidade</th>
                            <th>Medida</th>
                            <th>Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item): ?>
                            <tr id="row-<?= $item['ID'] ?>">
                                <td><?= htmlspecialchars($item['nome']) ?></td>
                                <td>
                                    <span id="preco-<?= $item['ID'] ?>">R$ <?= number_format($item['preco_unitario'], 2, ',', '.') ?></span>
                                    <input type="number" id="input-preco-<?= $item['ID'] ?>" class="form-control d-none" value="<?= $item['preco_unitario'] ?>" step="0.01">
                                </td>
                                <td><?= htmlspecialchars($item['fornecedor']) ?></td>
                                <td><?= htmlspecialchars($item['cadastrado_por']) ?></td>
                                <td>
                                    <span id="categoria-<?= $item['ID'] ?>"><?= htmlspecialchars($item['categoria']) ?></span>
                                    <select id="input-categoria-<?= $item['ID'] ?>" class="form-control d-none" onchange="atualizarSubcategorias(<?= $item['ID'] ?>)">
                                        <?php foreach ($categorias as $categoria): ?>
                                            <option value="<?= $categoria['ID'] ?>" <?= $item['categoria_id'] == $categoria['ID'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($categoria['nome']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td>
                                    <span id="subcategoria-<?= $item['ID'] ?>"><?= htmlspecialchars($item['subcategoria']) ?></span>
                                    <select id="input-subcategoria-<?= $item['ID'] ?>" class="form-control d-none">
                                        <?php if (isset($subcategorias[$item['categoria_id']])): ?>
                                            <?php foreach ($subcategorias[$item['categoria_id']] as $subcategoria): ?>
                                                <option value="<?= $subcategoria['ID'] ?>" <?= ($item['subcategoria'] == $subcategoria['nome']) ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($subcategoria['nome']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </td>
                                <td>
                                    <span id="quantidade-<?= $item['ID'] ?>"><?= htmlspecialchars($item['quantidade_atual']) ?></span>
                                    <input type="number" id="input-quantidade-<?= $item['ID'] ?>" class="form-control d-none" value="<?= $item['quantidade_atual'] ?>">
                                </td>
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
                novo_preco: $("#input-preco-" + id).val(),
                nova_medida: $("#input-medida-" + id).val(),
                nova_categoria: $("#input-categoria-" + id).val(),
                nova_quantidade: $("#input-quantidade-" + id).val()
            };

            $.post("lista_itens.php", data, function(response) {
                alert(response.message);
                location.reload();
            }, "json");
        }

        let subcategoriasDisponiveis = <?php echo json_encode($subcategorias); ?>;

        function atualizarSubcategorias(id) {
            let categoriaSelecionada = $("#input-categoria-" + id).val();
            let subcategoriaSelect = $("#input-subcategoria-" + id);

            // Limpa opções anteriores antes de carregar novas opções
            subcategoriaSelect.empty().append('<option value="">Selecione uma subcategoria</option>').prop("disabled", true);

            if (subcategoriasDisponiveis.hasOwnProperty(categoriaSelecionada)) {
                subcategoriasDisponiveis[categoriaSelecionada].forEach(function(sub) {
                    subcategoriaSelect.append(`<option value="${sub.ID}">${sub.nome}</option>`);
                });
                subcategoriaSelect.prop("disabled", false);
            }
        }



        function editarItem(id) {
            $("#preco-" + id).addClass("d-none");
            $("#input-preco-" + id).removeClass("d-none");

            $("#medida-" + id).addClass("d-none");
            $("#input-medida-" + id).removeClass("d-none");

            $("#categoria-" + id).addClass("d-none");
            $("#input-categoria-" + id).removeClass("d-none");

            $("#quantidade-" + id).addClass("d-none");
            $("#input-quantidade-" + id).removeClass("d-none");

            $("#subcategoria-" + id).addClass("d-none");
            $("#input-subcategoria-" + id).removeClass("d-none");

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
                novo_preco: $("#input-preco-" + id).val(),
                nova_medida: $("#input-medida-" + id).val(),
                nova_categoria: $("#input-categoria-" + id).val(),
                nova_subcategoria: $("#input-subcategoria-" + id).val(),
                nova_quantidade: $("#input-quantidade-" + id).val()
            };

            $.post("lista_itens.php", data, function(response) {
                alert(response.message);
                location.reload();
            }, "json");
        }
    </script>
    <!-- Bootstrap core JavaScript-->
    <script src="../vendor/jquery/jquery.min.js"></script>
    <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="../vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="../js/sb-admin-2.min.js"></script>

    <!-- Page level plugins -->
    <script src="../vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="../vendor/datatables/dataTables.bootstrap4.min.js"></script>

    <!-- Page level custom scripts -->
    <script src="../js/demo/datatables-demo.js"></script>

    </body>

</div>

<?php
include "footer.php";
?>