<?php
require_once "../php/verifica_adm.php";
require_once "../php/conexao.php";

/** @var mysqli $conexao */

// Busca os níveis disponíveis no banco
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
    <title>Adicionar Novo Curso - Painel Fluux</title>
    
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
        .form-select,
        .form-textarea {
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
        .form-select:focus,
        .form-textarea:focus {
            outline: none;
            border-color: var(--azul-medio);
            box-shadow: 0 0 0 3px rgba(47, 137, 232, 0.15);
        }

        .form-textarea {
            min-height: 120px;
            resize: vertical;
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
                <a href="cursos.php" class="active">Cursos</a>
                <a href="alunos.php">Alunos</a>
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
                    <h1>Novo Curso</h1>
                    <p>Cadastre um novo curso para disponibilizar aos alunos na plataforma.</p>
                </div>
                <div class="dashboard-actions">
                    <a href="cursos.php" class="btn-admin-secondary">
                        &larr; Voltar para Cursos
                    </a>
                </div>
            </header>

            <?php if ($erro): ?>
                <div style="background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; padding: 14px 18px; border-radius: 12px; margin-bottom: 20px; font-weight: 500;">
                    Erro ao cadastrar curso. Verifique se todos os campos foram preenchidos corretamente.
                </div>
            <?php endif; ?>

            <section class="panel-card">
                <div class="panel-card-header">
                    <h2 class="panel-card-title">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 20h9"></path>
                            <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
                        </svg>
                        Informações do Curso
                    </h2>
                </div>

                <div class="panel-card-body">
                    <form action="../php/cadastrar_curso.php" method="post">
                        
                        <div class="form-grid">

                            <!-- Nome do Curso -->
                            <div class="form-group full-width">
                                <label for="nome" class="form-label">Nome do Curso *</label>
                                <input 
                                    type="text" 
                                    id="nome" 
                                    name="nome" 
                                    class="form-input" 
                                    placeholder="Ex: Inteligência Artificial na Prática" 
                                    required 
                                    autofocus
                                >
                            </div>

                            <!-- Nível -->
                            <div class="form-group">
                                <label for="nivel_id" class="form-label">Nível de Dificuldade *</label>
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

                            <!-- Duração em Horas -->
                            <div class="form-group">
                                <label for="duracao" class="form-label">Carga Horária (horas) *</label>
                                <input 
                                    type="number" 
                                    id="duracao" 
                                    name="duracao" 
                                    class="form-input" 
                                    placeholder="Ex: 40" 
                                    min="1" 
                                    required
                                >
                            </div>

                            <!-- Status -->
                            <div class="form-group">
                                <label for="status" class="form-label">Status do Curso *</label>
                                <select id="status" name="status" class="form-select">
                                    <option value="ativo" selected>Ativo (Disponível aos alunos)</option>
                                    <option value="inativo">Inativo (Rascunho)</option>
                                </select>
                            </div>

                            <!-- Ementa / Descrição -->
                            <div class="form-group full-width">
                                <label for="ementa" class="form-label">Ementa / Descrição do Conteúdo</label>
                                <textarea 
                                    id="ementa" 
                                    name="ementa" 
                                    class="form-textarea" 
                                    placeholder="Escreva um resumo do que o aluno irá aprender neste curso..."
                                ></textarea>
                            </div>

                        </div>

                        <div class="form-actions">
                            <a href="cursos.php" class="btn-admin-secondary">Cancelar</a>
                            <button type="submit" class="btn-admin-primary">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                                    <polyline points="17 21 17 13 7 13 7 21"></polyline>
                                    <polyline points="7 3 7 8 15 8"></polyline>
                                </svg>
                                Salvar Curso
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

</body>
</html>
<?php
mysqli_close($conexao);
?>
