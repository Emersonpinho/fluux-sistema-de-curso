<?php
require_once "../php/verifica_adm.php";
require_once "../php/conexao.php";

/** @var mysqli $conexao */

$matricula = isset($_GET["matricula"]) ? (int)$_GET["matricula"] : 0;

if ($matricula <= 0) {
    header("Location: alunos.php");
    exit;
}

// Busca o aluno
$sql = "SELECT * FROM aluno WHERE matricula = $matricula";
$res = mysqli_query($conexao, $sql);

if (!$res || mysqli_num_rows($res) === 0) {
    header("Location: alunos.php?erro=aluno_nao_encontrado");
    exit;
}

$aluno = mysqli_fetch_assoc($res);

// Busca níveis
$res_niveis = mysqli_query($conexao, "SELECT * FROM nivel ORDER BY id ASC");

$nome_adm = isset($_SESSION["usuario_nome"]) ? $_SESSION["usuario_nome"] : "Administrador";
$id_adm = isset($_SESSION["usuario_id"]) ? $_SESSION["usuario_id"] : "ADM";
$primeira_letra = !empty($nome_adm) ? strtoupper(substr($nome_adm, 0, 1)) : "A";

$erro = isset($_GET["erro"]) ? $_GET["erro"] : null;

// Formata CPF para exibição inicial no campo
function formatarCPF($cpf) {
    $cpf_limpo = preg_replace('/[^0-9]/', '', $cpf);
    if (strlen($cpf_limpo) === 11) {
        return substr($cpf_limpo, 0, 3) . '.' .
               substr($cpf_limpo, 3, 3) . '.' .
               substr($cpf_limpo, 6, 3) . '-' .
               substr($cpf_limpo, 9, 2);
    }
    return $cpf;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Aluno - <?= htmlspecialchars($aluno["nome"]) ?> - Fluux</title>
    
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

        .dica-campo {
            font-size: 0.78rem;
            color: #64748b;
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
                    <h1>Editar Aluno</h1>
                    <p>Atualize os dados de cadastro da matrícula <strong>#<?= str_pad($aluno["matricula"], 4, '0', STR_PAD_LEFT) ?></strong>.</p>
                </div>
                <div class="dashboard-actions">
                    <a href="detalhes_aluno.php?matricula=<?= $aluno["matricula"] ?>" class="btn-admin-secondary">
                        &larr; Voltar para Detalhes
                    </a>
                </div>
            </header>

            <?php if ($erro === "duplicado"): ?>
                <div style="background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; padding: 14px 18px; border-radius: 12px; margin-bottom: 20px; font-weight: 500;">
                    Já existe outro aluno cadastrado com este CPF ou E-mail.
                </div>
            <?php elseif ($erro): ?>
                <div style="background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; padding: 14px 18px; border-radius: 12px; margin-bottom: 20px; font-weight: 500;">
                    Erro ao atualizar aluno. Verifique se todos os campos foram preenchidos corretamente.
                </div>
            <?php endif; ?>

            <section class="panel-card">
                <div class="panel-card-header">
                    <h2 class="panel-card-title">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                        </svg>
                        Editar Informações
                    </h2>
                </div>

                <div class="panel-card-body">
                    <form action="../php/editar_aluno_adm.php" method="post">
                        
                        <input type="hidden" name="matricula" value="<?= $aluno["matricula"] ?>">

                        <div class="form-grid">

                            <!-- Nome -->
                            <div class="form-group full-width">
                                <label for="nome" class="form-label">Nome Completo do Aluno *</label>
                                <input 
                                    type="text" 
                                    id="nome" 
                                    name="nome" 
                                    class="form-input" 
                                    value="<?= htmlspecialchars($aluno["nome"]) ?>" 
                                    required
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
                                    value="<?= htmlspecialchars(formatarCPF($aluno["cpf"])) ?>" 
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
                                    value="<?= htmlspecialchars($aluno["email"]) ?>" 
                                    required
                                >
                            </div>

                            <!-- Nova Senha (Opcional) -->
                            <div class="form-group">
                                <label for="senha" class="form-label">Nova Senha (Opcional)</label>
                                <input 
                                    type="password" 
                                    id="senha" 
                                    name="senha" 
                                    class="form-input" 
                                    placeholder="Deixe em branco para não alterar" 
                                    minlength="6"
                                >
                                <span class="dica-campo">Preencha somente se desejar redefinir a senha do aluno.</span>
                            </div>

                            <!-- Nível -->
                            <div class="form-group">
                                <label for="nivel_id" class="form-label">Nível de Entrada *</label>
                                <select id="nivel_id" name="nivel_id" class="form-select" required>
                                    <?php if ($res_niveis && mysqli_num_rows($res_niveis) > 0): ?>
                                        <?php while ($n = mysqli_fetch_assoc($res_niveis)): ?>
                                            <option value="<?= $n["id"] ?>" <?= ((int)$aluno["nivel_id"] === (int)$n["id"]) ? "selected" : "" ?>>
                                                <?= htmlspecialchars($n["nome"]) ?>
                                            </option>
                                        <?php endwhile; ?>
                                    <?php endif; ?>
                                </select>
                            </div>

                        </div>

                        <div class="form-actions">
                            <a href="detalhes_aluno.php?matricula=<?= $aluno["matricula"] ?>" class="btn-admin-secondary">Cancelar</a>
                            <button type="submit" class="btn-admin-primary">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                                    <polyline points="17 21 17 13 7 13 7 21"></polyline>
                                    <polyline points="7 3 7 8 15 8"></polyline>
                                </svg>
                                Salvar Alterações
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
