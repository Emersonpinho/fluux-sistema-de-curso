<?php

require_once "verifica_adm.php";
require_once "conexao.php";

/** @var mysqli $conexao */

/* =====================================================
   RECEBE OS DADOS DO FORMULÁRIO DE CADASTRO DE ALUNO
===================================================== */

$nome     = isset($_POST["nome"]) ? trim($_POST["nome"]) : "";
$cpf      = isset($_POST["cpf"]) ? preg_replace('/[^0-9]/', '', $_POST["cpf"]) : "";
$email    = isset($_POST["email"]) ? trim($_POST["email"]) : "";
$senha    = isset($_POST["senha"]) ? $_POST["senha"] : "";
$nivel_id = isset($_POST["nivel_id"]) ? (int)$_POST["nivel_id"] : 1;

if (empty($nome) || strlen($cpf) !== 11 || empty($email) || empty($senha)) {
    header("Location: ../admin/adicionar_aluno.php?erro=dados_invalidos");
    exit;
}

/* =====================================================
   GARANTE QUE OS NÍVEIS EXISTAM NO BANCO
===================================================== */
mysqli_query($conexao, "INSERT IGNORE INTO nivel (id, nome) VALUES (1, 'Iniciante'), (2, 'Intermediário'), (3, 'Avançado')");

/* =====================================================
   VERIFICA SE CPF OU E-MAIL JÁ EXISTEM
===================================================== */
$cpf_esc   = mysqli_real_escape_string($conexao, $cpf);
$email_esc = mysqli_real_escape_string($conexao, $email);

$check_sql = "SELECT matricula FROM aluno WHERE cpf = '$cpf_esc' OR email = '$email_esc'";
$check_res = mysqli_query($conexao, $check_sql);

if ($check_res && mysqli_num_rows($check_res) > 0) {
    header("Location: ../admin/adicionar_aluno.php?erro=duplicado");
    exit;
}

/* =====================================================
   INSERE O ALUNO NO BANCO
===================================================== */
$nome_esc  = mysqli_real_escape_string($conexao, $nome);
$senha_esc = mysqli_real_escape_string($conexao, $senha);

$sql = "
    INSERT INTO aluno (nome, cpf, email, senha, nivel_id)
    VALUES ('$nome_esc', '$cpf_esc', '$email_esc', '$senha_esc', $nivel_id)
";

$resultado = mysqli_query($conexao, $sql);

if ($resultado) {
    $nova_matricula = mysqli_insert_id($conexao);
    mysqli_close($conexao);
    header("Location: ../admin/alunos.php?sucesso=cadastrado");
    exit;
} else {
    $erro_msg = mysqli_error($conexao);
    mysqli_close($conexao);
    header("Location: ../admin/adicionar_aluno.php?erro=sql&msg=" . urlencode($erro_msg));
    exit;
}
?>
