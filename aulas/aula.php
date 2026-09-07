<?php
session_start();

require_once "../php/conexao.php";
require_once "../php/seed_aulas.php";

/** @var mysqli $conexao */

$curso_id = isset($_GET["curso_id"]) ? (int)$_GET["curso_id"] : 1;
$aula_id  = isset($_GET["aula_id"]) ? (int)$_GET["aula_id"] : 0;

// Busca informações do curso
$sql_curso = "
    SELECT c.codigo, c.nome, c.ementa, c.duracao, COALESCE(n.nome, 'Geral') AS nivel_nome
    FROM curso c
    LEFT JOIN nivel n ON c.nivel_id = n.id
    WHERE c.codigo = $curso_id
";
$res_curso = mysqli_query($conexao, $sql_curso);
$curso = ($res_curso && mysqli_num_rows($res_curso) > 0) ? mysqli_fetch_assoc($res_curso) : null;

if (!$curso) {
    die("Curso não encontrado.");
}

$esta_logado = isset($_SESSION["usuario_id"]);
$tipo_usuario = isset($_SESSION["tipo_usuario"]) ? $_SESSION["tipo_usuario"] : "visitante";
$matricula_aluno = $esta_logado ? (int)$_SESSION["usuario_id"] : null;
$nome_usuario = isset($_SESSION["usuario_nome"]) ? $_SESSION["usuario_nome"] : "";

// Verifica se o aluno está matriculado no curso (administradores têm acesso livre)
$matriculado = false;
if ($tipo_usuario === "adm") {
    $matriculado = true;
} else if ($esta_logado) {
    $chk_mat = mysqli_query($conexao, "SELECT 1 FROM matricula_curso WHERE aluno_matricula = $matricula_aluno AND curso_codigo = $curso_id");
    if ($chk_mat && mysqli_num_rows($chk_mat) > 0) {
        $matriculado = true;
    }
}

// Busca todas as aulas do curso na tabela aula
$sql_aulas = "SELECT id, curso_id, titulo, descricao, url FROM aula WHERE curso_id = $curso_id ORDER BY id ASC";
$res_aulas = mysqli_query($conexao, $sql_aulas);

$lista_aulas = [];
if ($res_aulas) {
    while ($row = mysqli_fetch_assoc($res_aulas)) {
        $lista_aulas[] = $row;
    }
}

// Identifica a aula atual
$aula_atual = null;
$indice_atual = 0;

if (!empty($lista_aulas)) {
    if ($aula_id > 0) {
        foreach ($lista_aulas as $idx => $a) {
            if ((int)$a["id"] === $aula_id) {
                $aula_atual = $a;
                $indice_atual = $idx;
                break;
            }
        }
    }
    // Se não encontrou ou não passou aula_id, seleciona a primeira aula
    if (!$aula_atual) {
        $aula_atual = $lista_aulas[0];
        $indice_atual = 0;
    }
}

// Busca imagens de apoio na tabela imagem_aula
$imagens_aula = [];
if ($aula_atual) {
    $cur_aula_id = (int)$aula_atual["id"];
    $sql_imgs = "SELECT id, url_imagem FROM imagem_aula WHERE aula_id = $cur_aula_id";
    $res_imgs = mysqli_query($conexao, $sql_imgs);
    if ($res_imgs) {
        while ($img_row = mysqli_fetch_assoc($res_imgs)) {
            $imagens_aula[] = $img_row;
        }
    }
}

// URLs de navegação anterior/próxima
$aula_anterior_id = ($indice_atual > 0) ? $lista_aulas[$indice_atual - 1]["id"] : null;
$aula_proxima_id  = ($indice_atual < count($lista_aulas) - 1) ? $lista_aulas[$indice_atual + 1]["id"] : null;

// Função para formatar URL do YouTube em formato Embed
function formatarUrlEmbed($url) {
    if (empty($url)) return "";
    if (strpos($url, "embed/") !== false) return $url;
    if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $url, $matches)) {
        return "https://www.youtube.com/embed/" . $matches[1];
    }
    return $url;
}

$url_video_embed = $aula_atual ? formatarUrlEmbed($aula_atual["url"]) : "";
$codigo_pad = str_pad($curso["codigo"], 3, "0", STR_PAD_LEFT);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $aula_atual ? htmlspecialchars($aula_atual["titulo"]) : "Aula" ?> - Fluux</title>

    <link rel="stylesheet" href="../css/aula-comum.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&family=Sora:wght@600;700;800&display=swap" rel="stylesheet">
</head>

<body>

    <!-- NAVBAR -->
    <div class="navbar">
        <div class="topbar">
            <a class="logo" href="../index.php">
                <img src="../assets/images/logos/logo fluux BRANCA.png" alt="Fluux" class="logo-img">
            </a>

            <nav class="nav-links">
                <a href="../index.php">Cursos</a>
                <a href="../pages/salvos.html">Salvos</a>
                <a href="../pages/sobre.html">Sobre</a>
                <a href="../pages/contato.html">Contato</a>
            </nav>

            <nav class="topbar-actions">
                <?php if ($esta_logado): ?>
                    <a href="../perfil/perfil.php" style="color: #c6ff5c; font-weight: 600;">
                        👤 <?= htmlspecialchars($nome_usuario) ?>
                    </a>
                    <a href="../php/logout.php">Sair</a>
                <?php else: ?>
                    <a href="../pages/login.php">Entrar</a>
                    <a href="../pages/cadastro.html">Cadastrar</a>
                <?php endif; ?>
            </nav>
        </div>
    </div>

    <?php if (!$matriculado): ?>
        <!-- BLOQUEIO DE ACESSO: ALUNO NÃO MATRICULADO -->
        <header class="aula-hero">
            <div class="hero-content">
                <a class="voltar-curso" href="../index.php">← Voltar para todos os cursos</a>
                <span class="aula-badge">Acesso Exclusivo</span>
                <h1><?= htmlspecialchars($curso["nome"]) ?></h1>
            </div>
        </header>

        <main class="container">
            <div class="player-card" style="padding: 60px 30px; text-align: center; max-width: 680px; margin: 40px auto;">
                <div style="font-size: 3.5rem; margin-bottom: 16px;">🔒</div>
                <h2 style="font-family: 'Sora', sans-serif; font-size: 1.5rem; color: #101c3d; margin-bottom: 12px;">
                    Matrícula necessária para assistir às aulas
                </h2>
                <p style="color: #64748b; font-size: 0.95rem; line-height: 1.6; margin-bottom: 25px;">
                    Você precisa estar matriculado no curso <strong><?= htmlspecialchars($curso["nome"]) ?></strong> para acessar o conteúdo em vídeo e os materiais de apoio.
                </p>
                <div style="display: flex; justify-content: center; gap: 14px; flex-wrap: wrap;">
                    <?php if ($esta_logado): ?>
                        <button class="btn-matricular btn-primario" data-codigo="<?= $codigo_pad ?>" style="cursor: pointer; padding: 12px 28px; background: #c6ff5c; color: #101c3d; border-radius: 999px; font-weight: 700; border: 0;">
                            Matricular-se gratuitamente
                        </button>
                    <?php else: ?>
                        <a href="../pages/login.php" class="btn-primario" style="padding: 12px 28px; background: #c6ff5c; color: #101c3d; border-radius: 999px; font-weight: 700;">
                            Fazer Login para Entrar
                        </a>
                    <?php endif; ?>
                    <a href="../index.php" style="display: inline-flex; align-items: center; padding: 12px 22px; background: #f1f5f9; color: #334155; border-radius: 999px; font-weight: 600;">
                        Ver outros cursos
                    </a>
                </div>
            </div>
        </main>

    <?php else: ?>

        <!-- SALA DE AULA LIBERADA (MATRICULADO) -->
        <header class="aula-hero">
            <div class="hero-content">
                <a class="voltar-curso" href="../perfil/perfil.php">← Voltar para Meu Perfil</a>
                <span class="aula-badge">
                    Curso #<?= $codigo_pad ?> • <?= htmlspecialchars($curso["nivel_nome"]) ?>
                </span>
                <h1><?= $aula_atual ? htmlspecialchars($aula_atual["titulo"]) : "Aula" ?></h1>
            </div>
        </header>

        <main class="container">
            <div class="aula-layout">

                <!-- COLUNA PRINCIPAL: PLAYER E CONTEÚDO -->
                <section class="aula-principal">

                    <!-- CARD DO PLAYER DE VÍDEO -->
                    <div class="player-card">
                        <div class="video-wrapper">
                            <?php if (!empty($url_video_embed)): ?>
                                <iframe
                                    src="<?= htmlspecialchars($url_video_embed) ?>"
                                    title="<?= htmlspecialchars($aula_atual["titulo"]) ?>"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                    allowfullscreen
                                ></iframe>
                            <?php else: ?>
                                <div class="placeholder-conteudo">
                                    <div class="placeholder-play">▶</div>
                                    <strong>Vídeo em preparação</strong>
                                    <span>O vídeo desta aula será disponibilizado em breve pelo professor.</span>
                                    <span class="placeholder-badge">Aguarde</span>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="player-info">
                            <div class="player-info-texto">
                                <strong><?= htmlspecialchars($aula_atual["titulo"]) ?></strong>
                                <span>Aula <?= ($indice_atual + 1) ?> de <?= count($lista_aulas) ?> do curso <?= htmlspecialchars($curso["nome"]) ?></span>
                            </div>

                            <?php if (!empty($aula_atual["url"])): ?>
                                <a class="btn-youtube" href="<?= htmlspecialchars($aula_atual["url"]) ?>" target="_blank" rel="noopener">
                                    Assistir no YouTube ↗
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- DESCRIÇÃO DA AULA -->
                    <section class="secao">
                        <h2>Sobre esta aula</h2>
                        <p>
                            <?= !empty($aula_atual["descricao"]) ? nl2br(htmlspecialchars($aula_atual["descricao"])) : "Nenhuma descrição fornecida para esta aula." ?>
                        </p>
                    </section>

                    <!-- MATERIAIS DE APOIO (Tabela imagem_aula) -->
                    <?php if (!empty($imagens_aula)): ?>
                        <section class="secao materiais-secao">
                            <h2>🖼️ Materiais de Apoio & Diagramas</h2>
                            <p>Clique em uma imagem para visualizá-la ou utilizá-la em seus estudos:</p>

                            <div class="materiais-grid">
                                <?php foreach ($imagens_aula as $img): 
                                    $caminho_imagem = (strpos($img["url_imagem"], "http") === 0) ? $img["url_imagem"] : "../" . ltrim($img["url_imagem"], "/");
                                ?>
                                    <a class="material-card" href="<?= htmlspecialchars($caminho_imagem) ?>" target="_blank" title="Clique para ampliar">
                                        <div class="material-thumb">
                                            <img src="<?= htmlspecialchars($caminho_imagem) ?>" alt="Material de Apoio">
                                        </div>
                                        <div class="material-legenda">
                                            <span>🔍 Ver em tamanho real</span>
                                        </div>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </section>
                    <?php endif; ?>

                    <!-- BOTÕES DE NAVEGAÇÃO ENTRE AULAS -->
                    <div class="navegacao-aula">
                        <?php if ($aula_anterior_id): ?>
                            <a class="nav-btn" href="aula.php?curso_id=<?= $curso_id ?>&aula_id=<?= $aula_anterior_id ?>">
                                ← Aula Anterior
                            </a>
                        <?php else: ?>
                            <span class="nav-btn desabilitado">← Aula Anterior</span>
                        <?php endif; ?>

                        <?php if ($aula_proxima_id): ?>
                            <a class="nav-btn" href="aula.php?curso_id=<?= $curso_id ?>&aula_id=<?= $aula_proxima_id ?>">
                                Próxima Aula →
                            </a>
                        <?php else: ?>
                            <span class="nav-btn desabilitado" style="background: rgba(198, 255, 92, 0.2); color: #101c3d; border: 1px solid #8fd12a;">
                                Curso Concluído 🎉
                            </span>
                        <?php endif; ?>
                    </div>

                </section>

                <!-- SIDEBAR: PLAYLIST DE TODAS AS AULAS -->
                <aside class="aula-sidebar">
                    <h2>Conteúdo do Curso</h2>

                    <div class="aulas-lista">
                        <?php if (!empty($lista_aulas)): ?>
                            <?php foreach ($lista_aulas as $pos => $a_item): 
                                $is_ativa = ((int)$a_item["id"] === (int)$aula_atual["id"]);
                            ?>
                                <a
                                    class="aula-item <?= $is_ativa ? 'ativa' : '' ?>"
                                    href="aula.php?curso_id=<?= $curso_id ?>&aula_id=<?= $a_item["id"] ?>"
                                >
                                    <span class="num"><?= str_pad($pos + 1, 2, '0', STR_PAD_LEFT) ?></span>
                                    <div class="texto">
                                        <strong><?= htmlspecialchars($a_item["titulo"]) ?></strong>
                                        <span><?= $is_ativa ? 'Assistindo agora' : 'Disponível' ?></span>
                                    </div>
                                    <span class="status">
                                        <?= $is_ativa ? '▶ Tocando' : 'Assistir' ?>
                                    </span>
                                </a>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p style="color: #64748b; font-size: 0.85rem;">Nenhuma aula cadastrada ainda.</p>
                        <?php endif; ?>
                    </div>
                </aside>

            </div>
        </main>

    <?php endif; ?>

    <footer class="site-footer">
        &copy; 2026 Fluux. Todos os direitos reservados.
    </footer>

    <script src="../js/matricula.js"></script>

</body>
</html>
