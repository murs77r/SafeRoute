<?php
header("Content-Type: application/json");

include "conexao.php";
include "funcoes.php";

exigir_metodo("POST");
$data = obter_json_body();

$email = isset($data["email"]) ? trim((string)$data["email"]) : "";
$senha = isset($data["senha"]) ? (string)$data["senha"] : "";
$acao = isset($data["acao"]) ? trim((string)$data["acao"]) : "";

if ($email === "" || $senha === "" || ($acao !== "login" && $acao !== "cadastro")) {
    erro("Dados obrigatorios ausentes ou invalidos.", 400);
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    erro("E-mail invalido.", 400);
}

$stmt = $conn->prepare("SELECT id, email, senha FROM usuarios WHERE email = ? LIMIT 1");
if (!$stmt) {
    erro("Falha interna ao consultar usuario.", 500);
}

$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result ? $result->fetch_assoc() : null;

if ($acao === "cadastro") {
    if ($usuario) {
        erro("Credenciais invalidas ou e-mail ja cadastrado.", 401);
    }

    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
    $insert = $conn->prepare("INSERT INTO usuarios (email, senha) VALUES (?, ?)");
    if (!$insert) {
        erro("Falha interna ao cadastrar usuario.", 500);
    }

    $insert->bind_param("ss", $email, $senha_hash);
    if (!$insert->execute()) {
        erro("Credenciais invalidas ou e-mail ja cadastrado.", 400);
    }

    sucesso("Autenticacao realizada com sucesso", [
        "usuario" => [
            "id" => $insert->insert_id,
            "email" => $email
        ]
    ], 200);
}

if (!$usuario) {
    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
    $insert = $conn->prepare("INSERT INTO usuarios (email, senha) VALUES (?, ?)");
    if (!$insert) {
        erro("Falha interna ao cadastrar usuario.", 500);
    }

    $insert->bind_param("ss", $email, $senha_hash);
    if (!$insert->execute()) {
        erro("Credenciais invalidas ou e-mail ja cadastrado.", 400);
    }

    sucesso("Autenticacao realizada com sucesso", [
        "usuario" => [
            "id" => $insert->insert_id,
            "email" => $email
        ]
    ], 200);
}

if (!password_verify($senha, $usuario["senha"])) {
    erro("Credenciais invalidas ou e-mail ja cadastrado.", 401);
}

sucesso("Autenticacao realizada com sucesso", [
    "usuario" => [
        "id" => (int)$usuario["id"],
        "email" => $usuario["email"]
    ]
], 200);