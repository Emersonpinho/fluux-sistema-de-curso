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
$msg_erro = "";
if ($erro === "senha") {
    $msg_erro = "As senhas não coincidem. Verifique e tente de novo.";
} elseif ($erro === "email") {
    $msg_erro = "Os emails não coincidem. Verifique e tente de novo.";
} elseif ($erro === "duplicado") {
    $msg_erro = "E-mail ou CPF já cadastrado no sistema.";
} elseif ($erro === "campos") {
    $msg_erro = "Preencha todos os campos obrigatórios.";
} elseif ($erro) {
    $msg_erro = "Ocorreu um erro ao realizar o cadastro. Tente novamente.";
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Aluno - Fluux</title>
    <link rel="stylesheet" href="../css/cadastro.css">

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

        .campo-erro {
            border-color: #dc2626 !important;
            box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.15) !important;
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
        <div class="auth-card auth-card-larga">

            <span class="auth-eyebrow">Comece agora</span>
            <h1>Cadastro de aluno</h1>
            <span class="subtitulo">Vamos começar sua jornada com a gente!</span>

            <div 
                class="msg-erro-animada" 
                id="alerta-erro" 
                style="<?= !empty($msg_erro) ? 'display: flex;' : 'display: none;' ?>"
            >
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
                <span id="alerta-erro-texto"><?= htmlspecialchars($msg_erro) ?></span>
            </div>

            <form id="form-cadastro" action="../php/cadastrar_aluno.php" method="post">

                <label for="nome">Nome do aluno</label>
                <input 
                    type="text" 
                    id="nome" 
                    name="nome" 
                    placeholder="Digite seu nome" 
                    required
                    autofocus
                >

                <div class="linha-dupla">
                    <div>
                        <label for="cpf">CPF</label>
                        <input 
                            type="text" 
                            id="cpf" 
                            name="cpf" 
                            placeholder="000.000.000-00" 
                            inputmode="numeric"
                            autocomplete="off"
                            required
                        >
                    </div>

                    <div>
                        <label for="nivel">Nível</label>
                        <select id="nivel" name="nivel_id">
                            <option value="1">Iniciante</option>
                            <option value="2">Intermediário</option>
                            <option value="3">Avançado</option>
                        </select>
                    </div>
                </div>

                <div class="linha-dupla">
                    <div>
                        <label for="email">Email</label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            placeholder="seuemail@exemplo.com" 
                            required
                        >
                    </div>

                    <div>
                        <label for="confirmar_email">Confirmar email</label>
                        <input 
                            type="email" 
                            id="confirmar_email" 
                            name="confirmar_email" 
                            placeholder="Repita o seu email" 
                            required
                        >
                    </div>
                </div>

                <div class="linha-dupla">
                    <div>
                        <label for="senha">Senha</label>
                        <input 
                            type="password" 
                            id="senha" 
                            name="senha" 
                            placeholder="Mínimo 6 caracteres" 
                            minlength="6" 
                            required
                        >
                    </div>

                    <div>
                        <label for="confirmar_senha">Confirmar senha</label>
                        <input 
                            type="password" 
                            id="confirmar_senha" 
                            name="confirmar_senha" 
                            placeholder="Repita a senha" 
                            minlength="6" 
                            required
                        >
                    </div>
                </div>

                <button type="submit" class="btn-entrar">
                    Matricular
                </button>

            </form>

            <p class="auth-rodape">
                Já tem uma conta?
                <a href="login.php">Entrar</a>
            </p>

            <p class="auth-voltar">
                <a href="../index.php">&larr; Voltar para os cursos</a>
            </p>

        </div>
    </div>


    <script>
        // Máscara e validação do CPF
        const cpfInput = document.getElementById('cpf');

        cpfInput.addEventListener('input', function () {
            let valor = this.value.replace(/\D/g, '');
            valor = valor.substring(0, 11);

            if (valor.length > 9) {
                valor = valor.replace(
                    /^(\d{3})(\d{3})(\d{3})(\d{1,2})$/,
                    '$1.$2.$3-$4'
                );
            } else if (valor.length > 6) {
                valor = valor.replace(
                    /^(\d{3})(\d{3})(\d{1,3})$/,
                    '$1.$2.$3'
                );
            } else if (valor.length > 3) {
                valor = valor.replace(
                    /^(\d{3})(\d{1,3})$/,
                    '$1.$2'
                );
            }

            this.value = valor;
        });

        cpfInput.addEventListener('beforeinput', function (event) {
            if (event.data && /\D/.test(event.data)) {
                event.preventDefault();
                return;
            }

            const numeros = this.value.replace(/\D/g, '');
            if (
                event.inputType === 'insertText' &&
                numeros.length >= 11
            ) {
                event.preventDefault();
            }
        });

        // Validação instantânea de e-mail e senha no frontend
        const formCadastro = document.getElementById('form-cadastro');
        const emailInput = document.getElementById('email');
        const confirmarEmailInput = document.getElementById('confirmar_email');
        const senhaInput = document.getElementById('senha');
        const confirmarSenhaInput = document.getElementById('confirmar_senha');
        const alertaErro = document.getElementById('alerta-erro');
        const alertaErroTexto = document.getElementById('alerta-erro-texto');

        formCadastro.addEventListener('submit', function (event) {
            // Limpa erros visuais anteriores
            confirmarEmailInput.classList.remove('campo-erro');
            confirmarSenhaInput.classList.remove('campo-erro');

            // 1. Validação do e-mail / gmail
            if (emailInput.value.trim().toLowerCase() !== confirmarEmailInput.value.trim().toLowerCase()) {
                event.preventDefault();
                alertaErroTexto.textContent = "Os emails não coincidem. Verifique e tente de novo.";
                alertaErro.style.display = "flex";
                confirmarEmailInput.classList.add('campo-erro');
                confirmarEmailInput.focus();
                return false;
            }

            // 2. Validação da senha
            if (senhaInput.value !== confirmarSenhaInput.value) {
                event.preventDefault();
                alertaErroTexto.textContent = "As senhas não coincidem. Verifique e tente de novo.";
                alertaErro.style.display = "flex";
                confirmarSenhaInput.classList.add('campo-erro');
                confirmarSenhaInput.focus();
                return false;
            }
        });

        // Verificação dinâmica ao digitar no e-mail
        function checarEmailsAoDigitar() {
            if (confirmarEmailInput.value.trim().toLowerCase() === emailInput.value.trim().toLowerCase()) {
                confirmarEmailInput.classList.remove('campo-erro');
                if (alertaErroTexto.textContent.toLowerCase().includes("email")) {
                    alertaErro.style.display = "none";
                }
            }
        }

        confirmarEmailInput.addEventListener('input', checarEmailsAoDigitar);
        emailInput.addEventListener('input', function () {
            if (confirmarEmailInput.value.length > 0) {
                checarEmailsAoDigitar();
            }
        });

        // Verificação dinâmica ao digitar na senha
        function checarSenhasAoDigitar() {
            if (confirmarSenhaInput.value === senhaInput.value) {
                confirmarSenhaInput.classList.remove('campo-erro');
                if (alertaErroTexto.textContent.includes("senha")) {
                    alertaErro.style.display = "none";
                }
            }
        }

        confirmarSenhaInput.addEventListener('input', checarSenhasAoDigitar);
        senhaInput.addEventListener('input', function () {
            if (confirmarSenhaInput.value.length > 0) {
                checarSenhasAoDigitar();
            }
        });
    </script>

</body>
</html>
