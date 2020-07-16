<style type="text/css">
    .gone {
        display: none;
    }

    .btn-extra-danger {
      color: #fff;
      background-color: #aa2723;
      border-color: #941f1a;
    }

    .btn-extra-danger:hover,
    .btn-extra-danger:focus,
    .btn-extra-danger.focus,
    .btn-extra-danger:active,
    .btn-extra-danger.active{
      color: #fff;
      background-color: #891511;
      border-color: #700815;
    }

</style>
<script>

    const settingsTablaPaquetes = {
        "bSort": false,
        'bFilter': true,
        "retrieve": true,
        "dom": 'CT<"clear">lfrtip',
        "tableTools": {
            "sSwfPath": "./swf/copy_csv_xls_pdf.swf"
        },
        "responsive": true,
        "scrollY": "500px",
        "scrollCollapse": true,
        "paging": true,
        "language": {
            "lengthMenu": "Mostrando _MENU_ paquetes por página",
            "search": "Buscar:",
            "zeroRecords": "No hay paquetes que coincidan con la búsqueda",
            "info": "Mostrando paquetes del _START_ al _END_ de _TOTAL_ paquetes totales.",
            "infoEmpty": "No se encontraron paquetes.",
            "infoFiltered": "(Filtrando sobre _MAX_ paquetes)",
            "paginate": {
                "first": "Primera",
                "last": "Última",
                "next": "Siguiente",
                "previous": "Anterior"
            },
            "loadingRecords": "Cargando Paquetes...",
            "processing": "Procesando...",
        },
        "footerCallback": function (row, data, start, end, display) {
            var api = this.api();
            if (this.fnSettings().fnRecordsDisplay() == 0) {
                api.column(3).footer().style.visibility = "hidden";
                return;
            }
            else {
                api.column(3).footer().style.visibility = "visible";
            }

            var intVal = function (i) {
                return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '') * 1 :
                    typeof i === 'number' ?
                        i : 0;
            };

            $(api.column(3).footer()).html(
                "<h5>Total Página: " + numberWithCommasNoFixed(
                api.column(3, {page: 'current'}).data().reduce(function (a, b) {
                    return intVal(a) + intVal(b.split(">")[1].split("<")[0]);
                }, 0)
                ) + " Libras</h5><br>" +
                "<h5><strong>Total: </strong>" + numberWithCommasNoFixed(
                api.column(3).data().reduce(function (a, b) {
                    return intVal(a) + intVal(b.split(">")[1].split("<")[0]);
                }, 0)
                ) + " Libras</h5>"
            );
        }
    };

    const settingsTablaPaquetesBusqueda = {
        "bSort": false,
        'bFilter': false,
        "retrieve": true,
        "dom": 'CT<"clear">lfrtip',
        "tableTools": {
            "sSwfPath": "./swf/copy_csv_xls_pdf.swf"
        },
        "responsive": true,
        "scrollY": "500px",
        "scrollCollapse": true,
        "paging": false,
        "language": {
            "lengthMenu": "Mostrando _MENU_ paquetes por página",
            "search": "Buscar:",
            "zeroRecords": "No hay paquetes que coincidan con la búsqueda",
            "info": "Mostrando paquetes del _START_ al _END_ de _TOTAL_ paquetes totales.",
            "infoEmpty": "No se encontraron paquetes.",
            "infoFiltered": "(Filtrando sobre _MAX_ paquetes)",
            "paginate": {
                "first": "Primera",
                "last": "Última",
                "next": "Siguiente",
                "previous": "Anterior"
            },
            "loadingRecords": "Cargando Paquetes...",
            "processing": "Procesando...",
        },
        "footerCallback": function (row, data, start, end, display) {
            var api = this.api();
            if (this.fnSettings().fnRecordsDisplay() == 0) {
                api.column(3).footer().style.visibility = "hidden";
                return;
            }
            else {
                api.column(3).footer().style.visibility = "visible";
            }

            var intVal = function (i) {
                return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '') * 1 :
                    typeof i === 'number' ?
                        i : 0;
            };

            $(api.column(3).footer()).html(
                "<h5>Total: " + numberWithCommasNoFixed(
                api.column(3, {page: 'current'}).data().reduce(function (a, b) {
                    return intVal(a) + intVal(b.split(">")[1].split("<")[0]);
                }, 0)
                ) + " Libras</h5>"
            );
        }
    };

    $(document).ready( function () {
        $('#historicoPaquetes').DataTable(settingsTablaPaquetes);

        $("#historicoPaquetes tbody").on("mouseover", "h5.popup", function () {
            var span = $(this).children("span").toggleClass("show", true);
        });

        $("#historicoPaquetes tbody").on("mouseout", "h5.popup", function () {
            var span = $(this).children("span").toggleClass("show", false);
        });


        $("#historicoPaquetes tbody").on("click", "img.info_pqt", function (){

            var tablaPaquetes = $('#historicoPaquetes').DataTable();
            var index = tablaPaquetes.row($(this).closest('tr')).index();
            var arr = tablaPaquetes.rows(index).data().toArray();
            var tracking = arr[0][0].replace("<br>", "").split(">")[1].split("<")[0];
            var uid = arr[0][1].split(">")[1].split("<")[0];
            var uname = arr[0][2].split(">")[1].split("<")[0];
            var peso = Number(arr[0][3].split(">")[1].split("<")[0]);
            var estado = arr[0][4].split(">")[1].split("<")[0];
            var fechaEntrega = "";
            if (estado == "Entregado")
                fechaEntrega = arr[0][4].split(">")[2].split("<")[0];


            // DESPLIEGUE DE INFORMACIÓN DE UN PAQUETE AUN EN INVENTARIO (Sin Entregar)
            if (fechaEntrega == ""){
                $.ajax({
                    url: "db/DBexecQuery.php",
                    type: "POST",
                    data: {
                        query: "SELECT fecha, R.rcid, plan FROM paquete P, carga R WHERE P.rcid = R.rcid AND tracking = '"+tracking+"'"
                    },
                    cache: false,
                    success: function(res){
                        if (res === '[]'){
                            bootbox.alert("No se encontró ningún paquete en la base de datos. Probablemente el paquete fue eliminado por alguien más, actualice la página.");
                            return;
                        }

                        var data = JSON.parse(res)[0];
                        var rcid = data["rcid"];
                        var fechaIngreso = data["fecha"];
                        var fec = fechaIngreso.split(" ")[0].split("-");
                        var hora = fechaIngreso.split(" ")[1].split(":");
                        var h = hora[0];
                        var m = hora[1];
                        var apm = "PM";
                        if (h > 12)
                            h = h-12;
                        else if (h < 12){
                            if (h == 0)
                                h = 12;
                            apm = "AM";
                        }

                        fechaIngreso = fec[2] + "/" + fec[1] + "/" + fec[0] + " a las " + h + ":" + m + " " + apm

                        var plan = data["plan"];
                        if (plan.includes("/")){
                            if (!plan.includes("Guatex"))
                                plan = "Por Ruta: " + plan;
                            else plan = "Guatex: " + plan.split(":")[1];
                        }
                        else if (plan == "" || plan.includes("whats") || plan.includes("mail"))
                            plan = "Sin Especificar";

                        bootbox.dialog({
                            backdrop: true,
                            closeButton: false,
                            title: "Paquete de " + uname,
                            message:
                            //TODO: agregar campos de nombre y id de cliente y trasladar el campo peso a la misma fila (implementar opción para modificar estos 3 campos)
                            "<div class='row' style='background-color: #dadada'>"+
                                "<div class='row'>"+
                                    "<div class='col-lg-1 col-md-1 col-sm-1 col-xs-1'></div>"+
                                    "<div class='control-group form-group col-lg-4 col-md-4 col-sm-4 col-xs-4'><div class='controls'><label align='center' style='color: #337ab7; text-align:center; width:100%'># Tracking</label><input type='text' class='form-control' style='text-align:center;" + (isAdmin ? " cursor: pointer;' onclick='updateTracking(this, "+index+", true)'":"'") + " value='"+tracking+"' readonly/></div></div>"+
                                    "<div class='control-group form-group col-lg-2 col-md-2 col-sm-2 col-xs-5'><div class='controls'><label align='center'  style='color: #337ab7; text-align:center; width:100%'>Peso</label><input id='librasEntrega' style='text-align:center;' value='"+peso+"' type='text' class='form-control' readonly/></div></div>"+
                                    (isMobile ? "<div class='col-xs-1'></div></div><div class='row'><div class='col-xs-1'></div>" : "") +
                                    "<div class='control-group form-group col-lg-4 col-md-4 col-sm-4 col-xs-5'><div class='controls'><label align='center'  style='color: #337ab7; text-align:center; width:100%'>Fecha de Ingreso</label><input id='tarifaEntrega' title='Registro de Carga #"+rcid+"' value='"+fechaIngreso+"' type='text' class='form-control' style='text-align:center;' readonly/>"+/*(isAdmin ? "<button onclick='trasladarPaquete(\""+tracking+"\")' style='color:#337ab7' type='button' class='btn btn-default col-lg-12 col-md-12 col-sm-12 col-xs-12''>Cambiar Registro de Carga</button>":"")+*/
                                    "<div class='col-lg-1 col-md-1 col-sm-1 col-xs-1'></div></div></div>"+
                                "</div>"+
                                "<div class='row'>"+
                                    "<div class='col-lg-2 col-md-2 col-sm-2 col-xs-2'></div>"+
                                    "<div class='control-group form-group col-lg-4 col-md-4 col-sm-4 col-xs-4'><div class='controls'><label style='color: #337ab7; text-align:center; width:100%'>Estado</label><input type='text' class='form-control' style='width:100%;text-align:center;' value='En Inventario' disabled/></div></div>"+
                                    "<div class='control-group form-group col-lg-4 col-md-4 col-sm-4 col-xs-4'><div class='controls'><label style='color: #337ab7; text-align:center; width:100%'>Plan de Entrega</label><input type='text' class='form-control' style='width:100%; text-align:center;' value='"+plan+"' disabled/></div></div>"+
                                    "<div class='col-lg-2 col-md-2 col-sm-2 col-xs-2'></div>"+
                                "</div>"+
                            "</div>",
                            buttons: {
                                cancel: {
                                    label: "Regresar",
                                    className: "btn btn-md btn-default alinear-izquierda"
                                },
                                eliminar: {
                                    label: "Eliminar Paquete",
                                    className: (isAdmin ? "btn btn-md btn-danger alinear-derecha" : "gone"),
                                    callback: function(){
                                        bootbox.confirm({
                                            closeButton: false,
                                            title: "Eliminar paquete",
                                            message: "Esta acción no podrá ser revertida, ¿desea continuar?",
                                            buttons:{
                                                cancel:{
                                                    label: "CANCELAR",
                                                    className: "btn btn-md btn-success alinear-izquierda"
                                                },
                                                confirm:{
                                                    label: "Si, Eliminar",
                                                    className: "btn btn-md btn-danger alinear-derecha"
                                                }
                                            },
                                            callback: function(call){
                                                if (call){
                                                    $.ajax({
                                                        url: "db/DBexecMultiQuery.php",
                                                        type: "POST",
                                                        data:{
                                                            query: "DELETE FROM paquete WHERE tracking = '"+tracking+"' ; " +
                                                            "UPDATE carga SET total_pqts = total_pqts - 1, total_lbs = total_lbs - " + peso + " WHERE rcid = " + rcid + "; "
                                                        },
                                                        cache: false,
                                                        success: function(resito){
                                                            if (resito == "1"){
                                                                bootbox.hideAll();
                                                                bootbox.alert("El paquete ha sido eliminado y se ha actualizado el registro de carga correspondiente.");
                                                                tablaPaquetes.rows(index).remove().draw(false);
                                                            }
                                                            else
                                                                bootbox.alert("No se pudo eliminar el paquete debido a un problema con la consulta a la base de datos. Intente nuevamente");
                                                        },
                                                        error: function(){
                                                            bootbox.alert("Ocurrió un problema al intentar conectarse al servidor. Intente nuevamente");
                                                        }
                                                    });
                                                }
                                            }
                                        });
                                        return false;
                                    }
                                }
                            }
                        });
                    },
                    error: function(){
                        bootbox.alert("Ocurrió un problema al intentar conectarse al servidor.");
                    }
                });
            }
            // DESPLIEGUE DE INFORMACIÓN DE UN PAQUETE YA ENTREGADO AL CLIENTE
            else{
                $.ajax({
                    url: "db/DBexecQuery.php",
                    type: "POST",
                    data: {
                        query: "SELECT R.fecha, E.fecha AS fechaEntrega, P.rcid, P.plan, ruta, paquetes, liquidado, tarifa FROM paquete P JOIN entrega E ON P.estado = E.fecha JOIN carga R ON P.rcid = R.rcid WHERE tracking = '"+tracking+"'"
                    },
                    cache: false,
                    success: function(res){
                        if (res === '[]'){
                            bootbox.alert("No se encontró ningún paquete en la base de datos. Probablemente el paquete fue eliminado por alguien más, actualice la página.");
                            return;
                        }
                        var data = JSON.parse(res)[0];
                        var rcid = data["rcid"];
                        var fechaBoleta = data["fechaEntrega"];
                        var fechaIngreso = data["fecha"];
                        var fec = fechaIngreso.split(" ")[0].split("-");
                        var hora = fechaIngreso.split(" ")[1].split(":");
                        var h = hora[0];
                        var m = hora[1];
                        var apm = "PM";
                        if (h > 12)
                            h = h-12;
                        else if (h < 12){
                            if (h == 0)
                                h = 12;
                            apm = "AM";
                        }
                        fechaIngreso = fec[2] + "/" + fec[1] + "/" + fec[0] + " a las " + h + ":" + m + " " + apm

                        var plan = data["plan"];
                        var cobroRuta = "";
                        var titlePaquetes = "";
                        if (plan.includes("/")){
                            if (data["ruta"] != null){
                                cobroRuta = "Q " + data["ruta"];
                                if (data["paquetes"] == 1)
                                    titlePaquetes = "Solamente se envió este paquete.";
                                else titlePaquetes = "Note que se enviaron " + data["paquetes"] + " paquetes al cliente.";
                            }
                            if (!plan.includes("Guatex"))
                                plan = "Por Ruta: " + plan;
                            else plan = "Guatex: " + plan.split(":")[1];
                        }

                        var liq = data["liquidado"];
                        var fechaLiquidacion = "";
                        if (liq != null){
                            fec = liq.split(" ")[0].split("-");
                            hora = liq.split(" ")[1].split(":");
                            h = hora[0];
                            m = hora[1];
                            apm = "PM";
                            if (h > 12)
                                h = h-12;
                            else if (h < 12){
                                if (h == 0)
                                    h = 12;
                                apm = "AM";
                            }
                            fechaLiquidacion = fec[2] + "/" + fec[1] + "/" + fec[0] + " a las " + h + ":" + m + " " + apm;
                        }
                        var tarifa = data["tarifa"];
                        var tarif = Number(tarifa.replace(/[Q,\s]/g, ""));
                        bootbox.dialog({
                            backdrop: true,
                            closeButton: false,
                            title: "Paquete de " + uname,
                            message:
                            //TODO: agregar campos de nombre y id de cliente y trasladar el campo peso a la misma fila (implementar opción para modificar estos 3 campos)
                            "<div class='row' style='background-color: #dadada'>"+
                                "<div class='row'>"+
                                    "<div class='col-lg-1 col-md-1 col-sm-1 col-xs-1'></div>"+
                                    "<div class='control-group form-group col-lg-4 col-md-4 col-sm-4 col-xs-4'><div class='controls'><label align='center' style='color: #337ab7; text-align:center; width:100%'># Tracking</label><input type='text' class='form-control' style='text-align:center;" + (isAdmin ? " cursor: pointer;' onclick='updateTracking(this, "+index+", true)'":"'") + " value='"+tracking+"' readonly/></div></div>"+
                                    "<div class='control-group form-group col-lg-2 col-md-2 col-sm-2 col-xs-5'><div class='controls'><label align='center'  style='color: #337ab7; text-align:center; width:100%'>Peso</label><input id='librasEntrega' style='text-align:center;' value='"+peso+"' type='text' class='form-control' readonly/></div></div>"+
                                    (isMobile ? "<div class='col-xs-1'></div></div><div class='row'><div class='col-xs-1'></div>" : "") +
                                    "<div class='control-group form-group col-lg-4 col-md-4 col-sm-4 col-xs-5'><div class='controls'><label align='center'  style='color: #337ab7; text-align:center; width:100%'>Fecha de Ingreso</label><input id='tarifaEntrega' title='Registro de Carga #"+rcid+"' value='"+fechaIngreso+"' type='text' class='form-control' style='text-align:center;' readonly/>"+/*(isAdmin ? "<button onclick='trasladarPaquete(\""+tracking+"\")' style='color:#337ab7' type='button' class='btn btn-default col-lg-12 col-md-12 col-sm-12 col-xs-12''>Cambiar Registro de Carga</button>":"")+*/
                                    "<div class='col-lg-1 col-md-1 col-sm-1 col-xs-1'></div></div></div>"+
                                "</div>"+
                                "<div class='row'>"+
                                    "<div class='col-lg-1 col-md-1 col-sm-1 col-xs-1'></div>"+
                                    "<div class='control-group form-group col-lg-5 col-md-5 col-sm-5 col-xs-5'><div class='controls'><label style='color: #337ab7; text-align:center; width:100%'>Estado</label><input type='text' class='form-control' style='width:100%;text-align:center;' " + (liq != null ? "value='Entregado y Liquidado' title='Entrega: "+fechaEntrega+", Liquidación: "+fechaLiquidacion+"'" : "value='Entregado (aún sin liquidar)' title='Entrega: "+fechaEntrega+"'") + " disabled/>"+(liq != null ? "<div class='control-group form-group col-lg-12 col-md-12 col-sm-12 col-xs-12'><div class='controls'><label style='color: #337ab7; text-align:center; width:100%'>Cobro por Paquete</label><input type='text' class='form-control' style='width:100%; text-align:center;' value='Q "+numberWithCommas(peso*tarif)+"' title='La tarifa del cliente era de "+tarifa+"' disabled/></div></div>" :"")+"</div></div>"+
                                    "<div class='control-group form-group col-lg-5 col-md-5 col-sm-5 col-xs-5'><div class='controls'><label style='color: #337ab7; text-align:center; width:100%'>Plan de Entrega</label><input type='text' class='form-control' style='width:100%; text-align:center;' value='"+plan+"' disabled/>"+(liq != null && cobroRuta != "" ? "<div class='control-group form-group col-lg-12 col-md-12 col-sm-12 col-xs-12'><div class='controls'><label style='color: #337ab7; text-align:center; width:100%'>Cobro por Envío</label><input type='text' class='form-control' style='width:100%; text-align:center;' value='"+cobroRuta+"' title='"+titlePaquetes+"' disabled/></div></div>" :"")+"</div></div>"+
                                    "<div class='col-lg-1 col-md-1 col-sm-1 col-xs-1'></div>"+
                                "</div>"+
                            "</div>",
                            buttons: {
                                cancel: {
                                    label: "Regresar",
                                    className: "btn btn-md btn-default alinear-izquierda"
                                },
                                eliminar: {
                                    label: "Eliminar Paquete",
                                    className: (isAdmin ? "btn btn-md btn-danger alinear-derecha" : "gone"),
                                    callback: function(){
                                        bootbox.confirm({
                                            closeButton: false,
                                            title: "Eliminar paquete",
                                            message: "Esta acción no podrá ser revertida, ¿desea continuar?",
                                            buttons:{
                                                cancel:{
                                                    label: "CANCELAR",
                                                    className: "btn btn-md btn-success alinear-izquierda"
                                                },
                                                confirm:{
                                                    label: "Si, Eliminar",
                                                    className: "btn btn-md btn-danger alinear-derecha"
                                                }
                                            },
                                            callback: function(call){
                                                if (call){
                                                    $.ajax({
                                                        url: "db/DBgetEntrega.php",
                                                        type: "POST",
                                                        data:{
                                                            select: "tarifa, subtotal, total",
                                                            where: "fecha = '" + fechaBoleta + "'"
                                                        },
                                                        cache: false,
                                                        success: function(res){
                                                            if (res === '[]'){
                                                                bootbox.alert("No se encontró la boleta asociada en la base de datos. Probablemente la boleta fue eliminada por alguien más, actualice la página.");
                                                                return;
                                                            }
                                                            var data = JSON.parse(res)[0];
                                                            var tarifa = Number(data["tarifa"].replace(/[Q,\s]/g, ""));
                                                            var resta = peso*tarifa;
                                                            var subt = Number(data["subtotal"].replace(/[Q,\s]/g, ""));
                                                            var tot = Number(data["total"].replace(/[Q,\s]/g, ""));

                                                            //TODO: continuar extracción del total de la boleta virtual
                                                            if (tarifa == 64)
                                                                resta = resta*1.065;
                                                            subt = "Q " + numberWithCommas(subt-resta);
                                                            tot = "Q " + numberWithCommas(tot-resta);

                                                            $.ajax({
                                                                url: "db/DBexecMultiQuery.php",
                                                                type: "POST",
                                                                data:{
                                                                    query: "DELETE FROM paquete WHERE tracking = '"+tracking+"' ; " +
                                                                    "UPDATE carga SET total_pqts = total_pqts - 1, total_lbs = total_lbs - " + peso + " WHERE rcid = " + rcid + "; " +
                                                                    "UPDATE entrega SET paquetes = paquetes - 1, libras = libras - " + peso + ", subtotal = '" + subt + "', total = '" + tot + "' WHERE fecha = '" + fechaBoleta + "';"
                                                                },
                                                                cache: false,
                                                                success: function(resito){
                                                                    if (resito == "1"){
                                                                        bootbox.hideAll();
                                                                        bootbox.alert("El paquete ha sido eliminado y se han actualizado el registro de carga y boleta de entrega correspondientes.");
                                                                        tablaPaquetes.rows(index).remove().draw(false);
                                                                    }
                                                                    else
                                                                        bootbox.alert("No se pudo eliminar el paquete debido a un problema con la consulta a la base de datos. Intente nuevamente");
                                                                },
                                                                error: function(){
                                                                    bootbox.alert("Ocurrió un problema al intentar conectarse al servidor. Intente nuevamente");
                                                                }
                                                            });
                                                        },
                                                        error: function(){
                                                            bootbox.alert("Ocurrió un problema al intentar conectarse al servidor.");
                                                        }
                                                    });
                                                }
                                            }
                                        });
                                        return false;
                                    }
                                }
                            }
                        });
                    },
                    error: function(){
                        bootbox.alert("Ocurrió un problema al intentar conectarse al servidor.");
                    }
                });
            }
        });

        var tabCargas = $('#historicoCargas').DataTable({
            "bSort" : false,
            "retrieve": true,
            "responsive": true,
            "scrollY": "500px",
            "scrollCollapse": true,
            "paging": true,
            "fixedColumns": true,
            "language": {
                "lengthMenu": "Mostrando _MENU_ registros de carga por página",
                "search": "Buscar:",
                "zeroRecords": "No hay registros de carga que coincidan con la búsqueda",
                "info": "Mostrando registros de carga del _START_ al _END_ de _TOTAL_ registros.",
                "infoEmpty": "No se encontraron registros de carga.",
                "infoFiltered": "(Filtrando sobre _MAX_ registros de carga)",
                "paginate": {
                    "first":      "Primera",
                    "last":       "Última",
                    "next":       "Siguiente",
                    "previous":   "Anterior"
                },
                "loadingRecords": "Cargando registros de carga...",
                "processing":     "Procesando...",
            },
            "footerCallback": function ( row, data, start, end, display ) {
                var api = this.api(), data;

                if (this.fnSettings().fnRecordsDisplay() == 0){
                    api.column(2).footer().style.visibility = "hidden";
                    api.column(3).footer().style.visibility = "hidden";
                    return;
                }
                else{
                    api.column(2).footer().style.visibility = "visible";
                    api.column(3).footer().style.visibility = "visible";
                }

                var intVal = function ( i ) {
                    return typeof i === 'string' ?
                        i.replace(/[\$,]/g, '')*1 :
                        typeof i === 'number' ?
                            i : 0;
                };

                $(api.column(2).footer() ).html(
                    "<h5>Total Página: " + numberWithCommasNoFixed(api.column(2, { page: 'current'} ).data().reduce( function (a, b) {
                                        return intVal(a) + intVal(b.split(">")[1].split("<")[0]);
                                        }, 0)) + " Paquetes</h5><br>" +
                    "<h5><strong>Total: </strong>" + numberWithCommasNoFixed(
                    api.column(2).data().reduce(function (a, b) {
                        return intVal(a) + intVal(b.split(">")[1].split("<")[0]);
                    }, 0)
                    ) + " Paquetes</h5>"
                );

                $(api.column(3).footer() ).html(
                    "<h5>Total: " + numberWithCommasNoFixed(api.column(3, { page: 'current'} ).data().reduce( function (a, b) {
                                        return intVal(a) + intVal(b.split(">")[1].split("<")[0]);
                                        }, 0 )) + " Libras</h5><br>" +
                    "<h5><strong>Total: </strong>" + numberWithCommasNoFixed(
                    api.column(3).data().reduce(function (a, b) {
                        return intVal(a) + intVal(b.split(">")[1].split("<")[0]);
                    }, 0)
                    ) + " Libras</h5>"
                );
            }
        });

        $("#historicoCargas tbody").on("click", "img.info_carga", function () {
            var index = tabCargas.row($(this).closest('tr')).index();
            var arr = tabCargas.rows(index).data().toArray();
            var rcid = arr[0][0].split(">")[1].split("<")[0];
            var fechilla = arr[0][1].split("title='")[1].split("'")[0];
            var paquetitos = arr[0][2].split(">")[1].split("<")[0];
            var libritas = arr[0][3].split(">")[1].split("<")[0];

            $.ajax({
                url: "db/DBexecQuery.php",
                type: "POST",
                data: {
                    query: "SELECT P.tracking AS tracking, P.uid AS uid, P.uname AS uname, P.libras AS libras, P.estado AS estado, E.liquidado AS liquidado, P.servicio as servicio, P.guide_number as guide_number FROM paquete P LEFT JOIN entrega E ON P.estado = E.fecha WHERE P.rcid = " + rcid
                },
                cache: false,
                success: function(res){
                    if (res === '[]'){
                        bootbox.alert("No se encontraron los paquetes de la carga. Probablemente la carga fue eliminada por alguien más, actualice la página.");
                        return;
                    }
                    var rows = JSON.parse(res);
                    var fec = fechilla.split(" ")[0].split("-");
                    var hora = fechilla.split(" ")[1].split(":");
                    var h = hora[0];
                    var m = hora[1];
                    var apm = "PM";
                    if (h > 12)
                        h = h-12;
                    else if (h < 12){
                        if (h == 0)
                            h = 12;
                        apm = "AM";
                    }

                    bootbox.dialog({
                        backdrop: true,
                        size: "large",
                        title: "Mercadería ingresada el " + fec[2] + "/" + fec[1] + "/" + fec[0] + " a las " + h + ":" + m + " " + apm,
                        message:
                        "<div class='container-flex m-all-1'>"+
                            "<table id='tablaPaquetesCarga' class='display' cellspacing='0' style='width: 100%;'><thead><tr><th class='dt-head-center'><h5 style='color:black'>Servicio</h5></th><th class='dt-head-center'><h5 style='color:black'>No. de Guía</h5></th><th class='dt-head-center'><h5 style='color:black'># Tracking</h5></th><th class='dt-head-center'><h5 style='color:black'>ID Cliente</h5></th><th class='dt-head-center'><h5 style='color:black'>Nombre Cliente</h5></th><th class='dt-head-center'><h5 style='color:black'>Peso</h5></th><th class='dt-head-center'><h5 style='color:black'>Estado</h5></th><th></th></tr></thead><tfoot><tr><th></th><th></th><th></th><th></th><th></th><th class='dt-head-center'></th><th></th><th></th></tr></tfoot><tbody></tbody></table>"+
                        "</div>",
                        buttons: {
                            cancel: {
                                label: "Regresar",
                                className: "btn btn-md btn-default alinear-izquierda"
                            },
                            eliminar: {
                                label: "Eliminar Registro de Cargas",
                                className: (isAdmin ? "btn btn-md btn-danger alinear-derecha" : "gone"),
                                callback: function(){
                                    bootbox.confirm({
                                        closeButton: false,
                                        title: "ADVERTENCIA",
                                        message: "Está a punto de eliminar el registro de carga, junto con todos los paquetes asociados a este. Dicha acción no podrá ser revertida, ¿desea continuar?",
                                        buttons:{
                                            cancel:{
                                                label: "CANCELAR",
                                                className: "btn btn-md btn-success alinear-izquierda"
                                            },
                                            confirm:{
                                                label: "Si, Eliminar",
                                                className: "btn btn-md btn-danger alinear-derecha"
                                            }
                                        },
                                        callback: function(call){
                                            if (call){
                                                bootbox.confirm({
                                                    closeButton: false,
                                                    title: "¡¡¡ÚLTIMA ADVERTENCIA!!!",
                                                    message: "Esta acción eliminará información muy valiosa, aún así... <br><br> <b>¿Desea continuar?</b>",
                                                    buttons:{
                                                        cancel:{
                                                            label: "¡¡¡CANCELAR!!!",
                                                            className: "btn btn-md btn-success alinear-izquierda"
                                                        },
                                                        confirm:{
                                                            label: "Si, Eliminar",
                                                            className: "btn btn-md btn-danger alinear-derecha"
                                                        }
                                                    },
                                                    callback: function(call){
                                                        if (call){
                                                            // TODO: Implementar el cambio de datos en la entrega asociado a los paquetes ya entregados dentro del registro de carga a ser borrado!
                                                            $.ajax({
                                                                url: "db/DBexecMultiQuery.php",
                                                                type: "POST",
                                                                data: {
                                                                    query: "DELETE FROM carga WHERE rcid = " + rcid + "; " +
                                                                        "DELETE FROM paquete WHERE rcid = " + rcid + ";"
                                                                },
                                                                cache: false,
                                                                success: function(res){
                                                                    if (res == "1"){
                                                                        tabCargas.rows(index).remove().draw(false);
                                                                        bootbox.hideAll();
                                                                        bootbox.alert("Se ha eliminado el registro de carga juntamente con todos sus paquetes asociados.");
                                                                    }
                                                                    else
                                                                        bootbox.alert("No se pudo eliminar el paquete debido a un problema con la consulta a la base de datos. Intente nuevamente");
                                                                },
                                                                error: function(){
                                                                    bootbox.alert("Ocurrió un problema al intentar conectarse al servidor. Intente nuevamente");
                                                                }
                                                            });
                                                        }
                                                    }
                                                });
                                            }
                                        }
                                    });
                                    return false;
                                }
                            }
                        }
                    }).on('shown.bs.modal', function (e) {
                        var tablita = $('#tablaPaquetesCarga').DataTable({
                            "bSort" : false,
                            "retrieve": true,
                            "responsive": true,
                            "scrollY": "500px",
                            "scrollCollapse": true,
                            "paging": false,
                            "language": {
                                "lengthMenu": "Mostrando _MENU_ paquetes por página",
                                "search": "Buscar:",
                                "zeroRecords": "No hay paquetes que coincidan con la búsqueda",
                                "info": "Mostrando paquetes del _START_ al _END_ de _TOTAL_ paquetes totales.",
                                "infoEmpty": "No se encontraron paquetes.",
                                "infoFiltered": "(Filtrando sobre _MAX_ paquetes)",
                                "paginate": {
                                    "first":      "Primera",
                                    "last":       "Última",
                                    "next":       "Siguiente",
                                    "previous":   "Anterior"
                                },
                                "loadingRecords": "Cargando Paquetes...",
                                "processing":     "Procesando...",
                            },
                            "footerCallback": function ( row, data, start, end, display ) {
                                var api = this.api(), data;
                                if (this.fnSettings().fnRecordsDisplay() == 0){
                                    api.column(5).footer().style.visibility = "hidden";
                                    return;
                                }
                                else{
                                    api.column(5).footer().style.visibility = "visible";
                                }

                                var intVal = function ( i ) {
                                    return typeof i === 'string' ?
                                        i.replace(/[\$,]/g, '')*1 :
                                        typeof i === 'number' ?
                                            i : 0;
                                };

                                $(api.column(5).footer() ).html(
                                    "<h5>Total: " + numberWithCommasNoFixed(
                                                        api.column(5, { page: 'current'} ).data().reduce( function (a, b) {
                                                        return intVal(a) + intVal(b.split(">")[1].split("<")[0]);
                                                        }, 0 )
                                                    ) + " Libras</h5>"
                                );
                            }
                        });

                        tablita.clear();
                        for (var i = 0; i < rows.length; i++){
                            var estado = "<h5 style='text-align: center;' class='btn-sm btn-warning'>En Inventario</h5>";
                            if (rows[i]["estado"] != null){
                                let fecha = convertToHumanDate(rows[i]["estado"]);
                                var color = "#f4cb38";
                                if (rows[i]["liquidado"] != null)
                                    color = "#4c883c";
                                estado = "<h5 style='text-align: center; cursor: default; background-color: "+color+";' class='popup btn-sm'>Entregado<span class='popuptext'>"+fecha+"</span></h5>";
                            }
                            var trackingsito = rows[i]["tracking"];
                            if (trackingsito.length > 20)
                                trackingsito = trackingsito.substr(0, trackingsito.length/2) + "<br>" + trackingsito.substr(trackingsito.length/2, trackingsito.length);
                            tablita.row.add([
                                "<h5 class='seleccionado'>"+rows[i]['servicio']+"</h5>",
                                "<h5 class='seleccionado'>"+rows[i]['guide_number']+"</h5>",
                                "<h5 class='seleccionado'>"+trackingsito+"</h5>",
                                "<h5 class='seleccionado'>"+rows[i]["uid"]+"</h5>",
                                "<h5 class='seleccionado'>"+rows[i]["uname"]+"</h5>",
                                "<h5 class='seleccionado'>"+rows[i]["libras"]+"</h5>",
                                estado,
                                "<img align='center' style='text-align:center; cursor:pointer;' class='info_pqt' src='images/info_paquete.png'/>"
                            ]);
                        }

                        tablita.draw(false);
                        tablita.columns.adjust().responsive.recalc();

                        $("#tablaPaquetesCarga tbody").on("click", "img.info_pqt", function (){
                            var indexito = tablita.row($(this).closest('tr')).index();
                            var arre = tablita.rows(indexito).data().toArray();

                            var tracking = arre[0][2].replace("<br>", "").split(">")[1].split("<")[0];
                            var uid = arre[0][3].split(">")[1].split("<")[0];
                            var uname = arre[0][4].split(">")[1].split("<")[0];
                            var peso = Number(arre[0][5].split(">")[1].split("<")[0]);
                            var estado = arre[0][6].split(">")[1].split("<")[0];
                            var fechaEntrega = "";
                            if (estado == "Entregado")
                                fechaEntrega = arre[0][6].split(">")[2].split("<")[0];


                            // DESPLIEGUE DE INFORMACIÓN DE UN PAQUETE AUN EN INVENTARIO (Sin Entregar)
                            if (fechaEntrega == ""){
                                $.ajax({
                                    url: "db/DBexecQuery.php",
                                    type: "POST",
                                    data: {
                                        query: "SELECT fecha, R.rcid, plan FROM paquete P, carga R WHERE P.rcid = R.rcid AND tracking = '"+tracking+"'"
                                    },
                                    cache: false,
                                    success: function(res){
                                        if (res === '[]'){
                                            bootbox.alert("No se encontró ningún paquete en la base de datos. Probablemente el paquete fue eliminado por alguien más, actualice la página.");
                                            return;
                                        }
                                        var data = JSON.parse(res)[0];
                                        var rcid = data["rcid"];
                                        var fechaIngreso = convertToHumanDate(data["fecha"]);

                                        var plan = data["plan"];
                                        if (plan.includes("/")){
                                            if (!plan.includes("Guatex"))
                                                plan = "Por Ruta: " + plan;
                                            else plan = "Guatex: " + plan.split(":")[1];
                                        }
                                        else if (plan == "" || plan.includes("whats") || plan.includes("mail"))
                                            plan = "Sin Especificar";

                                        bootbox.dialog({
                                            backdrop: true,
                                            closeButton: false,
                                            title: "Paquete de " + uname,
                                            message:
                                            //TODO: agregar campos de nombre y id de cliente y trasladar el campo peso a la misma fila (implementar opción para modificar estos 3 campos)
                                            "<div class='row' style='background-color: #dadada'>"+
                                                "<div class='row'>"+
                                                    "<div class='col-lg-1 col-md-1 col-sm-1 col-xs-1'></div>"+
                                                    "<div class='control-group form-group col-lg-4 col-md-4 col-sm-4 col-xs-4'><div class='controls'><label align='center' style='color: #337ab7; text-align:center; width:100%'># Tracking</label><input type='text' class='form-control' style='text-align:center;" + (isAdmin ? " cursor: pointer;' onclick='updateTracking(this, "+indexito+", false)'":"'") + " value='"+tracking+"' readonly/></div></div>"+
                                                    "<div class='control-group form-group col-lg-2 col-md-2 col-sm-2 col-xs-5'><div class='controls'><label align='center'  style='color: #337ab7; text-align:center; width:100%'>Peso</label><input id='librasEntrega' style='text-align:center;' value='"+peso+"' type='text' class='form-control' readonly/></div></div>"+
                                                    (isMobile ? "<div class='col-xs-1'></div></div><div class='row'><div class='col-xs-1'></div>" : "") +
                                                    "<div class='control-group form-group col-lg-4 col-md-4 col-sm-4 col-xs-5'><div class='controls'><label align='center'  style='color: #337ab7; text-align:center; width:100%'>Fecha de Ingreso</label><input id='tarifaEntrega' title='Registro de Carga #"+rcid+"' value='"+fechaIngreso+"' type='text' class='form-control' style='text-align:center;' readonly/>"+/*(isAdmin ? "<button onclick='trasladarPaquete(\""+tracking+"\")' style='color:#337ab7' type='button' class='btn btn-default col-lg-12 col-md-12 col-sm-12 col-xs-12''>Cambiar Registro de Carga</button>":"")+*/
                                                    "<div class='col-lg-1 col-md-1 col-sm-1 col-xs-1'></div></div></div>"+
                                                "</div>"+
                                                "<div class='row'>"+
                                                    "<div class='col-lg-2 col-md-2 col-sm-2 col-xs-2'></div>"+
                                                    "<div class='control-group form-group col-lg-4 col-md-4 col-sm-4 col-xs-4'><div class='controls'><label style='color: #337ab7; text-align:center; width:100%'>Estado</label><input type='text' class='form-control' style='width:100%;text-align:center;' value='En Inventario' disabled/></div></div>"+
                                                    "<div class='control-group form-group col-lg-4 col-md-4 col-sm-4 col-xs-4'><div class='controls'><label style='color: #337ab7; text-align:center; width:100%'>Plan de Entrega</label><input type='text' class='form-control' style='width:100%; text-align:center;' value='"+plan+"' disabled/></div></div>"+
                                                    "<div class='col-lg-2 col-md-2 col-sm-2 col-xs-2'></div>"+
                                                "</div>"+
                                            "</div>",
                                            buttons: {
                                                cancel: {
                                                    label: "Regresar",
                                                    className: "btn btn-md btn-default alinear-izquierda"
                                                },
                                                eliminar: {
                                                    label: "Eliminar Paquete",
                                                    className: (isAdmin ? "btn btn-md btn-danger alinear-derecha" : "gone"),
                                                    callback: function(){
                                                        bootbox.confirm({
                                                            closeButton: false,
                                                            title: "Eliminar paquete",
                                                            message: "Esta acción no podrá ser revertida, ¿desea continuar?",
                                                            buttons:{
                                                                cancel:{
                                                                    label: "CANCELAR",
                                                                    className: "btn btn-md btn-success alinear-izquierda"
                                                                },
                                                                confirm:{
                                                                    label: "Si, Eliminar",
                                                                    className: "btn btn-md btn-danger alinear-derecha"
                                                                }
                                                            },
                                                            callback: function(call){
                                                                if (call){
                                                                    $.ajax({
                                                                        url: "db/DBexecMultiQuery.php",
                                                                        type: "POST",
                                                                        data:{
                                                                            query: "DELETE FROM paquete WHERE tracking = '"+tracking+"' ; " +
                                                                            "UPDATE carga SET total_pqts = total_pqts - 1, total_lbs = total_lbs - " + peso + " WHERE rcid = " + rcid + "; "
                                                                        },
                                                                        cache: false,
                                                                        success: function(resito){
                                                                            if (resito == "1"){
                                                                                bootbox.alert("El paquete ha sido eliminado y se ha actualizado el registro de carga correspondiente.");
                                                                                tablita.rows(indexito).remove().draw(false);
                                                                                arr[0][4] = "<h5 class='seleccionado'>"+(paquetitos-1)+"</h5>";
                                                                                arr[0][5] = "<h5 class='seleccionado'>"+(libritas-peso)+"</h5>";
                                                                                tabCargas.row(index).data(arr[0]).draw(false);
                                                                            }
                                                                            else
                                                                                bootbox.alert("No se pudo eliminar el paquete debido a un problema con la consulta a la base de datos. Intente nuevamente");
                                                                        },
                                                                        error: function(){
                                                                            bootbox.alert("Ocurrió un problema al intentar conectarse al servidor. Intente nuevamente");
                                                                        }
                                                                    });
                                                                }
                                                            }
                                                        });
                                                    }
                                                }
                                            }
                                        });
                                    },
                                    error: function(){
                                        bootbox.alert("Ocurrió un problema al intentar conectarse al servidor.");
                                    }
                                });
                            }
                            // DESPLIEGUE DE INFORMACIÓN DE UN PAQUETE YA ENTREGADO AL CLIENTE
                            else{
                                $.ajax({
                                    url: "db/DBexecQuery.php",
                                    type: "POST",
                                    data: {
                                        query: "SELECT R.fecha, E.fecha AS fechaEntrega, P.rcid, P.plan, ruta, paquetes, liquidado, tarifa FROM paquete P JOIN entrega E ON P.estado = E.fecha JOIN carga R ON P.rcid = R.rcid WHERE tracking = '"+tracking+"'"
                                    },
                                    cache: false,
                                    success: function(res){
                                        if (res === '[]'){
                                            bootbox.alert("No se encontró ningún paquete en la base de datos. Probablemente el paquete fue eliminado por alguien más, actualice la página.");
                                            return;
                                        }
                                        var data = JSON.parse(res)[0];
                                        var rcid = data["rcid"];
                                        var fechaBoleta = data["fechaEntrega"];
                                        var fechaIngreso = convertToHumanDate(data["fecha"]);

                                        var plan = data["plan"];
                                        var cobroRuta = "";
                                        var titlePaquetes = "";
                                        if (plan.includes("/")){
                                            if (data["ruta"] != null){
                                                cobroRuta = "Q " + data["ruta"];
                                                if (data["paquetes"] == 1)
                                                    titlePaquetes = "Solamente se envió este paquete.";
                                                else titlePaquetes = "Note que se enviaron " + data["paquetes"] + " paquetes al cliente.";
                                            }
                                            if (!plan.includes("Guatex"))
                                                plan = "Por Ruta: " + plan;
                                            else plan = "Guatex: " + plan.split(":")[1];
                                        }

                                        var liq = data["liquidado"];
                                        var fechaLiquidacion = "";
                                        if (liq != null) {
                                            fechaLiquidacion = convertToHumanDate(liq);
                                        }

                                        var tarifa = data["tarifa"];
                                        var tarif = Number(tarifa.replace(/[Q,\s]/g, ""));
                                        bootbox.dialog({
                                            backdrop: true,
                                            closeButton: false,
                                            title: "Paquete de " + uname,
                                            message:
                                            //TODO: agregar campos de nombre y id de cliente y trasladar el campo peso a la misma fila (implementar opción para modificar estos 3 campos)
                                            "<div class='row' style='background-color: #dadada'>"+
                                                "<div class='row'>"+
                                                    "<div class='col-lg-1 col-md-1 col-sm-1 col-xs-1'></div>"+
                                                    "<div class='control-group form-group col-lg-4 col-md-4 col-sm-4 col-xs-4'><div class='controls'><label align='center' style='color: #337ab7; text-align:center; width:100%'># Tracking</label><input type='text' class='form-control' style='text-align:center;" + (isAdmin ? " cursor: pointer;' onclick='updateTracking(this, "+indexito+", false)'":"'") + " value='"+tracking+"' readonly/></div></div>"+
                                                    "<div class='control-group form-group col-lg-2 col-md-2 col-sm-2 col-xs-5'><div class='controls'><label align='center'  style='color: #337ab7; text-align:center; width:100%'>Peso</label><input id='librasEntrega' style='text-align:center;' value='"+peso+"' type='text' class='form-control' readonly/></div></div>"+
                                                    (isMobile ? "<div class='col-xs-1'></div></div><div class='row'><div class='col-xs-1'></div>" : "") +
                                                    "<div class='control-group form-group col-lg-4 col-md-4 col-sm-4 col-xs-5'><div class='controls'><label align='center'  style='color: #337ab7; text-align:center; width:100%'>Fecha de Ingreso</label><input id='tarifaEntrega' title='Registro de Carga #"+rcid+"' value='"+fechaIngreso+"' type='text' class='form-control' style='text-align:center;' readonly/>"+/*(isAdmin ? "<button onclick='trasladarPaquete(\""+tracking+"\")' style='color:#337ab7' type='button' class='btn btn-default col-lg-12 col-md-12 col-sm-12 col-xs-12''>Cambiar Registro de Carga</button>":"")+*/
                                                    "<div class='col-lg-1 col-md-1 col-sm-1 col-xs-1'></div></div></div>"+
                                                "</div>"+
                                                "<div class='row'>"+
                                                    "<div class='col-lg-1 col-md-1 col-sm-1 col-xs-1'></div>"+
                                                    "<div class='control-group form-group col-lg-5 col-md-5 col-sm-5 col-xs-5'><div class='controls'><label style='color: #337ab7; text-align:center; width:100%'>Estado</label><input type='text' class='form-control' style='width:100%;text-align:center;' " + (liq != null ? "value='Entregado y Liquidado' title='Entrega: "+fechaEntrega+", Liquidación: "+fechaLiquidacion+"'" : "value='Entregado (aún sin liquidar)' title='Entrega: "+fechaEntrega+"'") + " disabled/>"+(liq != null ? "<div class='control-group form-group col-lg-12 col-md-12 col-sm-12 col-xs-12'><div class='controls'><label style='color: #337ab7; text-align:center; width:100%'>Cobro por Paquete</label><input type='text' class='form-control' style='width:100%; text-align:center;' value='Q "+numberWithCommas(peso*tarif)+"' title='La tarifa del cliente era de "+tarifa+"' disabled/></div></div>" :"")+"</div></div>"+
                                                    "<div class='control-group form-group col-lg-5 col-md-5 col-sm-5 col-xs-5'><div class='controls'><label style='color: #337ab7; text-align:center; width:100%'>Plan de Entrega</label><input type='text' class='form-control' style='width:100%; text-align:center;' value='"+plan+"' disabled/>"+(liq != null && cobroRuta != "" ? "<div class='control-group form-group col-lg-12 col-md-12 col-sm-12 col-xs-12'><div class='controls'><label style='color: #337ab7; text-align:center; width:100%'>Cobro por Envío</label><input type='text' class='form-control' style='width:100%; text-align:center;' value='"+cobroRuta+"' title='"+titlePaquetes+"' disabled/></div></div>" :"")+"</div></div>"+
                                                    "<div class='col-lg-1 col-md-1 col-sm-1 col-xs-1'></div>"+
                                                "</div>"+
                                            "</div>",
                                            buttons: {
                                                cancel: {
                                                    label: "Regresar",
                                                    className: "btn btn-md btn-default alinear-izquierda"
                                                },
                                                eliminar: {
                                                    label: "Eliminar Paquete",
                                                    className: (isAdmin ? "btn btn-md btn-danger alinear-derecha" : "gone"),
                                                    callback: function(){
                                                        bootbox.confirm({
                                                            closeButton: false,
                                                            title: "Eliminar paquete",
                                                            message: "Esta acción no podrá ser revertida, ¿desea continuar?",
                                                            buttons:{
                                                                cancel:{
                                                                    label: "CANCELAR",
                                                                    className: "btn btn-md btn-success alinear-izquierda"
                                                                },
                                                                confirm:{
                                                                    label: "Si, Eliminar",
                                                                    className: "btn btn-md btn-danger alinear-derecha"
                                                                }
                                                            },
                                                            callback: function(call){
                                                                if (call){
                                                                    $.ajax({
                                                                        url: "db/DBgetEntrega.php",
                                                                        type: "POST",
                                                                        data:{
                                                                            select: "tarifa, subtotal, total",
                                                                            where: "fecha = '" + fechaBoleta + "'"
                                                                        },
                                                                        cache: false,
                                                                        success: function(res){
                                                                            if (res === '[]'){
                                                                                bootbox.alert("No se encontró ninguna boleta asociada en la base de datos. Probablemente la boleta fue eliminada por alguien más, actualice la página.");
                                                                                return;
                                                                            }
                                                                            var data = JSON.parse(res)[0];
                                                                            var tarifa = Number(data["tarifa"].replace(/[Q,\s]/g, ""));
                                                                            var resta = peso*tarifa;
                                                                            var subt = Number(data["subtotal"].replace(/[Q,\s]/g, ""));
                                                                            var tot = Number(data["total"].replace(/[Q,\s]/g, ""));

                                                                            //TODO: continuar extracción del total de la boleta virtual
                                                                            if (tarifa == 64)
                                                                                resta = resta*1.065;
                                                                            subt = "Q " + numberWithCommas(subt-resta);
                                                                            tot = "Q " + numberWithCommas(tot-resta);

                                                                            $.ajax({
                                                                                url: "db/DBexecMultiQuery.php",
                                                                                type: "POST",
                                                                                data:{
                                                                                    query: "DELETE FROM paquete WHERE tracking = '"+tracking+"' ; " +
                                                                                    "UPDATE carga SET total_pqts = total_pqts - 1, total_lbs = total_lbs - " + peso + " WHERE rcid = " + rcid + "; " +
                                                                                    "UPDATE entrega SET paquetes = paquetes - 1, libras = libras - " + peso + ", subtotal = '" + subt + "', total = '" + tot + "' WHERE fecha = '" + fechaBoleta + "';"
                                                                                },
                                                                                cache: false,
                                                                                success: function(resito){
                                                                                    if (resito == "1"){
                                                                                        bootbox.alert("El paquete ha sido eliminado y se han actualizado el registro de carga y boleta de entrega correspondientes.");
                                                                                        tablita.rows(indexito).remove().draw(false);
                                                                                        arr[0][4] = "<h5 class='seleccionado'>"+(paquetitos-1)+"</h5>";
                                                                                        arr[0][4] = "<h5 class='seleccionado'>"+(libritas-peso)+"</h5>";
                                                                                        tabCargas.row(index).data(arr[0]).draw(false);
                                                                                    }
                                                                                    else
                                                                                        bootbox.alert("No se pudo eliminar el paquete debido a un problema con la consulta a la base de datos. Intente nuevamente");
                                                                                },
                                                                                error: function(){
                                                                                    bootbox.alert("Ocurrió un problema al intentar conectarse al servidor. Intente nuevamente");
                                                                                }
                                                                            });
                                                                        }
                                                                    });
                                                                }
                                                            }
                                                        });
                                                    }
                                                }
                                            }
                                        });
                                    },
                                    error: function(){
                                        bootbox.alert("Ocurrió un problema al intentar conectarse al servidor.");
                                    }
                                });
                            }
                        });

                        $("#tablaPaquetesCarga tbody").on("mouseover", "h5.popup", function () {
                            var span = $(this).children("span").toggleClass("show", true);
                        });

                        $("#tablaPaquetesCarga tbody").on("mouseout", "h5.popup", function () {
                            var span = $(this).children("span").toggleClass("show", false);
                        });
                    });
                },
                error: function(){
                    bootbox.alert("Ocurrió un problema al intentar conectarse al servidor.");
                }
            });
        });

        var tabBoletas = $('#tablaBoletas').DataTable({
            "bSort" : false,
            "retrieve": true,
            "responsive": true,
            "scrollY": "500px",
            "scrollCollapse": true,
            "paging": true,
            "language": {
                "lengthMenu": "Mostrando _MENU_ boletas por página",
                "search": "Buscar:",
                "zeroRecords": "No hay boletas que coincidan con la búsqueda",
                "info": "Mostrando boletas del _START_ al _END_ de _TOTAL_ boletas.",
                "infoEmpty": "No se encontraron boletas.",
                "infoFiltered": "(Filtrando sobre _MAX_ boletas)",
                "paginate": {
                    "first":      "Primera",
                    "last":       "Última",
                    "next":       "Siguiente",
                    "previous":   "Anterior"
                },
                "loadingRecords": "Cargando Boletas...",
                "processing":     "Procesando...",
            },
            "footerCallback": function ( row, data, start, end, display ) {
                var api = this.api(), data;
                if (this.fnSettings().fnRecordsDisplay() == 0){
                    api.column(1).footer().style.visibility = "hidden";
                    api.column(2).footer().style.visibility = "hidden";
                    api.column(7).footer().style.visibility = "hidden";
                    return;
                }
                else{
                    api.column(1).footer().style.visibility = "visible";
                    api.column(2).footer().style.visibility = "visible";
                    api.column(7).footer().style.visibility = "visible";
                }

                var intVal = function ( i ) {
                    return typeof i === 'string' ?
                        i.replace(/[\$,]/g, '')*1 :
                        typeof i === 'number' ?
                            i : 0;
                };

                $(api.column(1).footer() ).html(
                    "<h5>Total Página: " + numberWithCommasNoFixed(
                                        api.column(1, { page: 'current'} ).data().reduce( function (a, b) {
                                        return intVal(a) + intVal(b.split(">")[1].split("<")[0]);
                                        }, 0 )
                                    ) + " Paquetes</h5><br>" +
                    "<h5><strong>Total: </strong>" + numberWithCommasNoFixed(
                    api.column(1).data().reduce(function (a, b) {
                        return intVal(a) + intVal(b.split(">")[1].split("<")[0]);
                    }, 0)
                    ) + " Paquetes</h5>"
                );
                $(api.column(2).footer() ).html(
                    "<h5>Total Página: " + numberWithCommasNoFixed(
                                        api.column(2, { page: 'current'} ).data().reduce( function (a, b) {
                                        return intVal(a) + intVal(b.split(">")[1].split("<")[0]);
                                        }, 0 )
                                    ) + " Libras</h5><br>" +
                    "<h5><strong>Total: </strong>" + numberWithCommasNoFixed(
                    api.column(2).data().reduce(function (a, b) {
                        return intVal(a) + intVal(b.split(">")[1].split("<")[0]);
                    }, 0)
                    ) + " Libras</h5>"
                );

                $(api.column(7).footer() ).html(
                    "<h5>Total Página: <br>Q " + numberWithCommas(
                                        api.column(7, { page: 'current'} ).data().reduce( function (a, b) {
                                        return intVal(a) + intVal(b.split(">")[1].split("<")[0].replace(/[Q,\s]/g, ""));
                                        }, 0)
                                    ) + "</h5><br>" +
                    "<h5><strong>Total Página:</strong><br>Q " + numberWithCommas(
                    api.column(7).data().reduce( function (a, b) {
                        return intVal(a) + intVal(b.split(">")[1].split("<")[0].replace(/[Q,\s]/g, ""));
                    }, 0)
                    ) + "</h5>"
                );
            }
        });

        $("#tablaBoletas tbody").on("mouseover", "h5.popup", function () {
            var span = $(this).children("span").toggleClass("show", true);
        });

        $("#tablaBoletas tbody").on("mouseout", "h5.popup", function () {
            var span = $(this).children("span").toggleClass("show", false);
        });

        $("#tablaBoletas tbody").on("click", "h5.seleccionado", function(){
            var index = tabBoletas.row($(this).closest('tr')).index();
            if (tabBoletas.rows(index).data().toArray()[0][4].split(">")[1].split("<")[0] == "Pendiente"){
                bootbox.alert("No se pueden seleccionar boletas pendientes de pago. Estas deben ser liquidadas individualmente.");
                return;
            }

            $(this).closest('tr').toggleClass("selected");
            tabBoletas.draw(false);
            if (tabBoletas.rows('.selected').data().toArray().length == 0)
                document.getElementById("divBotonLiquidarBoletas").style.visibility = "hidden";
            else document.getElementById("divBotonLiquidarBoletas").style.visibility= "visible";
        });

        $("#tablaBoletas tbody").on("click", "h5.boleta-paquetes", function (){
            var porLiq = document.getElementById("btnBoletasPorLiquidar").style.color == "white";

            var index = tabBoletas.row($(this).closest('tr')).index();
            var arr = tabBoletas.rows(index).data().toArray();
            var fecha = arr[0][0].split("title='")[1].split("'")[0];
            var uname = arr[0][4].split(">")[1].split("<")[0];

            $.ajax({
                url: "db/DBgetPaquete.php",
                type: "POST",
                data: {
                    select: "tracking, libras",
                    where: "estado = '"+fecha+"'"
                },
                cache: false,
                success: function(res){
                    if (res === '[]'){
                        bootbox.alert("No se encontró ningún paquete asociado a la boleta en la base de datos. Probablemente estos fueron eliminados por alguien más, actualice la página.");
                        return;
                    }
                    var rows = JSON.parse(res);
                    bootbox.dialog({
                        backdrop: true,
                        closeButton: false,
                        title: "Mercadería de " + uname,
                        message:
                        "<div class='row' style='background-color: #eaeaea'>"+
                            "<div class='row' style='margin-bottom: 1cm;'>"+
                                "<div class='col-lg-1 col-md-1 col-sm-1'></div>"+
                                "<div class='col-lg-10 col-md-10 col-sm-10'>"+
                                    "<table id='tablaMercaderiaBoleta' class='display' cellspacing='0'><thead><tr><th class='dt-head-center'><h5 style='color:black'># Tracking</h5></th><th class='dt-head-center'><h5 style='color:black'>Peso</h5></th></tr></thead><tfoot><tr><th class='dt-head-right' colspan='2'></th></tr></tfoot><tbody></tbody></table>"+
                                "</div>"+
                                "<div class='col-lg-1 col-md-1 col-sm-1'></div>"+
                            "</div></div",
                        buttons:{
                            regresar:{
                                label: "Regresar",
                                className: "btn btn-md btn-success alinear-derecha"
                            }
                        }
                    }).on('shown.bs.modal', function (e) {
                        var tablita = $('#tablaMercaderiaBoleta').DataTable({
                            scrollY: "215px",
                            scrollCollapse: true,
                            bSort: false,
                            paging: false,
                            info: false,
                            searching: false,
                            select: false,
                            retrieve: true,
                            responsive: true,
                            "footerCallback": function ( row, data, start, end, display ) {
                                var api = this.api(), data;
                                if (this.fnSettings().fnRecordsDisplay() == 0){
                                    api.column(1).footer().style.visibility = "hidden";
                                    return;
                                }
                                else
                                    api.column(1).footer().style.visibility = "visible";

                                var intVal = function ( i ) {
                                    return typeof i === 'string' ?
                                        i.replace(/[\$,]/g, '')*1 :
                                        typeof i === 'number' ?
                                            i : 0;
                                };

                                $(api.column(0).footer() ).html(
                                    "<h5>Total: " + numberWithCommasNoFixed(
                                                        api.column(1, { page: 'current'} ).data().reduce( function (a, b) {
                                                        return intVal(a) + intVal(b.replace(" libras", "").split(">")[1].split("<")[0]);
                                                        }, 0)
                                                    ) + " Libras</h5>"
                                );
                            }
                        });

                        tablita.clear();
                        for (var i = 0; i < rows.length; i++){
                            var trackingsito = rows[i]["tracking"];
                            if (trackingsito.length > 20)
                                    trackingsito = trackingsito.substr(0, trackingsito.length/2) + "<br>" + trackingsito.substr(trackingsito.length/2, trackingsito.length);
                            tablita.row.add([
                                "<h5 style='text-align:center;'>"+trackingsito+"</h5>",
                                "<h5 style='text-align:center;'>"+rows[i]["libras"]+" libras</h5>",
                            ]);
                        }
                        tablita.draw(false);
                        tablita.columns.adjust().responsive.recalc();
                    });
                },
                error: function(){
                    bootbox.alert("Ocurrió un problema al intentar conectarse al servidor.");
                }
            });

        });


        $("#tablaBoletas tbody").on("click", "img.info_boleta", function (){

            var porLiq = document.getElementById("btnBoletasPorLiquidar").style.color == "white";
            var index = tabBoletas.row($(this).closest('tr')).index();
            var arr = tabBoletas.rows(index).data().toArray();

            var fecha = arr[0][0].split("title='")[1].split("'")[0];
            var paquetes = arr[0][1].split(">")[1].split("<")[0];
            var libras = arr[0][2].split(">")[1].split("<")[0];
            var uid = arr[0][3].split(">")[1].split("<")[0];
            var uname = arr[0][4].split(">")[1].split("<")[0];
            var metodo = arr[0][5].split(">")[1].split("<")[0];
            var total = arr[0][7].split(">")[1].split("<")[0];
            //alert("alerta: " + arr[0][7]);
            var liquidado = null;
            if (!porLiq){
                liquidado = arr[0][7].split("title='")[1].split("'")[0];
                var fec = liquidado.split(" ")[0].split("-");
                var hora = liquidado.split(" ")[1].split(":");
                var h = hora[0];
                var m = hora[1];
                var s = hora[2];

                var apm = "PM";
                if (h > 12)
                    h = h-12;
                else if (h < 12){
                    if (h == 0)
                        h = 12;
                    apm = "AM";
                }
                liquidado = fec[2] + "/" + fec[1] + "/" + fec[0] + " a las " + h + ":" + m + ":" + s + " " + apm;
            }

            $.ajax({
                url: "db/DBexecQuery.php",
                type: "POST",
                data:{
                    query: "SELECT tarifa, subtotal, ruta, descuento, detalle, plan, tabla_mercaderia FROM entrega WHERE fecha = '" + fecha + "'"
                },
                cache: false,
                success: function(res){
                    var rows = JSON.parse(res);
                    if (res === '[]'){
                        bootbox.alert("No se encontró la boleta en la base de datos. Probablemente la boleta fue eliminada por alguien más, actualice la página.");
                        return;
                    }
                    let desc = '', comment = '';
                    if (rows[0]["descuento"] != null) {
                        desc = rows[0]["descuento"].split("@@@")[0];
                        comment = rows[0]["descuento"].split("@@@")[1];
                    }
                    let tableMercaderia = rows[0]['tabla_mercaderia'];
                    bootbox.dialog({
                        backdrop: true,
                        closeButton: false,
                        size: tableMercaderia === null ? 'medium' : 'large',
                        title: "Boleta de mercadería entregada a " + uname,
                        message:
                        "<div class='container-flex'>"+
                            "<div class='row'>"+
                            (!porLiq ?
                            "<div class='row'>"+
                                "<div class='col-lg-3 col-md-3 col-sm-3'></div>"+
                                "<div class='col-lg-6 col-md-6 col-sm-6'>"+
                                    "<div class='control-group form-group'><div class='controls'><label style='color: #337ab7; text-align:center; width:100%'>Fecha Liquidación</label><input type='text' class='form-control' style='width:100%; text-align:center;' value='"+liquidado+"' disabled/></div></div>"+
                                "</div>"+
                                "<div class='col-lg-3 col-md-3 col-sm-3'></div>"+
                            "</div>":"")+
                            (tableMercaderia !== null ? `<div class="mb-3">${tableMercaderia}</div>` :
                                "<div class='row'>"+
                                "<div class='col-lg-1 col-md-1 col-sm-1'></div>"+
                                "<div class='control-group form-group col-lg-2 col-md-2 col-sm-2'><div class='controls'><label align='center'  style='color: #337ab7; text-align:center; width:100%'>Paquetes</label><input style='text-align:center;' value='"+paquetes+"' type='text' class='form-control' disabled/></div></div>"+
                                "<div class='control-group form-group col-lg-2 col-md-2 col-sm-2'><div class='controls'><label align='center'  style='color: #337ab7; text-align:center; width:100%'>Libras</label><input style='text-align:center;' value='"+libras+"' type='text' class='form-control' disabled/></div></div>"+
                                "<div class='control-group form-group col-lg-2 col-md-2 col-sm-2'><div class='controls'><label align='center'  style='color: #337ab7; text-align:center; width:100%'>Tarifa</label><input value='"+rows[0]["tarifa"]+"' type='text' class='form-control' style='text-align:center;' disabled/></div></div>"+
                                "<div class='control-group form-group col-lg-4 col-md-4 col-sm-4'><div class='controls'><label align='center' style='color: #337ab7; text-align:center; width:100%'>Subtotal</label><input type='text' class='form-control' style='text-align:center' value='"+rows[0]["subtotal"]+"' disabled/></div></div>"+
                                "<div class='col-lg-1 col-md-1 col-sm-1'></div>"+
                                "</div>"
                            ) +
                            "<div class='row'>"+
                                "<div class='col-lg-2 col-md-2 col-sm-2'></div>"+
                                "<div class='col-lg-4 col-md-4 col-sm-4'>"+
                                    "<div class='control-group form-group'><div class='controls'><label style='color: #337ab7; text-align:center; width:100%'>Plan de Entrega</label><input type='text' class='form-control' style='width:100%; text-align:center;' value='"+rows[0]["plan"]+"' disabled/></div></div>"+
                                "</div>"+
                                "<div class='col-lg-4 col-md-4 col-sm-4'>"+
                                    "<div class='control-group form-group'><div class='controls'><label style='color: #337ab7; text-align:center; width:100%'>Forma de Pago</label><input type='text' class='form-control' style='width:100%; text-align:center;' value='"+metodo+"' disabled/></div></div>"+
                                "</div>"+
                                "<div class='col-lg-2 col-md-2 col-sm-2'></div>"+
                            "</div>"+
                            (rows[0]["ruta"] != null ?
                            "<div class='row'>"+
                                "<div class='col-lg-4 col-md-4 col-sm-4'></div>"+
                                "<div class='col-lg-4 col-md-4 col-sm-4'>"+
                                    "<div class='control-group form-group'><div class='controls'><label style='color: #337ab7; text-align:center; width:100%'>Costo de Envío (Q)</label><input type='text' class='form-control' style='width:100%; text-align:center;' value='Q "+rows[0]["ruta"]+"' disabled/></div></div>"+
                                "</div>"+
                                "<div class='col-lg-4 col-md-4 col-sm-4'></div>"+
                            "</div>":"")+
                            (desc !== '' ?
                                "<div class='row'>"+
                                    "<div class='col-lg-2 col-md-2 col-sm-2'></div>"+
                                    "<div class='control-group form-group col-lg-3 col-md-3 col-sm-3'><div class='controls'><label style='color: #337ab7; text-align:center; width: 100%;'>Descuento (Q)</label><input value='Q "+desc+"' type='text' class='form-control' style='text-align:center;' disabled/></div></div>"+
                                    "<div class='control-group form-group col-lg-5 col-md-5 col-sm-5'><div class='controls'><label style='color: #337ab7; text-align:center; width: 100%;'>Comentario</label><input value='"+comment+"' type='text' class='form-control' disabled/></div></div>"+
                                    "<div class='col-lg-2 col-md-2 col-sm-2'></div>"+
                                "</div>" :
                            (comment !== '') ?
                              "<div class='row'>"+
                                  "<div class='col-lg-3 col-md-3 col-sm-3'></div>"+
                                  "<div class='control-group form-group col-lg-6 col-md-6 col-sm-6'><div class='controls'><label style='color: #337ab7; text-align:center; width: 100%;'>Comentario</label><input value='"+comment+"' type='text' class='form-control' disabled/></div></div>"+
                                  "<div class='col-lg-3 col-md-3 col-sm-3'></div>"+
                              "</div>" :
                              "" )+
                            "<div class='row-same-height'>"+
                                "<div class='col-lg-2 col-md-2 col-sm-2'></div>"+
                                (rows[0]["detalle"] != null ?
                                "<div class='col-lg-4 col-md-4 col-sm-4'><div class='controls'><label align='center' style='color: #337ab7; text-align:center; width:100%'>Detalle</label><label style='color: gray; font-size: 11px;'>"+rows[0]["detalle"]+"</label>"+
                                "</div></div>":
                                "<div class='col-lg-2 col-md-2 col-sm-2'></div>")+
                                "<div class='control-group form-group col-lg-4 col-md-4 col-sm-4'><div class='controls'><label align='center' style='color: #337ab7; text-align:center; width:100%'>Total</label><input type='text' style='text-align:center' class='form-control' value='"+total+"' disabled/></div></div>"+
                            "</div>"+
                            "</div>"+
                        "</div>",
                        buttons: {
                            cancel: {
                                label: "Regresar",
                                className: "btn btn-md btn-danger alinear-izquierda"
                            },
                            desliquidar:{
                                label: "Desliquidar Boleta",
                                className: (!porLiq ? "btn btn-md btn-extra-danger alinear-izquierda" : "gone"),
                                callback: function(){
                                    bootbox.confirm({
                                        closeButton: false,
                                        message:"Se moverá esta boleta a 'Boletas Por Liquidar'.",
                                        buttons:{
                                            cancel:{
                                                label: "Regresar",
                                                className: "btn btn-md btn-info alinear-izquierda"
                                            },
                                            confirm:{
                                                label: "Continunar",
                                                className: "btn btn-md btn-danger alinear-derecha"
                                            }
                                        },
                                        callback: function(res){
                                            if (res){
                                                $.ajax({
                                                    url: "db/DBexecQuery.php",
                                                    type: "POST",
                                                    data: {
                                                        query: "UPDATE entrega SET liquidado = NULL WHERE fecha = '" + fecha + "'"
                                                    },
                                                    cache: false,
                                                    success: function(res){
                                                        if (res){
                                                            bootbox.hideAll();
                                                            $('#tablaBoletas').DataTable().rows(index).remove().draw(false);
                                                            bootbox.alert("Boleta desliquidada exitosamente.");
                                                        }
                                                    }
                                                });
                                            }
                                            else{
                                                bootbox.alert("No se pudo desliquidar la boleta.");
                                            }
                                        }
                                    });
                                    return false;
                                }
                            },
                            eliminar: {
                                label: "Eliminar Boleta",
                                className: (porLiq ? "btn btn-md btn-extra-danger alinear-izquierda" : "gone"),
                                callback: function(){
                                    bootbox.confirm({
                                        closeButton: false,
                                        message:"Se va a eliminar la boleta, esto provocará que los paquetes asociados se trasladen nuevamente al inventario.",
                                        buttons:{
                                            cancel:{
                                                label: "Regresar",
                                                className: "btn btn-md btn-info alinear-izquierda"
                                            },
                                            confirm:{
                                                label: "Continunar",
                                                className: "btn btn-md btn-danger alinear-derecha"
                                            }
                                        },
                                        callback: function(res){
                                            if (res){
                                                $.ajax({
                                                    url: "db/DBexecQuery.php",
                                                    type: "POST",
                                                    data: {
                                                        query: "UPDATE paquete SET estado = NULL WHERE estado = '" + fecha + "'"
                                                    },
                                                    cache: false,
                                                    success: function(res){
                                                        if (res){
                                                            $.ajax({
                                                                url: "db/DBexecQuery.php",
                                                                type: "POST",
                                                                data: {
                                                                    query: "DELETE FROM entrega WHERE fecha = '" + fecha + "'"
                                                                },
                                                                cache: false,
                                                                success: function(res){
                                                                    if (res){
                                                                        bootbox.hideAll();
                                                                        $('#tablaBoletas').DataTable().rows(index).remove().draw(false);
                                                                        bootbox.alert("Boleta eliminada exitosamente.");
                                                                    }
                                                                    else{
                                                                        bootbox.alert("No se pudo eliminar la boleta, sin embargo, los paquetes fueron enviados al inventario. Contacte al administrador de la base de datos para eliminar la boleta");
                                                                    }
                                                                },
                                                                error: function(){
                                                                    bootbox.alert("Ocurrió un problema al intentar conectarse al servidor. No se pudo eliminar la boleta, sin embargo, los paquetes fueron enviados al inventario. Contacte al administrador de la base de datos para eliminar la boleta");
                                                                }
                                                            });
                                                        }
                                                        else{
                                                            bootbox.alert("No se pudieron trasladar los paquetes al inventario. Intentelo nuevamente más tarde.");
                                                        }
                                                    },
                                                    error: function(){
                                                        bootbox.alert("Ocurrió un problema al intentar conectarse al servidor.");
                                                    }
                                                });
                                            }
                                        }
                                    });
                                    return false;
                                }
                            },
                            confirm: {
                                label: "Liquidar Boleta",
                                className: (porLiq ? "btn btn-md btn-success alinear-derecha" : "gone"),
                                callback: function() {
                                    var hoy = new Date();
                                    var fechita = hoy.getFullYear() + "-" + (hoy.getMonth()+1) + "-" + hoy.getDate() + " " + hoy.getHours() + ":" + hoy.getMinutes() + ":" + hoy.getSeconds();

                                    if (metodo == "Pendiente"){

                                        var nuevoSubtotal = Number(libras)*64*1.065;
                                        var agregado = nuevoSubtotal - Number(rows[0]["tarifa"].replace(/[Q,\s]/g, ""))*Number(libras);
                                        //alert("Agregado: " + agregado);
                                        var extra = nuevoSubtotal;
                                        nuevoSubtotal = "Q " + numberWithCommas(nuevoSubtotal);
                                        var nuevoTotal = "Q " + numberWithCommas(Number(total.replace(/[Q,\s]/g, "")) + agregado);

                                        var tarifAumnt = numberWithCommas(4), comision = 64*Number(libras)*0.065;
                                        if (rows[0]["tarifa"] != "Q 60"){
                                            var t = Number(rows[0]["tarifa"].replace(/[Q,\s]/g, ""));
                                            tarifAumnt = numberWithCommas(64 - t);
                                            extra -= t*Number(libras);
                                        }
                                        else
                                            extra -= Number(libras)*60;
                                        var detalleStr = "Pago con Tarjeta de Crédito:<br> &nbsp&nbsp* Aumento de Tarifa: Q "+tarifAumnt+"<br> &nbsp&nbsp* Comisión: Q "+numberWithCommas(comision)+"<br> &nbsp&nbsp* Monto total agregado:<br> &nbsp&nbsp&nbsp&nbsp&nbsp&nbspQ " + numberWithCommas(extra);

                                        var d = bootbox.dialog({
                                            backdrop: true,
                                            closeButton: false,
                                            size: "small",
                                            title: "Método de Pago",
                                            message: "<div class='row'><div class='row'><div class='col-lg-3 col-md-3 col-sm-3'></div><div class='col-lg-6 col-md-6 col-sm-6'><select id='metPago' onChange='this.options[this.selectedIndex].value == \"Tarjeta C.\" ? document.getElementById(\"divCreditoLiquidandoBoletaPendiente\").style.display=\"block\" : document.getElementById(\"divCreditoLiquidandoBoletaPendiente\").style.display=\"none\"; ' align:'center'><option value='Efectivo'>Efectivo</option><option value='Tarjeta C.'>Tarjeta de Crédito</option><option value='Cheque'>Cheque</option><option value='Transferencia'>Transferencia</option></select></div></div>"+
                                                "<br><div id='divCreditoLiquidandoBoletaPendiente' style='display:none;'><div class='row'><div class='col-lg-1 col-md-1 col-sm-1'></div>"+
                                                    "<div class='control-group form-group col-lg-5 col-md-5 col-sm-5'><div class='controls'><label align='center' style='font-size:12px; color: #337ab7; text-align:center; width:100%; white-space: nowrap;'>Nuevo Subtotal</label><input type='text' class='form-control' style='text-align:center' value='"+nuevoSubtotal+"' disabled/></div></div>"+
                                                        //"<div class='col-lg-2 col-md-2 col-sm-2'></div>"+
                                                        "<div class='control-group form-group col-lg-5 col-md-5 col-sm-5'><div class='controls'><label align='center' style='font-size:12px; color: #337ab7; text-align:center; width:100%; white-space: nowrap;'>Nuevo Total</label><input type='text' class='form-control' style='text-align:center' value='"+nuevoTotal+"' disabled/></div></div>"+
                                                        "<div class='col-lg-1 col-md-1 col-sm-1'></div></div>"+
                                                    "<div class='row'><div class='col-lg-2 col-md-2 col-sm-2'></div>"+
                                                        "<div class='col-lg-8 col-md-8 col-sm-8'><div class='controls'><label align='center' style='font-size:14px; color: #337ab7; text-align:center; width:100%'>Detalle</label><label style='color: gray; font-size: 11px; text-align:center; width:100%'>"+detalleStr+"</label></div></div>"+
                                                    "<div class='col-lg-2 col-md-2 col-sm-2'></div></div></div></div>"
                                            ,
                                            buttons:{
                                                cancel:{
                                                    label: "Regresar",
                                                    className: "btn btn-md btn-danger alinear-izquierda"
                                                },
                                                confirm:{
                                                    label: "Terminar Liquidación",
                                                    className: "btn btn-md btn-success alinear-derecha",
                                                    callback: function(){
                                                        var sel = document.getElementById("metPago");
                                                        var que = "UPDATE entrega SET metodo = '"+sel.options[sel.selectedIndex].value+"', liquidado = '"+fechita+"' WHERE fecha = '" + fecha + "'";
                                                        if (sel.options[sel.selectedIndex].value == "Tarjeta C.")
                                                            que = "UPDATE entrega SET metodo = '"+sel.options[sel.selectedIndex].value+"', liquidado = '"+fechita+"', subtotal='"+nuevoSubtotal+"', total='"+nuevoTotal+"', detalle='"+detalleStr+"' WHERE fecha = '" + fecha + "'";
                                                        $.ajax({
                                                            url: "db/DBexecQuery.php",
                                                            type: "POST",
                                                            data: {
                                                                query: que
                                                            },
                                                            cache: false,
                                                            success: function(res){
                                                                //console.log(table.rows($(this).closest('tr')).data().tracking);
                                                                if (res == 1){
                                                                    bootbox.hideAll();
                                                                    var table = $('#tablaBoletas').DataTable();
                                                                    table.rows(index).remove().draw(false);

                                                                    var fec = fechita.split(" ")[0].split("-");
                                                                    var hora = fechita.split(" ")[1].split(":");
                                                                    var h = hora[0];
                                                                    var m = hora[1];
                                                                    var s = hora[2];
                                                                    if (m < 10 && m.length == 1)
                                                                        m = "0"+m;
                                                                    if (s < 10 && s.length == 1)
                                                                        s = "0"+s;

                                                                    var apm = "PM";
                                                                    if (h > 12)
                                                                        h = h-12;
                                                                    else if (h < 12){
                                                                        if (h == 0)
                                                                            h = 12;
                                                                        apm = "AM";
                                                                    }


                                                                    bootbox.alert("La boleta ha sido marcada como liquidada.\nFecha de liquidación: " + fec[2] + "/" + fec[1] + "/" + fec[0] + " a las " + h + ":" + m + ":" + s + " " + apm);
                                                                }
                                                                else{
                                                                    bootbox.alert("Ocurrió un problema al intentar ejecutar la consulta a la base de datos. Intentelo luego.");
                                                                }
                                                            },
                                                            error: function() {
                                                                bootbox.alert("Ocurrió un problema al intentar conectarse al servidor.");
                                                            }
                                                        });
                                                    }
                                                }
                                            }
                                        });
                                        return false;
                                    }

                                    $.ajax({
                                        url: "db/DBexecQuery.php",
                                        type: "POST",
                                        data: {
                                            query: "UPDATE entrega SET liquidado = '"+fechita+"' WHERE fecha = '" + fecha + "'"
                                        },
                                        cache: false,
                                        success: function(res){
                                            //console.log(table.rows($(this).closest('tr')).data().tracking);
                                            if (res == 1){
                                                bootbox.hideAll();
                                                var table = $('#tablaBoletas').DataTable();
                                                table.rows(index).remove().draw(false);

                                                var fec = fechita.split(" ")[0].split("-");
                                                var hora = fechita.split(" ")[1].split(":");
                                                var h = hora[0];
                                                var m = hora[1];
                                                var s = hora[2];

                                                if (m < 10 && m.length == 1)
                                                    m = "0"+m;
                                                if (s < 10 && s.length == 1)
                                                    s = "0"+s;

                                                var apm = "PM";
                                                if (h > 12)
                                                    h = h-12;
                                                else if (h < 12){
                                                    if (h == 0)
                                                        h = 12;
                                                    apm = "AM";
                                                }
                                                bootbox.alert("La boleta ha sido marcada como liquidada.\nFecha de liquidación: " + fec[2] + "/" + fec[1] + "/" + fec[0] + " a las " + h + ":" + m + ":" + s + " " + apm);
                                            }
                                            else{
                                                bootbox.alert("Ocurrió un problema al intentar ejecutar la consulta a la base de datos. Intentelo luego.");
                                            }
                                        },
                                        error: function() {
                                            bootbox.alert("Ocurrió un problema al intentar conectarse al servidor.");
                                        }
                                    });
                                }
                            },
                            mercaderia:{
                                label: "Ver Mercadería",
                                className: "btn btn-md btn-warning alinear-derecha",
                                callback: function(){
                                    $.ajax({
                                        url: "db/DBgetPaquete.php",
                                        type: "POST",
                                        data: {
                                            select: "tracking, libras",
                                            where: "estado = '"+fecha+"'"
                                        },
                                        cache: false,
                                        success: function(res){
                                            if (res === '[]'){
                                                bootbox.alert("No se encontraron paquetes asociados a la boleta en la base de datos. Probablemente estos fueron eliminados por alguien más, actualice la página.");
                                                return;
                                            }
                                            var rows = JSON.parse(res);
                                            bootbox.dialog({
                                                backdrop: true,
                                                closeButton: false,
                                                title: "Mercadería de " + uname,
                                                message:
                                                "<div class='row' style='background-color: #eaeaea'>"+
                                                    "<div class='row' style='margin-bottom: 1cm;'>"+
                                                        "<div class='col-lg-1 col-md-1 col-sm-1'></div>"+
                                                        "<div class='col-lg-10 col-md-10 col-sm-10'>"+
                                                            "<table id='tablaMercaderiaBoleta' class='display' cellspacing='0'><thead><tr><th class='dt-head-center'><h5 style='color:black'># Tracking</h5></th><th class='dt-head-center'><h5 style='color:black'>Peso</h5></th></tr></thead><tfoot><tr><th class='dt-head-right' colspan='2'></th></tr></tfoot><tbody></tbody></table>"+
                                                        "</div>"+
                                                        "<div class='col-lg-1 col-md-1 col-sm-1'></div>"+
                                                    "</div></div",
                                                buttons:{
                                                    regresar:{
                                                        label: "Regresar",
                                                        className: "btn btn-md btn-success alinear-derecha"
                                                    }
                                                }
                                            }).on('shown.bs.modal', function (e) {
                                                var tablita = $('#tablaMercaderiaBoleta').DataTable({
                                                    scrollY: "215px",
                                                    scrollCollapse: true,
                                                    bSort: false,
                                                    paging: false,
                                                    info: false,
                                                    searching: false,
                                                    select: false,
                                                    retrieve: true,
                                                    responsive: true,
                                                    "footerCallback": function ( row, data, start, end, display ) {
                                                        var api = this.api(), data;
                                                        if (this.fnSettings().fnRecordsDisplay() == 0){
                                                            api.column(1).footer().style.visibility = "hidden";
                                                            return;
                                                        }
                                                        else
                                                            api.column(1).footer().style.visibility = "visible";

                                                        var intVal = function ( i ) {
                                                            return typeof i === 'string' ?
                                                                i.replace(/[\$,]/g, '')*1 :
                                                                typeof i === 'number' ?
                                                                    i : 0;
                                                        };

                                                        $(api.column(0).footer() ).html(
                                                            "<h5>Total: " + numberWithCommasNoFixed(
                                                                                api.column(1, { page: 'current'} ).data().reduce( function (a, b) {
                                                                                return intVal(a) + intVal(b.replace(" libras", "").split(">")[1].split("<")[0]);
                                                                                }, 0)
                                                                            ) + " Libras</h5>"
                                                        );
                                                    }
                                                });

                                                tablita.clear();
                                                for (var i = 0; i < rows.length; i++){
                                                    var trackingsito = rows[i]["tracking"];
                                                    if (trackingsito.length > 20)
                                                            trackingsito = trackingsito.substr(0, trackingsito.length/2) + "<br>" + trackingsito.substr(trackingsito.length/2, trackingsito.length);
                                                    tablita.row.add([
                                                        "<h5 style='text-align:center;'>"+trackingsito+"</h5>",
                                                        "<h5 style='text-align:center;'>"+rows[i]["libras"]+" libras</h5>",
                                                    ]);
                                                }
                                                tablita.draw(false);
                                                tablita.columns.adjust().responsive.recalc();
                                            });
                                        },
                                        error: function(){
                                            bootbox.alert("Ocurrió un problema al intentar conectarse al servidor.");
                                        }
                                    });
                                    return false;
                                }
                            }
                        }
                    }).find("div.modal-dialog").addClass("largeWidthDialog");
                },
                error: function(){
                    bootbox.alert("Ocurrió un problema al intentar conectarse al servidor.");
                }
            });
        });

    });
</script>

<div id="contenidoHistoricoPHP" class="container" style="padding-top: 4.5cm">
    <div class="row" id="divHistoricoPaquetes">
        <div class="col-lg-8 col-md-8 col-sm-8 col-lg-offset-2 col-md-offset-2 col-sm-offset-2" id="divPaquetesBusqueda" style="display: none">
            <div class="col-lg-8 col-md-8 col-sm-8">
            <label class="col-lg-4 col-md-4 col-sm-4" style="text-align: end; color: black">Tracking: </label>
            <input class="col-lg-8 col-md-8 col-sm-8"  type="text" id="paquetesBusqueda" placeholder="Tracking del paquete a buscar">
            </div>
            <button class="col-lg-3 col-md-3 col-sm-3 col-lg-offset-1 col-md-offset-1 col-sm-offset-1 btn btn-sm btn-primary" onclick="buscarPaquete()">Buscar</button>
        </div>
        <div class="col-lg-8 col-md-8 col-sm-8 col-lg-offset-2 col-md-offset-2 col-sm-offset-2" id="divPaquetesFecha" style="display: none">
            <div class="col-lg-4 col-md-4 col-sm-4 col-lg-offset-1 col-md-offset-1 col-sm-offset-1">
                <label style='color: #696969; width:100%; text-align: center'>Desde:
                    <input type="text" style="text-align: center" id='paquetesFechaInicial'>
                </label>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-4">
                <label style='color: #696969; width:100%; text-align: center'>Hasta
                    <input type="text" style="text-align: center" id='paquetesFechaFinal'>
                </label>
            </div>
            <button style="margin-top: 2.5%" class="col-lg-2 col-md-2 col-sm-2 col-lg-offset-1 col-md-offset-1 col-sm-offset-1 btn btn-sm btn-primary" onclick="buscarPaquetesFecha()">Buscar</button>
        </div>
        <table id="historicoPaquetes" class="display" width="100%" cellspacing="0" style="width: 100%;">
            <thead>
                <tr>
                    <th class="dt-head-center"><h5 style="color:black"># Tracking</h5></th>
                    <th class="dt-head-center"><h5 style="color:black">ID Cliente</h5></th>
                    <th class="dt-head-center"><h5 style="color:black">Nombre Cliente</h5></th>
                    <th class="dt-head-center"><h5 style="color:black">Peso</h5></th>
                    <th class="dt-head-center"><h5 style="color:black">Estado</h5></th>
                    <th></th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th class="dt-head-center"></th>
                    <th></th>
                    <th></th>
                </tr>
            </tfoot>
            <tbody>
            </tbody>
        </table>
    </div>
    <div class="row" id="divHistoricoCargas">
        <table id="historicoCargas" class="display" width="100%" cellspacing="0" style="width: 100%;">
            <thead>
                <tr>
                    <th class="dt-head-center"><h5 style="color:black">ID Carga</h5></th>
                    <th class="dt-head-center"><h5 style="color:black">Fecha Ingreso</h5></th>
                    <th class="dt-head-center"><h5 style="color:black">Paquetes</h5></th>
                    <th class="dt-head-center"><h5 style="color:black">Libras</h5></th>
                    <th></th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <th></th>
                    <th></th>
                    <th class="dt-head-center"></th>
                    <th class="dt-head-center"></th>
                    <th></th>
                </tr>
            </tfoot>
            <tbody>
            </tbody>
        </table>
    </div>
    <div class="row" id="divBoletas">
        <table id="tablaBoletas" class="display" width="100%" cellspacing="0" style="width: 100%;">
            <thead>
                <tr>
                    <th class="dt-head-center"><h5 style="color:black">Fecha Entrega</h5></th>
                    <th class="dt-head-center"><h5 style="color:black"># Paquetes</h5></th>
                    <th class="dt-head-center"><h5 style="color:black"># Libras</h5></th>
                    <th class="dt-head-center"><h5 style="color:black">ID Cliente</h5></th>
                    <th class="dt-head-center"><h5 style="color:black">Nombre Cliente</h5></th>
                    <th class="dt-head-center"><h5 style="color:black">Forma de Pago</h5></th>
                    <th class="dt-head-center"><h5 style="color:black">Entregado Vía</h5></th>
                    <th class="dt-head-center"><h5 style="color:black">Total</h5></th>
                    <th></th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <th colspan="2" class="dt-head-right"></th>
                    <th colspan="2" class="dt-head-left"></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th colspan="2" class="dt-head-left"></th>
                </tr>
            </tfoot>
            <tbody>
            </tbody>
        </table>
        <div class="container" align="center" style="background-color: white; position: fixed; left: 0; right: 0; bottom: 0; padding-bottom: 6px; z-index: 10026; visibility: hidden;" id="divBotonLiquidarBoletas">
            <div class='col-lg-12 col-md-12 col-sm-12' style="padding-top: 6px;">
                <div class='col-lg-2 col-md-2 col-sm-2'></div>
                <div class='col-lg-8 col-md-8 col-sm-8'>
                    <button onclick="liquidarBoletasSeleccionadas()" class="btn-lg btn-success" align="center" style="width: 100%; text-align: center;">Liquidar Boletas Seleccionadas</button>
                </div>
                <div class='col-lg-2 col-md-2 col-sm-2'></div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">

    function historicoPaquetesFecha(){

        document.getElementById("divHistoricoPaquetes").style.display = "block";
        if (document.getElementById('divPaquetesFecha').style.display === 'block')
            return;

        var t = $("#historicoPaquetes").DataTable();
        t.clear();
        t.draw();
        if (document.getElementById('divPaquetesBusqueda').style.display === 'block'){
            t.destroy();
            t = $("#historicoPaquetes").DataTable(settingsTablaPaquetes);
        }

        document.getElementById('divPaquetesBusqueda').style.display = 'none';
        document.getElementById('divPaquetesFecha').style.display = 'block';

        $("#paquetesFechaInicial").datepicker({
            showOtherMonths: true,
            selectOtherMonths: true,
            showAnim: "clip",
            minDate: new Date(2017, 10, 20),
            maxDate: 0,
            onSelect: function(dateText, inst){
                let date = new Date();
                date.setDate(inst.selectedDay);
                date.setMonth(inst.selectedMonth);
                date.setFullYear(inst.selectedYear);
                $("#paquetesFechaFinal").datepicker('option', 'minDate', date);
            }
        });
        let ini = new Date();
        ini.setDate(1);
        ini.setMonth(ini.getMonth()-2);
        $('#paquetesFechaInicial').datepicker("setDate", ini);

        $("#paquetesFechaFinal").datepicker({
            showOtherMonths: true,
            selectOtherMonths: true,
            showAnim: "slide",
            minDate: ini,
            maxDate: 0,
            onSelect: function(dateText, inst){
                let date = new Date();
                date.setDate(inst.selectedDay);
                date.setMonth(inst.selectedMonth);
                date.setFullYear(inst.selectedYear);
                $("#paquetesFechaInicial").datepicker('option', 'maxDate', date);
            }
        });
        $('#paquetesFechaFinal').datepicker("setDate", new Date());
    }

    function buscarPaquetesFecha(){
        let fIni= document.getElementById("paquetesFechaInicial").value.split('/');
        let fFin= document.getElementById("paquetesFechaFinal").value.split('/');
        let fechaInicio = fIni[2]+'-'+fIni[1]+'-'+fIni[0];
        let fechaFinal= fFin[2]+'-'+fFin[1]+'-'+fFin[0];

        $.ajax({
            url: 'db/DBexecQuery.php',
            method: 'POST',
            data: {
                query: "SELECT * FROM paquete P JOIN carga C ON P.rcid = C.rcid WHERE C.fecha BETWEEN '"
                        + fechaInicio + "' AND '" + fechaFinal + "'"
            },
            cache: false,
            success: function (res){
                if (res === '[]'){
                    bootbox.alert("No se encontró ningún paquete en la base de datos. Intente con otro rango de fechas");
                    return;
                }
                var rows = JSON.parse(res);
                let t = $("#historicoPaquetes").DataTable();
                t.clear();
                for (var i = 0; i < rows.length; i++){
                    var estado = "<h5 style='text-align: center; cursor: default;' class='btn-sm btn-warning'>En Inventario</h5>";
                    if (rows[i]["estado"] != null){
                        var fec = rows[i]["estado"].split(" ")[0].split("-");
                        var hora = rows[i]["estado"].split(" ")[1].split(":");
                        var h = hora[0];
                        var m = hora[1];
                        var s = hora[2];
                        var apm = "PM";
                        if (h > 12)
                            h = h-12;
                        else if (h < 12){
                            if (h == 0)
                                h = 12;
                            apm = "AM";
                        }

                        var color = "#f4cb38";
                        if (rows[i]["liquidado"] != null)
                            color = "#4c883c";
                        estado = "<h5 style='text-align: center; cursor: default; background-color: "+color+";' class='popup btn-sm'>Entregado<span class='popuptext'>"+fec[2] + "/" + fec[1] + "/" + fec[0] + " a las " + h + ":" + m + " " + apm+"</span></h5>";
                    }

                    t.row.add([
                        "<h5 class='seleccionado'>"+rows[i]["tracking"]+"</h5>",
                        "<h5 class='seleccionado'>"+rows[i]["uid"]+"</h5>",
                        "<h5 class='seleccionado'>"+rows[i]["uname"]+"</h5>",
                        "<h5 class='seleccionado'>"+rows[i]["libras"]+"</h5>",
                        estado,
                        "<img align='center' style='text-align:center; cursor:pointer;' class='info_pqt' src='images/info_paquete.png'/>"
                    ]);
                }
                //t.order([1, "asc"]);
                t.draw(false);
                t.columns.adjust().responsive.recalc();
            },
            error: function(){
                bootbox.alert("Ocurrió un problema al intentar conectarse al servidor. Intente realizar la búsqueda nuevamente.");
            }
        });

    }

    function historicoBusquedaPaquete(){
        document.getElementById("divHistoricoPaquetes").style.display = "block";
        if (document.getElementById('divPaquetesBusqueda').style.display === 'block')
            return;
        document.getElementById('divPaquetesBusqueda').style.display = 'block';
        document.getElementById('divPaquetesFecha').style.display = 'none';
        //*
        let t = $("#historicoPaquetes").DataTable();
        t.clear();
        t.draw(false);
        t.destroy();
        $("#historicoPaquetes").DataTable(settingsTablaPaquetesBusqueda);
    }

    function buscarPaquete(){
        let tracking = document.getElementById('paquetesBusqueda').value;
        if (tracking){
            $.ajax({
                url: 'db/DBgetPaquete.php',
                method: 'POST',
                data: {
                    select: "*",
                    where: "tracking LIKE '%" + tracking + "%'"
                },
                cache: false,
                success: function (arr){
                    if (arr !== '[]') {
                        let t = $("#historicoPaquetes").DataTable();
                        t.clear();
                        var rows = JSON.parse(arr);
                        for (var i = 0; i < rows.length; i++) {
                            var estado = "<h5 style='text-align: center; cursor: default;' class='btn-sm btn-warning'>En Inventario</h5>";
                            if (rows[i]["estado"] != null) {
                                var fec = rows[i]["estado"].split(" ")[0].split("-");
                                var hora = rows[i]["estado"].split(" ")[1].split(":");
                                var h = hora[0];
                                var m = hora[1];
                                var s = hora[2];
                                var apm = "PM";
                                if (h > 12)
                                    h = h - 12;
                                else if (h < 12) {
                                    if (h == 0)
                                        h = 12;
                                    apm = "AM";
                                }

                                var color = "#f4cb38";
                                if (rows[i]["liquidado"] != null)
                                    color = "#4c883c";
                                estado = "<h5 style='text-align: center; cursor: default; background-color: " + color + ";' class='popup btn-sm'>Entregado<span class='popuptext'>" + fec[2] + "/" + fec[1] + "/" + fec[0] + " a las " + h + ":" + m + " " + apm + "</span></h5>";
                            }

                            t.row.add([
                                "<h5 class='seleccionado'>" + rows[i]["tracking"] + "</h5>",
                                "<h5 class='seleccionado'>" + rows[i]["uid"] + "</h5>",
                                "<h5 class='seleccionado'>" + rows[i]["uname"] + "</h5>",
                                "<h5 class='seleccionado'>" + rows[i]["libras"] + "</h5>",
                                estado,
                                "<img align='center' style='text-align:center; cursor:pointer;' class='info_pqt' src='images/info_paquete.png'/>"
                            ]);
                        }

                        t.draw(false);
                        t.columns.adjust().responsive.recalc();
                    }
                    else{
                        bootbox.alert('No se encontró ningún paquete con este número de tracking');
                    }
                },
                error: function(){
                    bootbox.alert("Ocurrió un problema al intentar conectarse al servidor. Intente realizar la búsqueda nuevamente.");
                }
            });
        }
        else bootbox.alert('Por favor ingrese un tracking para poder realizar la búsqueda');
    }

    function updateTracking(campo, index, histPqts){
        var t = $(histPqts ? "#historicoPaquetes" : "#tablaPaquetesCarga").DataTable();
        var arr = t.rows(index).data().toArray();
        var d = bootbox.dialog({
            title: "Ingrese el nuevo tracking para el paquete.",
            message: "<input class='text-field form-control validate-field' id='promptTracking' placeholder='# Tracking' maxlength='50' onkeypress='return onlyLettersAndNumbers(this, event)'>",
            size: "small",
            buttons: {
                cancel: {
                    label: "Cancelar",
                    className: 'btn-sm btn-danger alinear-izquierda',
                    callback: function(){
                        $("#promptTracking").remove();
                    }
                },
                ok: {
                    label: "Cambiar",
                    className: 'btn-sm btn-info alinear-derecha',
                    callback: function () {
                        var result = document.getElementById("promptTracking")  .value;
                        if (result == "")
                            bootbox.alert("Debe ingresar un tracking para el paquete.");
                        else if (result.includes("<"))
                            bootbox.alert("El tracking no puede contener el caracter '<'");
                        else if (result.includes(">"))
                            bootbox.alert("El tracking no puede contener el caracter '>'");
                        else if (result.includes(","))
                            bootbox.alert("El tracking no puede contener comas.");
                        else if (result.length > 50)
                            bootbox.alert("El tracking no puede exceder los 50 caracteres.");
                        else {

                            var nt = ["NT", "NOTRACKING", "NO", "nt", "notracking", "no", "NoTracking"];
                            if (nt.indexOf(result) != -1){
                                $.ajax({
                                    url: "db/DBgetPaquete.php",
                                    type: "POST",
                                    data: {
                                        select: "MAX(tracking) AS max",
                                        where: "tracking LIKE 'NO\_TRACKING\_%'"
                                    },
                                    cache: false,
                                    success: function(arr) {
                                        var corre = Number(JSON.parse(arr.replace("[","").replace("]","")).max.split("\_")[2])+1;
                                        if (corre < 10)
                                            corre = "000"+corre;
                                        else if (corre < 100)
                                            corre = "00"+corre;
                                        else if (corre < 1000)
                                            corre = "0"+corre;

                                        tracking = "NO_TRACKING_"+corre;

                                        var t = $('#tablaNuevaCarga').DataTable();
                                        t.row.add( [
                                            "<h5 class='seleccionado ingCargaTracking'>"+tracking+"</h5>",
                                            "<h5 class='seleccionado ingCargaUid'>"+uid+"</h5>",
                                            "<h5 class='seleccionado ingCargaUname'>"+uname+"</h5>",
                                            "<h5 class='seleccionado ingCargaPeso'>"+peso+"</h5>",
                                            "<img style='cursor: pointer;' class='icon-delete'  src='images/remove.png'/>"
                                        ]).draw(false);

                                        document.getElementById("paquetes").innerHTML = "Paquetes: " + t.rows().data().length;
                                        document.getElementById("libras").innerHTML = "Libras: " + calcularLibras();

                                        document.getElementById("uid").value = "";
                                        document.getElementById("uname").value = "";
                                        document.getElementById("peso").value = "";
                                        document.getElementById("tracking").value = "";
                                        document.getElementById("tracking").focus();
                                    },
                                    error: function() {
                                        bootbox.alert("Ocurrió un problema al intentar conectarse al servidor. Intente agregar nuevamente el paquete.");
                                    }
                                });

                                return;
                            }

                            $.ajax({
                                url: "db/DBgetPaquete.php",
                                type: "POST",
                                data: {
                                    select: "COUNT(tracking) AS cant",
                                    where: "tracking = '"+result+"'"
                                },
                                cache: false,
                                success: function(res) {
                                    if (!res.includes("\"0\"")){
                                        bootbox.alert({
                                            message: "Ya existe un paquete registrado con este número de tracking, asegúrese de haber ingresado bien el dato. ",
                                            size: 'small',
                                            backdrop: true
                                        });
                                    }
                                    else{
                                        $.ajax({
                                            url: "db/DBsetPaquete.php",
                                            type: "POST",
                                            data: {
                                                set: "tracking = '" + result + "'",
                                                where: "tracking = '"+campo.value+"'"
                                            },
                                            cache: false,
                                            success: function(resin){
                                                if (resin.includes("ERROR")){
                                                    bootbox.alert("Ocurrió un error al consultar la base de datos. Se recibió el siguiente mensaje: <i><br>" + resin + "</i>");
                                                }
                                                else if (Number(resin) < 1)
                                                    bootbox.alert("No se pudo efectuar el cambio en la base de datos, intente nuevamente");
                                                else{
                                                    if (!histPqts && result.length > 20)
                                                        result = result.substr(0, result.length/2) + "<br>" + result.substr(result.length/2, result.length);
                                                    arr[0][0] = "<h5 class='seleccionado'>"+result+"</h5>";
                                                    t.row(index).data(arr[0]).draw(false);
                                                    campo.value = result;
                                                    d.hide();
                                                    $("#promptTracking").remove();
                                                    bootbox.alert("El tracking fue modificado exitosamente.");
                                                }
                                            },
                                            error: function() {
                                                bootbox.alert("Ocurrió un problema al intentar conectarse al servidor. Intente nuevamente");
                                            }
                                        });
                                    }
                                },
                                error: function() {
                                    bootbox.alert("Ocurrió un problema al intentar conectarse al servidor. Intente nuevamente");
                                }
                            });
                        }

                        return false;
                    }
                }
            }
        });

    }

    function trasladarPaquete(tracking){

    }

    function loadHistoricoPaquetes(){


        var t = $("#historicoPaquetes").DataTable();
        if (document.getElementById('divPaquetesBusqueda').style.display === 'block'){
            t.destroy();
            t = $("#historicoPaquetes").DataTable(settingsTablaPaquetes);
        }

        document.getElementById('divPaquetesBusqueda').style.display = 'none';
        document.getElementById('divPaquetesFecha').style.display = 'none';
        document.getElementById("divHistoricoPaquetes").style.display = "block";

        t.clear();
        var oTableTools = TableTools.fnGetInstance("historicoPaquetes");
        if ( oTableTools != null && oTableTools.fnResizeRequired()){
            oTableTools.fnResizeButtons();
        }

        $.ajax({
            url: "db/DBexecQuery.php",
            type: "POST",
            data:{
                query: "SELECT P.tracking AS tracking, P.uid AS uid, P.uname AS uname, P.libras AS libras, P.estado AS estado, E.liquidado AS liquidado, R.fecha AS fecha FROM paquete P LEFT JOIN entrega E ON P.estado = E.fecha JOIN carga R ON R.rcid = P.rcid ORDER BY fecha DESC, uname ASC, libras DESC"
            },
            cache: false,
            success: function(arr){
                var rows = JSON.parse(arr);
                for (var i = 0; i < rows.length; i++){
                    var estado = "<h5 style='text-align: center; cursor: default;' class='btn-sm btn-warning'>En Inventario</h5>";
                    if (rows[i]["estado"] != null){
                        var fec = rows[i]["estado"].split(" ")[0].split("-");
                        var hora = rows[i]["estado"].split(" ")[1].split(":");
                        var h = hora[0];
                        var m = hora[1];
                        var s = hora[2];
                        var apm = "PM";
                        if (h > 12)
                            h = h-12;
                        else if (h < 12){
                            if (h == 0)
                                h = 12;
                            apm = "AM";
                        }

                        var color = "#f4cb38";
                        if (rows[i]["liquidado"] != null)
                            color = "#4c883c";
                        estado = "<h5 style='text-align: center; cursor: default; background-color: "+color+";' class='popup btn-sm'>Entregado<span class='popuptext'>"+fec[2] + "/" + fec[1] + "/" + fec[0] + " a las " + h + ":" + m + " " + apm+"</span></h5>";
                    }

                    t.row.add([
                        "<h5 class='seleccionado'>"+rows[i]["tracking"]+"</h5>",
                        "<h5 class='seleccionado'>"+rows[i]["uid"]+"</h5>",
                        "<h5 class='seleccionado'>"+rows[i]["uname"]+"</h5>",
                        "<h5 class='seleccionado'>"+rows[i]["libras"]+"</h5>",
                        estado,
                        "<img align='center' style='text-align:center; cursor:pointer;' class='info_pqt' src='images/info_paquete.png'/>"
                    ]);
                }
                //t.order([1, "asc"]);
                t.draw(false);
                t.columns.adjust().responsive.recalc();
            },
            error: function(){
                bootbox.alert("Ocurrió un problema al intentar conectarse al servidor.");
            }
        });
    }

    function loadHistoricoCargas(){
        document.getElementById("divBoletas").style.display = "none";
        document.getElementById("divHistoricoPaquetes").style.display = "none";
        document.getElementById("divHistoricoCargas").style.display = "block";
        var t = $("#historicoCargas").DataTable();
        t.clear();
        $.ajax({
            url: "db/DBexecQuery.php",
            type: "POST",
            data:{
                query: "SELECT * FROM carga ORDER BY fecha DESC"
            },
            cache: false,
            success: function(arr){
                var rows = JSON.parse(arr);
                for (var i = 0; i < rows.length; i++){
                    var fec = rows[i]["fecha"].split(" ")[0].split("-");
                    var hora = rows[i]["fecha"].split(" ")[1].split(":");
                    var h = hora[0];
                    var m = hora[1];
                    var apm = "PM";
                    if (h > 12)
                        h = h-12;
                    else if (h < 12){
                        if (h == 0)
                            h = 12;
                        apm = "AM";
                    }

                    t.row.add([
                        "<h5 class='seleccionado'>"+rows[i]["rcid"]+"</h5>",
                        "<h5 class='seleccionado' title='"+rows[i]["fecha"]+"'>"+fec[2] + "/" + fec[1] + "/" + fec[0] + " a las " + h + ":" + m + " " + apm+"</h5>",
                        "<h5 class='seleccionado'>"+rows[i]["total_pqts"]+"</h5>",
                        "<h5 class='seleccionado'>"+rows[i]["total_lbs"]+"</h5>",
                        "<img align='center' style='text-align:center; cursor:pointer;' class='info_carga' src='images/info_carga.png'/>"
                    ]);
                }
                t.draw(false);
                t.columns.adjust().responsive.recalc();
            },
            error: function(){
                bootbox.alert("Ocurrió un problema al intentar conectarse al servidor.");
            }
        });
    }

    function loadBoletasLiquidadas(){
        document.getElementById("divHistoricoPaquetes").style.display = "none";
        document.getElementById("divHistoricoCargas").style.display = "none";
        document.getElementById("divBoletas").style.display = "block";
        var t = $('#tablaBoletas').DataTable();
        t.clear();

        $.ajax({
            url: "db/DBexecQuery.php",
            type: "POST",
            data:{
                query: "SELECT fecha, paquetes, libras, uid, uname, metodo, plan, total, liquidado FROM entrega WHERE liquidado IS NOT NULL ORDER BY fecha DESC"
            },
            cache: false,
            success: function(arr){
                document.getElementById("divBotonLiquidarBoletas").style.visibility = "hidden";
                var rows = JSON.parse(arr);
                for (var i = 0; i < rows.length; i++){
                    var fec = rows[i]["fecha"].split(" ")[0].split("-");
                    var hora = rows[i]["fecha"].split(" ")[1].split(":");
                    var h = hora[0];
                    var m = hora[1];
                    var s = hora[2];
                    var apm = "PM";
                    if (h > 12)
                        h = h-12;
                    else if (h < 12){
                        if (h == 0)
                            h = 12;
                        apm = "AM";
                    }
                    var plansito = rows[i]["plan"];
                    var plansin = plansito == "Oficina" ? "<h5 class='seleccionado btn-sm btn-success'>En Oficina</h5>" :
                        plansito.includes("Guatex") ? "<h5 class='popup btn-sm' style='text-align:center; cursor: default; background-color: #f4cb38'>Guatex<span class='popuptext'>"+plansito.split(":")[1]+"</span></h5>" :
                        "<h5 class='popup btn-sm btn-primary' style='text-align:center; cursor: default;'>Ruta<span class='popuptext'>"+plansito.split(":")[1]+"</span></h5>";
                    t.row.add([
                        "<h5 style='text-align:center;' title='"+rows[i]["fecha"]+"'>"+fec[2] + "/" + fec[1] + "/" + fec[0] + " a las " + h + ":" + m + " " + apm+"</h5>",
                        "<h5 class='boleta-paquetes' style='text-align:center; cursor:help;'>"+rows[i]["paquetes"]+"</h5>",
                        "<h5 style='text-align:center;'>"+rows[i]["libras"]+"</h5>",
                        "<h5 style='text-align:center;'>"+rows[i]["uid"]+"</h5>",
                        "<h5 style='text-align:center;'>"+rows[i]["uname"]+"</h5>",
                        "<h5 style='text-align:center;'>"+(rows[i]["metodo"] == "Tarjeta C." ? "Tarjeta de Crédito" : rows[i]["metodo"])+"</h5>",
                        plansin,
                        "<h5 style='text-align:center;' title='"+rows[i]["liquidado"]+"'>"+rows[i]["total"]+"</h5>",
                        "<img align='center' style='text-align:center; cursor:pointer;' class='info_boleta' src='images/info_boleta_entrega.png'/>"
                    ]);
                }
                t.draw(false);
                t.columns.adjust().responsive.recalc();
            },
            error: function(){
                bootbox.alert("Ocurrió un problema al intentar conectarse al servidor.");
            }
        });
    }

    function loadBoletasPorLiquidar(){
        document.getElementById("divHistoricoPaquetes").style.display = "none";
        document.getElementById("divHistoricoCargas").style.display = "none";
        document.getElementById("divBoletas").style.display = "block";
        var t = $('#tablaBoletas').DataTable();
        t.clear();
        $.ajax({
            url: "db/DBexecQuery.php",
            type: "POST",
            data:{
                query: "SELECT fecha, paquetes, libras, uid, uname, metodo, plan, total FROM entrega WHERE liquidado IS NULL ORDER BY fecha DESC"
            },
            cache: false,
            success: function(arr){
                document.getElementById("divBotonLiquidarBoletas").style.visibility = "hidden";
                var rows = JSON.parse(arr);
                for (var i = 0; i < rows.length; i++){
                    var fec = rows[i]["fecha"].split(" ")[0].split("-");
                    var hora = rows[i]["fecha"].split(" ")[1].split(":");
                    var h = hora[0];
                    var m = hora[1];
                    var s = hora[2];
                    var apm = "PM";
                    if (h > 12)
                        h = h-12;
                    else if (h < 12){
                        if (h == 0)
                            h = 12;
                        apm = "AM";
                    }
                    var plansito = rows[i]["plan"];
                    var plansin = plansito == "Oficina" ? "<h5 class='seleccionado btn-sm btn-success'>En Oficina</h5>" :
                        plansito.includes("Guatex") ? "<h5 class='popup btn-sm' style='text-align:center; cursor: default; background-color: #f4cb38'>Guatex<span class='popuptext'>"+plansito.split(":")[1]+"</span></h5>" :
                        "<h5 class='popup btn-sm btn-primary' style='text-align:center; cursor: default;'>Ruta<span class='popuptext'>"+plansito.split(":")[1]+"</span></h5>";
                    t.row.add([
                        "<h5 class='seleccionado' title='"+rows[i]["fecha"]+"'>"+fec[2] + "/" + fec[1] + "/" + fec[0] + " a las " + h + ":" + m + " " + apm+"</h5>",
                        "<h5 class='boleta-paquetes' style='text-align:center; cursor:help;'>"+rows[i]["paquetes"]+"</h5>",
                        "<h5 class='seleccionado'>"+rows[i]["libras"]+"</h5>",
                        "<h5 class='seleccionado'>"+rows[i]["uid"]+"</h5>",
                        "<h5 class='seleccionado'>"+rows[i]["uname"]+"</h5>",
                        "<h5 class='seleccionado'>"+(rows[i]["metodo"] == "Tarjeta C." ? "Tarjeta de Crédito" : rows[i]["metodo"])+"</h5>",
                        plansin,
                        "<h5 class='seleccionado'>"+rows[i]["total"]+"</h5>",
                        "<img align='center' style='text-align:center; cursor:pointer;' class='info_boleta' src='images/info_boleta_entrega.png'/>"
                    ]);
                }
                t.draw(false);
                t.columns.adjust().responsive.recalc();
            },
            error: function(){
                bootbox.alert("Ocurrió un problema al intentar conectarse al servidor.");
            }
        });
    }

    function liquidarBoletasSeleccionadas(){
        document.getElementById("divBotonLiquidarBoletas").style.visibility = "hidden";
        var data = $("#tablaBoletas").DataTable().rows(".selected").data().toArray();
        var fechas = "(";
        for (var i = 0; i < data.length; i++)
            fechas = fechas + (i == 0 ? "'":", '")+data[i][0].split("title='")[1].split("'")[0]+"'";
        fechas = fechas+")";

        //total = numberWithCommas(total);
        var total = 0;
        for (var i = 0; i < data.length; i++){
            total += Number(data[i][7].split(">")[1].split("<")[0].replace(/[Q,\s]/g, ""));
        }
        total = numberWithCommas(total);

        bootbox.confirm({
                title: "Liquidar Boletas",
                message: "Se van a liquidar " + data.length + " boletas, para un total de Q " + total,
                buttons: {
                    cancel: {
                        label: 'Cancelar',
                        className: "btn-danger"
                    },
                    confirm: {
                        label: 'Continuar',
                        className: "btn-success",
                    }
                },
                callback: function(res){
                    if (res){
                        var hoy = new Date();
                        var fechita = hoy.getFullYear() + "-" + (hoy.getMonth()+1) + "-" + hoy.getDate() + " " + hoy.getHours() + ":" + hoy.getMinutes() + ":" + hoy.getSeconds();
                        $.ajax({
                            url: "db/DBexecQuery.php",
                            type: "POST",
                            data:{
                                query: "UPDATE entrega SET liquidado = '"+fechita+"' WHERE fecha IN "+fechas
                            },
                            cache: false,
                            success: function(res){
                                if (res == 1){
                                    $("#tablaBoletas").DataTable().rows('.selected').remove().draw(false);
                                    var fec = fechita.split(" ")[0].split("-");
                                    var hora = fechita.split(" ")[1].split(":");
                                    var h = hora[0];
                                    var m = hora[1];
                                    var s = hora[2];
                                    if (m < 10 && m.length == 1)
                                        m = "0"+m;
                                    if (s < 10 && s.length == 1)
                                        s = "0"+s;

                                    var apm = "PM";
                                    if (h > 12)
                                        h = h-12;
                                    else if (h < 12){
                                        if (h == 0)
                                            h = 12;
                                        apm = "AM";
                                    }
                                    bootbox.alert("Boletas liquidadas exitosamente. Fecha de liquidación: " + fec[2] + "/" + fec[1] + "/" + fec[0] + " a las " + h + ":" + m + ":" + s + " " + apm);
                                }
                                else
                                    bootbox.alert("No fue posible realizar la operación. Vuelva a intentarlo luego");
                            },
                            error: function(){
                                document.getElementById("divBotonLiquidarBoletas").style.visibility = "visible";
                                bootbox.alert("Ocurrió un problema al intentar conectarse al servidor.");
                            }
                        });
                    }
                    else
                        document.getElementById("divBotonLiquidarBoletas").style.visibility = "visible";
                }
            });
            return;
    }

</script>