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