<?php
require_once "../php/verifica_adm.php";
require_once "../php/conexao.php";
require_once "../php/seed_aulas.php";

/** @var mysqli $conexao */

$curso_id = isset($_GET["curso_id"]) ? (int)$_GET["curso_id"] : 1;
$sucesso  = isset($_GET["sucesso"]) ? $_GET["sucesso"] : null;
$erro     = isset($_GET["erro"]) ? $_GET["erro"] : null;

// Busca informações do curso
$sql_c = "SELECT codigo, nome, duracao FROM curso WHERE codigo = $curso_id";
$res_c = mysqli_query($conexao, $sql_c);
$curso = ($res_c && mysqli_num_rows($res_c) > 0) ? mysqli_fetch_assoc($res_c) : null;

if (!$curso) {
    header("Location: cursos.php");
    exit;
}

// Busca aulas cadastradas na tabela aula e contagem de imagens na tabela imagem_aula
$sql_a = "
    SELECT a.id, a.titulo, a.descricao, a.url,
           (SELECT COUNT(*) FROM imagem_aula ia WHERE ia.aula_id = a.id) AS total_imagens
    FROM aula a
    WHERE a.curso_id = $curso_id
    ORDER BY a.id ASC
";
$res_a = mysqli_query($conexao, $sql_a);

$nome_adm = isset($_SESSION["usuario_nome"]) ? $_SESSION["usuario_nome"] : "Administrador";
$id_adm = isset($_SESSION["usuario_id"]) ? $_SESSION["usuario_id"] : "ADM";
$primeira_letra = !empty($nome_adm) ? strtoupper(substr($nome_adm, 0, 1)) : "A";
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Aulas - <?= htmlspecialchars($curso["nome"]) ?></title>
    
    <link rel="stylesheet" href="../css/admin.css">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&family=Sora:wght@600;700;800&display=swap" rel="stylesheet">
    <style>
        .grid-admin-aulas {
            display: grid;
            grid-template-columns: 360px 1fr;
            gap: 24px;
            align-items: start;
        }
        @media (max-width: 990px) {
            .grid-admin-aulas {
                grid-template-columns: 1fr;
            }
        }
        .form-campo {
            margin-bottom: 16px;
        }
        .form-campo label {
            display: block;
            font-size: 0.82rem;
            font-weight: 600;
            color: #334155;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .form-campo input,
        .form-campo textarea {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-family: inherit;
            font-size: 0.9rem;
            background: #f8fafc;
            box-sizing: border-box;
        }
        .form-campo textarea {
            resize: vertical;
            min-height: 80px;
        }
        .btn-submit-aula {
            width: 100%;
            padding: 12px;
            background: #2857c6;
            color: #ffffff;
            border: 0;
            border-radius: 8px;
            font-weight: 700;
            cursor: pointer;
            transition: 0.2s ease;
        }
        .btn-submit-aula:hover {
            background: #1f48aa;
        }
        .btn-del-aula {
            color: #dc2626;
            background: #fee2e2;
            padding: 5px 10px;
            border-radius: 6px;
            font-size: 0.78rem;
            font-weight: 600;
            display: inline-block;
        }
        .btn-del-aula:hover {
            background: #dc2626;
            color: #ffffff;
        }
        .btn-assistir-aula {
            color: #2563eb;
            background: #dbeafe;
            padding: 5px 10px;
            border-radius: 6px;
            font-size: 0.78rem;
            font-weight: 600;
            display: inline-block;
            margin-right: 6px;
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

    <main class="admin-main">

        <header class="dashboard-header">
            <div>
                <a href="cursos.php" style="color: #64748b; font-size: 0.85rem; display: inline-block; margin-bottom: 6px;">← Voltar para Gestão de Cursos</a>
                <h1>Aulas: <?= htmlspecialchars($curso["nome"]) ?></h1>
                <p>Gerencie o conteúdo em vídeo e materiais de apoio deste curso (Tabelas <code>aula</code> e <code>imagem_aula</code>).</p>
            </div>
        </header>

        <?php if ($sucesso === "cadastrada"): ?>
            <div style="background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; padding: 12px 18px; border-radius: 10px; margin-bottom: 20px;">
                ✓ Nova aula cadastrada com sucesso no banco de dados!
            </div>
        <?php elseif ($sucesso === "excluida"): ?>
            <div style="background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; padding: 12px 18px; border-radius: 10px; margin-bottom: 20px;">
                ✓ Aula e imagens associadas removidas com sucesso.
            </div>
        <?php endif; ?>

        <div class="grid-admin-aulas">

            <!-- FORMULÁRIO DE CADASTRO DE AULA -->
            <section class="panel-card">
                <div class="panel-card-header">
                    <h2 class="panel-card-title">➕ Cadastrar Nova Aula</h2>
                </div>
                <div class="panel-card-body">
                    <form action="../php/cadastrar_aula.php" method="POST">
                        <input type="hidden" name="curso_id" value="<?= $curso_id ?>">

                        <div class="form-campo">
                            <label for="titulo">Título da Aula *</label>
                            <input type="text" id="titulo" name="titulo" placeholder="Ex: 04. Manipulando Arrays" required>
                        </div>

                        <div class="form-campo">
                            <label for="url">Link do Vídeo (YouTube ou URL)</label>
                            <input type="text" id="url" name="url" placeholder="Ex: https://www.youtube.com/watch?v=...">
                        </div>

                        <div class="form-campo">
                            <label for="url_imagem">Imagem de Apoio / Diagrama (Opcional)</label>
                            <input type="text" id="url_imagem" name="url_imagem" placeholder="Ex: assets/images/cursos/web.webp">
                        </div>

                        <div class="form-campo">
                            <label for="descricao">Descrição / Resumo da Aula</label>
                            <textarea id="descricao" name="descricao" placeholder="Instruções e conceitos abordados na aula..."></textarea>
                        </div>

                        <button type="submit" class="btn-submit-aula">Salvar Aula no Banco</button>
                    </form>
                </div>
            </section>

            <!-- LISTAGEM DAS AULAS CADASTRADAS -->
            <section class="panel-card">
                <div class="panel-card-header">
                    <h2 class="panel-card-title">📑 Aulas Cadastradas (<?= mysqli_num_rows($res_a) ?>)</h2>
                </div>
                <div class="panel-card-body" style="padding: 0;">
                    <div class="table-responsive">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Ordem</th>
                                    <th>Título da Aula</th>
                                    <th>Materiais</th>
                                    <th>Vídeo</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($res_a && mysqli_num_rows($res_a) > 0): ?>
                                    <?php $i = 1; while ($a = mysqli_fetch_assoc($res_a)): ?>
                                        <tr>
                                            <td><strong>Aula <?= $i++ ?></strong></td>
                                            <td>
                                                <strong style="color: var(--cinza-titulo);"><?= htmlspecialchars($a["titulo"]) ?></strong>
                                                <div style="font-size: 0.78rem; color: #64748b; margin-top: 2px;">
                                                    <?= htmlspecialchars(mb_strimwidth($a["descricao"], 0, 60, "...")) ?>
                                                </div>
                                            </td>
                                            <td>
                                                <span style="font-size: 0.8rem; background: #f1f5f9; padding: 3px 8px; border-radius: 6px;">
                                                    🖼️ <?= (int)$a["total_imagens"] ?> imagem(ns)
                                                </span>
                                            </td>
                                            <td>
                                                <?php if (!empty($a["url"])): ?>
                                                    <a href="<?= htmlspecialchars($a["url"]) ?>" target="_blank" style="color: #ef4444; font-weight: 600; font-size: 0.8rem;">
                                                        ▶ Ver Vídeo
                                                    </a>
                                                <?php else: ?>
                                                    <span style="color: #94a3b8; font-size: 0.8rem;">Sem vídeo</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <a href="../aulas/aula.php?curso_id=<?= $curso_id ?>&aula_id=<?= $a["id"] ?>" target="_blank" class="btn-assistir-aula">
                                                    Assistir ↗
                                                </a>
                                                <a
                                                    href="../php/excluir_aula.php?id=<?= $a["id"] ?>&curso_id=<?= $curso_id ?>"
                                                    class="btn-del-aula"
                                                    onclick="return confirm('Tem certeza que deseja excluir esta aula?');"
                                                >
                                                    Excluir
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" style="text-align: center; padding: 25px; color: #64748b;">
                                            Nenhuma aula cadastrada ainda para este curso. Use o formulário ao lado para adicionar!
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

</body>
</html>
