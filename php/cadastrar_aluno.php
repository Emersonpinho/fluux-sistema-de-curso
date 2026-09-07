<?php

require "conexao.php";

/** @var mysqli $conexao */

$nome = isset($_POST["nome"]) ? trim($_POST["nome"]) : "";
$cpf = isset($_POST["cpf"]) ? preg_replace('/[^0-9]/', '', $_POST["cpf"]) : "";
$email = isset($_POST["email"]) ? trim($_POST["email"]) : "";
$confirmar_email = isset($_POST["confirmar_email"]) ? trim($_POST["confirmar_email"]) : "";
$senha = isset($_POST["senha"]) ? $_POST["senha"] : "";
$confirmar_senha = isset($_POST["confirmar_senha"]) ? $_POST["confirmar_senha"] : "";
$nivel_id = isset($_POST["nivel_id"]) ? (int)$_POST["nivel_id"] : 1;

if (empty($nome) || empty($cpf) || empty($email) || empty($confirmar_email) || empty($senha) || empty($confirmar_senha)) {
    mysqli_close($conexao);
    header("Location: ../pages/cadastro.php?erro=campos");
    exit;
}

if (strcasecmp($email, $confirmar_email) !== 0) {
    mysqli_close($conexao);
    header("Location: ../pages/cadastro.php?erro=email");
    exit;
}

if ($senha !== $confirmar_senha) {
    mysqli_close($conexao);
    header("Location: ../pages/cadastro.php?erro=senha");
    exit;
}

/* =====================================================
   GARANTE QUE OS NÍVEIS EXISTAM AUTOMATICAMENTE
   Se a tabela nivel estiver vazia, cria os 3 níveis padrão
===================================================== */
mysqli_query($conexao, "INSERT IGNORE INTO nivel (id, nome) VALUES (1, 'Iniciante'), (2, 'Intermediário'), (3, 'Avançado')");

/* =====================================================
   INSERE O ALUNO NO BANCO
===================================================== */
$nome_escapado = mysqli_real_escape_string($conexao, $nome);
$email_escapado = mysqli_real_escape_string($conexao, $email);
$senha_escapada = mysqli_real_escape_string($conexao, $senha);

$sql = "INSERT INTO aluno (nome, cpf, email, senha, nivel_id) VALUES ('$nome_escapado', '$cpf', '$email_escapado', '$senha_escapada', $nivel_id)";

$resultado = mysqli_query($conexao, $sql);

if ($resultado) {
    mysqli_close($conexao);
    header("Location: ../pages/sucesso_cadastro.html");
    exit;
} else {
    $erro_cod = mysqli_errno($conexao);
    mysqli_close($conexao);
    if ($erro_cod == 1062) {
        header("Location: ../pages/cadastro.php?erro=duplicado");
    } else {
        header("Location: ../pages/cadastro.php?erro=1");
    }
    exit;
}

?>