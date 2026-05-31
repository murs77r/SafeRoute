<?php
header("Content-Type: application/json; charset=UTF-8");

include "conexao.php";
include "funcoes.php";

function buscarUsuarioPorEmail($email) {
    global $conn;

    $stmt = $conn->prepare("SELECT id, email FROM usuario WHERE email = ?");

    if (!$stmt) {
        erro("Falha ao preparar consulta", 500);
        sair($conn);
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result;
}

function buscarUsuarioPorEmailESenha($email, $senha) {
    global $conn;

    $stmt = $conn->prepare("SELECT id, email FROM usuario WHERE email = ? AND senha = ?");

    if (!$stmt) {
        erro("Falha ao preparar consulta", 500);
        sair($conn);
    }

    $senha_hash = hash("sha256", $senha);

    $stmt->bind_param("ss", $email, $senha_hash);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result;
}

function respostaLogin($row) {
    http_response_code(200);
    return json_encode([
        "status" => "sucesso",
        "mensagem" => "Autenticação realizada com sucesso",
        "usuario" => [
            "id" => $row["id"],
            "email" => $row["email"]
        ]
    ]);
}

function respostaCadastro($row) {
    http_response_code(201);
    return json_encode([
        "status" => "sucesso",
        "mensagem" => "Cadastro realizado com sucesso",
        "usuario" => [
            "id" => $row["id"],
            "email" => $row["email"]
        ]
    ]);
}

function resultadoExiste($result) {
    if ($result && $result->num_rows === 1) {
        $resultado_existe = true;
    } else {
        $resultado_existe = false;
    }
    return $resultado_existe;
}

function cadastrarUsuario($email, $senha, $resposta = true) {
    global $conn;

    $stmt = $conn->prepare("INSERT INTO usuario (email, senha) VALUES (?, ?)");
    if (!$stmt) {
        erro("Falha ao preparar consulta", 500);
        sair($conn);
    }

    $senha_hash = hash("sha256", $senha);

    $stmt->bind_param("ss", $email, $senha_hash);
    if ($stmt->execute()) {
        if ($resposta) {
            $row = $stmt->get_result()->fetch_assoc();
            http_response_code(201);
            echo respostaCadastro($row);
        }
    } else {
        erro("Falha na execução do cadastro", 500);
        sair($conn);
    }
}

function acaoCadastro($email, $senha, $resposta = true) {
    $result = buscarUsuarioPorEmail($email);
    $usuario_existe = resultadoExiste($result);

    if ($usuario_existe) {
        erro("Credenciais inválidas ou e-mail já cadastrado.", 400);
    } else {
        cadastrarUsuario($email, $senha, $resposta);
    }
}

$data = json_decode(file_get_contents("php://input"), true);

$email = isset($data["email"]) ? $data["email"] : null;
$senha = isset($data["senha"]) ? $data["senha"] : null;
$acao = isset($data["acao"]) ? $data["acao"] : null;
    

if ($acao == "cadastro") {
    acaoCadastro($email, $senha);
} else if ($acao == "login") {
    $result = buscarUsuarioPorEmailESenha($email, $senha);
    $usuario_existe = resultadoExiste($result);

    if ($usuario_existe) {
        $row = $result->fetch_assoc();
        echo respostaLogin($row);
    } else {
        acaoCadastro($email, $senha, false);
        $result = buscarUsuarioPorEmailESenha($email, $senha);
        $row = $result->fetch_assoc();
        echo respostaLogin($row);
    }
} else {
    erro("Ação inválida", 400);
    sair($conn);
}