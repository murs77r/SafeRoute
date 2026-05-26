<?php
function erro($mensagem) {
    echo json_encode([
        "status" => "erro",
        "mensagem" => $mensagem
    ]);
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