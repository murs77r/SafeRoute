create database saferoute;
use saferoute;

create table usuario (
    id int not null primary key AUTO_INCREMENT,
    nome varchar(80) not null,
    senha varchar(255) not null
);

select * from usuario;

delete from usuario;