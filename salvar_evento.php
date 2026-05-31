<?php
header("Content-Type: application/json; charset=UTF-8");

include "conexao.php";
include "funcoes.php";

$data = json_decode(file_get_contents("php://input"), true);

$id_usuario = isset($data["usuario_id"]) ? $data["usuario_id"] : (isset($data["id_usuario"]) ? $data["id_usuario"] : null);
$nome_disciplina = isset($data["nome_disciplina"]) ? $data["nome_disciplina"] : null;
$descricao_atividade = isset($data["descricao_atividade"]) ? $data["descricao_atividade"] : null;
$data_entrega = isset($data["data_entrega"]) ? $data["data_entrega"] : null;

function cadastrarEvento($id_usuario, $nome_disciplina, $descricao_atividade, $data_entrega) {
    global $conn;

    $stmt = $conn->prepare("INSERT INTO eventos (usuario_id, nome_disciplina, descricao_atividade, data_entrega) VALUES (?, ?, ?, ?)");

    if (!$stmt) {
        erro("Falha ao preparar consulta", 500);
        sair($conn);
    }

    $stmt->bind_param("isss", $id_usuario, $nome_disciplina, $descricao_atividade, $data_entrega);

    if ($stmt->execute()) {
        http_response_code(201);
        echo json_encode([
            "status" => "sucesso",
            "mensagem" => "Evento salvo com sucesso.",
            "evento_id" => $stmt->insert_id
        ]);
    } else {
        erro("Falha ao cadastrar evento", 500);
    }
}

if ($id_usuario && $nome_disciplina && $descricao_atividade && $data_entrega) {
    cadastrarEvento($id_usuario, $nome_disciplina, $descricao_atividade, $data_entrega);
} else {
    erro("Falha ao salvar o evento. Dados incompletos.", 400);
}