<?php
header("Content-Type: application/json");

include "conexao.php";
include "funcoes.php";

$data = json_decode(file_get_contents("php://input"), true);

$email = isset($data["email"]) ? $data["email"] : null;
$senha = isset($data["senha"]) ? $data["senha"] : null;
$acao = isset($data["acao"]) ? $data["acao"] : null;

if ($acao == "cadastro") {
    $stmt = $conn->prepare("SELECT id, email FROM usuario WHERE email = ?");

    if (!$stmt) {
        http_response_code(500);
        erro("Falha ao preparar consulta");
        sair($conn);
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
} else if ($acao == "login") {
    $stmt = $conn->prepare("SELECT id, email FROM usuario WHERE email = ? AND senha = ?");

    if (!$stmt) {
        http_response_code(500);
        erro("Falha ao preparar consulta");
        sair($conn);
    }

    $stmt->bind_param("ss", $email, $senha);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    erro("Erro: Ação inválida");
    sair($conn);
}

if ($result && $result->num_rows === 1) {
    $usuario_existe = true;
} else {
    $usuario_existe = false;
}

if ($acao == "cadastro") {
    if ($usuario_existe) {
        erro("Usuário já existe");
    } else {
        $stmt = $conn->prepare("INSERT INTO usuario (email, senha) VALUES (?, ?)");
        if (!$stmt) {
            http_response_code(500);
            erro("Falha ao preparar consulta");
        }

        $stmt->bind_param("ss", $email, $senha);
        if ($stmt->execute()) {
            echo json_encode(["status" => "sucesso"]);
        } else {
            erro("Erro: falha na execução do cadastro");
        }
    }
} else if ($acao == "login") {
    if ($usuario_existe) {
        $row = $result->fetch_assoc();
        echo json_encode([
            "status" => "sucesso",
            "id" => $row["id"],
            "email" => $row["email"]
        ]);
    } else {
        erro("Email ou senha incorretos");
    }
}