<?php

session_start();

require "conexao.php";

/** @var mysqli $conexao */

/* =====================================================
   RECEBE OS DADOS DO FORMULÁRIO
===================================================== */

$email = isset($_POST["email"]) ? trim($_POST["email"]) : "";
$senha_digitada = isset($_POST["senha"]) ? $_POST["senha"] : "";

if (empty($email) || empty($senha_digitada)) {
    header("Location: ../pages/login.php?erro=1");
    exit;
}

/* =====================================================
   BUSCA O ALUNO NO BANCO (com escape de segurança)
===================================================== */

$email_escapado = mysqli_real_escape_string($conexao, $email);
$sql = "SELECT * FROM aluno WHERE email = '$email_escapado'";

$resultado = mysqli_query($conexao, $sql);

if (!$resultado) {
    die("Erro na consulta: " . mysqli_error($conexao));
}

$aluno = mysqli_fetch_assoc($resultado);

/* =====================================================
   VERIFICAÇÃO DE SENHA (Texto simples ou Hash)
===================================================== */

$senha_correta = false;

if ($aluno) {
    if ($senha_digitada === $aluno["senha"]) {
        $senha_correta = true;
    } elseif (function_exists('password_verify') && password_verify($senha_digitada, $aluno["senha"])) {
        $senha_correta = true;
    }
}

if ($senha_correta) {

    /* =================================================
       CRIA A SESSÃO DO ALUNO
    ================================================= */

    session_regenerate_id(true);

    $_SESSION["usuario_id"]    = $aluno["matricula"];
    $_SESSION["usuario_nome"]  = $aluno["nome"];
    $_SESSION["usuario_email"] = $aluno["email"];
    $_SESSION["tipo_usuario"]  = "aluno";

    mysqli_close($conexao);

    /* =================================================
       INICIA A SESSÃO E REDIRECIONA DIRETO PARA A HOME
    ================================================= */
    header("Location: ../index.php");
    exit;

} else {

    /* =================================================
       ERRO NO LOGIN: REDIRECIONA DE VOLTA PARA O LOGIN
    ================================================= */
    mysqli_close($conexao);
    header("Location: ../pages/login.php?erro=1");
    exit;
}
?>