<?php
$host = "localhost";
$user = "root"; // Usuário padrão do MySQL
$pass = "27H09g94B*"; // Senha (deixe vazia se não definiu)
$db = "stockbite";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM usuario";
$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {
    echo "User: " . $row["nome"] . " - Empresa: " . $row["empresa"] . "<br>";
}

$conn->close();
?>
