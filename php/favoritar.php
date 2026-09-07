<?php
// Endpoint PHP para gerenciamento de favoritos / cursos salvos do aluno
session_start();

header('Content-Type: application/json; charset=utf-8');

require_once "conexao.php";

/** @var mysqli $conexao */

// Inicializa a lista de favoritos na sessão se não existir
if (!isset($_SESSION['favoritos']) || !is_array($_SESSION['favoritos'])) {
    $_SESSION['favoritos'] = array();
}

$esta_logado = isset($_SESSION["usuario_id"]) && (!isset($_SESSION["tipo_usuario"]) || $_SESSION["tipo_usuario"] === "aluno");
$matricula = $esta_logado ? (int)$_SESSION["usuario_id"] : null;

$metodo = $_SERVER['REQUEST_METHOD'];

if ($metodo === 'GET') {
    $lista = array();

    if ($esta_logado && $conexao) {
        // Busca do banco de dados na tabela curso_salvo
        $sql = "SELECT curso_codigo FROM curso_salvo WHERE aluno_matricula = $matricula";
        $resultado = mysqli_query($conexao, $sql);

        if ($resultado) {
            while ($row = mysqli_fetch_assoc($resultado)) {
                $cd = (int)$row["curso_codigo"];
                $lista[] = str_pad($cd, 3, "0", STR_PAD_LEFT);
                $lista[] = (string)$cd;
            }
            $lista = array_values(array_unique($lista));
            $_SESSION['favoritos'] = $lista;
        } else {
            $lista = array_values($_SESSION['favoritos']);
        }
    } else {
        // Fallback para sessão se não logado
        $lista = array_values($_SESSION['favoritos']);
    }

    echo json_encode(array(
        'success' => true,
        'logado' => $esta_logado,
        'favoritos' => $lista
    ));
    exit;
}

if ($metodo === 'POST') {
    // Recebe os dados em formato JSON ou via POST tradicional
    $inputRaw = file_get_contents('php://input');
    $data = json_decode($inputRaw, true);
    
    $codigo = null;
    if ($data && isset($data['codigo'])) {
        $codigo = trim($data['codigo']);
    } else if (isset($_POST['codigo'])) {
        $codigo = trim($_POST['codigo']);
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
    $favoritado = false;
    $mensagem = '';

    if ($esta_logado && $conexao) {
        // Verifica se já está salvo no banco
        $sql_check = "SELECT 1 FROM curso_salvo WHERE aluno_matricula = $matricula AND curso_codigo = $codigo_int";
        $check = mysqli_query($conexao, $sql_check);

        if ($check && mysqli_num_rows($check) > 0) {
            // Já está salvo -> Remove
            mysqli_query($conexao, "DELETE FROM curso_salvo WHERE aluno_matricula = $matricula AND curso_codigo = $codigo_int");
            $favoritado = false;
            $mensagem = 'Curso removido dos salvos!';
        } else {
            // Não está salvo -> Insere
            mysqli_query($conexao, "INSERT IGNORE INTO curso_salvo (aluno_matricula, curso_codigo) VALUES ($matricula, $codigo_int)");
            $favoritado = true;
            $mensagem = 'Curso salvo com sucesso!';
        }

        // Atualiza a lista atual do banco
        $sql_todos = "SELECT curso_codigo FROM curso_salvo WHERE aluno_matricula = $matricula";
        $res_todos = mysqli_query($conexao, $sql_todos);
        $nova_lista = array();

        if ($res_todos) {
            while ($r = mysqli_fetch_assoc($res_todos)) {
                $cd = (int)$r["curso_codigo"];
                $nova_lista[] = str_pad($cd, 3, "0", STR_PAD_LEFT);
                $nova_lista[] = (string)$cd;
            }
            $nova_lista = array_values(array_unique($nova_lista));
        }
        $_SESSION['favoritos'] = $nova_lista;

    } else {
        // Se não logado, gerencia via sessão temporária
        $pos = array_search($codigo_str, $_SESSION['favoritos']);
        if ($pos === false) {
            $pos = array_search((string)$codigo_int, $_SESSION['favoritos']);
        }

        if ($pos !== false) {
            array_splice($_SESSION['favoritos'], $pos, 1);
            $favoritado = false;
            $mensagem = 'Curso removido dos salvos!';
        } else {
            $_SESSION['favoritos'][] = $codigo_str;
            $_SESSION['favoritos'][] = (string)$codigo_int;
            $_SESSION['favoritos'] = array_values(array_unique($_SESSION['favoritos']));
            $favoritado = true;
            $mensagem = 'Curso salvo! (Faça login para vincular à sua conta)';
        }
        $nova_lista = array_values($_SESSION['favoritos']);
    }

    echo json_encode(array(
        'success' => true,
        'logado' => $esta_logado,
        'codigo' => $codigo,
        'favoritado' => $favoritado,
        'mensagem' => $mensagem,
        'favoritos' => $nova_lista
    ));
    exit;
}

echo json_encode(array(
    'success' => false,
    'message' => 'Método de requisição não suportado.'
));
exit;

