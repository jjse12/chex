update cliente set tarifa_express = 25 where tarifa_express is null;
update cliente set tarifa_express = tarifa_express + 3;
update cliente set desaduanaje_express = desaduanaje_express + 3;
alter table cliente
    change tarifa_express tarifa_express decimal(5,2) not null default 28.00;
alter table cliente
    change desaduanaje_express desaduanaje_express decimal(5,2) not null default 28.00;
alter table cliente
    add seguro decimal(5, 2) default 0.02 not null;