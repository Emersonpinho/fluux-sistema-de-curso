<?php

session_start();

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Fluux - Seu Sistema de Cursos</title>

    <link rel="stylesheet" href="css/global.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&family=Sora:wght@600;700;800&display=swap"
        rel="stylesheet"
    >
</head>

<body>

    <!-- =====================================================
         NAVBAR
    ====================================================== -->

    <div class="navbar">

        <div class="topbar">

            <!-- LOGO -->

            <a class="logo" href="index.php">

                <img
                    src="assets/images/logos/logo fluux BRANCA.png"
                    alt="Fluux"
                    class="logo-img"
                >

            </a>


            <!-- MENU -->

            <nav class="nav-links">

                <a href="index.php">
                    Cursos
                </a>

                <a href="pages/salvos.html">
                    Salvos
                </a>

                <a href="pages/sobre.html">
                    Sobre
                </a>

                <a href="pages/contato.html">
                    Contato
                </a>

            </nav>


            <!-- BUSCA -->

            <div class="nav-search">

                <svg
                    width="15"
                    height="15"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="white"
                    stroke-width="2"
                >

                    <circle
                        cx="11"
                        cy="11"
                        r="7"
                    ></circle>

                    <line
                        x1="21"
                        y1="21"
                        x2="16.65"
                        y2="16.65"
                    ></line>

                </svg>

                <input
                    type="text"
                    id="busca-curso"
                    placeholder="Buscar curso..."
                >

            </div>


            <!-- =================================================
                 ÁREA DE LOGIN / USUÁRIO
            ================================================== -->

            <nav class="topbar-actions">

                <?php if (isset($_SESSION["usuario_id"])): ?>

                    <!-- USUÁRIO LOGADO -->

                    <div class="usuario-logado">

                        <?php if (isset($_SESSION["tipo_usuario"]) && $_SESSION["tipo_usuario"] === "adm"): ?>
                            <a
                                href="admin/index.php"
                                class="usuario-link"
                                style="background: rgba(198, 255, 92, 0.15); padding: 5px 12px; border-radius: 999px; border: 1px solid rgba(198, 255, 92, 0.35);"
                            >

                                <span class="usuario-avatar" style="background: #c6ff5c; color: #101c3d;">
                                    <?= strtoupper(substr($_SESSION["usuario_nome"], 0, 1)) ?>
                                </span>

                                <span class="usuario-nome" style="color: #c6ff5c;">
                                    <?= htmlspecialchars($_SESSION["usuario_nome"]) ?> (ADM)
                                </span>

                            </a>
                        <?php else: ?>
                            <a
                                href="perfil/perfil.php"
                                class="usuario-link"
                            >

                                <span class="usuario-avatar">
                                    <?= strtoupper(substr($_SESSION["usuario_nome"], 0, 1)) ?>
                                </span>

                                <span class="usuario-nome">
                                    <?= htmlspecialchars($_SESSION["usuario_nome"]) ?>
                                </span>

                            </a>
                        <?php endif; ?>


                        <a
                            href="php/logout.php"
                            class="btn-sair"
                        >
                            Sair
                        </a>

                    </div>


                <?php else: ?>

                    <!-- USUÁRIO NÃO LOGADO -->

                    <div class="auth-links">

                        <a href="pages/login.php">
                            Entrar
                        </a>

                        <a href="pages/cadastro.html">
                            Cadastrar
                        </a>

                    </div>

                <?php endif; ?>

            </nav>

        </div>

    </div>


    <!-- =====================================================
         HERO
    ====================================================== -->

    <header class="hero">

        <div class="hero-grid">

            <div class="hero-content">

                <span class="hero-eyebrow">
                    Plataforma 100% gratuita
                </span>


                <h1>
                    Desperte seu potencial com
                    <span class="destaque">
                        nossos cursos
                    </span>.
                </h1>


                <p>
                    Cursos de tecnologia pensados para quem está começando do zero:
                    do primeiro código até os fundamentos de segurança da informação.
                </p>


                <div class="hero-cta-row">

                    <a
                        class="btn-primario"
                        href="cadastro.html"
                    >
                        Criar conta grátis
                    </a>

                    <a
                        class="link-secundario"
                        href="#cursos"
                    >
                        Ver cursos
                    </a>

                </div>


                <div class="hero-stats">

                    <div class="stat">

                        <strong>
                            5
                        </strong>

                        <span>
                            Cursos disponíveis
                        </span>

                    </div>


                    <div class="stat">

                        <strong>
                            100%
                        </strong>

                        <span>
                            Online e gratuito
                        </span>

                    </div>


                    <div class="stat">

                        <strong>
                            0 a 1
                        </strong>

                        <span>
                            Do zero ao avançado
                        </span>

                    </div>

                </div>

            </div>


            <!-- =================================================
                 ILUSTRAÇÃO
            ================================================== -->

            <div class="hero-visual">

                <svg
                    class="personagem"
                    viewBox="0 0 420 480"
                    xmlns="http://www.w3.org/2000/svg"
                    role="img"
                    aria-label="Ilustração de um estudante com mochila usando notebook"
                >

                    <ellipse
                        cx="210"
                        cy="452"
                        rx="120"
                        ry="16"
                        fill="#0d1530"
                        opacity=".18"
                    ></ellipse>


                    <!-- mochila -->

                    <path
                        d="M112 210 q-26 0 -26 30 v120 q0 26 26 26 h20 v-176 z"
                        fill="#8fd12a"
                    ></path>

                    <rect
                        x="118"
                        y="228"
                        width="26"
                        height="130"
                        rx="10"
                        fill="#c6ff5c"
                    ></rect>

                    <rect
                        x="124"
                        y="250"
                        width="14"
                        height="34"
                        rx="6"
                        fill="#8fd12a"
                    ></rect>


                    <!-- perna -->

                    <path
                        d="M168 330 q-6 60 -2 108 h34 q4 -54 10 -104 z"
                        fill="#101c3d"
                    ></path>

                    <path
                        d="M236 330 q10 56 14 108 h34 q0 -56 -8 -108 z"
                        fill="#0d1530"
                    ></path>

                    <rect
                        x="164"
                        y="430"
                        width="42"
                        height="20"
                        rx="8"
                        fill="#0a1226"
                    ></rect>

                    <rect
                        x="248"
                        y="430"
                        width="42"
                        height="20"
                        rx="8"
                        fill="#0a1226"
                    ></rect>


                    <!-- corpo / jaqueta -->

                    <path
                        d="M150 220 q60 -30 122 0 q14 8 16 46 v70 q0 20 -18 20 h-118 q-18 0 -18 -20 v-70 q2 -38 16 -46 z"
                        fill="#2857c6"
                    ></path>

                    <path
                        d="M182 224 q28 -14 58 0 l-8 106 h-42 z"
                        fill="#4aa9ef"
                    ></path>

                    <rect
                        x="196"
                        y="236"
                        width="30"
                        height="76"
                        rx="8"
                        fill="#c6ff5c"
                        opacity=".9"
                    ></rect>


                    <!-- braço -->

                    <path
                        d="M150 244 q-30 6 -34 40 q-2 22 16 30 l18 -14 q-8 -14 -2 -34 z"
                        fill="#2857c6"
                    ></path>

                    <path
                        d="M270 244 q30 6 34 40 q2 22 -16 30 l-18 -14 q8 -14 2 -34 z"
                        fill="#2857c6"
                    ></path>

                    <circle
                        cx="132"
                        cy="312"
                        r="13"
                        fill="#f2c199"
                    ></circle>

                    <circle
                        cx="288"
                        cy="312"
                        r="13"
                        fill="#f2c199"
                    ></circle>


                    <!-- pescoço e cabeça -->

                    <rect
                        x="192"
                        y="176"
                        width="36"
                        height="30"
                        rx="10"
                        fill="#f2c199"
                    ></rect>

                    <circle
                        cx="210"
                        cy="150"
                        r="46"
                        fill="#f2c199"
                    ></circle>


                    <!-- cabelo -->

                    <circle
                        cx="170"
                        cy="132"
                        r="17"
                        fill="#101c3d"
                    ></circle>

                    <circle
                        cx="184"
                        cy="112"
                        r="18"
                        fill="#101c3d"
                    ></circle>

                    <circle
                        cx="208"
                        cy="102"
                        r="19"
                        fill="#101c3d"
                    ></circle>

                    <circle
                        cx="234"
                        cy="108"
                        r="18"
                        fill="#101c3d"
                    ></circle>

                    <circle
                        cx="250"
                        cy="128"
                        r="16"
                        fill="#101c3d"
                    ></circle>

                    <circle
                        cx="192"
                        cy="120"
                        r="14"
                        fill="#101c3d"
                    ></circle>

                    <circle
                        cx="228"
                        cy="122"
                        r="13"
                        fill="#101c3d"
                    ></circle>


                    <!-- rosto -->

                    <circle
                        cx="194"
                        cy="152"
                        r="4"
                        fill="#1f2a44"
                    ></circle>

                    <circle
                        cx="226"
                        cy="152"
                        r="4"
                        fill="#1f2a44"
                    ></circle>

                    <path
                        d="M198 168 q12 10 24 0"
                        stroke="#1f2a44"
                        stroke-width="3"
                        fill="none"
                        stroke-linecap="round"
                    ></path>


                    <!-- notebook -->

                    <g transform="translate(148 292) rotate(-4)">

                        <rect
                            x="0"
                            y="0"
                            width="126"
                            height="80"
                            rx="8"
                            fill="#101c3d"
                        ></rect>

                        <rect
                            x="8"
                            y="8"
                            width="110"
                            height="64"
                            rx="4"
                            fill="#0a1226"
                        ></rect>

                        <rect
                            x="18"
                            y="20"
                            width="60"
                            height="7"
                            rx="3"
                            fill="#4aa9ef"
                        ></rect>

                        <rect
                            x="18"
                            y="34"
                            width="80"
                            height="7"
                            rx="3"
                            fill="#8892a8"
                        ></rect>

                        <rect
                            x="18"
                            y="48"
                            width="46"
                            height="7"
                            rx="3"
                            fill="#c6ff5c"
                        ></rect>

                        <rect
                            x="-6"
                            y="78"
                            width="138"
                            height="10"
                            rx="4"
                            fill="#1a2650"
                        ></rect>

                    </g>

                </svg>


                <div class="badge-flutuante badge-nota">

                    <span class="badge-icone">
                        🎓
                    </span>

                    <span>
                        100% gratuito
                        <small>
                            sem mensalidade
                        </small>
                    </span>

                </div>


                <div class="badge-flutuante badge-alunos">

                    <span class="badge-icone">
                        💻
                    </span>

                    <span>
                        5 cursos
                        <small>
                            prontos pra começar
                        </small>
                    </span>

                </div>

            </div>

        </div>

    </header>


    <!-- =====================================================
         CURSOS
    ====================================================== -->

    <main class="container" id="cursos">

        <div class="section-titulo">

            <div>

                <span class="section-eyebrow">
                    Nossos cursos
                </span>

                <h2>
                    Escolha por onde começar
                </h2>

            </div>

            <p class="subtitle">
                Selecione um curso para visualizar mais informações.
            </p>

        </div>


        <section class="cursos-grid">


            <!-- CURSO 001 -->

            <article class="curso-card">

                <div class="curso-thumb">

                    <img
                        src="assets/images/cursos/web.webp"
                        alt="Curso Desenvolvimento Web"
                    >

                    <span class="curso-tag">
                        Iniciante
                    </span>

                </div>


                <div class="curso-info">

                    <span class="curso-codigo">
                        Código 001
                    </span>

                    <h3>
                        Desenvolvimento Web
                    </h3>

                    <span class="curso-carga">
                        Em andamento
                    </span>


                    <div class="curso-acoes">

                        <a
                            class="btn-detalhes"
                            href="pages/cursos/curso-web.html"
                        >
                            Realizar curso
                        </a>

                        <button
                            class="btn-salvar btn-favorito"
                            data-codigo="001"
                            aria-label="Salvar curso"
                            title="Salvar curso"
                        >

                            <svg
                                width="18"
                                height="18"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            >

                                <path
                                    d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"
                                ></path>

                            </svg>

                        </button>

                    </div>

                </div>

            </article>


            <!-- CURSO 002 -->

            <article class="curso-card">

                <div class="curso-thumb">

                    <img
                        src="assets/images/cursos/backend.webp"
                        alt="Curso Backend"
                    >

                    <span class="curso-tag">
                        Intermediário
                    </span>

                </div>


                <div class="curso-info">

                    <span class="curso-codigo">
                        Código 002
                    </span>

                    <h3>
                        Desenvolvimento Backend
                    </h3>

                    <span class="curso-carga">
                        Em andamento
                    </span>


                    <div class="curso-acoes">

                        <a
                            class="btn-detalhes"
                            href="pages/cursos/curso-backend.html"
                        >
                            Realizar curso
                        </a>

                        <button
                            class="btn-salvar btn-favorito"
                            data-codigo="002"
                            aria-label="Salvar curso"
                            title="Salvar curso"
                        >

                            <svg
                                width="18"
                                height="18"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            >

                                <path
                                    d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"
                                ></path>

                            </svg>

                        </button>

                    </div>

                </div>

            </article>


            <!-- CURSO 003 -->

            <article class="curso-card">

                <div class="curso-thumb">

                    <img
                        src="assets/images/cursos/mobile.jpg"
                        alt="Curso Mobile"
                    >

                    <span class="curso-tag">
                        Iniciante
                    </span>

                </div>


                <div class="curso-info">

                    <span class="curso-codigo">
                        Código 003
                    </span>

                    <h3>
                        Desenvolvimento Mobile
                    </h3>

                    <span class="curso-carga">
                        Em andamento
                    </span>


                    <div class="curso-acoes">

                        <a
                            class="btn-detalhes"
                            href="pages/cursos/curso-mobile.html"
                        >
                            Realizar curso
                        </a>

                        <button
                            class="btn-salvar btn-favorito"
                            data-codigo="003"
                            aria-label="Salvar curso"
                            title="Salvar curso"
                        >

                            <svg
                                width="18"
                                height="18"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            >

                                <path
                                    d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"
                                ></path>

                            </svg>

                        </button>

                    </div>

                </div>

            </article>


            <!-- CURSO 004 -->

            <article class="curso-card">

                <div class="curso-thumb">

                    <img
                        src="assets/images/cursos/algoritmo.jpg"
                        alt="Estrutura de Dados"
                    >

                    <span class="curso-tag">
                        Avançado
                    </span>

                </div>


                <div class="curso-info">

                    <span class="curso-codigo">
                        Código 004
                    </span>

                    <h3>
                        Estrutura de Dados e Algoritmos
                    </h3>

                    <span class="curso-carga">
                        Em andamento
                    </span>


                    <div class="curso-acoes">

                        <a
                            class="btn-detalhes"
                            href="pages/cursos/curso-estrutura-dados.html"
                        >
                            Realizar curso
                        </a>

                        <button
                            class="btn-salvar btn-favorito"
                            data-codigo="004"
                            aria-label="Salvar curso"
                            title="Salvar curso"
                        >

                            <svg
                                width="18"
                                height="18"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            >

                                <path
                                    d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"
                                ></path>

                            </svg>

                        </button>

                    </div>

                </div>

            </article>


            <!-- CURSO 005 -->

            <article class="curso-card">

                <div class="curso-thumb">

                    <img
                        src="assets/images/cursos/ciberseguranca.webp"
                        alt="Curso Cibersegurança"
                    >

                    <span class="curso-tag">
                        Intermediário
                    </span>

                </div>


                <div class="curso-info">

                    <span class="curso-codigo">
                        Código 005
                    </span>

                    <h3>
                        Cibersegurança
                    </h3>

                    <span class="curso-carga">
                        Em andamento
                    </span>


                    <div class="curso-acoes">

                        <a
                            class="btn-detalhes"
                            href="pages/cursos/curso-ciberseguranca.html"
                        >
                            Realizar curso
                        </a>

                        <button
                            class="btn-salvar btn-favorito"
                            data-codigo="005"
                            aria-label="Salvar curso"
                            title="Salvar curso"
                        >

                            <svg
                                width="18"
                                height="18"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            >

                                <path
                                    d="M19 21l-7-5-7 5V5a2 2 0 0 1 2 2z"
                                ></path>

                            </svg>

                        </button>

                    </div>

                </div>

            </article>

        </section>


        <!-- FOOTER -->

        <footer class="site-footer">

            &copy; 2026 Fluux. Todos os direitos reservados.

        </footer>

    </main>


    <!-- SCRIPTS -->

    <script src="js/busca-curso.js"></script>

    <script src="js/favoritos.js"></script>

</body>

</html>