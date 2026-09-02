<?php
require_once "../php/verifica_adm.php";
require_once "../php/conexao.php";
require_once "../php/seed_cursos.php";

/** @var mysqli $conexao */

$sucesso = isset($_GET["sucesso"]) ? $_GET["sucesso"] : null;

// Busca todos os cursos com os dados de nível e administrador
$query_cursos = "
    SELECT c.codigo, c.nome, c.status, c.duracao, c.ementa, c.adm_id,
           COALESCE(n.nome, 'Geral') AS nivel_nome,
           COALESCE(a.nome, 'Administrador') AS adm_nome
    FROM curso c
    LEFT JOIN nivel n ON c.nivel_id = n.id
    LEFT JOIN adm a ON c.adm_id = a.id
    ORDER BY c.codigo ASC
";
$resultado_cursos = mysqli_query($conexao, $query_cursos);
$total_cursos = ($resultado_cursos) ? mysqli_num_rows($resultado_cursos) : 0;

$nome_adm = isset($_SESSION["usuario_nome"]) ? $_SESSION["usuario_nome"] : "Administrador";
$id_adm = isset($_SESSION["usuario_id"]) ? $_SESSION["usuario_id"] : "ADM";
$primeira_letra = !empty($nome_adm) ? strtoupper(substr($nome_adm, 0, 1)) : "A";
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Cursos - Painel Fluux</title>
    
    <link rel="stylesheet" href="../css/admin.css">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&family=Sora:wght@600;700;800&display=swap" rel="stylesheet">
    <style>
        .msg-sucesso-topo {
            background: #ecfdf5;
            color: #065f46;
            border: 1px solid #a7f3d0;
            padding: 14px 18px;
            border-radius: 12px;
            margin-bottom: 25px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .ementa-resumo {
            max-width: 320px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            color: #64748b;
            font-size: 0.82rem;
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

        <!-- CABEÇALHO DA PÁGINA -->
        <header class="dashboard-header">
            <div>
                <h1>Gestão de Cursos</h1>
                <p>Gerencie todos os cursos disponíveis na plataforma Fluux (Total: <strong><?= $total_cursos ?></strong> cursos).</p>
            </div>
            <div class="dashboard-actions">
                <a href="adicionar_curso.php" class="btn-admin-primary">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    Adicionar Novo Curso
                </a>
            </div>
        </header>

        <?php if ($sucesso === "cadastrado"): ?>
            <div class="msg-sucesso-topo">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg>
                Curso cadastrado com sucesso e já disponível na base de dados!
            </div>
        <?php endif; ?>

        <!-- TABELA DE CURSOS -->
        <section class="panel-card">
            <div class="panel-card-header">
                <h2 class="panel-card-title">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                    </svg>
                    Lista Completa de Cursos
                </h2>
            </div>

            <div class="panel-card-body" style="padding: 0;">
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Cód.</th>
                                <th>Nome do Curso</th>
                                <th>Nível</th>
                                <th>Carga Horária</th>
                                <th>Ementa / Conteúdo</th>
                                <th>Cadastrado por</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($resultado_cursos && mysqli_num_rows($resultado_cursos) > 0): ?>
                                <?php while ($curso = mysqli_fetch_assoc($resultado_cursos)): ?>
                                    <tr>
                                        <td><strong>#<?= str_pad($curso["codigo"], 3, '0', STR_PAD_LEFT) ?></strong></td>
                                        <td>
                                            <strong style="color: var(--cinza-titulo);"><?= htmlspecialchars($curso["nome"]) ?></strong>
                                        </td>
                                        <td>
                                            <span class="badge-nivel"><?= htmlspecialchars($curso["nivel_nome"]) ?></span>
                                        </td>
                                        <td><strong><?= htmlspecialchars($curso["duracao"]) ?></strong> horas</td>
                                        <td>
                                            <div class="ementa-resumo" title="<?= htmlspecialchars($curso["ementa"]) ?>">
                                                <?= !empty($curso["ementa"]) ? htmlspecialchars($curso["ementa"]) : "<em>Sem ementa definida</em>" ?>
                                            </div>
                                        </td>
                                        <td>
                                            <span style="font-size: 0.82rem; color: #475569;"><?= htmlspecialchars($curso["adm_nome"]) ?></span>
                                        </td>
                                        <td>
                                            <span class="badge-status <?= strtolower($curso["status"]) === 'ativo' ? 'badge-ativo' : 'badge-inativo' ?>">
                                                <?= htmlspecialchars($curso["status"]) ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" style="text-align: center; padding: 40px; color: #8992a5;">
                                        Nenhum curso cadastrado no momento.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

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
