<?php

session_start();

// Limpa todas as informações da sessão
$_SESSION = [];

// Destrói a sessão
session_destroy();

// Volta para a página inicial
header("Location: ../index.php");
exit;

?>