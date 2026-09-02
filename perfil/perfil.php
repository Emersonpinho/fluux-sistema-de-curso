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


    </main>



    <footer class="site-footer">

        &copy; 2026 Fluux. Todos os direitos reservados.

    </footer>


</body>

</html>