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

    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["id"])) {
        $id_item = intval($_POST["id"]);
        $nova_quantidade = floatval($_POST["nova_quantidade_total_estoque"]);

        // Verifica se já existe um registro para o item no estoque
        $stmt = $pdo->prepare("SELECT quantidade FROM estoque WHERE id_item = :id_item LIMIT 1");
        $stmt->bindParam(":id_item", $id_item);
        $stmt->execute();
        $estoque_existente = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($estoque_existente) {
            // Atualiza a quantidade mantendo a data_hora_entrada original
            $query = "UPDATE estoque SET quantidade = :nova_quantidade WHERE id_item = :id_item";
        } else {
            // Insere um novo registro no estoque
            $query = "INSERT INTO estoque (id_item, data_hora_entrada, quantidade) VALUES (:id_item, NOW(), :nova_quantidade)";
        }

        $stmt = $pdo->prepare($query);
        $stmt->bindParam(":id_item", $id_item);
        $stmt->bindParam(":nova_quantidade", $nova_quantidade);
        $stmt->execute();

        echo json_encode(["success" => true, "message" => "Estoque atualizado com sucesso!"]);
        exit();
    }


    // Buscar os itens do banco de dados
    $stmt = $pdo->query("
        SELECT 
            i.ID, i.nome, i.preco_unitario, 
            f.empresa AS fornecedor, 
            c.ID AS categoria_id, c.nome AS categoria, 
            sc.nome AS subcategoria, 
            um.ID AS medida_id, um.nome AS medida, 
            COALESCE(SUM(i.quantidade_medida), 0) AS quantidade_unitaria,
            COALESCE(SUM(e.quantidade), 0) AS unidades_estoque,
            (COALESCE(SUM(e.quantidade), 0) * COALESCE(SUM(i.quantidade_medida), 0)) AS quantidade_total_estoque,
            (COALESCE(SUM(e.quantidade), 0) * COALESCE(SUM(i.preco_unitario), 0)) AS valor_total_estoque
            FROM item i
            LEFT JOIN fornecedor f ON i.id_fornecedor = f.ID
            LEFT JOIN categoria c ON i.id_categoria = c.ID
            LEFT JOIN categoria sc ON i.id_subcategoria = sc.ID
            LEFT JOIN unidades_medida um ON i.id_medida = um.ID
            LEFT JOIN estoque e ON i.id = e.id_item
            GROUP BY i.ID, i.nome, i.preco_unitario, f.empresa, c.ID, c.nome, sc.nome, um.ID, um.nome;
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
                                            <input type="number" id="input-quantidade_total_estoque-<?= $item['ID'] ?>" class="form-control d-none" value="<?= $item['quantidade_total_estoque'] ?>" step="0.1">
                                        </td>
                                        <td><?= htmlspecialchars($item['medida']) ?></td>
                                        <td><?= htmlspecialchars($item['unidades_estoque']) ?></td>
                                        <td><span id="quantidade_total_estoque-<?= $item['ID'] ?>">R$ <?= number_format($item['valor_total_estoque'], 2, ',', '.') ?></span></td>
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
                        nova_quantidade_total_estoque: $("#input-quantidade_total_estoque-" + id).val(),
                    };

                    $.post("estoque.php", data, function(response) {
                        alert(response.message);
                        location.reload();
                    }, "json");
                }

                function editarItem(id) {
                    $("#quantidade_total_estoque-" + id).addClass("d-none");
                    $("#input-quantidade_total_estoque-" + id).removeClass("d-none");

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
                        nova_quantidade_total_estoque: $("#input-quantidade_total_estoque-" + id).val(),
                    };

                    console.log("Enviando dados:", data); // Verifique no console do navegador

                    $.post("estoque.php", data, function(response) {
                        console.log("Resposta do servidor:", response); // Verifique a resposta
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