<?php
function erro($mensagem, $codigo = 400) {
    http_response_code($codigo);
    echo json_encode([
        "status" => "erro",
        "mensagem" => $mensagem
    ]);
}

function sair($con) {
    $con->close();
    exit;
}