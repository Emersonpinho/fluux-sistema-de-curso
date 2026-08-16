<?php
// Endpoint PHP para gerenciamento de favoritos do aluno
session_start();

header('Content-Type: application/json; charset=utf-8');

// Inicializa a lista de favoritos na sessão se não existir
if (!isset($_SESSION['favoritos']) || !is_array($_SESSION['favoritos'])) {
    $_SESSION['favoritos'] = array();
}

$metodo = $_SERVER['REQUEST_METHOD'];

if ($metodo === 'GET') {
    // Retorna a lista atual de favoritos
    echo json_encode(array(
        'success' => true,
        'favoritos' => array_values($_SESSION['favoritos'])
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

    $favoritado = false;
    $pos = array_search($codigo, $_SESSION['favoritos']);

    if ($pos !== false) {
        // Já está favoritado -> remove
        array_splice($_SESSION['favoritos'], $pos, 1);
        $favoritado = false;
        $mensagem = 'Curso removido dos favoritos!';
    } else {
        // Não está favoritado -> adiciona
        $_SESSION['favoritos'][] = $codigo;
        $favoritado = true;
        $mensagem = 'Curso adicionado aos favoritos!';
    }

    echo json_encode(array(
        'success' => true,
        'codigo' => $codigo,
        'favoritado' => $favoritado,
        'mensagem' => $mensagem,
        'favoritos' => array_values($_SESSION['favoritos'])
    ));
    exit;
}

echo json_encode(array(
    'success' => false,
    'message' => 'Método de requisição não suportado.'
));
exit;
