<?php
require_once "../php/verifica_adm.php";
require_once "../php/conexao.php";

/** @var mysqli $conexao */

$sucesso = isset($_GET["sucesso"]) ? $_GET["sucesso"] : null;
$erro    = isset($_GET["erro"]) ? $_GET["erro"] : null;

// Busca todos os alunos com dados do nível e contagem de cursos
$query_alunos = "
    SELECT a.matricula, a.nome, a.cpf, a.email,
           COALESCE(n.nome, 'Iniciante') AS nivel_nome,
           (SELECT COUNT(*) FROM matricula_curso mc WHERE mc.aluno_matricula = a.matricula) AS total_matriculas,
           (SELECT COUNT(*) FROM curso_salvo cs WHERE cs.aluno_matricula = a.matricula) AS total_salvos
    FROM aluno a
    LEFT JOIN nivel n ON a.nivel_id = n.id
    ORDER BY a.matricula DESC
";
$resultado_alunos = mysqli_query($conexao, $query_alunos);
$total_alunos = ($resultado_alunos) ? mysqli_num_rows($resultado_alunos) : 0;

$nome_adm = isset($_SESSION["usuario_nome"]) ? $_SESSION["usuario_nome"] : "Administrador";
$id_adm = isset($_SESSION["usuario_id"]) ? $_SESSION["usuario_id"] : "ADM";
$primeira_letra = !empty($nome_adm) ? strtoupper(substr($nome_adm, 0, 1)) : "A";

// Função para formatar CPF (ex: 12345678901 -> 123.456.789-01)
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
    <title>Gerenciar Alunos - Painel Fluux</title>
    
    <link rel="stylesheet" href="../css/admin.css">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&family=Sora:wght@600;700;800&display=swap" rel="stylesheet">
    <style>
        .aluno-nome-col {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .aluno-link-detalhes {
            color: var(--cinza-titulo);
            font-weight: 600;
            transition: color 0.2s;
        }
        .aluno-link-detalhes:hover {
            color: var(--azul-escuro);
            text-decoration: underline;
        }

        .cpf-tag {
            font-family: monospace;
            background: #f1f5f9;
            padding: 3px 8px;
            border-radius: 6px;
            font-size: 0.82rem;
            color: #475569;
        }

        .search-container {
            display: flex;
            align-items: center;
            background: #ffffff;
            border: 1px solid var(--borda);
            border-radius: var(--radius-md);
            padding: 8px 14px;
            box-shadow: var(--sombra-suave);
            margin-bottom: 25px;
            gap: 10px;
        }

        .search-container input {
            border: none;
            outline: none;
            width: 100%;
            font-size: 0.92rem;
            color: #334155;
            background: transparent;
        }

        .acoes-col {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-icon-acao {
            width: 32px;
            height: 32px;
            border-radius: var(--radius-sm);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s var(--ease);
            border: 1px solid transparent;
        }

        .btn-icon-ver {
            background: #eff6ff;
            color: #2563eb;
            border-color: #dbeafe;
        }
        .btn-icon-ver:hover {
            background: #2563eb;
            color: #ffffff;
        }

        .btn-icon-editar {
            background: #f8fafc;
            color: #475569;
            border-color: #e2e8f0;
        }
        .btn-icon-editar:hover {
            background: #0ea5e9;
            color: #ffffff;
            border-color: #0ea5e9;
        }

        .btn-icon-excluir {
            background: #fef2f2;
            color: #dc2626;
            border-color: #fee2e2;
        }
        .btn-icon-excluir:hover {
            background: #dc2626;
            color: #ffffff;
            border-color: #dc2626;
        }

        .alerta-feedback {
            padding: 14px 18px;
            border-radius: 12px;
            margin-bottom: 25px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .alerta-sucesso {
            background: #ecfdf5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }
        .alerta-erro {
            background: #fef2f2;
            color: #dc2626;
            border: 1px solid #fecaca;
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

        <!-- CABEÇALHO DA PÁGINA -->
        <header class="dashboard-header">
            <div>
                <h1>Gestão de Alunos</h1>
                <p>Consulte, edite e gerencie todos os alunos cadastrados no sistema (Total: <strong id="contador-total"><?= $total_alunos ?></strong>).</p>
            </div>
            <div class="dashboard-actions">
                <a href="adicionar_aluno.php" class="btn-admin-primary">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    Novo Aluno
                </a>
            </div>
        </header>

        <!-- FEEDBACKS -->
        <?php if ($sucesso === "cadastrado"): ?>
            <div class="alerta-feedback alerta-sucesso">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg>
                Aluno cadastrado com sucesso!
            </div>
        <?php elseif ($sucesso === "excluido"): ?>
            <div class="alerta-feedback alerta-sucesso">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg>
                Aluno e todos os seus vínculos foram excluídos com sucesso.
            </div>
        <?php elseif ($erro): ?>
            <div class="alerta-feedback alerta-erro">
                Erro ao processar solicitação. Tente novamente.
            </div>
        <?php endif; ?>

        <!-- BARRA DE PESQUISA EM TEMPO REAL -->
        <div class="search-container">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#7b8497" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="11" cy="11" r="8"></circle>
                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
            <input 
                type="text" 
                id="input-busca-alunos" 
                placeholder="Pesquisar por nome, matrícula, CPF ou e-mail do aluno..."
                autocomplete="off"
            >
        </div>

        <!-- TABELA DE ALUNOS -->
        <section class="panel-card">
            <div class="panel-card-header">
                <h2 class="panel-card-title">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                    Lista Completa de Alunos
                </h2>
            </div>

            <div class="panel-card-body" style="padding: 0;">
                <div class="table-responsive">
                    <table class="admin-table" id="tabela-alunos">
                        <thead>
                            <tr>
                                <th>Matrícula</th>
                                <th>Nome do Aluno</th>
                                <th>CPF</th>
                                <th>E-mail</th>
                                <th>Nível</th>
                                <th>Matrículas</th>
                                <th style="text-align: right; padding-right: 20px;">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($resultado_alunos && mysqli_num_rows($resultado_alunos) > 0): ?>
                                <?php while ($aluno = mysqli_fetch_assoc($resultado_alunos)): ?>
                                    <?php $letra_aluno = strtoupper(substr($aluno["nome"], 0, 1)); ?>
                                    <tr class="linha-aluno">
                                        <td><strong>#<?= str_pad($aluno["matricula"], 4, '0', STR_PAD_LEFT) ?></strong></td>
                                        <td>
                                            <div class="aluno-nome-col">
                                                <div class="student-avatar"><?= $letra_aluno ?></div>
                                                <div>
                                                    <a href="detalhes_aluno.php?matricula=<?= $aluno["matricula"] ?>" class="aluno-link-detalhes" title="Clique para ver o perfil completo">
                                                        <?= htmlspecialchars($aluno["nome"]) ?>
                                                    </a>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="cpf-tag"><?= htmlspecialchars(formatarCPF($aluno["cpf"])) ?></span>
                                        </td>
                                        <td>
                                            <span style="color: #475569;"><?= htmlspecialchars($aluno["email"]) ?></span>
                                        </td>
                                        <td>
                                            <span class="badge-nivel"><?= htmlspecialchars($aluno["nivel_nome"]) ?></span>
                                        </td>
                                        <td>
                                            <strong style="color: #2563eb;"><?= $aluno["total_matriculas"] ?></strong> curso(s)
                                        </td>
                                        <td style="text-align: right; padding-right: 20px;">
                                            <div class="acoes-col" style="justify-content: flex-end;">
                                                <!-- Ver Detalhes -->
                                                <a href="detalhes_aluno.php?matricula=<?= $aluno["matricula"] ?>" class="btn-icon-acao btn-icon-ver" title="Ver Detalhes do Aluno">
                                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                        <circle cx="12" cy="12" r="3"></circle>
                                                    </svg>
                                                </a>

                                                <!-- Editar -->
                                                <a href="editar_aluno.php?matricula=<?= $aluno["matricula"] ?>" class="btn-icon-acao btn-icon-editar" title="Editar Aluno">
                                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                                    </svg>
                                                </a>

                                                <!-- Excluir -->
                                                <button type="button" 
                                                   class="btn-icon-acao btn-icon-excluir" 
                                                   title="Excluir Aluno"
                                                   onclick="abrirModalExcluir(<?= $aluno['matricula'] ?>, '<?= addslashes(htmlspecialchars($aluno['nome'])) ?>')">
                                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <polyline points="3 6 5 6 21 6"></polyline>
                                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr id="linha-sem-dados">
                                    <td colspan="7" style="text-align: center; padding: 40px; color: #8992a5;">
                                        Nenhum aluno cadastrado no momento.
                                    </td>
                                </tr>
                            <?php endif; ?>
                            <tr id="linha-nao-encontrado" style="display: none;">
                                <td colspan="7" style="text-align: center; padding: 35px; color: #8992a5;">
                                    Nenhum aluno encontrado correspondente à busca.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

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

    <!-- SCRIPT DE BUSCA EM TEMPO REAL E MODAL -->
    <script>
        const inputBusca = document.getElementById('input-busca-alunos');
        const linhas = document.querySelectorAll('.linha-aluno');
        const linhaNaoEncontrado = document.getElementById('linha-nao-encontrado');
        const contador = document.getElementById('contador-total');
        const totalOriginal = <?= (int)$total_alunos ?>;

        inputBusca.addEventListener('input', function() {
            const termo = this.value.toLowerCase().trim();
            let visiveis = 0;

            linhas.forEach(linha => {
                const texto = linha.textContent.toLowerCase();
                if (texto.includes(termo)) {
                    linha.style.display = '';
                    visiveis++;
                } else {
                    linha.style.display = 'none';
                }
            });

            if (linhaNaoEncontrado) {
                linhaNaoEncontrado.style.display = (visiveis === 0 && linhas.length > 0) ? '' : 'none';
            }

            if (contador) {
                contador.textContent = (termo === '') ? totalOriginal : `${visiveis} de ${totalOriginal}`;
            }
        });

        // Funções do Modal
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
