use old_chex;

create table vendedor
(
    id     smallint unsigned auto_increment,
    nombre varchar(100) not null,
    constraint vendedor_pk
        primary key (id)
);

create unique index vendedor_nombre_uindex
    on vendedor (nombre);

insert into vendedor (nombre) values  ('Denise Durand'), ('Jean Paul Durand'), ('Ludin Sánchez'), ('Wendy Sánchez');

create table comisiones_vendedor_cliente
(
    id               int auto_increment,
    vendedor_id      smallint unsigned not null,
    cliente_id       int unsigned      not null,
    comision_libra   decimal(4, 2)     not null,
    comision_paquete decimal(4, 2)     not null,
    constraint comisiones_vendedor_cliente_pk
        primary key (id),
    constraint comisiones_vendedor_cliente_cliente_fk
        foreign key (cliente_id) references cliente (ccid)
            on update cascade on delete cascade,
    constraint comisiones_vendedor_cliente_vendedor_fk
        foreign key (vendedor_id) references vendedor (id)
            on update cascade on delete cascade
);

create unique index comisiones_vendedor_cliente_cliente_id_uindex
    on comisiones_vendedor_cliente (cliente_id);

alter table cliente
    modify cid varchar(10) not null;

alter table cliente
    modify celular varchar(16) not null;

alter table cliente
    change telefono telefono_secundario varchar(16) null default null;

update cliente set telefono_secundario = null where telefono_secundario = '0';

alter table cliente
    add departamento varchar(50) not null after telefono_secundario;

alter table cliente
    add municipio varchar(50) not null after departamento;

alter table cliente
    add zona varchar(2) default '' not null after municipio;

alter table cliente
    change direccion direccion varchar(200) default '' not null;

alter table cliente
    modify comentario varchar(500) default null null;

alter table cliente
    add nit_nombre varchar(100) default null null after direccion;

alter table cliente
    add nit_numero varchar(10) default null null after nit_nombre;

alter table cliente
    add referencia varchar(50) not null default '' after comentario;

alter table cliente
    add tipo varchar(50) default '' not null after referencia;

update cliente set cumple = '1000-01-01' where cumple is null;

alter table cliente
    change cumple cumple date not null;

alter table cliente
    change comentario comentario varchar(500) default null null;

update cliente set comentario = null where comentario = '';

alter table cliente
    modify genero varchar(1) default 'M' not null;

alter table cliente
    modify fecha_registro date not null;


create table sincronizacion_clientes
(
    fecha                        datetime default current_timestamp() not null,
    cantidad_clientes_ingresados smallint unsigned                    not null,
    constraint sincronizacion_clientes_pk
        primary key (fecha)
);

insert into sincronizacion_clientes(cantidad_clientes_ingresados) value (0);

