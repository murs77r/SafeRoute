<?php
header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);

$nome = isset($data["nome"]) ? $data["nome"] : null;
$senha = isset($data["senha"]) ? $data["senha"] : null;

include "conexao.php";

$stmt = $conn->prepare("INSERT INTO usuario (nome, senha) VALUES (?, ?)");
if (!$stmt) {
    http_response_code(500);
    echo json_encode(["error" => "Falha ao preparar a consulta"]);
    $conn->close();
    exit;
}

$stmt->bind_param("ss", $nome, $senha);
if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false]);
}

$stmt->close();
$conn->close();