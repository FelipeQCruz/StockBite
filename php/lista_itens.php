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

    // Atualizar preço, medida, quantidade e categoria via AJAX
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["id"])) {
        $id = intval($_POST["id"]);
        $updates = [];

        if (isset($_POST["novo_preco"])) {
            $updates[] = "preco_unitario = " . floatval($_POST["novo_preco"]);
        }
        if (isset($_POST["nova_medida"])) {
            $updates[] = "id_medida = " . intval($_POST["nova_medida"]);
        }
        if (isset($_POST["nova_categoria"])) {
            $nova_categoria = intval($_POST["nova_categoria"]);
            $updates[] = "id_categoria = $nova_categoria";

            // Resetar a subcategoria caso não pertença à nova categoria
            if (isset($_POST["nova_subcategoria"])) {
                $nova_subcategoria = intval($_POST["nova_subcategoria"]);

                // Verifica se a subcategoria pertence à categoria selecionada
                $stmt = $pdo->prepare("SELECT ID FROM categoria WHERE ID = :subcategoria AND id_pai = :categoria");
                $stmt->bindParam(":subcategoria", $nova_subcategoria);
                $stmt->bindParam(":categoria", $nova_categoria);
                $stmt->execute();

                if ($stmt->fetch()) {
                    $updates[] = "id_subcategoria = $nova_subcategoria";
                } else {
                    $updates[] = "id_subcategoria = NULL"; // Resetar caso inválido
                }
            }
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

    // Buscar categorias principais
    $stmt = $pdo->query("SELECT * FROM categoria WHERE id_pai IS NULL");
    $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Buscar todas as subcategorias organizadas por categoria
    $stmt = $pdo->query("SELECT * FROM categoria WHERE id_pai IS NOT NULL");
    $subcategorias = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $subcategorias[$row['id_pai']][] = ["ID" => $row["ID"], "nome" => $row["nome"]];
    }

    // Buscar os itens do banco de dados
    $stmt = $pdo->query("
        SELECT 
            i.ID, i.nome, i.preco_unitario, 
            f.empresa AS fornecedor, 
            u.nome AS cadastrado_por,
            c.ID AS categoria_id, c.nome AS categoria, 
            sc.nome AS subcategoria, 
            um.ID AS medida_id, um.nome AS medida, 
            COALESCE(SUM(i.quantidade_medida), 0) AS quantidade_atual
        FROM item i
        LEFT JOIN fornecedor f ON i.id_fornecedor = f.ID
        LEFT JOIN usuario u ON i.email_cadastro = u.email
        LEFT JOIN categoria c ON i.id_categoria = c.ID
        LEFT JOIN categoria sc ON i.id_subcategoria = sc.ID
        LEFT JOIN unidades_medida um ON i.id_medida = um.ID
        GROUP BY i.ID, i.nome, i.preco_unitario, f.empresa, u.nome, c.ID, c.nome, sc.nome, um.ID, um.nome
    ");

    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro ao conectar ao banco de dados: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pt">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>SB Admin 2 - Tables</title>

    <!-- Custom fonts for this template -->
    <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="../css/sb-admin-2.min.css" rel="stylesheet">

    <!-- Custom styles for this page -->
    <link href="../vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">

</head>

<body id="page-top">
    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
                <div class="sidebar-brand-icon rotate-n-15">
                    <i class="fas fa-laugh-wink"></i>
                </div>
                <div class="sidebar-brand-text mx-3">StockBite</div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item active">
                <a class="nav-link" href="index.html">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                Menu
            </div>

            <!-- Nav Item - Pages Collapse Menu -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseOne"
                    aria-expanded="true" aria-controls="collapseOne">
                    <i class="fas fa-box fa-sm fa-fw mr-2 text-gray-400"></i>
                    <span>Produtos</span>
                </a>
                <div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Opções:</h6>
                        <a class="collapse-item" href="php/cadastro_item.php">Cadastrar</a>
                        <a class="collapse-item" href="php/visualiza_item.php">Visualizar</a>
                    </div>
                </div>
            </li>

            <!-- Nav Item - Pages Collapse Menu -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTwo"
                    aria-expanded="true" aria-controls="collapseTwo">
                    <i class="fas fa-box fa-sm fa-fw mr-2 text-gray-400"></i>
                    <span>Fornecedores</span>
                </a>
                <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Opções:</h6>
                        <a class="collapse-item" href="php/cadastro_fornecedor.php">Cadastrar</a>
                        <a class="collapse-item" href="php/visualiza_fornecedor.php">Visualizar</a>
                    </div>
                </div>
            </li>

            <!-- Nav Item - Utilities Collapse Menu -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseThree"
                    aria-expanded="true" aria-controls="collapseThree">
                    <i class="fas fa-box fa-sm fa-fw mr-2 text-gray-400"></i>
                    <span>Estoque</span>
                </a>
                <div id="collapseThree" class="collapse" aria-labelledby="headingThree"
                    data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Opções:</h6>
                        <a class="collapse-item" href="php.html">Visualizar</a>
                    </div>
                </div>
            </li>

            <!-- Nav Item - Utilities Collapse Menu -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseFour"
                    aria-expanded="true" aria-controls="collapseFour">
                    <i class="fas fa-box fa-sm fa-fw mr-2 text-gray-400"></i>
                    <span>Usuários</span>
                </a>
                <div id="collapseFour" class="collapse" aria-labelledby="headingFour"
                    data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Opções:</h6>
                        <a class="collapse-item" href="php/visualiza_usuarios.php">Visualizar</a>
                        <a class="collapse-item" href="php/cadastro_usuarios.php">Cadastrar</a>
                    </div>
                </div>
            </li>

            <!-- Nav Item - Utilities Collapse Menu -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseFive"
                    aria-expanded="true" aria-controls="collapseFive">
                    <i class="fas fa-box fa-sm fa-fw mr-2 text-gray-400"></i>
                    <span>Geral</span>
                </a>
                <div id="collapseFive" class="collapse" aria-labelledby="headingFive"
                    data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Categorias:</h6>
                        <a class="collapse-item" href="php/adiciona_categorias.php">Adicionar</a>
                    </div>
                </div>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                Addons
            </div>

            <!-- Nav Item - Pages Collapse Menu -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePages"
                    aria-expanded="true" aria-controls="collapsePages">
                    <i class="fas fa-fw fa-folder"></i>
                    <span>Pages</span>
                </a>
                <div id="collapsePages" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Login Screens:</h6>
                        <a class="collapse-item" href="login.html">Login</a>
                        <a class="collapse-item" href="register.html">Register</a>
                        <a class="collapse-item" href="forgot-password.html">Forgot Password</a>
                        <div class="collapse-divider"></div>
                        <h6 class="collapse-header">Other Pages:</h6>
                        <a class="collapse-item" href="404.html">404 Page</a>
                        <a class="collapse-item" href="blank.html">Blank Page</a>
                    </div>
                </div>
            </li>

            <!-- Nav Item - Charts -->
            <li class="nav-item">
                <a class="nav-link" href="charts.html">
                    <i class="fas fa-fw fa-chart-area"></i>
                    <span>Charts</span></a>
            </li>

            <!-- Nav Item - Tables -->
            <li class="nav-item">
                <a class="nav-link" href="tables.html">
                    <i class="fas fa-fw fa-table"></i>
                    <span>Tables</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>

            <!-- Sidebar Message -->
            <div class="sidebar-card d-none d-lg-flex">
                <img class="sidebar-card-illustration mb-2" src="../img/undraw_rocket.svg" alt="...">
                <p class="text-center mb-2">Precisa de ajuda?</p>
                <a class="btn btn-success btn-sm" href="mailto:">Contatar suporte</a>
            </div>

        </ul>
        <!-- End of Sidebar -->

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
                                    <th>Medida</th>
                                    <th>Quantidade</th>
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

</html>