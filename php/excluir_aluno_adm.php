<?php

require_once "verifica_adm.php";
require_once "conexao.php";

/** @var mysqli $conexao */

$matricula = isset($_GET["matricula"]) ? (int)$_GET["matricula"] : 0;

if ($matricula <= 0) {
    header("Location: ../admin/alunos.php?erro=matricula_invalida");
    exit;
}

/* =====================================================
   EXCLUSÃO SEGURA:
   1. Remove matrículas do aluno (matricula_curso)
   2. Remove cursos salvos nos favoritos (curso_salvo)
   3. Remove o registro principal da tabela aluno
===================================================== */

// 1. Remove matrículas
mysqli_query($conexao, "DELETE FROM matricula_curso WHERE aluno_matricula = $matricula");

// 2. Remove cursos salvos
mysqli_query($conexao, "DELETE FROM curso_salvo WHERE aluno_matricula = $matricula");

// 3. Remove o aluno
$resultado = mysqli_query($conexao, "DELETE FROM aluno WHERE matricula = $matricula");

if ($resultado) {
    mysqli_close($conexao);
    header("Location: ../admin/alunos.php?sucesso=excluido");
    exit;
} else {
    $erro_msg = mysqli_error($conexao);
    mysqli_close($conexao);
    header("Location: ../admin/alunos.php?erro=sql&msg=" . urlencode($erro_msg));
    exit;
}
?>
