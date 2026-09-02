<?php

require_once "verifica_adm.php";
require_once "conexao.php";

/** @var mysqli $conexao */

/* =====================================================
   RECEBE OS DADOS DO FORMULÁRIO DE EDIÇÃO DE ALUNO
===================================================== */

$matricula = isset($_POST["matricula"]) ? (int)$_POST["matricula"] : 0;
$nome      = isset($_POST["nome"]) ? trim($_POST["nome"]) : "";
$cpf       = isset($_POST["cpf"]) ? preg_replace('/[^0-9]/', '', $_POST["cpf"]) : "";
$email     = isset($_POST["email"]) ? trim($_POST["email"]) : "";
$senha     = isset($_POST["senha"]) ? $_POST["senha"] : "";
$nivel_id  = isset($_POST["nivel_id"]) ? (int)$_POST["nivel_id"] : 1;

if ($matricula <= 0 || empty($nome) || strlen($cpf) !== 11 || empty($email)) {
    header("Location: ../admin/editar_aluno.php?matricula=$matricula&erro=dados_invalidos");
    exit;
}

/* =====================================================
   VERIFICA SE CPF OU E-MAIL PERTENCEM A OUTRO ALUNO
===================================================== */
$cpf_esc   = mysqli_real_escape_string($conexao, $cpf);
$email_esc = mysqli_real_escape_string($conexao, $email);

$check_sql = "SELECT matricula FROM aluno WHERE (cpf = '$cpf_esc' OR email = '$email_esc') AND matricula != $matricula";
$check_res = mysqli_query($conexao, $check_sql);

if ($check_res && mysqli_num_rows($check_res) > 0) {
    header("Location: ../admin/editar_aluno.php?matricula=$matricula&erro=duplicado");
    exit;
}

/* =====================================================
   ATUALIZA OS DADOS DO ALUNO
===================================================== */
$nome_esc = mysqli_real_escape_string($conexao, $nome);

if (!empty($senha)) {
    // Se digitou uma nova senha, atualiza a senha também
    $senha_esc = mysqli_real_escape_string($conexao, $senha);
    $sql = "
        UPDATE aluno 
        SET nome = '$nome_esc', cpf = '$cpf_esc', email = '$email_esc', senha = '$senha_esc', nivel_id = $nivel_id
        WHERE matricula = $matricula
    ";
} else {
    // Mantém a senha atual
    $sql = "
        UPDATE aluno 
        SET nome = '$nome_esc', cpf = '$cpf_esc', email = '$email_esc', nivel_id = $nivel_id
        WHERE matricula = $matricula
    ";
}

$resultado = mysqli_query($conexao, $sql);

if ($resultado) {
    mysqli_close($conexao);
    header("Location: ../admin/detalhes_aluno.php?matricula=$matricula&sucesso=editado");
    exit;
} else {
    $erro_msg = mysqli_error($conexao);
    mysqli_close($conexao);
    header("Location: ../admin/editar_aluno.php?matricula=$matricula&erro=sql&msg=" . urlencode($erro_msg));
    exit;
}
?>
