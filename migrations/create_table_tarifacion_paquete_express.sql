create table tarifacion_paquete_express
(
    tracking        varchar(50)   not null primary key,
    tarifa_especial decimal(5, 2) null,
    precio_fob      decimal(6, 2) not null,
    arancel         decimal(3, 2) not null,
    comentario      varchar(256)  null
);
