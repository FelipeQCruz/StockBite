<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "estoque_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

$parent_id = isset($_GET['categoria_id']) ? (int)$_GET['categoria_id'] : 0;

$subcategorias = [];
if ($parent_id > 0) {
    $sql = "SELECT ID, nome FROM categoria WHERE id_pai = $parent_id ORDER BY nome";
    $result = $conn->query($sql);

    while ($row = $result->fetch_assoc()) {
        $subcategorias[] = $row;
    }
}

header('Content-Type: application/json');
echo json_encode($subcategorias);

$conn->close();
?>
