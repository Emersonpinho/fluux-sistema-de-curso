<?php

require "conexao.php";

/** @var mysqli $conexao */

$nome = $_POST["nome"];
$cpf = $_POST["cpf"];
$email = $_POST["email"];
$senha = $_POST["senha"];
$nivel = $_POST["nivel"];

$sql = "insert into aluno (nome, cpf, email, senha, nivel) values ('$nome', '$cpf', '$email', '$senha', '$nivel')";

$resultado = mysqli_query($conexao, $sql);

if ($resultado) {
    echo "Cadastro realizado com sucesso!";
} else {
    echo "Erro ao cadastrar aluno: " . mysqli_error($conexao);
}

?>