<?php
require_once "../php/verifica_adm.php";
require_once "../php/conexao.php";
require_once "../php/seed_cursos.php";

/** @var mysqli $conexao */

// 1. Total de Alunos
$res_alunos = mysqli_query($conexao, "SELECT COUNT(*) AS total FROM aluno");
$total_alunos = 0;
if ($res_alunos) {
    $row = mysqli_fetch_assoc($res_alunos);
    $total_alunos = isset($row["total"]) ? $row["total"] : 0;
}

// 2. Total de Cursos
$res_cursos = mysqli_query($conexao, "SELECT COUNT(*) AS total FROM curso");
$total_cursos = 0;
if ($res_cursos) {
    $row = mysqli_fetch_assoc($res_cursos);
    $total_cursos = isset($row["total"]) ? $row["total"] : 0;
}

// 3. Total de Aulas
$res_aulas = mysqli_query($conexao, "SELECT COUNT(*) AS total FROM aula");
$total_aulas = 0;
if ($res_aulas) {
    $row = mysqli_fetch_assoc($res_aulas);
    $total_aulas = isset($row["total"]) ? $row["total"] : 0;
}

// 4. Total de Matrículas
$res_matriculas = mysqli_query($conexao, "SELECT COUNT(*) AS total FROM matricula_curso");
$total_matriculas = 0;
if ($res_matriculas) {
    $row = mysqli_fetch_assoc($res_matriculas);
    $total_matriculas = isset($row["total"]) ? $row["total"] : 0;
}

// 5. Cursos recentes
$query_cursos = "
    SELECT c.codigo, c.nome, c.status, c.duracao, COALESCE(n.nome, 'Geral') AS nivel_nome 
    FROM curso c 
    LEFT JOIN nivel n ON c.nivel_id = n.id 
    ORDER BY c.codigo ASC 
    LIMIT 6
";
$resultado_cursos = mysqli_query($conexao, $query_cursos);

// 6. Alunos recentes
$query_alunos = "
    SELECT matricula, nome, email, cpf 
    FROM aluno 
    ORDER BY matricula DESC 
    LIMIT 5
";
$resultado_alunos = mysqli_query($conexao, $query_alunos);

$nome_adm = isset($_SESSION["usuario_nome"]) ? $_SESSION["usuario_nome"] : "Administrador";
$id_adm = isset($_SESSION["usuario_id"]) ? $_SESSION["usuario_id"] : "ADM";
$primeira_letra = !empty($nome_adm) ? strtoupper(substr($nome_adm, 0, 1)) : "A";
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard do Administrador - Fluux</title>
    
    <link rel="stylesheet" href="../css/admin.css">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&family=Sora:wght@600;700;800&display=swap" rel="stylesheet">
</head>

<body>

    <!-- =====================================================
         NAVBAR ADMINISTRATIVA
    ====================================================== -->
    <nav class="admin-navbar">
        <div class="admin-nav-container">
            <div class="admin-brand">
                <a href="index.php">
                    <img src="../assets/images/logos/logo fluux BRANCA.png" alt="Fluux Logo">
                </a>
                <span class="admin-tag">Administrador</span>
            </div>

            <div class="admin-nav-links">
                <a href="index.php" class="active">Dashboard</a>
                <a href="cursos.php">Cursos</a>
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

    <!-- =====================================================
         CONTEÚDO PRINCIPAL
    ====================================================== -->
    <main class="admin-main">

        <!-- CABEÇALHO DO DASHBOARD -->
        <header class="dashboard-header">
            <div>
                <h1>Painel de Controle</h1>
                <p>Bem-vindo de volta, <strong><?= htmlspecialchars($nome_adm) ?></strong>. Acompanhe os números da sua plataforma.</p>
            </div>
            <div class="dashboard-actions">
                <a href="adicionar_curso.php" class="btn-admin-primary">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    Novo Curso
                </a>
                <a href="alunos.php" class="btn-admin-secondary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                    </svg>
                    Ver Todos os Alunos
                </a>
            </div>
        </header>

        <!-- CARDS DE ESTATÍSTICAS (KPIs) -->
        <section class="stats-grid">
            
            <!-- Card Alunos -->
            <div class="stat-card">
                <div class="stat-icon-box stat-icon-alunos">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                </div>
                <div class="stat-info">
                    <span class="stat-label">Total de Alunos</span>
                    <span class="stat-number"><?= number_format($total_alunos, 0, ',', '.') ?></span>
                </div>
            </div>

            <!-- Card Cursos -->
            <div class="stat-card">
                <div class="stat-icon-box stat-icon-cursos">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                    </svg>
                </div>
                <div class="stat-info">
                    <span class="stat-label">Cursos Cadastrados</span>
                    <span class="stat-number"><?= number_format($total_cursos, 0, ',', '.') ?></span>
                </div>
            </div>

            <!-- Card Aulas -->
            <div class="stat-card">
                <div class="stat-icon-box stat-icon-aulas">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polygon points="23 7 16 12 23 17 23 7"></polygon>
                        <rect x="1" y="5" width="15" height="14" rx="2" ry="2"></rect>
                    </svg>
                </div>
                <div class="stat-info">
                    <span class="stat-label">Total de Aulas</span>
                    <span class="stat-number"><?= number_format($total_aulas, 0, ',', '.') ?></span>
                </div>
            </div>

            <!-- Card Matrículas -->
            <div class="stat-card">
                <div class="stat-icon-box stat-icon-matriculas">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                    </svg>
                </div>
                <div class="stat-info">
                    <span class="stat-label">Matrículas Ativas</span>
                    <span class="stat-number"><?= number_format($total_matriculas, 0, ',', '.') ?></span>
                </div>
            </div>

        </section>

        <!-- SEÇÕES DE DADOS (CURSOS & ALUNOS) -->
        <div class="dashboard-content-grid">

            <!-- TABELA DE CURSOS -->
            <section class="panel-card">
                <div class="panel-card-header">
                    <h2 class="panel-card-title">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                            <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
                        </svg>
                        Cursos na Plataforma
                    </h2>
                    <a href="cursos.php" class="panel-card-link">Ver todos &rarr;</a>
                </div>

                <div class="panel-card-body" style="padding: 0;">
                    <div class="table-responsive">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Cód.</th>
                                    <th>Nome do Curso</th>
                                    <th>Nível</th>
                                    <th>Duração</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($resultado_cursos && mysqli_num_rows($resultado_cursos) > 0): ?>
                                    <?php while ($curso = mysqli_fetch_assoc($resultado_cursos)): ?>
                                        <tr>
                                            <td><strong>#<?= str_pad($curso["codigo"], 3, '0', STR_PAD_LEFT) ?></strong></td>
                                            <td><strong><?= htmlspecialchars($curso["nome"]) ?></strong></td>
                                            <td><span class="badge-nivel"><?= htmlspecialchars($curso["nivel_nome"]) ?></span></td>
                                            <td><?= htmlspecialchars($curso["duracao"]) ?>h</td>
                                            <td>
                                                <span class="badge-status <?= strtolower($curso["status"]) === 'ativo' ? 'badge-ativo' : 'badge-inativo' ?>">
                                                    <?= htmlspecialchars($curso["status"]) ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" style="text-align: center; padding: 30px; color: #8992a5;">
                                            Nenhum curso cadastrado no momento.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <!-- LISTA DE ALUNOS RECENTES -->
            <section class="panel-card">
                <div class="panel-card-header">
                    <h2 class="panel-card-title">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                        </svg>
                        Alunos Recentes
                    </h2>
                    <a href="alunos.php" class="panel-card-link">Ver todos &rarr;</a>
                </div>

                <div class="panel-card-body">
                    <ul class="student-list">
                        <?php if ($resultado_alunos && mysqli_num_rows($resultado_alunos) > 0): ?>
                            <?php while ($aluno = mysqli_fetch_assoc($resultado_alunos)): ?>
                                <?php $letra_aluno = strtoupper(substr($aluno["nome"], 0, 1)); ?>
                                <li class="student-item" style="cursor: pointer;" onclick="window.location.href='detalhes_aluno.php?matricula=<?= $aluno['matricula'] ?>'">
                                    <div class="student-left">
                                        <div class="student-avatar"><?= $letra_aluno ?></div>
                                        <div class="student-details">
                                            <a href="detalhes_aluno.php?matricula=<?= $aluno["matricula"] ?>" class="student-name" style="color: inherit; text-decoration: none;">
                                                <?= htmlspecialchars($aluno["nome"]) ?>
                                            </a>
                                            <span class="student-email"><?= htmlspecialchars($aluno["email"]) ?></span>
                                        </div>
                                    </div>
                                    <span class="student-matricula">Matrícula: <strong>#<?= str_pad($aluno["matricula"], 4, '0', STR_PAD_LEFT) ?></strong></span>
                                </li>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <li style="text-align: center; padding: 25px; color: #8992a5;">
                                Nenhum aluno cadastrado no momento.
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </section>

        </div>

    </main>

    <!-- =====================================================
         RODAPÉ
    ====================================================== -->
    <footer class="admin-footer">
        &copy; <?= date('Y') ?> Fluux - Painel de Administração. Todos os direitos reservados.
    </footer>

</body>
</html>
<?php
mysqli_close($conexao);
?>
