CREATE DATABASE IF NOT EXISTS saferoute;
USE saferoute;

CREATE TABLE IF NOT EXISTS usuarios (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(150) NOT NULL,
    senha VARCHAR(255) NOT NULL,
    UNIQUE KEY uq_usuarios_email (email)
);

CREATE TABLE IF NOT EXISTS eventos (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    usuario_id INT NOT NULL,
    nome_disciplina VARCHAR(100) NOT NULL,
    descricao_atividade VARCHAR(150) NOT NULL,
    data_entrega DATE NOT NULL,
    CONSTRAINT fk_eventos_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios (id) ON DELETE CASCADE,
    INDEX idx_eventos_usuario_data (usuario_id, data_entrega)
);

CREATE TABLE IF NOT EXISTS requisicoes_sucesso (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    endpoint VARCHAR(255) NOT NULL,
    metodo VARCHAR(10) NOT NULL,
    registro_json JSON NOT NULL,
    criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_requisicoes_sucesso_endpoint_data (endpoint, criado_em)
);