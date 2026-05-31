<?php
function habilitar_cors_livre() {
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Accept");
    header("Access-Control-Max-Age: 86400");

    if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
        http_response_code(204);
        exit;
    }
}

function responder_json($codigo, $payload) {
    http_response_code($codigo);
    echo json_encode($payload);
    exit;
}

function sucesso($mensagem, $dados = [], $codigo = 200) {
    responder_json($codigo, array_merge([
        "status" => "sucesso",
        "mensagem" => $mensagem
    ], $dados));
}

function erro($mensagem, $codigo = 400) {
    responder_json($codigo, [
        "status" => "erro",
        "mensagem" => $mensagem
    ]);
}

function exigir_metodo($metodo) {
    if ($_SERVER["REQUEST_METHOD"] !== $metodo) {
        erro("Metodo HTTP nao permitido.", 405);
    }
}

function obter_json_body() {
    $conteudo = file_get_contents("php://input");
    $dados = json_decode($conteudo, true);

    if (!is_array($dados)) {
        erro("JSON invalido no corpo da requisicao.", 400);
    }

    return $dados;
}

function sair($con) {
    $con->close();
    exit;
}

function enviar_post($data, $path) {
    $opcoes = [
        "http" => [
            "method" => "POST",
            "header" => "Content-Type: application/json\r\n",
            "content" => json_encode($data)
        ]
    ];
    $contexto = stream_context_create($opcoes);
    $resposta = file_get_contents("http://localhost/".$path.".php",false,$contexto);

    echo $resposta;
}