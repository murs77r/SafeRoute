create database saferoute;
use saferoute;

create table usuario (
    id int not null primary key AUTO_INCREMENT,
    email varchar(150) not null,
    senha varchar(255) not null
);

create table evento (
    id int not null primary key AUTO_INCREMENT,
    id_usuario int not null,
    nome_disciplina varchar(100) not null,
    descricao_atividade varchar(150) not null,
    data_entrega date not null
);