<?php

require "conexao.php";

/** @var mysqli $conexao */

$nome = $_POST["nome"];

$cpf = preg_replace('/[^0-9]/', '', $_POST["cpf"]);

$email = $_POST["email"];
$senha = $_POST["senha"];
$confirmar_senha = $_POST["confirmar_senha"];

if ($senha !== $confirmar_senha) {
    die("As senhas não coincidem!");
}

$nivel = $_POST["nivel"];

$sql = "insert into aluno (nome, cpf, email, senha, nivel) values ('$nome', '$cpf', '$email', '$senha', '$nivel')";

$resultado = mysqli_query($conexao, $sql);

if ($resultado) {
    header("Location: ../pages/cadastro_sucesso.html");
    exit;
} else {
    echo "Erro ao cadastrar aluno: " . mysqli_error($conexao);
};

mysqli_close($conexao);

?>