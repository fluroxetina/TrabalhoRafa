create database SistemaDeVendas;

use SistemaDeVendas;

create table Espacos(
	idEspacos int not null primary key auto_increment,
	nome varchar(45) not null,
    tipo Enum("salas de reunião", "laboratórios", "quadras esportivas") not null,
    capacidade int not null,
    descricao text not null 
	
);

create table Usuario(
	idUsuario int not null primary key auto_increment,
	nome varchar(45) not null,
    email varchar(45) not null,
    telefone varchar(20) not null
    
);

create table reserva(
	idUsuarioE int not null,
	idEspacoE int not null,
    dataReserva Datetime Default Current_Timestamp,
	Foreign Key (idUsuarioE) References Usuario(idUsuario),
	Foreign Key (idEspacoE) References Espacos(idEspacos)
    
);


insert into Usuario(nome, email, telefone)
values("admin", "admin", "123");



insert into Espacos(nome,tipo,capacidade,descricao)
values("bla","laboratórios", 100, "Deus te ama");

insert into reserva(idUsuarioE,idEspacoE)
values(1,1);

select * from Espacos;
select * from Usuario;
select * from reserva;

-- drop database SistemaDeVendas;










