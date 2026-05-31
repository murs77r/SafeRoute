<?php
header("Content-Type: application/json; charset=UTF-8");

include "conexao.php";
include "funcoes.php";

$data = json_decode(file_get_contents("php://input"), true);

$evento_id = isset($data["evento_id"]) ? $data["evento_id"] : (isset($data["id_evento"]) ? $data["id_evento"] : null);
$usuario_id = isset($data["usuario_id"]) ? $data["usuario_id"] : (isset($data["id_usuario"]) ? $data["id_usuario"] : null);

function buscarEventoParaExclusao($evento_id, $usuario_id) {
    global $conn;

    $stmt = $conn->prepare("SELECT id FROM eventos WHERE id = ? AND usuario_id = ?");

    if (!$stmt) {
        erro("Falha ao preparar consulta", 500);
        sair($conn);
    }

    $stmt->bind_param("ii", $evento_id, $usuario_id);
    $stmt->execute();

    return $stmt->get_result();
}

function excluirEvento($evento_id, $usuario_id) {
    global $conn;

    $stmt = $conn->prepare("DELETE FROM eventos WHERE id = ? AND usuario_id = ?");

    if (!$stmt) {
        erro("Falha ao preparar consulta", 500);
        sair($conn);
    }

    $stmt->bind_param("ii", $evento_id, $usuario_id);

    if ($stmt->execute() && $stmt->affected_rows === 1) {
        http_response_code(200);
        echo json_encode([
            "status" => "sucesso",
            "mensagem" => "Evento excluido com sucesso.",
            "evento_id" => $evento_id
        ]);
    } else {
        erro("Evento nao encontrado.", 404);
    }
}

if ($evento_id && $usuario_id) {
    $result = buscarEventoParaExclusao($evento_id, $usuario_id);

    if ($result && $result->num_rows === 1) {
        excluirEvento($evento_id, $usuario_id);
    } else {
        erro("Evento nao encontrado.", 404);
    }
} else {
    erro("Falha ao excluir o evento. Dados incompletos.", 400);
}