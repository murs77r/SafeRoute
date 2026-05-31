<?php

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$arquivo = __DIR__ . $path;

if ($path !== '/' && is_file($arquivo)) {
    return false;
}

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Accept');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

function decodificarJsonResposta($conteudo) {
    $conteudo = trim($conteudo);

    if ($conteudo === '') {
        return null;
    }

    $dados = json_decode($conteudo, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        return $conteudo;
    }

    return $dados;
}

function deveRegistrarSucesso($arquivo, $codigoStatus, $corpoResposta) {
    if (pathinfo($arquivo, PATHINFO_EXTENSION) !== 'php') {
        return false;
    }

    if ($codigoStatus < 200 || $codigoStatus >= 300) {
        return false;
    }

    $resposta = decodificarJsonResposta($corpoResposta);

    if (is_array($resposta) && array_key_exists('status', $resposta)) {
        return $resposta['status'] === 'sucesso';
    }

    return true;
}

function registrarSucessoEndpoint($conn, $path, $codigoStatus, $corpoResposta, $corpoRequisicao) {
    if (!isset($conn) || !($conn instanceof mysqli)) {
        return;
    }

    $registro = [
        'metodo' => $_SERVER['REQUEST_METHOD'],
        'endpoint' => $path,
        'status_code' => $codigoStatus,
        'ip' => $_SERVER['REMOTE_ADDR'] ?? null,
        'query_params' => $_GET,
        'body' => $corpoRequisicao,
        'resposta' => decodificarJsonResposta($corpoResposta),
        'registrado_em' => gmdate('c')
    ];

    $registroJson = json_encode($registro, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE);

    if ($registroJson === false) {
        return;
    }

    $stmt = $conn->prepare('INSERT INTO requisicoes_sucesso (endpoint, metodo, registro_json) VALUES (?, ?, ?)');

    if (!$stmt) {
        return;
    }

    $metodo = $_SERVER['REQUEST_METHOD'];
    $stmt->bind_param('sss', $path, $metodo, $registroJson);
    $stmt->execute();
    $stmt->close();
}

$rotas = [
    '/' => 'index.html',
    '/auth' => 'auth.php',
    '/auth.php' => 'auth.php',
    '/salvar_evento' => 'salvar_evento.php',
    '/salvar_evento.php' => 'salvar_evento.php',
    '/editar_evento' => 'editar_evento.php',
    '/editar_evento.php' => 'editar_evento.php',
    '/excluir_evento' => 'excluir_evento.php',
    '/excluir_evento.php' => 'excluir_evento.php',
    '/listar_eventos' => 'listar_eventos.php',
    '/listar_eventos.php' => 'listar_eventos.php'
];

if (isset($rotas[$path])) {
    $target = __DIR__ . '/' . $rotas[$path];

    if (is_file($target)) {
        if (pathinfo($target, PATHINFO_EXTENSION) === 'php') {
            ob_start();
            include $target;

            $corpoResposta = ob_get_clean();
            $codigoStatus = http_response_code();

            if ($codigoStatus === false) {
                $codigoStatus = 200;
            }

            $corpoRequisicao = isset($data) ? $data : null;

            if (deveRegistrarSucesso($target, $codigoStatus, $corpoResposta)) {
                registrarSucessoEndpoint(isset($conn) ? $conn : null, $path, $codigoStatus, $corpoResposta, $corpoRequisicao);
            }

            echo $corpoResposta;
            exit;
        }

        include $target;
        exit;
    }
}

http_response_code(404);
header('Content-Type: application/json');
echo json_encode([
    'status' => 'erro',
    'mensagem' => 'Endpoint nao encontrado.'
]);
