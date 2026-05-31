<?php

$host = getenv('DB_HOST') ?: 'localhost';
$usuario = getenv('DB_USER') ?: 'root';
$db_senha = getenv('DB_PASSWORD') ?: '';
$banco = getenv('DB_NAME') ?: 'saferoute';
$porta = (int)(getenv('DB_PORT') ?: 3306);

$conn = new mysqli($host, $usuario, $db_senha, $banco, $porta);

if ($conn->connect_error) {
    die('Erro na conexão: ' . $conn->connect_error);
}

$conn->set_charset('utf8mb4');
