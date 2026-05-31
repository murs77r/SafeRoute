<?php
header("Content-Type: application/json");

include "funcoes.php";
habilitar_cors_livre();
include "conexao.php";

exigir_metodo("POST");
$data = obter_json_body();

$usuario_id = isset($data["usuario_id"]) ? (int)$data["usuario_id"] : 0;
$nome_disciplina = isset($data["nome_disciplina"]) ? trim((string)$data["nome_disciplina"]) : "";
$descricao_atividade = isset($data["descricao_atividade"]) ? trim((string)$data["descricao_atividade"]) : "";
$data_entrega = isset($data["data_entrega"]) ? trim((string)$data["data_entrega"]) : "";

if ($usuario_id <= 0 || $nome_disciplina === "" || $descricao_atividade === "" || $data_entrega === "") {
    erro("Falha ao salvar o evento. Dados incompletos.", 400);
}

$formato_valido = preg_match('/^\d{4}-\d{2}-\d{2}$/', $data_entrega) === 1;
if (!$formato_valido) {
    erro("Falha ao salvar o evento. Dados incompletos.", 400);
}

$validar_usuario = $conn->prepare("SELECT id FROM usuarios WHERE id = ? LIMIT 1");
if (!$validar_usuario) {
    erro("Falha ao salvar o evento. Dados incompletos.", 500);
}

$validar_usuario->bind_param("i", $usuario_id);
$validar_usuario->execute();
$res_usuario = $validar_usuario->get_result();
if (!$res_usuario || $res_usuario->num_rows === 0) {
    erro("Falha ao salvar o evento. Dados incompletos.", 400);
}

$stmt = $conn->prepare(
    "INSERT INTO eventos (usuario_id, nome_disciplina, descricao_atividade, data_entrega) VALUES (?, ?, ?, ?)"
);
if (!$stmt) {
    erro("Falha ao salvar o evento. Dados incompletos.", 500);
}

$stmt->bind_param("isss", $usuario_id, $nome_disciplina, $descricao_atividade, $data_entrega);

if (!$stmt->execute()) {
    erro("Falha ao salvar o evento. Dados incompletos.", 500);
}

sucesso("Evento cadastrado com sucesso.", [
    "evento_id" => $stmt->insert_id
], 201);
