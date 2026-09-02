<?php

require_once "verifica_adm.php";
require_once "conexao.php";

/** @var mysqli $conexao */

/* =====================================================
   RECEBE OS DADOS DO FORMULÁRIO DE CURSO
===================================================== */

$nome     = isset($_POST["nome"]) ? trim($_POST["nome"]) : "";
$nivel_id = isset($_POST["nivel_id"]) ? (int)$_POST["nivel_id"] : 1;
$duracao  = isset($_POST["duracao"]) ? (int)$_POST["duracao"] : 0;
$status   = isset($_POST["status"]) ? trim($_POST["status"]) : "ativo";
$ementa   = isset($_POST["ementa"]) ? trim($_POST["ementa"]) : "";

$adm_id   = isset($_SESSION["usuario_id"]) ? $_SESSION["usuario_id"] : "ADM01";

if (empty($nome) || $duracao <= 0) {
    header("Location: ../admin/adicionar_curso.php?erro=campos_invalidos");
    exit;
}

/* =====================================================
   INSERE O CURSO NO BANCO DE DADOS
===================================================== */

$nome_esc   = mysqli_real_escape_string($conexao, $nome);
$status_esc = mysqli_real_escape_string($conexao, $status);
$ementa_esc = mysqli_real_escape_string($conexao, $ementa);
$adm_id_esc = mysqli_real_escape_string($conexao, $adm_id);

$sql = "
    INSERT INTO curso (adm_id, nome, status, ementa, nivel_id, duracao)
    VALUES ('$adm_id_esc', '$nome_esc', '$status_esc', '$ementa_esc', $nivel_id, $duracao)
";

$resultado = mysqli_query($conexao, $sql);

if ($resultado) {
    mysqli_close($conexao);
    header("Location: ../admin/cursos.php?sucesso=cadastrado");
    exit;
} else {
    $erro_msg = mysqli_error($conexao);
    mysqli_close($conexao);
    header("Location: ../admin/adicionar_curso.php?erro=sql&msg=" . urlencode($erro_msg));
    exit;
}
?>
