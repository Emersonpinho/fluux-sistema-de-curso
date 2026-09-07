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
    header("Location: ../admin/login.php?erro=campos_vazios");
    exit;
}

/* =====================================================
   BUSCA O ADMINISTRADOR (com escape contra SQL Injection)
===================================================== */

$email_escapado = mysqli_real_escape_string($conexao, $email);
$sql = "SELECT * FROM adm WHERE email = '$email_escapado'";

$resultado = mysqli_query($conexao, $sql);

if (!$resultado) {
    die("Erro na consulta: " . mysqli_error($conexao));
}

$adm = mysqli_fetch_assoc($resultado);

/* =====================================================
   VERIFICA LOGIN (Compatível com PHP 5.4+)
===================================================== */

$senha_valida = false;

if ($adm) {
    if ($senha_digitada === $adm["senha"]) {
        $senha_valida = true;
    } elseif (function_exists('password_verify') && password_verify($senha_digitada, $adm["senha"])) {
        $senha_valida = true;
    }
}

if ($senha_valida) {

    /* =================================================
       CRIA A SESSÃO DO ADMINISTRADOR
    ================================================= */

    session_regenerate_id(true);

    $_SESSION["usuario_id"]    = $adm["id"];
    $_SESSION["usuario_nome"]  = $adm["nome"];
    $_SESSION["usuario_email"] = $adm["email"];
    $_SESSION["tipo_usuario"]  = "adm";

    mysqli_close($conexao);

    /* =================================================
       REDIRECIONA PARA O PAINEL ADMINISTRATIVO
    ================================================= */
    header("Location: ../admin/index.php");
    exit;

} else {

    /* =================================================
       ERRO NO LOGIN
    ================================================= */
    mysqli_close($conexao);
    header("Location: ../admin/login.php?erro=invalido");
    exit;
}
?>
