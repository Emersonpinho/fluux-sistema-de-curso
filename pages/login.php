<?php
session_start();

// Se o usuário já estiver logado, redireciona para a home
if (isset($_SESSION["usuario_id"])) {
    if (isset($_SESSION["tipo_usuario"]) && $_SESSION["tipo_usuario"] === "adm") {
        header("Location: ../admin/index.php");
    } else {
        header("Location: ../index.php");
    }
    exit;
}

$erro = isset($_GET["erro"]) ? $_GET["erro"] : null;
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entrar - Fluux</title>
    <link rel="stylesheet" href="../css/login.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&family=Sora:wght@600;700;800&display=swap" rel="stylesheet">
    <style>
        .msg-erro-animada {
            display: flex;
            align-items: center;
            gap: 10px;
            background: #fef2f2;
            color: #dc2626;
            border: 1px solid #fecaca;
            padding: 12px 16px;
            border-radius: 12px;
            font-size: 0.88rem;
            font-weight: 500;
            margin-top: 18px;
            animation: surgirErro 0.3s cubic-bezier(0.4, 0, 0.2, 1) forwards;
        }

        @keyframes surgirErro {
            from { opacity: 0; transform: translateY(-8px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .msg-erro-animada svg {
            flex-shrink: 0;
        }
    </style>
</head>

<body>

    <div class="auth-topo">
        <a class="logo" href="../index.php">
            <img src="../assets/images/logos/logo fluux BRANCA.png" alt="Fluux" class="logo-img">
        </a>
    </div>

    <div class="auth-wrapper">
        <div class="auth-card">

            <span class="auth-eyebrow">Bem-vindo de volta</span>
            <h1>Login do aluno</h1>
            <span class="subtitulo">É bom ter você conosco novamente!</span>

            <?php if ($erro): ?>
                <div class="msg-erro-animada">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                    <span>E-mail ou senha incorretos. Verifique e tente de novo.</span>
                </div>
            <?php endif; ?>
            
            <form action="../php/login.php" method="post">

                <label for="email">Email</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    placeholder="seuemail@exemplo.com" 
                    required 
                    autofocus
                >

                <label for="senha">Senha</label>
                <div class="campo-senha">
                    <input 
                        type="password" 
                        id="senha" 
                        name="senha" 
                        placeholder="Digite sua senha" 
                        required
                    >
                </div>

                <div class="opcoes">
                    <label>
                        <input type="checkbox" name="lembrar">
                        Lembrar de mim
                    </label>
                    <a class="link-secundario" href="#">Esqueceu a senha?</a>
                </div>

                <button type="submit" class="btn-entrar">Entrar</button>

            </form>

            <p class="auth-rodape">
                Não tem uma conta?
                <a href="cadastro.html">Crie uma!</a>
            </p>

            <p class="auth-voltar">
                <a href="../index.php">&larr; Voltar para os cursos</a>
            </p>

            <p style="text-align: center; margin-top: 18px; font-size: 0.8rem; border-top: 1px solid #eef2f6; padding-top: 14px;">
                <a href="../admin/login.php" style="color: #888; text-decoration: none;">🔒 Acesso Administrativo</a>
            </p>

        </div>
    </div>

</body>
</html>
