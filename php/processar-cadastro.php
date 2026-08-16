<?php
// Script de processamento do formulário de cadastro de aluno

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome  = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_SPECIAL_CHARS);
    $cpf   = filter_input(INPUT_POST, 'cpf', FILTER_SANITIZE_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $nivel = filter_input(INPUT_POST, 'nivel', FILTER_SANITIZE_SPECIAL_CHARS);
    $curso = filter_input(INPUT_POST, 'curso', FILTER_SANITIZE_SPECIAL_CHARS);

    ?>
    <!DOCTYPE html>
    <html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Cadastro Realizado - Fluux</title>
        <link rel="stylesheet" href="../css/cadastro.css">
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    </head>
    <body>
        <div class="auth-topo">
            <a class="logo" href="../index.html">
                <img src="../assets/images/logos/logo fluux BRANCA.png" alt="Fluux" class="logo-img">
            </a>
        </div>
        <div class="auth-wrapper">
            <div class="auth-card">
                <span class="auth-eyebrow">Sucesso!</span>
                <h1>Matrícula Realizada</h1>
                <p class="subtitulo">Bem-vindo(a), <strong><?php echo htmlspecialchars($nome); ?></strong>!</p>
                <p>Sua inscrição no curso <strong><?php echo htmlspecialchars($curso); ?></strong> foi processada com sucesso.</p>
                <div style="margin-top: 2rem;">
                    <a href="../pages/login.html" class="btn-entrar" style="display:inline-block; text-decoration:none; text-align:center;">Fazer Login</a>
                </div>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit;
} else {
    header('Location: ../pages/cadastro.html');
    exit;
}
