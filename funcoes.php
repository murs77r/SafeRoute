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