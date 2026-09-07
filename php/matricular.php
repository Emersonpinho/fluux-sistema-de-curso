<?php
// Endpoint para gerenciamento de matrículas (tabela matricula_curso)
session_start();

header('Content-Type: application/json; charset=utf-8');

require_once "conexao.php";

/** @var mysqli $conexao */

$esta_logado = isset($_SESSION["usuario_id"]) && (!isset($_SESSION["tipo_usuario"]) || $_SESSION["tipo_usuario"] === "aluno");
$matricula = $esta_logado ? (int)$_SESSION["usuario_id"] : null;

$metodo = $_SERVER['REQUEST_METHOD'];

// Consulta as matrículas atuais do aluno
if ($metodo === 'GET') {
    if (!$esta_logado) {
        echo json_encode(array(
            'success' => true,
            'logado' => false,
            'matriculas' => array()
        ));
        exit;
    }

    $lista = array();
    $sql = "SELECT curso_codigo FROM matricula_curso WHERE aluno_matricula = $matricula";
    $res = mysqli_query($conexao, $sql);

    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            $cd = (int)$row["curso_codigo"];
            $lista[] = str_pad($cd, 3, "0", STR_PAD_LEFT);
            $lista[] = (string)$cd;
        }
        $lista = array_values(array_unique($lista));
    }

    echo json_encode(array(
        'success' => true,
        'logado' => true,
        'matriculas' => $lista
    ));
    exit;
}

// Realiza ou cancela uma matrícula
if ($metodo === 'POST') {
    if (!$esta_logado) {
        echo json_encode(array(
            'success' => false,
            'logado' => false,
            'message' => 'Faça login para se matricular no curso.'
        ));
        exit;
    }

    $inputRaw = file_get_contents('php://input');
    $data = json_decode($inputRaw, true);

    $codigo = null;
    $acao = 'matricular';

    if ($data && isset($data['codigo'])) {
        $codigo = trim($data['codigo']);
        if (isset($data['acao'])) $acao = trim($data['acao']);
    } else if (isset($_POST['codigo'])) {
        $codigo = trim($_POST['codigo']);
        if (isset($_POST['acao'])) $acao = trim($_POST['acao']);
    }

    if (!$codigo) {
        echo json_encode(array(
            'success' => false,
            'message' => 'Código do curso não informado.'
        ));
        exit;
    }

    $codigo_int = (int)$codigo;
    $codigo_str = str_pad($codigo_int, 3, "0", STR_PAD_LEFT);

    if ($acao === 'cancelar') {
        // Cancela a matrícula
        mysqli_query($conexao, "DELETE FROM matricula_curso WHERE aluno_matricula = $matricula AND curso_codigo = $codigo_int");

        echo json_encode(array(
            'success' => true,
            'logado' => true,
            'codigo' => $codigo_str,
            'matriculado' => false,
            'mensagem' => 'Matrícula cancelada com sucesso.'
        ));
        exit;
    }

    // Verifica se já está matriculado
    $check = mysqli_query($conexao, "SELECT 1 FROM matricula_curso WHERE aluno_matricula = $matricula AND curso_codigo = $codigo_int");

    if ($check && mysqli_num_rows($check) > 0) {
        echo json_encode(array(
            'success' => true,
            'logado' => true,
            'codigo' => $codigo_str,
            'matriculado' => true,
            'mensagem' => 'Você já está matriculado neste curso!'
        ));
        exit;
    }

    // Insere a matrícula no banco
    $ins = mysqli_query($conexao, "INSERT INTO matricula_curso (aluno_matricula, curso_codigo) VALUES ($matricula, $codigo_int)");

    if ($ins) {
        echo json_encode(array(
            'success' => true,
            'logado' => true,
            'codigo' => $codigo_str,
            'matriculado' => true,
            'mensagem' => 'Matrícula confirmada com sucesso! Bem-vindo ao curso.'
        ));
    } else {
        echo json_encode(array(
            'success' => false,
            'message' => 'Erro ao realizar matrícula: ' . mysqli_error($conexao)
        ));
    }
    exit;
}

echo json_encode(array(
    'success' => false,
    'message' => 'Método de requisição não suportado.'
));
exit;
