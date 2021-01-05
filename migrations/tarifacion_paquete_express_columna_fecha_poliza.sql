alter table tarifacion_paquete_express
    add fecha_poliza varchar(10) null after poliza;

update tarifacion_paquete_express set fecha_poliza = DATE_FORMAT(fecha_creacion, '%d/%m/%Y') where 1;

alter table tarifacion_paquete_express modify fecha_poliza varchar(10) not null;

