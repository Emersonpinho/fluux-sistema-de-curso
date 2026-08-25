<?php

require "conexao.php";

/** @var mysqli $conexao */


$email = $_POST["email"];
$senha_digitada = $_POST["senha"];

$sql = "select * from aluno where email = '$email'";
$resultado = mysqli_query($conexao, $sql);
$aluno = mysqli_fetch_assoc($resultado);

if ($aluno && password_verify($senha_digitada, $aluno["senha"])) {
    // deu certo senha bate certim
} else {
    // aqui deu errado é logico
}

?>