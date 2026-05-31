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

$rotas = [
    '/' => 'index.html',
    '/auth' => 'auth.php',
    '/auth.php' => 'auth.php',
    '/salvar_evento' => 'salvar_evento.php',
    '/salvar_evento.php' => 'salvar_evento.php',
    '/listar_eventos' => 'listar_eventos.php',
    '/listar_eventos.php' => 'listar_eventos.php'
];

if (isset($rotas[$path])) {
    $target = __DIR__ . '/' . $rotas[$path];

    if (is_file($target)) {
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
