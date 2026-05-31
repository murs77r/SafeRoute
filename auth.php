<?php
header("Content-Type: application/json; charset=UTF-8");

include "conexao.php";
include "funcoes.php";

function buscarUsuarioPorEmail($email) {
    global $conn;

    $stmt = $conn->prepare("SELECT id, email FROM usuarios WHERE email = ?");

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

    $stmt = $conn->prepare("SELECT id, email FROM usuarios WHERE email = ? AND senha = ?");

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
        "mensagem" => "Login realizado com sucesso.",
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

function cadastrarUsuario($email, $senha) {
    global $conn;

    $stmt = $conn->prepare("INSERT INTO usuarios (email, senha) VALUES (?, ?)");
    if (!$stmt) {
        erro("Falha ao preparar consulta", 500);
        sair($conn);
    }

    $senha_hash = hash("sha256", $senha);

    $stmt->bind_param("ss", $email, $senha_hash);
    if ($stmt->execute()) {
        return [
            "id" => $conn->insert_id,
            "email" => $email
        ];
    } else {
        erro("Falha ao criar usuário", 500);
        sair($conn);
    }
}

function autenticarUsuario($email, $senha) {
    $result = buscarUsuarioPorEmailESenha($email, $senha);

    if (resultadoExiste($result)) {
        return $result->fetch_assoc();
    }

    $usuario = buscarUsuarioPorEmail($email);
    if (resultadoExiste($usuario)) {
        erro("Credenciais inválidas.", 401);
    }

    return cadastrarUsuario($email, $senha);
}

$data = json_decode(file_get_contents("php://input"), true);

$email = isset($data["email"]) ? $data["email"] : null;
$senha = isset($data["senha"]) ? $data["senha"] : null;
$acao = isset($data["acao"]) ? $data["acao"] : null;
    

if (!$email || !$senha) {
    erro("E-mail e senha são obrigatórios.", 400);
}

if ($acao !== "login") {
    erro("Ação inválida. Use \"login\".", 400);
}

$row = autenticarUsuario($email, $senha);
echo respostaLogin($row);