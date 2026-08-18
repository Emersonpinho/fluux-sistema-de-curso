<?php
// Script de processamento do formulário de login de aluno

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $senha = isset($_POST['senha']) ? $_POST['senha'] : '';

    // ---- Validações básicas ----
    if (!$email || $senha === '') {
        header('Location: ../pages/login.html?erro=' . urlencode('Preencha email e senha corretamente.'));
        exit;
    }

    // ---- AQUI entra a sua lógica de banco de dados ----
    // Exemplo (adapte para o seu banco / PDO / mysqli):
    //
    // $stmt = $pdo->prepare("SELECT * FROM alunos WHERE email = ?");
    // $stmt->execute(array($email));
    // $aluno = $stmt->fetch();
    //
    // if (!$aluno || !password_verify($senha, $aluno['senha'])) {
    //     header('Location: ../pages/login.html?erro=' . urlencode('Email ou senha incorretos.'));
    //     exit;
    // }
    //
    // $nome = $aluno['nome'];
    //
    // Opcional: iniciar sessão do aluno
    // session_start();
    // $_SESSION['aluno_id']   = $aluno['id'];
    // $_SESSION['aluno_nome'] = $aluno['nome'];

    // Enquanto não há banco de dados ligado, usamos o que veio do form:
    $partes_email = explode('@', $email);
    $nome = $partes_email[0]; // troque pelo nome real vindo do banco

    // ---- Redireciona para a página de sucesso já com os dados ----
    $dados = http_build_query(array(
        'nome'  => $nome,
        'email' => $email
    ));

    header('Location: ../pages/sucesso-login.html?' . $dados);
    exit;

} else {
    header('Location: ../pages/login.html');
    exit;
}