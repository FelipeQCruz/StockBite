<?php
include "php/visualiza_fornecedor.php";
include "header.php"
?>

<!DOCTYPE html>
<html lang="pt">

<body>
    <!-- Tabela -->
    <!-- Begin Page Content -->
    <div class="container-fluid">

        <!-- Page Heading -->
        <h1 class="h3 mb-2 text-gray-800">Fornecedores</h1>

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
                                <th>Empresa</th>
                                <th>CNPJ</th>
                                <th>Razão social</th>
                                <th>Nome vendedor</th>
                                <th>Telefone</th>
                                <th>E-mail</th>
                                <th>Ação</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($fornecedores as $fornecedor): ?>
                                <tr id="row-<?= $fornecedor['ID'] ?>">
                                    <td>
                                        <span id="empresa-<?= $fornecedor['ID'] ?>"><?= htmlspecialchars($fornecedor['empresa']) ?></span>
                                        <input type="text" id="input-empresa-<?= $fornecedor['ID'] ?>" class="form-control d-none" value="<?= htmlspecialchars($fornecedor['empresa']) ?>">
                                    </td>
                                    <td><?= htmlspecialchars($fornecedor['CNPJ']) ?></td>
                                    <td>
                                        <span id="razao-<?= $fornecedor['ID'] ?>"><?= htmlspecialchars($fornecedor['razao_social']) ?></span>
                                        <input type="text" id="input-razao-<?= $fornecedor['ID'] ?>" class="form-control d-none" value="<?= htmlspecialchars($fornecedor['razao_social']) ?>">
                                    </td>
                                    <td>
                                        <span id="vendedor-<?= $fornecedor['ID'] ?>"><?= htmlspecialchars($fornecedor['nome_vendedor']) ?></span>
                                        <input type="text" id="input-vendedor-<?= $fornecedor['ID'] ?>" class="form-control d-none" value="<?= htmlspecialchars($fornecedor['nome_vendedor']) ?>">
                                    </td>
                                    <td>
                                        <span id="telefone-<?= $fornecedor['ID'] ?>"><?= htmlspecialchars($fornecedor['telefone']) ?></span>
                                        <input type="text" id="input-telefone-<?= $fornecedor['ID'] ?>" class="form-control d-none" value="<?= htmlspecialchars($fornecedor['telefone']) ?>">
                                    </td>
                                    <td>
                                        <span id="email-<?= $fornecedor['ID'] ?>"><?= htmlspecialchars($fornecedor['email']) ?></span>
                                        <input type="text" id="input-email-<?= $fornecedor['ID'] ?>" class="form-control d-none" value="<?= htmlspecialchars($fornecedor['email']) ?>">
                                    </td>
                                    <td>
                                        <button class="btn btn-primary btn-sm" id="editar-<?= $fornecedor['ID'] ?>" onclick="editarFornecedor(<?= $fornecedor['ID'] ?>)">Editar</button>
                                        <button class="btn btn-success btn-sm d-none" id="salvar-<?= $fornecedor['ID'] ?>" onclick="salvarAlteracoes(<?= $fornecedor['ID'] ?>)">Salvar</button>
                                        <button class="btn btn-danger btn-sm d-none" id="cancelar-<?= $fornecedor['ID'] ?>" onclick="cancelarEdicao(<?= $fornecedor['ID'] ?>)">Cancelar</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <script>
            function editarFornecedor(id) {
                $("#empresa-" + id).addClass("d-none");
                $("#input-empresa-" + id).removeClass("d-none");

                $("#razao-" + id).addClass("d-none");
                $("#input-razao-" + id).removeClass("d-none");

                $("#vendedor-" + id).addClass("d-none");
                $("#input-vendedor-" + id).removeClass("d-none");

                $("#telefone-" + id).addClass("d-none");
                $("#input-telefone-" + id).removeClass("d-none");

                $("#email-" + id).addClass("d-none");
                $("#input-email-" + id).removeClass("d-none");

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
                    nova_empresa: $("#input-empresa-" + id).val(),
                    nova_razao_social: $("#input-razao-" + id).val(),
                    novo_vendedor: $("#input-vendedor-" + id).val(),
                    novo_telefone: $("#input-telefone-" + id).val(),
                    novo_email: $("#input-email-" + id).val()
                };

                $.post("visualiza_fornecedor.php", data, function(response) {
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
    </div>
</body>

</html>
<?php
include "footer.php"
?>