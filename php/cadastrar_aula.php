<?php
require_once "verifica_adm.php";
require_once "conexao.php";

/** @var mysqli $conexao */

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../admin/cursos.php");
    exit;
}

$curso_id  = isset($_POST["curso_id"]) ? (int)$_POST["curso_id"] : 0;
$titulo    = isset($_POST["titulo"]) ? trim($_POST["titulo"]) : "";
$descricao = isset($_POST["descricao"]) ? trim($_POST["descricao"]) : "";
$url       = isset($_POST["url"]) ? trim($_POST["url"]) : "";
$url_imagem= isset($_POST["url_imagem"]) ? trim($_POST["url_imagem"]) : "";

if ($curso_id <= 0 || empty($titulo)) {
    header("Location: ../admin/gerenciar_aulas.php?curso_id=$curso_id&erro=campos_obrigatorios");
    exit;
}

$titulo_esc    = mysqli_real_escape_string($conexao, $titulo);
$descricao_esc = mysqli_real_escape_string($conexao, $descricao);
$url_esc       = mysqli_real_escape_string($conexao, $url);

// Insere a aula na tabela aula
$sql_aula = "INSERT INTO aula (curso_id, titulo, descricao, url) VALUES ($curso_id, '$titulo_esc', '$descricao_esc', '$url_esc')";
$res_aula = mysqli_query($conexao, $sql_aula);

if ($res_aula) {
    $aula_id = mysqli_insert_id($conexao);

    // Se informou imagem de apoio, insere na tabela imagem_aula
    if (!empty($url_imagem)) {
        $img_esc = mysqli_real_escape_string($conexao, $url_imagem);
        mysqli_query($conexao, "INSERT INTO imagem_aula (aula_id, url_imagem) VALUES ($aula_id, '$img_esc')");
    }

    header("Location: ../admin/gerenciar_aulas.php?curso_id=$curso_id&sucesso=cadastrada");
    exit;
} else {
    $erro = urlencode(mysqli_error($conexao));
    header("Location: ../admin/gerenciar_aulas.php?curso_id=$curso_id&erro=$erro");
    exit;
}
