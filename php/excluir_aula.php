<?php
require_once "verifica_adm.php";
require_once "conexao.php";

/** @var mysqli $conexao */

$aula_id  = isset($_GET["id"]) ? (int)$_GET["id"] : 0;
$curso_id = isset($_GET["curso_id"]) ? (int)$_GET["curso_id"] : 0;

if ($aula_id <= 0) {
    header("Location: ../admin/cursos.php");
    exit;
}

// 1. Remove primeiro as imagens de apoio da tabela imagem_aula (chave estrangeira)
mysqli_query($conexao, "DELETE FROM imagem_aula WHERE aula_id = $aula_id");

// 2. Remove a aula da tabela aula
mysqli_query($conexao, "DELETE FROM aula WHERE id = $aula_id");

header("Location: ../admin/gerenciar_aulas.php?curso_id=$curso_id&sucesso=excluida");
exit;
