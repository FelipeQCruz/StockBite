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

    // Atualizar fornecedor via AJAX
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["id"])) {
        $id = intval($_POST["id"]);
        $updates = [];

        if (isset($_POST["nova_empresa"])) {
            $updates[] = "empresa = '" . htmlspecialchars($_POST["nova_empresa"], ENT_QUOTES) . "'";
        }
        if (isset($_POST["novo_cnpj"])) {
            $updates[] = "CNPJ = " . intval($_POST["novo_cnpj"]);
        }
        if (isset($_POST["nova_razao_social"])) {
            $updates[] = "razao_social = '" . htmlspecialchars($_POST["nova_razao_social"], ENT_QUOTES) . "'";
        }
        if (isset($_POST["novo_vendedor"])) {
            $updates[] = "nome_vendedor = '" . htmlspecialchars($_POST["novo_vendedor"], ENT_QUOTES) . "'";
        }
        if (isset($_POST["novo_telefone"])) {
            $updates[] = "telefone = '" . htmlspecialchars($_POST["novo_telefone"], ENT_QUOTES) . "'";
        }
        if (isset($_POST["novo_email"])) {
            $updates[] = "email = '" . htmlspecialchars($_POST["novo_email"], ENT_QUOTES) . "'";
        }

        if (!empty($updates)) {
            $query = "UPDATE fornecedor SET " . implode(", ", $updates) . " WHERE ID = :id";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(":id", $id);
            $stmt->execute();
        }

        echo json_encode(["success" => true, "message" => "Fornecedor atualizado com sucesso!"]);
        exit();
    }

    // Buscar fornecedores do banco de dados
    $stmt = $pdo->query("SELECT * FROM fornecedor");
    $fornecedores = $stmt->fetchAll(PDO::FETCH_ASSOC);
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

</body>

</html>