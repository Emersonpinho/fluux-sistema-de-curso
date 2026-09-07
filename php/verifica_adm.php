<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* =====================================================
   VERIFICAÇÃO DE SEGURANÇA (MIDDLEWARE)
   Garante que apenas administradores autenticados acessem
===================================================== */

if (!isset($_SESSION["usuario_id"]) || !isset($_SESSION["tipo_usuario"]) || $_SESSION["tipo_usuario"] !== "adm") {
    // Redireciona para o login do admin
    header("Location: login.php");
    exit;
}
?>
