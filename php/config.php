<?php
$dbHost = "localhost";
$dbUsername = "root";
$dbPassword = "27H09g94B*";
$dbName = "stockbite";

$conexao = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName);

if($conexao->connect_error)
{
    die("Falha na conexão: " . $conexao->connect_error);
}
?>