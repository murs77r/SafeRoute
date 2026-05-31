<?php
header("Content-Type: application/json; charset=UTF-8");

include "conexao.php";
include "funcoes.php";

$data = json_decode(file_get_contents("php://input"), true);

$evento_id = isset($data["evento_id"]) ? $data["evento_id"] : (isset($data["id_evento"]) ? $data["id_evento"] : null);
$usuario_id = isset($data["usuario_id"]) ? $data["usuario_id"] : (isset($data["id_usuario"]) ? $data["id_usuario"] : null);
$nome_disciplina = isset($data["nome_disciplina"]) ? $data["nome_disciplina"] : null;
$descricao_atividade = isset($data["descricao_atividade"]) ? $data["descricao_atividade"] : null;
$data_entrega = isset($data["data_entrega"]) ? $data["data_entrega"] : null;

function buscarEvento($evento_id, $usuario_id) {
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

function atualizarEvento($evento_id, $usuario_id, $nome_disciplina, $descricao_atividade, $data_entrega) {
    global $conn;

    $stmt = $conn->prepare("UPDATE eventos SET nome_disciplina = ?, descricao_atividade = ?, data_entrega = ? WHERE id = ? AND usuario_id = ?");

    if (!$stmt) {
        erro("Falha ao preparar consulta", 500);
        sair($conn);
    }

    $stmt->bind_param("sssii", $nome_disciplina, $descricao_atividade, $data_entrega, $evento_id, $usuario_id);

    if ($stmt->execute()) {
        http_response_code(200);
        echo json_encode([
            "status" => "sucesso",
            "mensagem" => "Evento atualizado com sucesso.",
            "evento_id" => $evento_id
        ]);
    } else {
        erro("Falha ao atualizar o evento", 500);
    }
}

if ($evento_id && $usuario_id && $nome_disciplina && $descricao_atividade && $data_entrega) {
    $result = buscarEvento($evento_id, $usuario_id);

    if ($result && $result->num_rows === 1) {
        atualizarEvento($evento_id, $usuario_id, $nome_disciplina, $descricao_atividade, $data_entrega);
    } else {
        erro("Evento nao encontrado.", 404);
    }
} else {
    erro("Falha ao editar o evento. Dados incompletos.", 400);
}