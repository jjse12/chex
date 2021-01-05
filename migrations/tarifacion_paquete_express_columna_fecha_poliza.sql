alter table tarifacion_paquete_express
    add fecha_poliza datetime null after poliza;

update tarifacion_paquete_express set fecha_poliza = fecha_creacion where 1;

alter table tarifacion_paquete_express modify fecha_poliza datetime not null;

