<?php
require_once "../php/verifica_adm.php";
require_once "../php/conexao.php";

/** @var mysqli $conexao */

// Busca níveis
$res_niveis = mysqli_query($conexao, "SELECT * FROM nivel ORDER BY id ASC");

$nome_adm = isset($_SESSION["usuario_nome"]) ? $_SESSION["usuario_nome"] : "Administrador";
$id_adm = isset($_SESSION["usuario_id"]) ? $_SESSION["usuario_id"] : "ADM";
$primeira_letra = !empty($nome_adm) ? strtoupper(substr($nome_adm, 0, 1)) : "A";

$erro = isset($_GET["erro"]) ? $_GET["erro"] : null;
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Aluno - Painel Fluux</title>
    
    <link rel="stylesheet" href="../css/admin.css">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&family=Sora:wght@600;700;800&display=swap" rel="stylesheet">
    <style>
        .form-container {
            max-width: 800px;
            margin: 0 auto;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-bottom: 20px;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        .form-label {
            font-size: 0.88rem;
            font-weight: 600;
            color: var(--cinza-titulo);
        }

        .form-input,
        .form-select {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid var(--borda);
            border-radius: var(--radius-md);
            font-size: 0.95rem;
            color: #334155;
            background: #ffffff;
            transition: all 0.2s var(--ease);
        }

        .form-input:focus,
        .form-select:focus {
            outline: none;
            border-color: var(--azul-medio);
            box-shadow: 0 0 0 3px rgba(47, 137, 232, 0.15);
        }

        .form-actions {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 14px;
            margin-top: 10px;
            padding-top: 20px;
            border-top: 1px solid var(--borda);
        }

        @media (max-width: 600px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
            .form-actions {
                flex-direction: column;
            }
            .form-actions button,
            .form-actions a {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>

<body>

    <!-- NAVBAR ADMINISTRATIVA -->
    <nav class="admin-navbar">
        <div class="admin-nav-container">
            <div class="admin-brand">
                <a href="index.php">
                    <img src="../assets/images/logos/logo fluux BRANCA.png" alt="Fluux Logo">
                </a>
                <span class="admin-tag">Administrador</span>
            </div>

            <div class="admin-nav-links">
                <a href="index.php">Dashboard</a>
                <a href="cursos.php">Cursos</a>
                <a href="alunos.php" class="active">Alunos</a>
                <a href="../index.php" target="_blank">Visualizar Portal ↗</a>
            </div>

            <div class="admin-nav-user">
                <div class="admin-avatar"><?= $primeira_letra ?></div>
                <div class="admin-user-info">
                    <span class="admin-user-name"><?= htmlspecialchars($nome_adm) ?></span>
                    <span class="admin-user-role"><?= htmlspecialchars($id_adm) ?></span>
                </div>
                <a href="../php/logout.php" class="btn-nav-sair">Sair</a>
            </div>
        </div>
    </nav>

    <!-- CONTEÚDO PRINCIPAL -->
    <main class="admin-main">

        <div class="form-container">

            <header class="dashboard-header" style="margin-bottom: 25px;">
                <div>
                    <h1>Novo Aluno</h1>
                    <p>Cadastre manualmente um novo aluno na plataforma Fluux.</p>
                </div>
                <div class="dashboard-actions">
                    <a href="alunos.php" class="btn-admin-secondary">
                        &larr; Voltar para Alunos
                    </a>
                </div>
            </header>

            <?php if ($erro === "duplicado"): ?>
                <div style="background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; padding: 14px 18px; border-radius: 12px; margin-bottom: 20px; font-weight: 500;">
                    Já existe um aluno cadastrado com este CPF ou E-mail.
                </div>
            <?php elseif ($erro): ?>
                <div style="background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; padding: 14px 18px; border-radius: 12px; margin-bottom: 20px; font-weight: 500;">
                    Erro ao cadastrar aluno. Verifique se todos os campos foram preenchidos corretamente.
                </div>
            <?php endif; ?>

            <section class="panel-card">
                <div class="panel-card-header">
                    <h2 class="panel-card-title">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <line x1="19" y1="8" x2="19" y2="14"></line>
                            <line x1="22" y1="11" x2="16" y2="11"></line>
                        </svg>
                        Dados Cadastrais do Aluno
                    </h2>
                </div>

                <div class="panel-card-body">
                    <form action="../php/cadastrar_aluno_adm.php" method="post">
                        
                        <div class="form-grid">

                            <!-- Nome -->
                            <div class="form-group full-width">
                                <label for="nome" class="form-label">Nome Completo do Aluno *</label>
                                <input 
                                    type="text" 
                                    id="nome" 
                                    name="nome" 
                                    class="form-input" 
                                    placeholder="Ex: João da Silva" 
                                    required 
                                    autofocus
                                >
                            </div>

                            <!-- CPF -->
                            <div class="form-group">
                                <label for="cpf" class="form-label">CPF *</label>
                                <input 
                                    type="text" 
                                    id="cpf" 
                                    name="cpf" 
                                    class="form-input" 
                                    placeholder="000.000.000-00" 
                                    inputmode="numeric"
                                    autocomplete="off"
                                    required
                                >
                            </div>

                            <!-- E-mail -->
                            <div class="form-group">
                                <label for="email" class="form-label">E-mail *</label>
                                <input 
                                    type="email" 
                                    id="email" 
                                    name="email" 
                                    class="form-input" 
                                    placeholder="aluno@exemplo.com" 
                                    required
                                >
                            </div>

                            <!-- Senha -->
                            <div class="form-group">
                                <label for="senha" class="form-label">Senha Inicial *</label>
                                <input 
                                    type="password" 
                                    id="senha" 
                                    name="senha" 
                                    class="form-input" 
                                    placeholder="Mínimo 6 caracteres" 
                                    minlength="6"
                                    required
                                >
                            </div>

                            <!-- Nível -->
                            <div class="form-group">
                                <label for="nivel_id" class="form-label">Nível de Entrada *</label>
                                <select id="nivel_id" name="nivel_id" class="form-select" required>
                                    <?php if ($res_niveis && mysqli_num_rows($res_niveis) > 0): ?>
                                        <?php while ($n = mysqli_fetch_assoc($res_niveis)): ?>
                                            <option value="<?= $n["id"] ?>"><?= htmlspecialchars($n["nome"]) ?></option>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <option value="1">Iniciante</option>
                                        <option value="2">Intermediário</option>
                                        <option value="3">Avançado</option>
                                    <?php endif; ?>
                                </select>
                            </div>

                        </div>

                        <div class="form-actions">
                            <a href="alunos.php" class="btn-admin-secondary">Cancelar</a>
                            <button type="submit" class="btn-admin-primary">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                                    <polyline points="17 21 17 13 7 13 7 21"></polyline>
                                    <polyline points="7 3 7 8 15 8"></polyline>
                                </svg>
                                Cadastrar Aluno
                            </button>
                        </div>

                    </form>
                </div>
            </section>

        </div>

    </main>

    <!-- FOOTER -->
    <footer class="admin-footer">
        &copy; <?= date('Y') ?> Fluux - Painel de Administração. Todos os direitos reservados.
    </footer>

    <!-- Máscara de CPF -->
    <script>
        const cpfInput = document.getElementById('cpf');
        cpfInput.addEventListener('input', function () {
            let valor = this.value.replace(/\D/g, '').substring(0, 11);
            if (valor.length > 9) {
                valor = valor.replace(/^(\d{3})(\d{3})(\d{3})(\d{1,2})$/, '$1.$2.$3-$4');
            } else if (valor.length > 6) {
                valor = valor.replace(/^(\d{3})(\d{3})(\d{1,3})$/, '$1.$2.$3');
            } else if (valor.length > 3) {
                valor = valor.replace(/^(\d{3})(\d{1,3})$/, '$1.$2');
            }
            this.value = valor;
        });
    </script>

</body>
</html>
<?php
mysqli_close($conexao);
?>
