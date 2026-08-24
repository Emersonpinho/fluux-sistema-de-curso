<?php

$servidor = "localhost";
$usuario = "root";
$senha = "usbw";
$banco = "fluux";

$conexao = mysqli_connect($servidor, $usuario, $senha, $banco);

if (!$conexao) {
    die("Erro ao conectar com servidor: " . mysqli_connect_error());
}

?>