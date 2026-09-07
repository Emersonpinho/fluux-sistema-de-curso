<?php

session_start();

require "../php/conexao.php";

/** @var mysqli $conexao */


/* =====================================================
   VERIFICA SE O USUÁRIO ESTÁ LOGADO
===================================================== */

if (!isset($_SESSION["usuario_id"])) {

    header("Location: ../pages/login.html");
    exit;

}

// Se for administrador, redireciona para o painel admin
if (isset($_SESSION["tipo_usuario"]) && $_SESSION["tipo_usuario"] === "adm") {
    header("Location: ../admin/index.php");
    exit;
}


/* =====================================================
   PEGA A MATRÍCULA DA SESSÃO
===================================================== */

$matricula = $_SESSION["usuario_id"];


/* =====================================================
   BUSCA OS DADOS DO USUÁRIO
===================================================== */

$sql = "SELECT matricula, nome, cpf, email, nivel_id
        FROM aluno
        WHERE matricula = '$matricula'";

$resultado = mysqli_query($conexao, $sql);

if (!$resultado) {
    die("Erro ao buscar usuário: " . mysqli_error($conexao));
}

$aluno = mysqli_fetch_assoc($resultado);

if (!$aluno) {
    die("Usuário não encontrado.");
}


/* =====================================================
   DADOS PARA EXIBIÇÃO
===================================================== */

$nome = $aluno["nome"];
$email = $aluno["email"];
$cpf = $aluno["cpf"];
$matricula = $aluno["matricula"];
$nivel_id = $aluno["nivel_id"];

$inicial = strtoupper(substr($nome, 0, 1));


/* =====================================================
   BUSCA AS MATRÍCULAS DO ALUNO (Tabela matricula_curso)
===================================================== */

$sql_matriculas = "
    SELECT c.codigo, c.nome, c.ementa, c.duracao, n.nome AS nivel_nome
    FROM matricula_curso mc
    JOIN curso c ON mc.curso_codigo = c.codigo
    JOIN nivel n ON c.nivel_id = n.id
    WHERE mc.aluno_matricula = '$matricula'
    ORDER BY c.codigo ASC
";

$resultado_matriculas = mysqli_query($conexao, $sql_matriculas);
$cursos_matriculados = [];

if ($resultado_matriculas) {
    while ($row_m = mysqli_fetch_assoc($resultado_matriculas)) {
        $cursos_matriculados[] = $row_m;
    }
}


/* =====================================================
   BUSCA OS CURSOS SALVOS DO ALUNO (Tabela curso_salvo)
===================================================== */

$sql_salvos = "
    SELECT c.codigo, c.nome, c.ementa, c.duracao, n.nome AS nivel_nome
    FROM curso_salvo cs
    JOIN curso c ON cs.curso_codigo = c.codigo
    JOIN nivel n ON c.nivel_id = n.id
    WHERE cs.aluno_matricula = '$matricula'
    ORDER BY c.codigo ASC
";

$resultado_salvos = mysqli_query($conexao, $sql_salvos);
$cursos_salvos = [];

if ($resultado_salvos) {
    while ($row = mysqli_fetch_assoc($resultado_salvos)) {
        $cursos_salvos[] = $row;
    }
}

// Mapa de imagens e links padrão dos cursos
$info_cursos = [
    1 => ["imagem" => "../assets/images/cursos/web.webp", "link" => "../pages/cursos/curso-web.html"],
    2 => ["imagem" => "../assets/images/cursos/backend.webp", "link" => "../pages/cursos/curso-backend.html"],
    3 => ["imagem" => "../assets/images/cursos/mobile.jpg", "link" => "../pages/cursos/curso-mobile.html"],
    4 => ["imagem" => "../assets/images/cursos/algoritmo.jpg", "link" => "../pages/cursos/curso-estrutura-dados.html"],
    5 => ["imagem" => "../assets/images/cursos/ciberseguranca.webp", "link" => "../pages/cursos/curso-ciberseguranca.html"],
];

mysqli_close($conexao);

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Meu Perfil - Fluux</title>

    <link rel="stylesheet" href="../css/perfil.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&family=Sora:wght@600;700;800&display=swap"
        rel="stylesheet"
    >

</head>


<body>


    <!-- =================================================
         NAVBAR
    ================================================== -->

    <div class="navbar">

        <div class="topbar">


            <a class="logo" href="../index.php">

                <img
                    src="../assets/images/logos/logo fluux BRANCA.png"
                    alt="Fluux"
                    class="logo-img"
                >

            </a>


            <nav class="nav-links">

                <a href="../index.php">
                    Cursos
                </a>

                <a href="../pages/salvos.html">
                    Salvos
                </a>

                <a href="../pages/sobre.html">
                    Sobre
                </a>

                <a href="../pages/contato.html">
                    Contato
                </a>

            </nav>


            <div class="perfil-nav">

                <div class="perfil-avatar pequeno">
                    <?php echo htmlspecialchars($inicial); ?>
                </div>

                <span>
                    <?php echo htmlspecialchars($nome); ?>
                </span>

                <a href="../php/logout.php" class="btn-sair">
                    Sair
                </a>

            </div>


        </div>

    </div>



    <!-- =================================================
         CONTEÚDO
    ================================================== -->

    <main class="perfil-container">


        <section class="perfil-card">


            <!-- CABEÇALHO DO PERFIL -->

            <div class="perfil-header">


                <div class="perfil-avatar grande">

                    <?php echo htmlspecialchars($inicial); ?>

                </div>


                <div>

                    <span class="perfil-label">
                        Meu perfil
                    </span>

                    <h1>
                        <?php echo htmlspecialchars($nome); ?>
                    </h1>

                    <p>
                        <?php echo htmlspecialchars($email); ?>
                    </p>

                </div>


            </div>



            <!-- INFORMAÇÕES -->

            <div class="perfil-info">


                <h2>
                    Informações da conta
                </h2>


                <div class="info-grid">


                    <div class="info-item">

                        <span>
                            Nome
                        </span>

                        <strong>
                            <?php echo htmlspecialchars($nome); ?>
                        </strong>

                    </div>


                    <div class="info-item">

                        <span>
                            E-mail
                        </span>

                        <strong>
                            <?php echo htmlspecialchars($email); ?>
                        </strong>

                    </div>


                    <div class="info-item">

                        <span>
                            Matrícula
                        </span>

                        <strong>
                            <?php echo htmlspecialchars($matricula); ?>
                        </strong>

                    </div>


                    <div class="info-item">

                        <span>
                            CPF
                        </span>

                        <strong>
                            <?php echo htmlspecialchars($cpf); ?>
                        </strong>

                    </div>


                    <div class="info-item">

                        <span>
                            Nível da conta
                        </span>

                        <strong>
                            <?php echo htmlspecialchars($nivel_id); ?>
                        </strong>

                    </div>


                </div>


            </div>



            <!-- AÇÕES -->

            <div class="perfil-acoes">

                <a href="../index.php" class="btn-voltar">
                    Voltar para cursos
                </a>

                <a href="../php/logout.php" class="btn-sair-perfil">
                    Sair da conta
                </a>

            </div>


        </section>


        <!-- =================================================
             MEUS CURSOS EM ANDAMENTO (Tabela matricula_curso)
        ================================================== -->

        <section class="perfil-card perfil-card-salvos" style="margin-top: 30px;">

            <div class="perfil-secao-header">
                <div>
                    <h2>Meus Cursos em Andamento</h2>
                    <p class="perfil-secao-desc">Cursos em que você está matriculado atualmente.</p>
                </div>
                <span class="badge-contagem badge-contagem-matriculas" style="background: rgba(198, 255, 92, 0.2); color: #101c3d; border: 1px solid #8fd12a;"><?php echo count($cursos_matriculados); ?> matriculado(s)</span>
            </div>

            <?php if (count($cursos_matriculados) > 0): ?>

                <div class="salvos-grid-perfil matriculas-grid-perfil">

                    <?php foreach ($cursos_matriculados as $c_mat): 
                        $cod_num = (int)$c_mat["codigo"];
                        $cod_pad = str_pad($cod_num, 3, "0", STR_PAD_LEFT);
                        $img = isset($info_cursos[$cod_num]["imagem"]) ? $info_cursos[$cod_num]["imagem"] : "../assets/images/cursos/web.webp";
                        $lnk = isset($info_cursos[$cod_num]["link"]) ? $info_cursos[$cod_num]["link"] : "../index.php";
                    ?>

                        <article class="curso-card-perfil" data-card-matricula="<?php echo $cod_pad; ?>">

                            <div class="curso-thumb-perfil">
                                <img src="<?php echo $img; ?>" alt="<?php echo htmlspecialchars($c_mat["nome"]); ?>">
                                <span class="curso-tag-perfil" style="background: #101c3d; color: #c6ff5c; border: 1px solid #c6ff5c;">Matriculado ✓</span>
                            </div>

                            <div class="curso-info-perfil">
                                <span class="curso-cod-perfil">Código <?php echo $cod_pad; ?> • <?= htmlspecialchars($c_mat["nivel_nome"]) ?></span>
                                <h3><?php echo htmlspecialchars($c_mat["nome"]); ?></h3>
                                <p class="curso-desc-perfil"><?php echo htmlspecialchars($c_mat["ementa"]); ?></p>

                                <div class="curso-meta-perfil">
                                    <span>⏱️ <?php echo htmlspecialchars($c_mat["duracao"]); ?>h de carga horária</span>
                                </div>

                                <div class="curso-acoes-perfil">
                                    <a href="../aulas/aula.php?curso_id=<?php echo $cod_num; ?>" class="btn-acessar-curso">Estudar agora →</a>
                                    <button class="btn-remover-salvo btn-cancelar-matricula" data-codigo="<?php echo $cod_pad; ?>" aria-label="Cancelar matrícula" title="Cancelar matrícula">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <line x1="18" y1="6" x2="6" y2="18"></line>
                                            <line x1="6" y1="6" x2="18" y2="18"></line>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                        </article>

                    <?php endforeach; ?>

                </div>

            <?php else: ?>

                <div class="empty-salvos-perfil empty-matriculas-perfil">
                    <div class="empty-icone">🎓</div>
                    <h3>Você ainda não está matriculado em nenhum curso</h3>
                    <p>Escolha um dos nossos cursos gratuitos e dê o próximo passo na sua carreira!</p>
                    <a href="../index.php#cursos" class="btn-explorar">Ver cursos disponíveis</a>
                </div>

            <?php endif; ?>

        </section>


        <!-- =================================================
             MEUS CURSOS SALVOS (Tabela curso_salvo)
        ================================================== -->

        <section class="perfil-card perfil-card-salvos">

            <div class="perfil-secao-header">
                <div>
                    <h2>Meus Cursos Salvos</h2>
                    <p class="perfil-secao-desc">Cursos que você guardou para assistir ou consultar mais tarde.</p>
                </div>
                <span class="badge-contagem"><?php echo count($cursos_salvos); ?> salvo(s)</span>
            </div>

            <?php if (count($cursos_salvos) > 0): ?>

                <div class="salvos-grid-perfil">

                    <?php foreach ($cursos_salvos as $curso): 
                        $cod_num = (int)$curso["codigo"];
                        $cod_pad = str_pad($cod_num, 3, "0", STR_PAD_LEFT);
                        $img = isset($info_cursos[$cod_num]["imagem"]) ? $info_cursos[$cod_num]["imagem"] : "../assets/images/cursos/web.webp";
                        $lnk = isset($info_cursos[$cod_num]["link"]) ? $info_cursos[$cod_num]["link"] : "../index.php";
                    ?>

                        <article class="curso-card-perfil" data-card-codigo="<?php echo $cod_pad; ?>">

                            <div class="curso-thumb-perfil">
                                <img src="<?php echo $img; ?>" alt="<?php echo htmlspecialchars($curso["nome"]); ?>">
                                <span class="curso-tag-perfil"><?php echo htmlspecialchars($curso["nivel_nome"]); ?></span>
                            </div>

                            <div class="curso-info-perfil">
                                <span class="curso-cod-perfil">Código <?php echo $cod_pad; ?></span>
                                <h3><?php echo htmlspecialchars($curso["nome"]); ?></h3>
                                <p class="curso-desc-perfil"><?php echo htmlspecialchars($curso["ementa"]); ?></p>

                                <div class="curso-meta-perfil">
                                    <span>⏱️ <?php echo htmlspecialchars($curso["duracao"]); ?>h de carga horária</span>
                                </div>

                                <div class="curso-acoes-perfil">
                                    <a href="<?php echo $lnk; ?>" class="btn-acessar-curso">Acessar curso</a>
                                    <button class="btn-remover-salvo btn-favorito salvo favoritado" data-codigo="<?php echo $cod_pad; ?>" aria-label="Remover dos salvos" title="Remover dos salvos">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="2">
                                            <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                        </article>

                    <?php endforeach; ?>

                </div>

            <?php else: ?>

                <div class="empty-salvos-perfil">
                    <div class="empty-icone">🔖</div>
                    <h3>Você ainda não possui cursos salvos</h3>
                    <p>Explore os cursos disponíveis na nossa plataforma e salve os seus preferidos para encontrá-los aqui a qualquer momento.</p>
                    <a href="../index.php#cursos" class="btn-explorar">Explorar cursos</a>
                </div>

            <?php endif; ?>

        </section>


    </main>



    <footer class="site-footer">

        &copy; 2026 Fluux. Todos os direitos reservados.

    </footer>


    <script src="../js/favoritos.js"></script>
    <script src="../js/matricula.js"></script>

</body>

</html>