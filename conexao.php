<?php

$host = 'localhost';
$usuario = 'root';
$db_senha = 'senac';
$banco = 'saferoute';

$conn = new mysqli($host, $usuario, $db_senha, $banco);

if ($conn->connect_error) {
    die('Erro na conexão: ' . $conn->connect_error);
}

$conn->set_charset('utf8mb4');