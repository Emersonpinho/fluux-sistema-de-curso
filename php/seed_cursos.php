<?php

/**
 * Script de Auto-Seeding para Cursos Iniciais
 * Insere os cursos padrão do Fluux se a tabela estiver vazia.
 */

if (!isset($conexao)) {
    require_once "conexao.php";
}

/** @var mysqli $conexao */

// 1. Garante que os níveis existam
mysqli_query($conexao, "INSERT IGNORE INTO nivel (id, nome) VALUES (1, 'Iniciante'), (2, 'Intermediário'), (3, 'Avançado')");

// 2. Garante que um administrador padrão exista para associar aos cursos
$adm_padrao_id = isset($_SESSION["usuario_id"]) ? $_SESSION["usuario_id"] : "ADM01";
$adm_padrao_id_escapado = mysqli_real_escape_string($conexao, $adm_padrao_id);

$check_adm = mysqli_query($conexao, "SELECT id FROM adm WHERE id = '$adm_padrao_id_escapado'");
if ($check_adm && mysqli_num_rows($check_adm) === 0) {
    mysqli_query($conexao, "INSERT IGNORE INTO adm (id, nome, email, senha) VALUES ('$adm_padrao_id_escapado', 'Administrador Fluux', 'adm@fluux.com', 'admin123')");
}

// 3. Verifica se já existem cursos
$check_cursos = mysqli_query($conexao, "SELECT COUNT(*) AS total FROM curso");
$total_existente = 0;

if ($check_cursos) {
    $row = mysqli_fetch_assoc($check_cursos);
    $total_existente = isset($row["total"]) ? (int)$row["total"] : 0;
}

// Se não houver cursos, insere os 5 cursos padrão do portal
if ($total_existente === 0) {
    $cursos_padrao = [
        [
            "nome"     => "Desenvolvimento Web",
            "status"   => "ativo",
            "ementa"   => "Aprenda os fundamentos de HTML5, CSS3 e JavaScript para criar sites modernos, interativos e responsivos.",
            "nivel_id" => 1,
            "duracao"  => 40
        ],
        [
            "nome"     => "Desenvolvimento Backend",
            "status"   => "ativo",
            "ementa"   => "Domine a lógica de programação no servidor, construção de APIs REST, PHP e manipulação de banco de dados MySQL.",
            "nivel_id" => 2,
            "duracao"  => 60
        ],
        [
            "nome"     => "Desenvolvimento Mobile",
            "status"   => "ativo",
            "ementa"   => "Crie aplicativos modernos para Android e iOS com foco em boas práticas de design, arquitetura e usabilidade mobile.",
            "nivel_id" => 1,
            "duracao"  => 50
        ],
        [
            "nome"     => "Estrutura de Dados e Algoritmos",
            "status"   => "ativo",
            "ementa"   => "Aprofunde seus conhecimentos em listas encadeadas, árvores, grafos e algoritmos de busca e ordenação para otimizar sistemas.",
            "nivel_id" => 3,
            "duracao"  => 80
        ],
        [
            "nome"     => "Cibersegurança",
            "status"   => "ativo",
            "ementa"   => "Conceitos fundamentais de segurança da informação, redes de computadores, criptografia e proteção contra vulnerabilidades digitais.",
            "nivel_id" => 2,
            "duracao"  => 45
        ]
    ];

    foreach ($cursos_padrao as $c) {
        $nome_esc = mysqli_real_escape_string($conexao, $c["nome"]);
        $status_esc = mysqli_real_escape_string($conexao, $c["status"]);
        $ementa_esc = mysqli_real_escape_string($conexao, $c["ementa"]);
        $nivel_id_int = (int)$c["nivel_id"];
        $duracao_int = (int)$c["duracao"];

        $sql = "INSERT INTO curso (adm_id, nome, status, ementa, nivel_id, duracao) 
                VALUES ('$adm_padrao_id_escapado', '$nome_esc', '$status_esc', '$ementa_esc', $nivel_id_int, $duracao_int)";
        mysqli_query($conexao, $sql);
    }
}
?>
