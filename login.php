<?php
header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);

$nome = isset($data["nome"]) ? $data["nome"] : null;
$senha = isset($data["senha"]) ? $data["senha"] : null;

include "conexao.php";

$stmt = $conn->prepare("SELECT id, nome FROM usuario WHERE nome = ? AND senha = ?");
if (!$stmt) {
    http_response_code(500);
    echo json_encode(["error" => "Falha ao preparar a consulta"]);
    $conn->close();
    exit;
}

$stmt->bind_param("ss", $nome, $senha);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows === 1) {
    $row = $result->fetch_assoc();
    echo json_encode([
        "success" => true,
        "id" => $row["id"],
        "nome" => $row["nome"]
    ]);
} else {
    echo json_encode(["success" => false]);
}

$stmt->close();
$conn->close();