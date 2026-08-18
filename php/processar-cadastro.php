<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome  = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_SPECIAL_CHARS);
    $cpf   = filter_input(INPUT_POST, 'cpf', FILTER_SANITIZE_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $senha = isset($_POST['senha']) ? $_POST['senha'] : '';
    $confirmar_senha = isset($_POST['confirmar_senha']) ? $_POST['confirmar_senha'] : '';
    $nivel = filter_input(INPUT_POST, 'nivel', FILTER_SANITIZE_SPECIAL_CHARS);
    $curso = filter_input(INPUT_POST, 'curso', FILTER_SANITIZE_SPECIAL_CHARS);

    // ---- Validações básicas ----
    $erros = array();

    if (!$nome)  $erros[] = 'Nome é obrigatório.';
    if (!$cpf)   $erros[] = 'CPF é obrigatório.';
    if (!$email) $erros[] = 'Email inválido.';
    if (strlen($senha) < 6) $erros[] = 'A senha deve ter no mínimo 6 caracteres.';
    if ($senha !== $confirmar_senha) $erros[] = 'As senhas não coincidem.';

    if (!empty($erros)) {
        // Volta para o cadastro.html mostrando a mensagem de erro
        header('Location: ../pages/cadastro.html?erro=' . urlencode(implode(' ', $erros)));
        exit;
    }

    // ---- AQUI entra a sua lógica de banco de dados ----
    // Exemplo (adapte para o seu banco / PDO / mysqli):
    //
    // $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
    //
    // $stmt = $pdo->prepare(
    //     "INSERT INTO alunos (nome, cpf, email, senha, nivel, curso) VALUES (?, ?, ?, ?, ?, ?)"
    // );
    // $stmt->execute(array($nome, $cpf, $email, $senha_hash, $nivel, $curso));

    // ---- Redireciona para a página de sucesso já com os dados ----
    $dados = http_build_query(array(
        'nome'  => $nome,
        'email' => $email,
        'curso' => $curso,
        'nivel' => $nivel
    ));

    header('Location: ../pages/sucesso-cadastro.html?' . $dados);
    exit;

} else {
    header('Location: ../pages/cadastro.html');
    exit;
}