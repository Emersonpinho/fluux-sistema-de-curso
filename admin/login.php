<?php
session_start();

// Se já estiver logado como administrador, redireciona direto para o painel
if (isset($_SESSION["usuario_id"]) && isset($_SESSION["tipo_usuario"]) && $_SESSION["tipo_usuario"] === "adm") {
    header("Location: index.php");
    exit;
}

$erro = isset($_GET["erro"]) ? $_GET["erro"] : null;
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Administrativo - Fluux</title>
    <link rel="stylesheet" href="../css/login.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&family=Sora:wght@600;700;800&display=swap" rel="stylesheet">
    <style>
        .badge-adm {
            background: rgba(198, 255, 92, 0.18);
            color: #101c3d;
            border: 1px solid rgba(198, 255, 92, 0.5);
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.76rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            padding: 6px 14px;
            border-radius: 999px;
            margin-bottom: 14px;
        }
        .btn-adm {
            background: var(--lima);
            color: var(--navy);
            box-shadow: 0 4px 15px rgba(198, 255, 92, 0.25);
        }
        .btn-adm:hover {
            background: #d8ff8a;
            box-shadow: 0 10px 25px rgba(198, 255, 92, 0.4);
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

            <span class="badge-adm">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                </svg>
                Área Restrita
            </span>

            <h1>Login do Administrador</h1>
            <span class="subtitulo">Acesse o painel de gerenciamento do Fluux</span>

            <?php if ($erro === "invalido"): ?>
                <div class="msg-erro">
                    E-mail ou senha de administrador incorretos.
                </div>
            <?php elseif ($erro === "campos_vazios"): ?>
                <div class="msg-erro">
                    Por favor, preencha todos os campos.
                </div>
            <?php endif; ?>
            
            <form action="../php/login_adm.php" method="post">

                <label for="email">E-mail do Administrador</label>
                <input type="email" id="email" name="email" placeholder="adm@fluux.com" required autofocus>

                <label for="senha">Senha</label>
                <div class="campo-senha">
                    <input type="password" id="senha" name="senha" placeholder="Digite sua senha de acesso" required>
                </div>

                <div class="opcoes">
                    <label>
                        <input type="checkbox" name="lembrar">
                        Manter conectado
                    </label>
                </div>

                <button type="submit" class="btn-entrar btn-adm">Entrar no Painel</button>

            </form>

            <p class="auth-voltar">
                <a href="../index.php">&larr; Voltar para o portal de cursos</a>
            </p>

        </div>
    </div>

    <footer class="site-footer">
        &copy; <?= date('Y'); ?> Fluux. Todos os direitos reservados.
    </footer>

</body>
</html>
