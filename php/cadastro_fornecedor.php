<?php
session_start();

// Conexão com o banco de dados
include "conexao.php";

// Processamento do formulário
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $empresa = $_POST['empresa'];
    $CNPJ = $_POST['CNPJ'];
    $razao_social = $_POST['razao_social'];
    $nome_vendedor = $_POST['nome_vendedor'];
    $telefone = $_POST['telefone'];
    $email = $_POST['email'];

    if (empty($empresa) || empty($CNPJ) || empty($razao_social) || empty($nome_vendedor) || empty($telefone) || empty($email)) {
        $mensagem = "Todos os campos são obrigatórios!";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO fornecedor (empresa, CNPJ, razao_social, nome_vendedor, telefone, email) 
                VALUES (:empresa, :CNPJ, :razao_social, :nome_vendedor, :telefone, :email)");

            $stmt->bindParam(":empresa", $empresa);
            $stmt->bindParam(":CNPJ", $CNPJ);
            $stmt->bindParam(":razao_social", $razao_social);
            $stmt->bindParam(":nome_vendedor", $nome_vendedor);
            $stmt->bindParam(":telefone", $telefone);
            $stmt->bindParam(":email", $email);
            $stmt->execute();


            // Exibir popup antes do redirecionamento
            echo
            "<script>
                alert('fornecedor cadastrado com sucesso!');
                window.location.href = '" . $_SERVER['PHP_SELF'] . "';
            </script>";
            exit();
        } catch (PDOException $e) {
            $mensagem = "Erro ao cadastrar fornecedor: " . $e->getMessage();
        }
    }
}
?>