<?php
require_once "../php/verifica_adm.php";
require_once "../php/conexao.php";

/** @var mysqli $conexao */

$matricula = isset($_GET["matricula"]) ? (int)$_GET["matricula"] : 0;

if ($matricula <= 0) {
    header("Location: alunos.php");
    exit;
}

// 1. Busca os dados do aluno
$sql_aluno = "
    SELECT a.matricula, a.nome, a.cpf, a.email, a.nivel_id, COALESCE(n.nome, 'Iniciante') AS nivel_nome
    FROM aluno a
    LEFT JOIN nivel n ON a.nivel_id = n.id
    WHERE a.matricula = $matricula
";
$res_aluno = mysqli_query($conexao, $sql_aluno);

if (!$res_aluno || mysqli_num_rows($res_aluno) === 0) {
    header("Location: alunos.php?erro=aluno_nao_encontrado");
    exit;
}

$aluno = mysqli_fetch_assoc($res_aluno);

// 2. Busca os cursos matriculados
$sql_matriculas = "
    SELECT c.codigo, c.nome, c.duracao, c.status, COALESCE(n.nome, 'Geral') AS nivel_nome
    FROM matricula_curso mc
    JOIN curso c ON mc.curso_codigo = c.codigo
    LEFT JOIN nivel n ON c.nivel_id = n.id
    WHERE mc.aluno_matricula = $matricula
    ORDER BY c.nome ASC
";
$res_matriculas = mysqli_query($conexao, $sql_matriculas);
$total_matriculas = ($res_matriculas) ? mysqli_num_rows($res_matriculas) : 0;

// 3. Busca os cursos salvos (favoritos)
$sql_salvos = "
    SELECT c.codigo, c.nome, c.duracao, c.status, COALESCE(n.nome, 'Geral') AS nivel_nome
    FROM curso_salvo cs
    JOIN curso c ON cs.curso_codigo = c.codigo
    LEFT JOIN nivel n ON c.nivel_id = n.id
    WHERE cs.aluno_matricula = $matricula
    ORDER BY c.nome ASC
";
$res_salvos = mysqli_query($conexao, $sql_salvos);
$total_salvos = ($res_salvos) ? mysqli_num_rows($res_salvos) : 0;

// Formata CPF
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

$nome_adm = isset($_SESSION["usuario_nome"]) ? $_SESSION["usuario_nome"] : "Administrador";
$id_adm = isset($_SESSION["usuario_id"]) ? $_SESSION["usuario_id"] : "ADM";
$primeira_letra_adm = !empty($nome_adm) ? strtoupper(substr($nome_adm, 0, 1)) : "A";
$letra_aluno = strtoupper(substr($aluno["nome"], 0, 1));
$sucesso = isset($_GET["sucesso"]) ? $_GET["sucesso"] : null;
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil do Aluno - <?= htmlspecialchars($aluno["nome"]) ?> - Fluux</title>
    
    <link rel="stylesheet" href="../css/admin.css">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&family=Sora:wght@600;700;800&display=swap" rel="stylesheet">
    <style>
        .perfil-card-hero {
            background: #ffffff;
            border-radius: var(--radius-lg);
            padding: 30px;
            box-shadow: var(--sombra-card);
            border: 1px solid var(--borda);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 30px;
        }

        .perfil-hero-info {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .perfil-avatar-gigante {
            width: 76px;
            height: 76px;
            border-radius: 50%;
            background: var(--azul-escuro);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            font-weight: 700;
            box-shadow: 0 6px 20px rgba(40, 87, 198, 0.25);
        }

        .perfil-hero-dados h1 {
            font-family: var(--font-display);
            font-size: 1.5rem;
            color: var(--cinza-titulo);
            line-height: 1.2;
        }

        .perfil-hero-sub {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-top: 6px;
            flex-wrap: wrap;
            font-size: 0.88rem;
            color: #64748b;
        }

        .perfil-botoes-acao {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn-acao-editar {
            background: #2563eb;
            color: #ffffff;
            padding: 10px 18px;
            border-radius: var(--radius-md);
            font-size: 0.88rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s var(--ease);
        }
        .btn-acao-editar:hover {
            background: #1d4ed8;
            transform: translateY(-2px);
        }

        .btn-acao-excluir {
            background: #fee2e2;
            color: #dc2626;
            border: 1px solid #fecaca;
            padding: 10px 18px;
            border-radius: var(--radius-md);
            font-size: 0.88rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s var(--ease);
        }
        .btn-acao-excluir:hover {
            background: #dc2626;
            color: #ffffff;
            transform: translateY(-2px);
        }

        .detalhes-info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 16px;
            margin-bottom: 30px;
        }

        .detalhe-dado-box {
            background: #ffffff;
            border-radius: var(--radius-md);
            padding: 20px;
            border: 1px solid var(--borda);
            box-shadow: var(--sombra-suave);
        }

        .detalhe-dado-box span {
            font-size: 0.78rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #7b8497;
            display: block;
            margin-bottom: 4px;
        }

        .detalhe-dado-box strong {
            font-size: 1.05rem;
            color: var(--cinza-titulo);
            word-break: break-all;
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
                <div class="admin-avatar"><?= $primeira_letra_adm ?></div>
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

        <?php if ($sucesso === "editado"): ?>
            <div style="background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; padding: 14px 18px; border-radius: 12px; margin-bottom: 25px; font-weight: 500;">
                ✓ Dados do aluno atualizados com sucesso!
            </div>
        <?php endif; ?>

        <!-- HERO DO PERFIL DO ALUNO -->
        <section class="perfil-card-hero">
            <div class="perfil-hero-info">
                <div class="perfil-avatar-gigante"><?= $letra_aluno ?></div>
                <div class="perfil-hero-dados">
                    <h1><?= htmlspecialchars($aluno["nome"]) ?></h1>
                    <div class="perfil-hero-sub">
                        <span>Matrícula: <strong>#<?= str_pad($aluno["matricula"], 4, '0', STR_PAD_LEFT) ?></strong></span>
                        <span>•</span>
                        <span class="badge-nivel"><?= htmlspecialchars($aluno["nivel_nome"]) ?></span>
                    </div>
                </div>
            </div>

            <div class="perfil-botoes-acao">
                <a href="editar_aluno.php?matricula=<?= $aluno["matricula"] ?>" class="btn-acao-editar">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                    </svg>
                    Editar
                </a>
                <button type="button" 
                   class="btn-acao-excluir" 
                   style="border: none; cursor: pointer;"
                   onclick="abrirModalExcluir(<?= $aluno['matricula'] ?>, '<?= addslashes(htmlspecialchars($aluno['nome'])) ?>')">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="3 6 5 6 21 6"></polyline>
                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                    </svg>
                    Excluir
                </button>
                <a href="alunos.php" class="btn-admin-secondary">
                    &larr; Voltar
                </a>
            </div>
        </section>

        <!-- DADOS CADASTRAIS EM GRID -->
        <section class="detalhes-info-grid">
            <div class="detalhe-dado-box">
                <span>E-mail</span>
                <strong><?= htmlspecialchars($aluno["email"]) ?></strong>
            </div>

            <div class="detalhe-dado-box">
                <span>CPF</span>
                <strong><?= htmlspecialchars(formatarCPF($aluno["cpf"])) ?></strong>
            </div>

            <div class="detalhe-dado-box">
                <span>Cursos Matriculados</span>
                <strong style="color: #2563eb;"><?= $total_matriculas ?> curso(s)</strong>
            </div>

            <div class="detalhe-dado-box">
                <span>Cursos Salvos</span>
                <strong style="color: #7c3aed;"><?= $total_salvos ?> curso(s)</strong>
            </div>
        </section>

        <!-- CURSOS MATRICULADOS E SALVOS -->
        <div class="dashboard-content-grid">

            <!-- CURSOS MATRICULADOS -->
            <section class="panel-card">
                <div class="panel-card-header">
                    <h2 class="panel-card-title">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                            <polyline points="22 4 12 14.01 9 11.01"></polyline>
                        </svg>
                        Cursos Matriculados (<?= $total_matriculas ?>)
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
                                    <th>Duração</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($res_matriculas && mysqli_num_rows($res_matriculas) > 0): ?>
                                    <?php while ($cm = mysqli_fetch_assoc($res_matriculas)): ?>
                                        <tr>
                                            <td><strong>#<?= str_pad($cm["codigo"], 3, '0', STR_PAD_LEFT) ?></strong></td>
                                            <td><strong><?= htmlspecialchars($cm["nome"]) ?></strong></td>
                                            <td><span class="badge-nivel"><?= htmlspecialchars($cm["nivel_nome"]) ?></span></td>
                                            <td><?= htmlspecialchars($cm["duracao"]) ?>h</td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" style="text-align: center; padding: 25px; color: #8992a5;">
                                            O aluno ainda não realizou nenhuma matrícula.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <!-- CURSOS SALVOS / FAVORITOS -->
            <section class="panel-card">
                <div class="panel-card-header">
                    <h2 class="panel-card-title">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path>
                        </svg>
                        Cursos Salvos (<?= $total_salvos ?>)
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
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($res_salvos && mysqli_num_rows($res_salvos) > 0): ?>
                                    <?php while ($cs = mysqli_fetch_assoc($res_salvos)): ?>
                                        <tr>
                                            <td><strong>#<?= str_pad($cs["codigo"], 3, '0', STR_PAD_LEFT) ?></strong></td>
                                            <td><strong><?= htmlspecialchars($cs["nome"]) ?></strong></td>
                                            <td><span class="badge-nivel"><?= htmlspecialchars($cs["nivel_nome"]) ?></span></td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3" style="text-align: center; padding: 25px; color: #8992a5;">
                                            O aluno não possui cursos salvos nos favoritos.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

        </div>

    </main>

    <!-- =====================================================
         MODAL DE CONFIRMAÇÃO DE EXCLUSÃO
    ====================================================== -->
    <div class="modal-overlay" id="modal-excluir" style="display: none;">
        <div class="modal-card">
            <div class="modal-icon-wrap modal-icon-perigo">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                    <line x1="12" y1="9" x2="12" y2="13"></line>
                    <line x1="12" y1="17" x2="12.01" y2="17"></line>
                </svg>
            </div>
            <h3>Excluir Aluno</h3>
            <p>Tem certeza que deseja excluir o aluno <strong id="modal-nome-aluno"></strong>?</p>
            <span class="modal-aviso">⚠️ Esta ação é irreversível e removerá todas as matrículas e cursos salvos vinculados a este aluno.</span>
            <div class="modal-botoes">
                <button type="button" class="btn-modal-cancelar" onclick="fecharModalExcluir()">Cancelar</button>
                <a href="#" id="modal-link-confirmar" class="btn-modal-confirmar">Sim, Excluir</a>
            </div>
        </div>
    </div>

    <!-- FOOTER -->
    <footer class="admin-footer">
        &copy; <?= date('Y') ?> Fluux - Painel de Administração. Todos os direitos reservados.
    </footer>

    <!-- SCRIPT DO MODAL -->
    <script>
        function abrirModalExcluir(matricula, nome) {
            document.getElementById('modal-nome-aluno').textContent = nome;
            document.getElementById('modal-link-confirmar').href = '../php/excluir_aluno_adm.php?matricula=' + matricula;
            document.getElementById('modal-excluir').style.display = 'flex';
        }

        function fecharModalExcluir() {
            document.getElementById('modal-excluir').style.display = 'none';
        }

        window.addEventListener('click', function(e) {
            const modal = document.getElementById('modal-excluir');
            if (e.target === modal) {
                fecharModalExcluir();
            }
        });
    </script>

</body>
</html>
<?php
mysqli_close($conexao);
?>
