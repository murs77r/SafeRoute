<?php
header("Content-Type: application/json; charset=UTF-8");

include "conexao.php";
include "funcoes.php";

$id_usuario = isset($_GET["id_usuario"]) ? $_GET["id_usuario"] : null;
$limit = isset($_GET["limit"]) ? $_GET["limit"] : 3;

function listarEventos($id_usuario, $limit) {
    global $conn;

    $stmt = $conn->prepare("SELECT id, nome_disciplina, descricao_atividade, data_entrega FROM evento WHERE id_usuario = ? ORDER BY data_entrega ASC LIMIT ?");

    if (!$stmt) {
        erro("Falha ao preparar consulta", 500);
        sair($conn);
    }

    $stmt->bind_param("ii", $id_usuario, $limit);

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $eventos = [];
        while ($row = $result->fetch_assoc()) {
            $eventos[] = [
                "id" => $row["id"],
                "nome_disciplina" => $row["nome_disciplina"],
                "descricao_atividade" => $row["descricao_atividade"],
                "data_entrega" => $row["data_entrega"]
            ];
        }
        echo json_encode([
            "status" => "sucesso",
            "eventos" => $eventos
        ]);
    } else {
        erro("Falha ao listar eventos", 500);
    }
}

if ($id_usuario) {
    listarEventos($id_usuario, $limit);
} else {
    erro("ID do usuário é obrigatório", 400);
}