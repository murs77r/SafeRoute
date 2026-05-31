<?php
header("Content-Type: application/json");

include "conexao.php";
include "funcoes.php";

exigir_metodo("GET");

$usuario_id = isset($_GET["usuario_id"]) ? (int)$_GET["usuario_id"] : 0;
$limit = isset($_GET["limit"]) ? (int)$_GET["limit"] : 0;

if ($usuario_id <= 0) {
    erro("Parametro usuario_id obrigatorio.", 400);
}

if ($limit > 0) {
    $sql = "SELECT id, nome_disciplina, descricao_atividade, data_entrega
            FROM eventos
            WHERE usuario_id = ?
            ORDER BY data_entrega ASC, id ASC
            LIMIT ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        erro("Falha ao listar eventos.", 500);
    }

    $stmt->bind_param("ii", $usuario_id, $limit);
} else {
    $sql = "SELECT id, nome_disciplina, descricao_atividade, data_entrega
            FROM eventos
            WHERE usuario_id = ?
            ORDER BY data_entrega ASC, id ASC";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        erro("Falha ao listar eventos.", 500);
    }

    $stmt->bind_param("i", $usuario_id);
}

$stmt->execute();
$result = $stmt->get_result();
$eventos = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $eventos[] = [
            "id" => (int)$row["id"],
            "nome_disciplina" => $row["nome_disciplina"],
            "descricao_atividade" => $row["descricao_atividade"],
            "data_entrega" => $row["data_entrega"]
        ];
    }
}

responder_json(200, [
    "status" => "sucesso",
    "eventos" => $eventos
]);
