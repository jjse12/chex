alter table cliente modify tarifa int(4) default null null;

alter table cliente alter column tarifa_express set default null;

UPDATE cliente SET tarifa = NULL where tarifa = 60;

UPDATE cliente SET tarifa_express = NULL where tarifa_express = 25;