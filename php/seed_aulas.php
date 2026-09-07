<?php
/**
 * Auto-seeding para as tabelas aula e imagem_aula
 * Garante que existam aulas e imagens de apoio iniciais para os cursos.
 */

if (!isset($conexao)) {
    require_once "conexao.php";
}

/** @var mysqli $conexao */

// Garante que existam cursos antes de popular aulas
require_once "seed_cursos.php";

// Verifica se já existem aulas cadastradas
$check_aulas = mysqli_query($conexao, "SELECT COUNT(*) AS total FROM aula");
$total_aulas = 0;

if ($check_aulas) {
    $row = mysqli_fetch_assoc($check_aulas);
    $total_aulas = isset($row["total"]) ? (int)$row["total"] : 0;
}

if ($total_aulas === 0) {

    // Aulas iniciais organizadas por código do curso
    $aulas_iniciais = [
        // Curso 1: Desenvolvimento Web
        1 => [
            [
                "titulo"    => "01. Introdução ao HTML5 e Estrutura Semântica",
                "descricao" => "Conheça as principais tags estruturais do HTML5, semântica web e como montar a base do seu primeiro site.",
                "url"       => "https://www.youtube.com/embed/Ejkb_YpuHWs",
                "imagens"   => ["assets/images/cursos/web.webp"]
            ],
            [
                "titulo"    => "02. Estilização Moderna com CSS3 e Flexbox",
                "descricao" => "Aprenda a alinhar e distribuir elementos na tela de maneira responsiva utilizando CSS Flexbox e boas práticas de estilização.",
                "url"       => "https://www.youtube.com/embed/Ejkb_YpuHWs",
                "imagens"   => []
            ],
            [
                "titulo"    => "03. Interatividade com JavaScript e DOM",
                "descricao" => "Adicione vida e interatividade à sua página capturando eventos de clique, manipulando elementos e validando formulários.",
                "url"       => "https://www.youtube.com/embed/Ejkb_YpuHWs",
                "imagens"   => []
            ]
        ],

        // Curso 2: Desenvolvimento Backend
        2 => [
            [
                "titulo"    => "01. Fundamentos de Servidor e PHP",
                "descricao" => "Entenda como funciona o ciclo de requisição e resposta HTTP, instalação de ambiente local e sintaxe básica do PHP.",
                "url"       => "https://www.youtube.com/embed/Ejkb_YpuHWs",
                "imagens"   => ["assets/images/cursos/backend.webp"]
            ],
            [
                "titulo"    => "02. Conexão com Banco de Dados MySQL e CRUD",
                "descricao" => "Aprenda a realizar operações de inserção, consulta, atualização e remoção de dados conectando PHP com MySQL via mysqli.",
                "url"       => "https://www.youtube.com/embed/Ejkb_YpuHWs",
                "imagens"   => []
            ]
        ],

        // Curso 3: Desenvolvimento Mobile
        3 => [
            [
                "titulo"    => "01. Arquitetura e Primeiros Passos no Mobile",
                "descricao" => "Visão geral do ecossistema mobile, diferenças entre nativo e híbrido, e configuração inicial do ambiente de desenvolvimento.",
                "url"       => "https://www.youtube.com/embed/Ejkb_YpuHWs",
                "imagens"   => ["assets/images/cursos/mobile.jpg"]
            ]
        ],

        // Curso 4: Estrutura de Dados e Algoritmos
        4 => [
            [
                "titulo"    => "01. Complexidade de Algoritmos e Notação Big-O",
                "descricao" => "Aprenda a analisar a eficiência de tempo e memória dos seus códigos utilizando a notação assintótica Big-O.",
                "url"       => "https://www.youtube.com/embed/Ejkb_YpuHWs",
                "imagens"   => ["assets/images/cursos/algoritmo.jpg"]
            ]
        ],

        // Curso 5: Cibersegurança
        5 => [
            [
                "titulo"    => "01. Princípios de Segurança e Criptografia",
                "descricao" => "Conceitos fundamentais da tríade CIA (Confidencialidade, Integridade e Disponibilidade) e técnicas de proteção digital.",
                "url"       => "https://www.youtube.com/embed/Ejkb_YpuHWs",
                "imagens"   => ["assets/images/cursos/ciberseguranca.webp"]
            ]
        ]
    ];

    foreach ($aulas_iniciais as $curso_id => $lista_aulas) {
        // Verifica se o curso existe antes de inserir aulas
        $chk_curso = mysqli_query($conexao, "SELECT codigo FROM curso WHERE codigo = $curso_id");
        if ($chk_curso && mysqli_num_rows($chk_curso) > 0) {
            foreach ($lista_aulas as $a) {
                $tit_esc  = mysqli_real_escape_string($conexao, $a["titulo"]);
                $desc_esc = mysqli_real_escape_string($conexao, $a["descricao"]);
                $url_esc  = mysqli_real_escape_string($conexao, $a["url"]);

                $sql_aula = "INSERT INTO aula (curso_id, titulo, descricao, url) VALUES ($curso_id, '$tit_esc', '$desc_esc', '$url_esc')";
                if (mysqli_query($conexao, $sql_aula)) {
                    $aula_id_gerado = mysqli_insert_id($conexao);

                    // Insere as imagens de apoio na tabela imagem_aula
                    if (!empty($a["imagens"])) {
                        foreach ($a["imagens"] as $img_url) {
                            $img_esc = mysqli_real_escape_string($conexao, $img_url);
                            mysqli_query($conexao, "INSERT INTO imagem_aula (aula_id, url_imagem) VALUES ($aula_id_gerado, '$img_esc')");
                        }
                    }
                }
            }
        }
    }

}
