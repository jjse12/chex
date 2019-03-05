<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/dt-1.10.16/r-2.2.0/datatables.min.css"/>
<script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.10.16/r-2.2.0/datatables.min.js"></script>

<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css">
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script>
    $.datepicker.regional['es'] = {
        closeText: 'Cerrar',
        prevText: '< Ant',
        nextText: 'Sig >',
        currentText: 'Hoy',
        monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
        monthNamesShort: ['Ene','Feb','Mar','Abr', 'May','Jun','Jul','Ago','Sep', 'Oct','Nov','Dic'],
        dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
        dayNamesShort: ['Dom','Lun','Mar','Mié','Juv','Vie','Sáb'],
        dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','Sá'],
        weekHeader: 'Sm',
        dateFormat: 'dd/mm/yy',
        firstDay: 1,
        isRTL: false,
        showMonthAfterYear: false,
        yearSuffix: ''
    };
    $.datepicker.setDefaults($.datepicker.regional['es']);
</script>
<script>
    $(document).ready( function () {
        var table = $('#inventario').DataTable({
            "retrieve": true,
            "dom": 'CT<"clear">lfrtip',
            "tableTools": {
                "sSwfPath": "./swf/copy_csv_xls_pdf.swf"
            },
            "select": true,
            "responsive": false,
            "scrollY": "500px",
            "scrollCollapse": true,
            "paging": false,
            "fixedColumns": true,
            "language": {
                "lengthMenu": "Display _MENU_ records per page",
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
            "order": [[4, 'asc']],
            "columnDefs": [
                {
                    "targets": [0, 2, 7],
                    "orderable": false
                }
            ],
            "footerCallback": function ( row, data, start, end, display ) {
                var api = this.api(), data;
                if (this.fnSettings().fnRecordsDisplay() == 0){
                    api.column(4).footer().style.visibility = "hidden";
                    return;
                }
                else
                    api.column(4).footer().style.visibility = "visible";

                var intVal = function ( i ) {
                    return typeof i === 'string' ?
                        i.replace(/[\$,]/g, '')*1 :
                        typeof i === 'number' ?
                            i : 0;
                };

                $(api.column(4).footer() ).html(
                    "<h5>Total: " + numberWithCommasNoFixed(api.column(5, { page: 'current'} ).data().reduce( function (a, b) {
                                        return intVal(a) + intVal(b.split(">")[1].split("<")[0]);
                                        }, 0)) + " Libras</h5>"
                );
            }
        });

        $(".buscarIngreso").keyup(function () {
            let val = $(this).val();
            table.column(1).search(val).draw(false);
        });
        $(".buscarPlan").keyup(function () {
            let val = $(this).val();
            table.column(6).search(val).draw(false);
        });

        $("#inventario tbody").on("click", "h5.seleccionado", function () {
            $(this).closest('tr').toggleClass("selected");
            table.draw(false);
            if (table.rows('.selected').data().toArray().length == 0)
                document.getElementById("divBotones").style.visibility = "hidden";
            else document.getElementById("divBotones").style.visibility= "visible";
        });

        $("#inventario tbody").on("mouseover", "h5.popup", function () {
            $(this).children("span").stop(true, true).delay(200).fadeIn(500);
        });

        $("#inventario tbody").on("mouseout", "h5.popup", function () {
            $(this).children("span").stop(true, true).delay(200).fadeOut(500);
        });

        $("#inventario tbody").on("mouseover", "h5.popup-notif", function () {
            $(this).children("div").stop(true, true).delay(200).fadeIn(500);
        });

        $("#inventario tbody").on("mouseout", "h5.popup-notif", function () {
            $(this).children("div").stop(true, true).delay(200).fadeOut(500);
        });

        $("#inventario tbody").on("click", "img.icon-email", function (e) {
            e.stopPropagation();
            var index = table.row($(this).closest('tr')).index();
            var arr = table.rows(index).data().toArray();
            var sinNotificar = !arr[0][6].includes("Notificado por Whatsapp");
            var tracking = arr[0][2].replace("<br>", "").split(">")[1].split("<")[0];
            var avisando = arr[0][6].includes("Avisar");
            var sete = "plan = " + (sinNotificar ? (avisando ? "'@email'" : "'email'") : (avisando ? "'@whatsmail'" : "'whatsmail'"));
            $.ajax({
                url: "db/DBsetPaquete.php",
                type: "POST",
                data: {
                    set: sete,
                    where: "tracking = '" + tracking + "'"
                },
                cache: false,
                success: function(res){
                    if (res.includes("ERROR")){
                        bootbox.alert("Ocurrió un error al consultar la base de datos. Se recibió el siguiente mensaje: <i><br>" + res + "</i>");
                    }
                    else if (Number(res) < 1){
                        bootbox.alert("No se pudo efectuar el cambio en la base de datos, intente nuevamente");
                    }
                    else{
                        arr[0][6] = sinNotificar ? (!avisando ? "<div class='row'><div class='col-lg-4 col-md-4 col-sm-4 col-xs-4'></div><img title='Notificado por Email' class='col-lg-4 col-md-4 col-sm-4 col-xs-4' src='images/email20px.png'/></div><h5 class='popup-notif sin-plan plan btn-sm btn-danger'>Sin Especificar<div class='popupicon'><div class='row'><label class='col-lg-12 col-md-12 col-sm-12 col-xs-12' style='text-align:center;'>Notificar</label><img class='icon-whatsapp' src='images/whatsapp35px.png'/></div></div></h5>":

                            "<div class='row'><div class='col-lg-4 col-md-4 col-sm-4 col-xs-4'></div><img title='Notificado por Email' class='col-lg-4 col-md-4 col-sm-4 col-xs-4' src='images/email20px.png'/></div><h5 class='popup-notif sin-plan plan btn-sm' style='background-color: #eaeaea; color: #444'>Avisar<div class='popupicon'><div class='row'><label class='col-lg-12 col-md-12 col-sm-12 col-xs-12' style='text-align:center;'>Notificar</label><img class='icon-whatsapp' src='images/whatsapp35px.png'/></div></div></h5>"
                         ):
                        (!avisando ? "<div class='row'><div class='col-lg-1 col-md-1 col-sm-1 col-xs-1'></div><img title='Notificado por Email' class='col-lg-4 col-md-4 col-sm-4 col-xs-4' src='images/email20px.png'/><div class='col-lg-2 col-md-2 col-sm-2 col-xs-2'></div><img title='Notificado por Whatsapp' class='col-lg-4 col-md-4 col-sm-4 col-xs-4' src='images/whatsapp20px.png'/></div><h5 class='sin-plan plan btn-sm btn-danger'>Sin Especificar</h5>":
                            "<div class='row'><div class='col-lg-1 col-md-1 col-sm-1 col-xs-1'></div><img title='Notificado por Email' class='col-lg-4 col-md-4 col-sm-4 col-xs-4' src='images/email20px.png'/><div class='col-lg-2 col-md-2 col-sm-2 col-xs-2'></div><img title='Notificado por Whatsapp' class='col-lg-4 col-md-4 col-sm-4 col-xs-4' src='images/whatsapp20px.png'/></div><h5 class='popup-notif sin-plan plan btn-sm' style='background-color: #eaeaea; color: #444'>Avisar"
                        );
                        table.row(index).data(arr[0]).draw(false);
                    }
                },
                error: function() {
                    bootbox.alert("Ocurrió un problema al intentar conectarse al servidor.");
                }
            });
        });

        $("#inventario tbody").on("click", "img.icon-whatsapp", function (e) {
            e.stopPropagation();
            var index = table.row($(this).closest('tr')).index();
            var arr = table.rows(index).data().toArray();
            var sinNotificar = !arr[0][6].includes("Notificado por Email");
            var tracking = arr[0][2].replace("<br>", "").split(">")[1].split("<")[0];
            var avisando = arr[0][6].includes("Avisar");
            var sete = "plan = " + (sinNotificar ? (avisando ? "'@whats'" : "'whats'") : (avisando ? "'@whatsmail'" : "'whatsmail'"));
            ///*
            $.ajax({
                url: "db/DBsetPaquete.php",
                type: "POST",
                data: {
                    set: sete,
                    where: "tracking = '" + tracking + "'"
                },
                cache: false,
                success: function(res){
                    if (res.includes("ERROR")){
                        bootbox.alert("Ocurrió un error al consultar la base de datos. Se recibió el siguiente mensaje: <i><br>" + res + "</i>");
                    }
                    else if (Number(res) < 1){
                        bootbox.alert("No se pudo efectuar el cambio en la base de datos, intente nuevamente");
                    }
                    else{
                        arr[0][6] = sinNotificar ? (!avisando ? "<div class='row'><div class='col-lg-4 col-md-4 col-sm-4 col-xs-4'></div><img title='Notificado por Whatsapp' class='col-lg-4 col-md-4 col-sm-4 col-xs-4' src='images/whatsapp20px.png'/></div><h5 class='popup-notif sin-plan plan btn-sm btn-danger'>Sin Especificar<div class='popupicon'><div class='row'><label class='col-lg-12 col-md-12 col-sm-12 col-xs-12' style='text-align:center;'>Notificar</label><img class='icon-email' src='images/email35px.png'/></div></div></h5>" :
                            "<div class='row'><div class='col-lg-4 col-md-4 col-sm-4 col-xs-4'></div><img title='Notificado por Whatsapp' class='col-lg-4 col-md-4 col-sm-4 col-xs-4' src='images/whatsapp20px.png'/></div><h5 class='popup-notif sin-plan plan btn-sm' style='background-color: #eaeaea; color: #444'>Avisar<div class='popupicon'><div class='row'><label class='col-lg-12 col-md-12 col-sm-12 col-xs-12' style='text-align:center;'>Notificar</label><img class='icon-email' src='images/email35px.png'/></div></div></h5>"
                        ) :
                        (!avisando ? "<div class='row'><div class='col-lg-1 col-md-1 col-sm-1 col-xs-1'></div><img title='Notificado por Email' class='col-lg-4 col-md-4 col-sm-4 col-xs-4' src='images/email20px.png'/><div class='col-lg-2 col-md-2 col-sm-2 col-xs-2'></div><img title='Notificado por Whatsapp' class='col-lg-4 col-md-4 col-sm-4 col-xs-4' src='images/whatsapp20px.png'/></div><h5 class='sin-plan plan btn-sm btn-danger'>Sin Especificar</h5>" :
                            "<div class='row'><div class='col-lg-1 col-md-1 col-sm-1 col-xs-1'></div><img title='Notificado por Email' class='col-lg-4 col-md-4 col-sm-4 col-xs-4' src='images/email20px.png'/><div class='col-lg-2 col-md-2 col-sm-2 col-xs-2'></div><img title='Notificado por Whatsapp' class='col-lg-4 col-md-4 col-sm-4 col-xs-4' src='images/whatsapp20px.png'/></div><h5 class='popup-notif sin-plan plan btn-sm' style='background-color: #eaeaea; color: #444'>Avisar"
                        );
                        table.row(index).data(arr[0]).draw(false);
                    }
                },
                error: function() {
                    bootbox.alert("Ocurrió un problema al intentar conectarse al servidor.");
                }
            });
        });

        $("#inventario tbody").on("click", "h5.plan", function () {
            var index = table.row($(this).closest('tr')).index();
            var arr = table.rows(index).data().toArray();
            var nombre = arr[0][4].split(">")[1].split("<")[0];
            var uid = arr[0][3].split(">")[1].split("<")[0];
            var tracking = arr[0][2].replace("<br>", "").split(">")[1].split("<")[0];

            var plan = "";
            if (arr[0][6].includes("Oficina"))
                plan = "Oficina";
            else if (arr[0][6].includes("Guatex"))
                plan = "Guatex:"+arr[0][6].split(">")[2].split("<")[0];
            if (arr[0][6].includes("Esperando"))
                plan = arr[0][6].split(">")[2].split(" Paquetes")[0];
            if (arr[0][6].includes("En Ruta"))
                plan = arr[0][6].split(">")[2].split("<")[0].replace("-", "").replace("-","");

            var arreglo = ["Cliente", "CLIENTE", "cliente", "Anónimo", "ANÓNIMO", "anónimo", "Anonimo", "ANONIMO", "anonimo"];
            var anonimo = arreglo.indexOf(uid) != -1;

            bootbox.dialog({
                closeButton: false,
                title: "Plan de Entrega para el Paquete de " + nombre,
                message:"<div class='row' style='background-color: #dadada'>"+
                    "<div class='col-lg-12 col-md-12 col-sm-12 col-xs-12'>"+
                        "<form novalidate>"+
                            "<br>"+
                            "<div class='control-group form-group col-lg-6 col-md-6 col-sm-6 col-xs-12'>"+
                                "<label style='color: #337ab7; width:100%; text-align: center'>Plan de Entrega</label>"+
                                    "<button onclick='toggleActivadito(this)' id='btnOficina' style='width:50%; color:#337ab7' type='button' class='btn btn-default'>Oficina</button>"+
                                    "<button onclick='toggleActivadito(this)' id='btnRuta' style='width:50%; color:#337ab7' type='button' class='btn btn-default'>En Ruta</button>"+
                                    "<button onclick='toggleActivadito(this)' id='btnGuatex' style='width:50%; color:#337ab7' type='button' class='btn btn-default'>Guatex</button>"+
                                    "<button onclick='toggleActivadito(this)' id='btnEsperando' style='width:50%; color:#337ab7' type='button' class='btn btn-default'>Esperando</button>"+
                                //"</div>"+
                            "</div>"+
                            "<div class='col-lg-6 col-md-6 col-sm-6 col-xs-12'>"+
                                "<div id='divFechaRuta' style='display:none'>"+
                                    "<label style='color: #696969; width:100%; text-align: center'>Fecha de Ruta</label>"+
                                    //"<input type='text' id='form_carga_fecha' value='"+tomor+"' style='display:none' class='form-control'/>"+
                                "<br></div>"+
                                "<div id='divEsperandoCantidad' style='display:none'>"+
                                    "<label style='color: #696969; width:100%; text-align: center'>Cantidad de Paquetes Faltantes</label>"+
                                    "<input placeholder='Paquetes Faltantes' onkeyup='this.value=this.value.replace(/^0+/, \"\");' onkeypress='return integersonly(this, event);' type='text' maxlength='2' style='text-align:center;'  class='form-control' id='form_carga_esperando'/>"+
                                "</div>"+
                                "<label style='"+(anonimo?"display:none;":"")+" color: #696969; font-size:12px; padding-left:20%; padding-right: 20%; width:100%; text-align: center'>Aplicar a todos los paquetes de " + nombre + "</label><input type='checkbox' "+(anonimo? "style='display:none;'":"")+" class='form-control' id='form_carga_check_esperando'/><br>"+
                            "</div>"+
                            "<br>"+
                            "</div>"+
                        "</form>"+
                    "</div>"+
                "</div>",
                buttons: {
                    cancel: {
                        label: "Cancelar Plan de Entrega",
                        className: "btn btn-md btn-danger alinear-izquierda"
                    },
                    confirm: {
                        label: "<div id='spanLlenarCamposCarga' style='display:none'><span class='dialog-text'>Ingrese correctamente el campo solicitado.</span></div>Asignar Plan de Entrega",
                        className: "btn btn-md btn-success alinear-derecha",
                        callback: function() {

                            var esp = document.getElementById("form_carga_esperando").value;
                            var plan = "";
                            if (document.getElementById("btnOficina").style.color == "white")
                                plan = "Oficina";
                            else if (document.getElementById("btnGuatex").style.color == "white"){
                                var f = $("#divFechaRuta").datepicker("getDate");
                                plan = "Guatex:" + (f.getDate() < 10 ? "0" : "") + f.getDate() + "/" + (f.getMonth()+1 < 10 ? "0" : "") + (f.getMonth()+1) + "/" + f.getFullYear() ;
                            }
                            else if (document.getElementById("btnEsperando").style.color == "white"){
                                if (esp.replace(/\s/g,'').length === 0 || esp < 1){
                                    document.getElementById("spanLlenarCamposCarga").style.display="inline";
                                    setTimeout(function() {$('#spanLlenarCamposCarga').fadeOut('slow');}, 3000);
                                    return false;
                                }

                                plan = document.getElementById("form_carga_esperando").value;

                            }
                            else if (document.getElementById("btnRuta").style.color == "white"){
                                var f = $("#divFechaRuta").datepicker("getDate");
                                plan = (f.getDate() < 10 ? "0" : "") + f.getDate() + "/" + (f.getMonth()+1 < 10 ? "0" : "") + (f.getMonth()+1) + "/" + f.getFullYear() ;
                            }

                            var wher = "tracking = '" + tracking + "'";
                            var todos = false;
                            if (document.getElementById("form_carga_check_esperando").checked){
                                wher = "estado IS NULL AND uid = '" + uid + "'";
                                todos = true;
                            }

                            $.ajax({
                                url: "db/DBsetPaquete.php",
                                type: "POST",
                                data: {
                                    set: "plan='"+plan+"'",
                                    where: wher
                                },
                                cache: false,
                                success: function(res){
                                    if (res.includes("ERROR")){
                                        bootbox.alert("Ocurrió un error al consultar la base de datos. Se recibió el siguiente mensaje: <i><br>" + res + "</i>");
                                    }
                                    else if (Number(res) < 1){
                                        bootbox.alert("No se pudo efectuar el cambio en la base de datos, intente nuevamente");
                                    }
                                    else{
                                        bootbox.hideAll();
                                        if (todos){
                                            if (isChrome)
                                                loadInventario();
                                            else{
                                                table.rows().every( function(rowIdx, tableLoop, rowLoop) {
                                                    var dataRow = String(this.data()).split(",");
                                                    if (dataRow[3].split(">")[1].split("<")[0].toUpperCase() == uid.toUpperCase()){
                                                        dataRow[6] = plan == "" ? "<h5 class='popup-notif sin-plan plan btn-sm btn-danger'>Sin Especificar<div class='popupicon'><div class='row'><label class='col-lg-12 col-md-12 col-sm-12 col-xs-12' style='text-align:center;'>Notificar</label><img class='icon-email' src='images/email35px.png'/>&nbsp&nbsp<img class='icon-whatsapp' src='images/whatsapp35px.png'/></div></div></h5>" :
                                                        plan == "Oficina" ? "<h5 class='plan btn-sm btn-success'>En Oficina</h5>" :
                                                        plan.includes("Guatex") ? "<h5 class='popup plan btn-sm' style='background-color: #f4cb38'>Guatex<span class='popuptext'>"+plan.split(":")[1]+"</span></h5>" :
                                                        plan.length < 3 ? "<h5 class='popup plan btn-sm' style='background-color: #ff8605'>Esperando<span class='popuptext'>"+plan+" Paquetes</span></h5>":
                                                        "<h5 class='popup plan btn-sm btn-primary' style='text-align:center'>En Ruta<span class='popuptext'>-"+plan+"-</span></h5>";
                                                        table.row(rowIdx).data(dataRow).draw(false);
                                                    }
                                                });
                                                bootbox.alert("Se actualizó el plan de entrega de todos los paquetes de " + nombre + ".");
                                            }
                                        }
                                        else{
                                            arr[0][6] = plan == "" ? "<h5 class='popup-notif sin-plan plan btn-sm btn-danger'>Sin Especificar<div class='popupicon'><div class='row'><label class='col-lg-12 col-md-12 col-sm-12 col-xs-12' style='text-align:center;'>Notificar</label><img class='icon-email' src='images/email35px.png'/>&nbsp&nbsp<img class='icon-whatsapp' src='images/whatsapp35px.png'/></div></div></h5>" :
                                                        plan == "Oficina" ? "<h5 class='plan btn-sm btn-success'>En Oficina</h5>" :
                                                        plan.includes("Guatex") ? "<h5 class='popup plan btn-sm' style='background-color: #f4cb38'>Guatex<span class='popuptext'>"+plan.split(":")[1]+"</span></h5>" :
                                                        plan.length < 3 ? "<h5 class='popup plan btn-sm' style='background-color: #ff8605'>Esperando<span class='popuptext'>"+plan+" Paquetes</span></h5>":
                                                        "<h5 class='popup plan btn-sm btn-primary' style='text-align:center'>En Ruta<span class='popuptext'>-"+plan+"-</span></h5>";
                                            table.row(index).data(arr[0]).draw(false);
                                            bootbox.alert("Se actualizó la información del paquete exitosamente.");
                                        }
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

            $("#divFechaRuta").datepicker({
                showOtherMonths: true,
                selectOtherMonths: true,
                showAnim: "slideDown",
                minDate: 0,
                maxDate: "+1M"
            });
            var tom = new Date();
            tom.setTime(tom.getTime() + 86400000);
            $('#divFechaRuta').datepicker("setDate", tom);

            if (plan == "Oficina"){
                document.getElementById("btnOficina").style.backgroundColor = "#337ab7";
                document.getElementById("btnOficina").style.color = "white";
            }
            else if (plan.includes("Guatex")){
                document.getElementById("btnGuatex").style.backgroundColor = "#337ab7";
                document.getElementById("btnGuatex").style.color = "white";
                document.getElementById("divFechaRuta").style.display = "block";
                var fechaArr = plan.split(":")[1].split("/");
                $('#divFechaRuta').datepicker("setDate", new Date(Number(fechaArr[2]),Number(fechaArr[1])-1, Number(fechaArr[0])));
            }
            else if (plan.length > 0){
                if (plan.length >= 3) {
                    document.getElementById("btnRuta").style.backgroundColor = "#337ab7";
                    document.getElementById("btnRuta").style.color = "white";
                    document.getElementById("divFechaRuta").style.display = "block";
                    //document.getElementById("form_carga_fecha").value = row.plan;
                    var fechaArr = plan.split("/");
                    $('#divFechaRuta').datepicker("setDate", new Date(Number(fechaArr[2]),Number(fechaArr[1])-1, Number(fechaArr[0])));
                }
                else {
                    document.getElementById("btnEsperando").style.backgroundColor = "#337ab7";
                    document.getElementById("btnEsperando").style.color = "white";
                    document.getElementById("divEsperandoCantidad").style.display = "block";
                    document.getElementById("form_carga_esperando").value = plan;
                }
            }
        });


        $("#inventario tbody").on("click", "img.icon-update", function (){

            var index = table.row($(this).closest('tr')).index();
            var arr = table.rows(index).data().toArray();
            var celulares = arr[0][0].split('data-celulares=')[1].split(' ')[0];
            var extras = arr[0][0].split('data-cobro-extra=')[1].split(' ')[0];
            var fechaIng = arr[0][1].split(">")[1].split("<")[0];
            var rcid = arr[0][1].split("#")[1].split("'")[0];
            var tracking = arr[0][2].replace("<br>", "").split(">")[1].split("<")[0];
            var uid = arr[0][3].split(">")[1].split("<")[0];
            var uname = arr[0][4].split(">")[1].split("<")[0];
            var peso = arr[0][5].split(">")[1].split("<")[0];
            var plan = "";
            if (arr[0][6].includes("Oficina"))
                plan = "Oficina";
            else if (arr[0][6].includes("Guatex"))
                plan = "Guatex:"+arr[0][6].split(">")[2].split("<")[0];
            if (arr[0][6].includes("Esperando"))
                plan = arr[0][6].split(">")[2].split(" Paquetes")[0];
            if (arr[0][6].includes("En Ruta"))
                plan = arr[0][6].split(">")[2].split("<")[0].replace("-", "").replace("-","");

            var tom = new Date();
            tom.setTime(tom.getTime() + 86400000);
            bootbox.dialog({
                closeButton: false,
                title: "Modificar paquete de " + uname,
                message:"<div class='row' style='background-color: #dadada'>"+
                            "<div class='col-lg-12 col-md-12 col-sm-12 col-xs-12'>"+
                                "<form novalidate>"+
                                    "<div class='control-group form-group col-lg-4 col-md-4 col-sm-4 col-xs-4'><div class='controls'><label style='color: #337ab7; text-align:center; width:100%'># Tracking </label><input value='"+tracking+"' type='text' style='text-align: center;' class='form-control' readonly /></div></div>"+
                                    "<div class='control-group form-group col-lg-4 col-md-4 col-sm-4 col-xs-4'><div class='controls'><label style='color: #337ab7; text-align:center; width:100%'># Registro de Cargas</label><input readonly value='" + rcid + "'' type='text' style='width:100%; text-align: center;' class='form-control'/></div></div>"+
                                    "<div class='control-group form-group col-lg-4 col-md-4 col-sm-4 col-xs-4'><div class='controls'><label style='color: #337ab7; text-align:center; width:100%'>Fecha de Ingreso</label><input readonly value='" + fechaIng + "'' type='text' style='width:100%; text-align: center;' class='form-control'/></div></div>"+
                                    "<div class='control-group form-group col-lg-3 col-md-3 col-sm-3 col-xs-3'><div class='controls'><label style='color: #337ab7; text-align:center; width:100%'>Peso</label><input placeholder='Peso' value='" + peso +"' onkeyup='this.value=this.value.replace(/^0+/, \"\");' onkeypress='return integersonly(this, event);' type='text' maxlength='3' style='text-align:center;' class='form-control' id='form_carga_libras'/></div></div>"+
                                    "<div class='control-group form-group col-lg-3 col-md-3 col-sm-3 col-xs-3'><div class='controls'><label style='color: #337ab7; text-align:center; width:100%'>ID Cliente</label><input onfocusout='getUserName2(this.value)' value='" + uid + "' style='text-align: center;' type='text' maxlength='7' class='form-control' placeholder='ID Cliente' id='form_carga_uid'/><div id='spanIDCliente' style='display:none'><span class='dialog-text'> Atención: No existe ningún cliente asociado a este ID.</span></div></div></div>"+
                                    "<div class='control-group form-group col-lg-6 col-md-6 col-sm-6 col-xs-6'><div class='controls'><label style='color: #337ab7; text-align:center; width:100%'>Nombre Cliente</label><input placeholder='Nombre Cliente' value='" + uname + "' style='text-align: center;' type='email' maxlength='50' class='form-control' id='form_carga_uname' /></div></div>"+
                                    "<div class='control-group form-group col-lg-3 col-md-3 col-sm-3 col-xs-3'><div class='controls'><label style='color: #337ab7; text-align:center; width:100%'>Celulares</label><input placeholder='Cantidad' value='" + (celulares > 0 ? celulares : "") +"' onkeyup='this.value=this.value.replace(/^0+/, \"\");' onkeypress='return integersonly(this, event);' type='text' maxlength='3' style='text-align:center;' class='form-control' id='form_carga_celulares'/></div></div>"+
                                    "<div class='control-group form-group col-lg-3 col-md-3 col-sm-3 col-xs-3'><div class='controls'><label style='color: #337ab7; text-align:center; width:100%'>Cobro Extra</label><input placeholder='Monto (Q)' value='" + (extras > 0 ? extras : "") +"' onkeyup='this.value=this.value.replace(/^0+/, \"\");' onkeypress='return integersonly(this, event);' type='text' maxlength='5' style='text-align:center;' class='form-control' id='form_carga_cobro_extra'/></div></div>"+
                                    "<div class='control-group form-group col-lg-6 col-md-6 col-sm-6 col-xs-6'>"+
                                        "<label style='color: #337ab7; width:100%; text-align: center'>Plan de Entrega</label>"+
                                            "<button onclick='toggleActivadito(this)' id='btnOficina' style='width:50%; color:#337ab7' type='button' class='btn btn-default'>Oficina</button>"+
                                            "<button onclick='toggleActivadito(this)' id='btnRuta' style='width:50%; color:#337ab7' type='button' class='btn btn-default'>En Ruta</button>"+
                                            "<button onclick='toggleActivadito(this)' id='btnGuatex' style='width:50%; color:#337ab7' type='button' class='btn btn-default'>Guatex</button>"+
                                            "<button onclick='toggleActivadito(this)' id='btnEsperando' style='width:50%; color:#337ab7' type='button' class='btn btn-default'>Esperando</button>"+
                                        //"</div>"+
                                    "</div>"+
                                    "<div class='col-lg-offset-3 col-md-offset-3 col-sm-offset-3 col-xs-offset-3 col-lg-6 col-md-6 col-sm-6 col-xs-6' style='margin-bottom: 10px;'>"+
                                        "<div id='divFechaRuta' style='display:none'>"+
                                            "<label style='color: #696969; width:100%; text-align: center'>Fecha de Ruta</label>"+
                                        "<br></div>"+
                                        "<div id='divEsperandoCantidad' style='display:none'>"+
                                            "<label style='color: #696969; width:100%; text-align: center'>Cantidad de Paquetes Faltantes</label>"+
                                            "<input placeholder='Paquetes Faltantes' onkeyup='this.value=this.value.replace(/^0+/, \"\");' onkeypress='return integersonly(this, event);' type='text' maxlength='2' style='text-align:center;'  class='form-control' id='form_carga_esperando'/>"+
                                        "</div>"+
                                    "</div>"+
                                    "<br>"+
                                    "</div>"+
                                "</form>"+
                            "</div>"+
                        "</div>",
                buttons: {
                    cancel: {
                        label: "Cancelar",
                        className: "btn btn-md btn-danger alinear-izquierda"
                    },
                    confirm: {
                        label: "<div id='spanLlenarCamposCarga' style='display:none'><span class='dialog-text'> Asegurese de llenar todos los campos.</span></div>Guardar Cambios",
                        className: "btn btn-md btn-success alinear-derecha",
                        callback: function() {
                            var uid = document.getElementById("form_carga_uid").value;
                            var uname = document.getElementById("form_carga_uname").value;
                            var pesito = document.getElementById("form_carga_libras").value;
                            var esp = document.getElementById("form_carga_esperando").value;
                            var celularesN = document.getElementById("form_carga_celulares").value;
                            var extrasN = document.getElementById("form_carga_cobro_extra").value;
                            if (celularesN == '')
                                celularesN = 0;
                            if (extrasN == '')
                                extrasN = 0;

                            var plan = "";

                            if (uid.replace(/\s/g,'').length === 0 || uname.replace(/\s/g,'').length === 0 || pesito.length === 0){
                                document.getElementById("spanLlenarCamposCarga").style.display="inline";
                                setTimeout(function() {
                                    $('#spanLlenarCamposCarga').fadeOut('slow');
                                }, 3000);
                                return false;
                            }


                            if (document.getElementById("btnOficina").style.color == "white")
                                plan = "Oficina";
                            else if (document.getElementById("btnGuatex").style.color == "white"){
                                var f = $("#divFechaRuta").datepicker("getDate");
                                plan = "Guatex:" + (f.getDate() < 10 ? "0" : "") + f.getDate() + "/" + (f.getMonth()+1 < 10 ? "0" : "") + (f.getMonth()+1) + "/" + f.getFullYear() ;
                            }
                            else if (document.getElementById("btnEsperando").style.color == "white"){
                                if (esp.replace(/\s/g,'').length === 0 || esp < 1){
                                    document.getElementById("spanLlenarCamposCarga").style.display="inline";
                                setTimeout(function() {
                                    $('#spanLlenarCamposCarga').fadeOut('slow');
                                }, 3000);
                                return false;
                                }
                                plan = document.getElementById("form_carga_esperando").value;
                            }
                            else if (document.getElementById("btnRuta").style.color == "white"){
                                var f = $("#divFechaRuta").datepicker("getDate");
                                plan = (f.getDate() < 10 ? "0" : "") + f.getDate() + "/" + (f.getMonth()+1 < 10 ? "0" : "") + (f.getMonth()+1) + "/" + f.getFullYear() ;
                            }

                            $.ajax({
                                url: "db/DBsetPaquete.php",
                                type: "POST",
                                data: {
                                    set: "uid='"+uid+"', uname='"+uname+"', libras="+pesito+", plan='"+plan+"' ,"
                                            + "celulares="+celularesN+", cobro_extra="+extrasN,
                                    where: "tracking = '"+tracking+"'"
                                },
                                cache: false,
                                success: function(res){
                                    if (res.includes("ERROR")){
                                        bootbox.alert("Ocurrió un error al consultar la base de datos. Se recibió el siguiente mensaje: <i><br>" + res + "</i>");
                                    }
                                    else if (Number(res) < 1)
                                        bootbox.alert("No se pudo efectuar el cambio en la base de datos, intente nuevamente");
                                    else{
                                        var especial = celularesN + extrasN > 0;
                                        if (peso != pesito){
                                            $.ajax({
                                                url: "db/DBsetCarga.php",
                                                type: "POST",
                                                data: {
                                                    set: "total_lbs = total_lbs - (" + (peso-pesito) + ")",
                                                    where: "rcid = " + rcid
                                                },
                                                cache: false,
                                                success: function(res){
                                                    if (res.includes("ERROR")){
                                                        bootbox.alert("Ocurrió un error al consultar la base de datos para actualizar el registro de cargas asociado al paquete. Se recibió el siguiente mensaje: <i><br>" + res + "</i><br>" + "Contacte con el administrador de base de datos para poder ajustar el registro de carga");
                                                    }
                                                    else if (Number(res) < 1){
                                                        bootbox.alert("No se pudo actualizar el peso del registro de carga asociado al paquete. Contacte con el administrador de base de datos para poder ajustar el registro de carga");
                                                    }
                                                    else{
                                                        bootbox.alert("La información del paquete ha sido actualizada. El total de libras del registro de carga asociado también ha sido actualizado.");
                                                        var table = $('#inventario').DataTable();
                                                        arr[0][0] = `<h5 class='seleccionado' data-celulares=${celularesN} data-cobro-extra=${extrasN} >${especial ? "<span title='Celulares: "+ celularesN + ", Cobro Extra: Q"+ numberWithCommas(extrasN) +"' style='color: gold;'><i class='fa fa-star fa-2x fa-lg'></i><small style='display: none;'>Especial</small></span>" : ""}</h5>`,
                                                        arr[0][3] = "<h5 class='seleccionado'>"+uid+"</h5>";
                                                        arr[0][4] = "<h5 class='seleccionado'>"+uname+"</h5>";
                                                        arr[0][5] = "<h5 class='seleccionado'>"+pesito+"</h5>";
                                                        arr[0][6] = plan == "" ? "<h5 class='popup-notif sin-plan plan btn-sm btn-danger'>Sin Especificar<div class='popupicon'><div class='row'><label class='col-lg-12 col-md-12 col-sm-12 col-xs-12' style='text-align:center;'>Notificar</label><img class='icon-email' src='images/email35px.png'/>&nbsp&nbsp<img class='icon-whatsapp' src='images/whatsapp35px.png'/></div></div></h5>" :
                                                                    plan == "Oficina" ? "<h5 class='plan btn-sm btn-success'>En Oficina</h5>" :
                                                                    plan.includes("Guatex") ? "<h5 class='popup plan btn-sm' style='background-color: #f4cb38'>Guatex<span class='popuptext'>"+plan.split(":")[1]+"</span></h5>" :
                                                                    plan.length < 3 ? "<h5 class='popup plan btn-sm' style='background-color: #ff8605'>Esperando<span class='popuptext'>"+plan+" Paquetes</span></h5>":
                                                                    "<h5 class='popup plan btn-sm btn-primary' style='text-align:center'>En Ruta<span class='popuptext'>-"+plan+"-</span></h5>";
                                                        table.row(index).data(arr[0]);
                                                        table.order([4, "asc"]);
                                                        table.draw(false);
                                                    }

                                                },
                                                error: function(){
                                                    bootbox.alert("No se pudo actualizar el peso del registro de carga asociado al paquete. Contacte con el administrador de base de datos para poder ajustar el registro de carga");
                                                }
                                            });
                                        }
                                        else{
                                            bootbox.alert("Se actualizó la información del paquete exitosamente.");
                                            var table = $('#inventario').DataTable();
                                            arr[0][0] = `<h5 class='seleccionado' data-celulares=${celularesN} data-cobro-extra=${extrasN} >${especial ? "<span title='Celulares: "+ celularesN + ", Cobro Extra: Q"+ numberWithCommas(extrasN) +"' style='color: gold;'><i class='fa fa-star fa-2x fa-lg'></i><small style='display: none;'>Especial</small></span>" : ""}</h5>`,
                                            arr[0][3] = "<h5 class='seleccionado'>"+uid+"</h5>";
                                            arr[0][4] = "<h5 class='seleccionado'>"+uname+"</h5>";
                                            arr[0][5] = "<h5 class='seleccionado'>"+pesito+"</h5>";
                                            arr[0][6] = plan == "" ? "<h5 class='popup-notif sin-plan plan btn-sm btn-danger'>Sin Especificar<div class='popupicon'><div class='row'><label class='col-lg-12 col-md-12 col-sm-12 col-xs-12' style='text-align:center;'>Notificar</label><img class='icon-email' src='images/email35px.png'/>&nbsp&nbsp<img class='icon-whatsapp' src='images/whatsapp35px.png'/></div></div></h5>" :
                                                        plan == "Oficina" ? "<h5 class='plan btn-sm btn-success'>En Oficina</h5>" :
                                                        plan.includes("Guatex") ? "<h5 class='popup plan btn-sm' style='background-color: #f4cb38'>Guatex<span class='popuptext'>"+plan.split(":")[1]+"</span></h5>" :
                                                        plan.length < 3 ? "<h5 class='popup plan btn-sm' style='background-color: #ff8605'>Esperando<span class='popuptext'>"+plan+" Paquetes</span></h5>":
                                                        "<h5 class='popup plan btn-sm btn-primary' style='text-align:center'>En Ruta<span class='popuptext'>-"+plan+"-</span></h5>";
                                            table.row(index).data(arr[0]);
                                            table.order([4, "asc"]);
                                            table.draw(false);
                                        }
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

            $("#divFechaRuta").datepicker({
                showOtherMonths: true,
                selectOtherMonths: true,
                showAnim: "slideDown",
                minDate: 0,
                maxDate: "+1M"
            });

            $('#divFechaRuta').datepicker("setDate", tom);

            if (plan == "Oficina"){
                document.getElementById("btnOficina").style.backgroundColor = "#337ab7";
                document.getElementById("btnOficina").style.color = "white";
            }
            else if (plan.includes("Guatex")){
                document.getElementById("btnGuatex").style.backgroundColor = "#337ab7";
                document.getElementById("btnGuatex").style.color = "white";
                document.getElementById("divFechaRuta").style.display = "block";
                var fechaArr = plan.split(":")[1].split("/");
                $('#divFechaRuta').datepicker("setDate", new Date(Number(fechaArr[2]),Number(fechaArr[1])-1, Number(fechaArr[0])));
            }
            else if (plan.length > 0){
                if (plan.length >= 3) {
                    document.getElementById("btnRuta").style.backgroundColor = "#337ab7";
                    document.getElementById("btnRuta").style.color = "white";
                    document.getElementById("divFechaRuta").style.display = "block";
                    //document.getElementById("form_carga_fecha").value = row.plan;
                    var fechaArr = plan.split("/");
                    $('#divFechaRuta').datepicker("setDate", new Date(Number(fechaArr[2]),Number(fechaArr[1])-1, Number(fechaArr[0])));
                }
                else {
                    document.getElementById("btnEsperando").style.backgroundColor = "#337ab7";
                    document.getElementById("btnEsperando").style.color = "white";
                    document.getElementById("divEsperandoCantidad").style.display = "block";
                    document.getElementById("form_carga_esperando").value = plan;
                }
            }
        });

        loadInventario();
    });

    function toggleActivadito(boton){
        if (boton.style.color == "white"){
            if (boton.innerHTML == "En Ruta" || boton.innerHTML == "Guatex")
                document.getElementById("divFechaRuta").style.display = "none";
            else if (boton.innerHTML == "Esperando")
                document.getElementById("divEsperandoCantidad").style.display = "none";

            boton.style.backgroundColor = "#fff";
            boton.style.color = "#337ab7";
        }
        else{
            document.getElementById("divEsperandoCantidad").style.display = "none";
                document.getElementById("divFechaRuta").style.display = "none";
            if (boton.innerHTML == "En Ruta" || boton.innerHTML == "Guatex")
                document.getElementById("divFechaRuta").style.display = "block";
            else if (boton.innerHTML == "Esperando"){
                document.getElementById("divEsperandoCantidad").style.display = "block";
                document.getElementById("form_carga_esperando").focus();
            }
            document.getElementById("btnOficina").style.backgroundColor = "#fff";
            document.getElementById("btnOficina").style.color = "#337ab7";
            document.getElementById("btnRuta").style.backgroundColor = "#fff";
            document.getElementById("btnRuta").style.color = "#337ab7";
            document.getElementById("btnEsperando").style.backgroundColor = "#fff";
            document.getElementById("btnEsperando").style.color = "#337ab7";
            document.getElementById("btnGuatex").style.backgroundColor = "#fff";
            document.getElementById("btnGuatex").style.color = "#337ab7";
            boton.style.backgroundColor = "#337ab7";
            boton.style.color = "white";
        }
    }

    function getUserName2(id){
        var arreglo = ["Cliente", "CLIENTE", "cliente", "Anónimo", "ANÓNIMO", "anónimo", "Anonimo", "ANONIMO", "anonimo"];
        $.ajax({
            url: "db/DBgetUserNamePostUid.php",
            type: "POST",
            data: {
                uid: id
            },
            cache: false,
            success: function(name) {
                if (arreglo.indexOf(id) == -1 && name.replace(/\s/g,'').length == 0){
                    document.getElementById("spanIDCliente").style.display="inline";
                    setTimeout(function() {
                    $('#spanIDCliente').fadeOut('slow');
                    }, 3000);
                }
                else
                    document.getElementById("form_carga_uname").value=name;
            },
            error: function(){
                if (arreglo.indexOf(id) == -1 && name.replace(/\s/g,'').length == 0){
                    document.getElementById("spanIDCliente").style.display="inline";
                    setTimeout(function() {
                    $('#spanID').fadeOut('slow');
                    }, 3000);
                }
            }
        });
    }
</script>

<div class="container" style="padding-top: 4.5cm">
    <table id="inventario" class="display compact" width="100%" cellspacing="0">
        <thead>
            <tr>
                <th class="dt-head-center"><span style="color: transparent; -webkit-text-stroke-width: 2px; -webkit-text-stroke-color: gold"><i class="fa fa-star fa-2x"></i></span></th>
                <th class="dt-head-center"><h5 style="color:black">Fecha de Ingreso</h5></th>
                <th class="dt-head-center"><h5 style="color:black"># Tracking</h5></th>
                <th class="dt-head-center"><h5 style="color:black">ID Cliente</h5></th>
                <th class="dt-head-center"><h5 style="color:black">Nombre Cliente</h5></th>
                <th class="dt-head-center"><h5 style="color:black">Peso</h5></th>
                <th class="dt-head-center"><h5 style="color:black">Plan de Entrega</h5></th>
                <th></th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <th></th>
                <th class="dt-head-center"><input class="buscarIngreso" type="text" placeholder="Buscar"/></th>
                <th></th>
                <th></th>
                <th colspan="2" class="dt-head-right"></th>
                <th class="dt-head-center"><input class="buscarPlan" type="text" placeholder="Buscar"/></th>
                <th></th>
            </tr>
        </tfoot>
        <tbody>
        </tbody>
    </table>
</div>

<div class="container" align="center" style="background-color: white; position: fixed; left: 0; right: 0; bottom: 0; padding-bottom: 6px; z-index: 100; visibility: hidden;" id="divBotones">
    <div class='col-lg-12 col-md-12 col-sm-12 col-xs-12' style="padding-top: 6px;">
        <div class='col-lg-2 col-md-2 col-sm-2'></div>
        <div class='col-lg-8 col-md-8 col-sm-8 col-xs-12'>
            <button onclick="notificarSeleccionados()" class="btn-lg btn-primary" align="center" style="width:28%; text-align: center ;">Notificar</button>
            <button onclick="entregarSeleccionados()" class="btn-lg btn-success" align="center" style="width: 42%; text-align: center ;">Entregar Mercadería</button>
            <button onclick="planificarEntrega()" class="btn-lg btn-warning" align="center" style="width: 28%; text-align: center;">Plan de Entrega</button>
        </div>
    </div>
</div>

<script type="text/javascript">
    var isChrome = /Chrome/.test(navigator.userAgent) && /Google Inc/.test(navigator.vendor);

    function loadInventario(){
        var table = $('#inventario').DataTable();
        table.clear();
        document.getElementById("divBotones").style.visibility = "hidden";
        $.ajax({
            url: "db/DBgetInventario.php",
            cache: false,
            success: function(arr) {
                if (arr != "]"){
                    var rows = JSON.parse(arr);
                    for (var i = 0; i < rows.length; i++){
                        var f = rows[i][1].split(" ")[0].split("-");
                        var fechaIngreso = f[2] +"/" +f[1] +"/"+f[0];
                        var plansito = "";
                        switch (rows[i][6]){
                            case "":
                                plansito = "<h5 class='popup-notif sin-plan plan btn-sm btn-danger'>Sin Especificar<div class='popupicon'><div class='row'><label class='col-lg-12 col-md-12 col-sm-12 col-xs-12' style='text-align:center;'>Notificar</label><img class='icon-email' src='images/email35px.png'/>&nbsp&nbsp<img class='icon-whatsapp' src='images/whatsapp35px.png'/></div></div></h5>";
                            break;
                            case "whats":
                                plansito = "<div class='row'><div class='col-lg-4 col-md-4 col-sm-4 col-xs-4'></div><img title='Notificado por Whatsapp' class='col-lg-4 col-md-4 col-sm-4 col-xs-4' src='images/whatsapp20px.png'/></div><h5 class='popup-notif sin-plan plan btn-sm btn-danger'>Sin Especificar<div class='popupicon'><div class='row'><label class='col-lg-12 col-md-12 col-sm-12 col-xs-12' style='text-align:center;'>Notificar</label><img class='icon-email' src='images/email35px.png'/></div></div></h5>";
                            break;
                            case "email":
                                plansito = "<div class='row'><div class='col-lg-4 col-md-4 col-sm-4 col-xs-4'></div><img title='Notificado por Email' class='col-lg-4 col-md-4 col-sm-4 col-xs-4' src='images/email20px.png'/></div><h5 class='popup-notif sin-plan plan btn-sm btn-danger'>Sin Especificar<div class='popupicon'><div class='row'><label class='col-lg-12 col-md-12 col-sm-12 col-xs-12' style='text-align:center;'>Notificar</label><img class='icon-whatsapp' src='images/whatsapp35px.png'/></div></div></h5>";
                            break;
                            case "whatsmail":
                                plansito = "<div class='row'><div class='col-lg-1 col-md-1 col-sm-1 col-xs-1'></div><img title='Notificado por Email' class='col-lg-4 col-md-4 col-sm-4 col-xs-4' src='images/email20px.png'/><div class='col-lg-2 col-md-2 col-sm-2 col-xs-2'></div><img title='Notificado por Whatsapp' class='col-lg-4 col-md-4 col-sm-4 col-xs-4' src='images/whatsapp20px.png'/></div><h5 class='sin-plan plan btn-sm btn-danger'>Sin Especificar</h5>";
                            break;
                            case "@whats":
                                plansito = "<div class='row'><div class='col-lg-4 col-md-4 col-sm-4 col-xs-4'></div><img title='Notificado por Whatsapp' class='col-lg-4 col-md-4 col-sm-4 col-xs-4' src='images/whatsapp20px.png'/></div><h5 class='popup-notif sin-plan plan btn-sm' style='background-color: #eaeaea; color: #444'>Avisar<div class='popupicon'><div class='row'><label class='col-lg-12 col-md-12 col-sm-12 col-xs-12' style='text-align:center;'>Notificar</label><img class='icon-email' src='images/email35px.png'/></div></div></h5>";
                            break;
                            case "@email":
                                plansito = "<div class='row'><div class='col-lg-4 col-md-4 col-sm-4 col-xs-4'></div><img title='Notificado por Email' class='col-lg-4 col-md-4 col-sm-4 col-xs-4' src='images/email20px.png'/></div><h5 class='popup-notif sin-plan plan btn-sm' style='background-color: #eaeaea; color: #444'>Avisar<div class='popupicon'><div class='row'><label class='col-lg-12 col-md-12 col-sm-12 col-xs-12' style='text-align:center;'>Notificar</label><img class='icon-whatsapp' src='images/whatsapp35px.png'/></div></div></h5>";
                            break;
                            case "@whatsmail":
                                plansito = "<div class='row'><div class='col-lg-1 col-md-1 col-sm-1 col-xs-1'></div><img title='Notificado por Email' class='col-lg-4 col-md-4 col-sm-4 col-xs-4' src='images/email20px.png'/><div class='col-lg-2 col-md-2 col-sm-2 col-xs-2'></div><img title='Notificado por Whatsapp' class='col-lg-4 col-md-4 col-sm-4 col-xs-4' src='images/whatsapp20px.png'/></div><h5 class='popup-notif sin-plan plan btn-sm' style='background-color: #eaeaea; color: #444'>Avisar";
                            break;
                            case "Oficina":
                                plansito = "<h5 class='plan btn-sm btn-success'>En Oficina</h5>";
                            break;
                            default:
                                if (rows[i][6].includes("/")){
                                    if (rows[i][6].includes("Guatex"))
                                        plansito = "<h5 class='popup plan btn-sm' style='text-align:center; background-color: #f4cb38'>Guatex<span class='popuptext'>"+rows[i][6].split(":")[1]+"</span></h5>";
                                    else
                                        plansito = "<h5 class='popup plan btn-sm btn-primary' style='text-align:center'>En Ruta<span class='popuptext'>-"+rows[i][6]+"-</span></h5>";
                                }
                                else if (rows[i][6] < 1){
                                    plansito = "<h5 class='popup-notif sin-plan plan btn-sm' style='background-color: #eaeaea; color: #444'>Avisar<div class='popupicon'><div class='row'><label class='col-lg-12 col-md-12 col-sm-12 col-xs-12' style='text-align:center;'>Notificar</label><img class='icon-email' src='images/email35px.png'/>&nbsp&nbsp<img class='icon-whatsapp' src='images/whatsapp35px.png'/></div></div></h5>";
                                }
                                else plansito = "<h5 class='popup plan btn-sm' style='background-color: #ff8605'>Esperando<span class='popuptext'>"+rows[i][6]+" Paquetes</span></h5>";
                            break;
                        }
                        var celulares = rows[i][7];
                        var extras = rows[i][8];
                        var especial = celulares + extras > 0;
                        var trackingsito = rows[i][2];
                        if (trackingsito.length > 20)
                                trackingsito = trackingsito.substr(0, trackingsito.length/2) + "<br>" +
                                trackingsito.substr(trackingsito.length/2, trackingsito.length);
                        table.row.add([
                            `<h5 class='seleccionado' data-celulares=${celulares} data-cobro-extra=${extras} >${especial ? "<span title='Celulares: "+ celulares + ", Cobro Extra: Q"+ numberWithCommas(extras) +"' style='color: gold;'><i class='fa fa-star fa-2x fa-lg'></i><small style='display:none;'>Especial</small></span>" : ""}</h5>`,
                            "<h5 title='Registro de Carga #"+rows[i][0]+"' class='seleccionado'>"+fechaIngreso+"</h5>",
                            "<h5 class='seleccionado'>"+trackingsito+"</h5>",
                            "<h5 class='seleccionado'>"+rows[i][3]+"</h5>",
                            "<h5 class='seleccionado'>"+rows[i][4]+"</h5>",
                            "<h5 class='seleccionado'>"+rows[i][5]+"</h5>",
                            plansito,
                            "<img class='icon-update' src='images/edit.png'/>"
                        ]);
                    }
                    table.order([4, "asc"]);
                    table.draw(false);
                    table.columns.adjust().responsive.recalc();
                }
            },
            error: function(){
                bootbox.alert("No se pudo cargar el inventario, ocurrió un problema al intentar conectarse al servidor.");
            }
        });
    }

    var isMobile = (/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|ipad|iris|kindle|Android|Silk|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(navigator.userAgent)
    || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(navigator.userAgent.substr(0,4)));

    function notificarSeleccionados(){
        document.getElementById("divBotones").style.visibility = "hidden";
        var data = $("#inventario").DataTable().rows(".selected").data().toArray();

        var nombre = data[0][3];
        var continuar = true;
        for (var i = 1; i < data.length; i++){
            if (nombre.toUpperCase() != data[i][3].toUpperCase()){
                continuar = false;
                break;
            }
        }
        nombre = nombre.split(">")[1].split("<")[0]

        var arreglo = ["Cliente", "CLIENTE", "cliente", "Anónimo", "ANÓNIMO", "anónimo", "Anonimo", "ANONIMO", "anonimo"];

        if (!continuar){
            bootbox.dialog({
                closeButton: false,
                title: "¡Atención!",
                message: "La mercadería seleccionada pertenece a diferentes clientes, solo se puede notificar a un cliente a la vez.",
                buttons: {
                    confirm: {
                        label: 'Entendido',
                        className: "btn-primary",
                        callback: function(){
                            document.getElementById("divBotones").style.visibility = "visible";
                        }
                    }
                }
            });
            return;
        }

        bootbox.dialog({
            size: 'medium',
            closeButton: false,
            title: "¿Por cuál medio desea notificar al cliente?",
            message: "<div class='row'><div class='row'><div class='row'><div class='col-lg-1 col-md-1 col-sm-1 col-xs-1'></div><img class='col-lg-5 col-md-5 col-sm-5 col-xs-5' align='middle' style='cursor: pointer;' src='images/whatsapp128px.png' onclick='notificarViaWhatsApp("+(arreglo.indexOf(nombre)>-1)+")'></img><img class='col-lg-5 col-md-5 col-sm-5 col-xs-5' align='middle' style='cursor: pointer;' src='images/email128px.png' onclick='notificarViaEmail("+(arreglo.indexOf(nombre)>-1)+")'></img></div><div class='row'><div class='col-lg-1 col-md-1 col-sm-1 col-xs-1'></div><label class='col-lg-5 col-md-5 col-sm-5 col-xs-5' style='text-align: center; color: black; cursor: pointer;' onclick='notificarViaWhatsApp("+(arreglo.indexOf(nombre)>-1)+")'>Vía Whatsapp</label><label class='col-lg-5 col-md-5 col-sm-5 col-xs-5' style='text-align: center; color: black; cursor: pointer;' onclick='notificarViaEmail("+(arreglo.indexOf(nombre)>-1)+")'>Vía Correo Electrónico</label></div></div>",
            buttons: {
                confirm: {
                    label: 'Regresar',
                    className: "btn-default alinear-izquierda",
                    callback: function(){
                        document.getElementById("divBotones").style.visibility = "visible";
                    }
                }
            }
        });
    }

    var whatsWebWindow = null;

    function notificarViaWhatsApp(uidCliente){
        bootbox.hideAll();
        var data = $("#inventario").DataTable().rows(".selected").data().toArray();
        var numero = "";
        if (uidCliente){
            var nombres = [];
            for (let i = 0; i < data.length; i++){
                if (nombres.indexOf(data[i][4].split(">")[1].split("<")[0]) == -1) {
                    nombres.push(data[i][4].split(">")[1].split("<")[0]);
                }
            }

            var querysita = "SELECT celular, tarifa, CONCAT(nombre, ' ', apellido) AS usuario FROM cliente WHERE ";
            for (let i = 0; i < nombres.length; i++)
                querysita = querysita + "CONCAT(nombre, ' ', apellido) = '" + nombres[i] + "' OR ";
            querysita = querysita.substring(0, querysita.length-3);

            $.ajax({
                url: "db/DBexecQuery.php",
                type: "POST",
                data:{
                    query: querysita
                },
                cache: false,
                success: function(arr){
                    var rows = JSON.parse(arr);
                    var encontrados = rows.length;

                    var msg = "";
                    if (encontrados == 0){
                        msg = "<div class='row' style='background-color: #dadada'>"+
                            "<div class='col-lg-12 col-md-12 col-sm-12 col-xs-12'>"+
                                "<p style='color: black'>Por favor ingresa los siguientes datos del cliente para poder enviar la notificación.</p>"+
                                "<form novalidate>"+
                                    "<div class='control-group form-group col-lg-6 col-md-6 col-sm-6 col-xs-6'><div class='controls'><label style='color: #337ab7; text-align:center; width:100%'>Número de Celular:</label><input align='middle' style='text-align:center; width: 100%;' type='text' id='inputNotifWhatsNumero' placeholder='Número de Celular del Cliente' maxlength='8' onkeypress='return integersonly(this, event)'></div></div>"+
                                    "<div class='control-group form-group col-lg-6 col-md-6 col-sm-6 col-xs-6'><div class='controls'><label style='color: #337ab7; text-align:center; width:100%'>Nombre y Apellido:</label><input align='middle' style='text-align:center; width: 100%;' type='text' id='inputNotifWhatsCliente' placeholder='Nombre y Apellido del cliente'></div></div>"+
                                "</form>"+
                            "</div>"+
                        "</div>";
                    }
                    else if (encontrados == 1){
                        msg = "Los paquetes poseen un ID de Cliente auxiliar. Por medio de uno de los nombres de clientes que figuran en los registro de los paquetes, se encontró el siguiente número de celular en la base de datos:<br><div class='row'><div class='col-lg-4 col-md-4 col-sm-4 col-xs-4'></div><label class='col-lg-4 col-md-4 col-sm-4 col-xs-4' align='middle' style='font-size: 20px; background-color: #dadada; text-align: center; color: #349b25; border-radius: 7px'>"+rows[0]["celular"]+"</label></div><div class='row'><label class='col-lg-12 col-md-12 col-sm-12 col-xs-12' align='middle' style='text-align: center; color: black;'>¿Enviar notificación por whatsaap a este número?</label></div>";
                    }
                    else{
                        bootbox.alert("Se detectaron nombres de cliente diferentes entre los paquetes seleccionados, y estos están asociados a diferentes números de celular. Por favor cerciorate de seleccionar los paquetes de un solo cliente a la vez.");
                        return;
                    }
                    bootbox.dialog({
                        size: 'medium',
                        closeButton: false,
                        title: (encontrados == 0 ? "¡No se encontró un número de celular asociado!" : "Confirmar número de celular"),
                        message: msg,
                        buttons: {
                            regresar: {
                                label: 'Regresar',
                                className: "btn-default alinear-izquierda",
                                callback: function(){
                                    document.getElementById("divBotones").style.visibility = "visible";
                                }
                            },
                            ingresar: {
                                label: "Ingresar número manualmente",
                                className: (encontrados == 0 ? "gone" : "btn-primary alinear-izquierda"),
                                callback: function(){
                                    msg = "<div class='row' style='background-color: #dadada'>"+
                                            "<div class='col-lg-12 col-md-12 col-sm-12 col-xs-12'>"+
                                                "<p style='color: black'>Por favor ingresa los siguientes datos del cliente para poder enviar la notificación.</p>"+
                                                "<form novalidate>"+
                                                    "<div class='control-group form-group col-lg-6 col-md-6 col-sm-6 col-xs-6'><div class='controls'><label style='color: #337ab7; text-align:center; width:100%'>Número de celular:</label><input align='middle' style='text-align:center; width: 100%;' type='text' id='inputNotifWhatsNumero' placeholder='Número de celular del cliente' maxlength='8' onkeypress='return integersonly(this, event)'></div></div>"+
                                                    "<div class='control-group form-group col-lg-6 col-md-6 col-sm-6 col-xs-6'><div class='controls'><label style='color: #337ab7; text-align:center; width:100%'>Nombre y apellido:</label><input align='middle' style='text-align:center; width: 100%;' type='text' id='inputNotifWhatsCliente' placeholder='Nombre y apellido del cliente'></div></div>"+
                                                "</form>"+
                                            "</div>"+
                                        "</div>";
                                    bootbox.dialog({
                                        size: 'medium',
                                        closeButton: false,
                                        title: "Ingreso de número de celular",
                                        message: msg,
                                        buttons: {
                                            regresar: {
                                                label: 'Regresar',
                                                className: "btn-default alinear-izquierda",
                                                callback: function(){
                                                    document.getElementById("divBotones").style.visibility = "visible";
                                                }
                                            },
                                            confirm: {
                                                label: 'Listo, continuar',
                                                className: "btn-success alinear-derecha",
                                                callback: function(){
                                                    var urlmensaje = "";
                                                    var whatsNumber = document.getElementById("inputNotifWhatsNumero").value;
                                                    var nombre = document.getElementById("inputNotifWhatsCliente").value;
                                                    if (whatsNumber.length == 0 || nombre.length == 0){
                                                        bootbox.alert("Por favor llena correctamente los campos.");
                                                        return false;
                                                    }
                                                    whatsNumber = "502"+whatsNumber;

                                                    urlmensaje = "Buen día " + nombre + ", de parte de Chispudito Express te informamos que los siguientes paquetes ya están disponibles en nuestra bodega de Guatemala:%0A%0A";

                                                    var strPaquetes = "", pesoTotal = 0;
                                                    for (var i = 0; i < data.length; i++){
                                                        strPaquetes += "*  Tracking: " + data[i][2].replace("<br>", "").split(">")[1].split("<")[0] + "%0A    Peso: " + data[i][5].split(">")[1].split("<")[0] + " lb.%0A";
                                                        pesoTotal += Number(data[i][5].split(">")[1].split("<")[0]);
                                                    }
                                                    var totales = "%0A - Paquetes: "+data.length+"%0A - Peso total: "+pesoTotal+" lb.%0A - Total a pagar: Q"+(pesoTotal*60)+" (tarifa aplica en *efectivo*)%0A";
                                                    urlmensaje += strPaquetes + totales + "%0APor favor confírmanos si te los enviamos a ruta o pasas a recogerlos a oficina (recuerda que si solicitas servicio ruta, debes agregar al total el costo del envío).%0AGracias por preferirnos. Chispudito Express";
                                                    urlmensaje = urlmensaje.replace(" ", "%20");

                                                    if (whatsWebWindow != null && !whatsWebWindow.closed){
                                                        whatsWebWindow.location.replace("https://web.whatsapp.com/send?phone="+whatsNumber+"&text="+urlmensaje);
                                                        whatsWebWindow.focus();
                                                    }
                                                    else
                                                        whatsWebWindow = window.open("https://web.whatsapp.com/send?phone="+whatsNumber+"&text="+urlmensaje);

                                                    bootbox.confirm({
                                                        size: "small",
                                                        title: "La página de Whatsapp Web ha sido cargada",
                                                        message: "¿Ha concluido la notificación del cliente?",
                                                        buttons: {
                                                          cancel: {
                                                            label: "No",
                                                            className: "btn btn-md btn-warning alinear-izquierda"
                                                          },
                                                          confirm: {
                                                              label: "Si",
                                                              className: "btn btn-md btn-success alinear-derecha"
                                                          }
                                                        },
                                                        callback: function(res){
                                                            if (res){
                                                                var t = $("#inventario").DataTable();
                                                                t.rows(".selected").nodes().to$().removeClass("selected");
                                                                t.draw(false);
                                                            }
                                                            else{
                                                                bootbox.confirm({
                                                                    message: "En caso de que whatsapp no haya reconocido el número del cliente, recuerda que puedes notificarle por correo electrónico",
                                                                    buttons: {
                                                                      cancel: {
                                                                        label: "OK, regresar",
                                                                        className: "btn btn-md btn-default alinear-izquierda"
                                                                      },
                                                                      confirm: {
                                                                          label: "Si, enviar por correo electrónico",
                                                                          className: "btn btn-md btn-success alinear-derecha"
                                                                      }
                                                                    },
                                                                    callback: function(res){
                                                                        if (res){
                                                                            notificarViaEmail(uidCliente);
                                                                        }
                                                                        else{
                                                                            document.getElementById("divBotones").style.visibility = "visible";
                                                                        }
                                                                    }
                                                                });
                                                            }
                                                        }
                                                    });
                                                }
                                            }
                                        }
                                    });
                                }
                            },
                            confirm: {
                                label: (encontrados == 0 ? 'Listo, continuar' : "Si, continuar"),
                                className: (encontrados  > 1 ? "gone" : "btn-success alinear-derecha"),
                                callback: function(){
                                    var urlmensaje = "", whatsNumber = "", tarifa = 60;
                                    if (encontrados == 0){
                                        whatsNumber = document.getElementById("inputNotifWhatsNumero").value;
                                        var nombre = document.getElementById("inputNotifWhatsCliente").value;
                                        if (whatsNumber.length == 0 || nombre.length == 0){
                                            bootbox.alert("Por favor llena correctamente los campos.");
                                            return false;
                                        }
                                        whatsNumber = "502"+whatsNumber;
                                        urlmensaje = "Buen día " + nombre + ", de parte de Chispudito Express te informamos que los siguientes paquetes ya están disponibles en nuestra bodega de Guatemala:%0A%0A";
                                    }
                                    else{
                                        whatsNumber = "502"+rows[0]["celular"];
                                        urlmensaje = "Buen día " + rows[0]["usuario"] + ", de parte de Chispudito Express te informamos que los siguientes paquetes ya están disponibles en nuestra bodega de Guatemala:%0A%0A";
                                        tarifa = Number(rows[0]["tarifa"]);
                                    }

                                    var strPaquetes = "", pesoTotal = 0;
                                    for (var i = 0; i < data.length; i++){
                                        strPaquetes += "*  Tracking: " + data[i][2].replace("<br>", "").split(">")[1].split("<")[0] + "%0A    Peso: " + data[i][5].split(">")[1].split("<")[0] + " lb.%0A";
                                        pesoTotal += Number(data[i][5].split(">")[1].split("<")[0]);
                                    }
                                    var totales = "%0A - Paquetes: "+data.length+"%0A - Peso total: "+pesoTotal+" lb.%0A - Total a pagar: Q"+(pesoTotal*tarifa)+" (tarifa aplica en *efectivo*)%0A";

                                    urlmensaje += strPaquetes + totales + "%0APor favor confírmanos si te los enviamos a ruta o pasas a recogerlos a oficina (recuerda que si solicitas servicio ruta, debes agregar al total el costo del envío).%0AGracias por preferirnos. Chispudito Express";
                                    urlmensaje = urlmensaje.replace(" ", "%20").replace("Ã¡", "á").replace("Ã©", "é").replace("Ã³", "ó").replace("Ãº", "ú").replace("Ã¼", "ü").replace("Ã±", "ñ").replace("Ã", "í");

                                    if (whatsWebWindow != null && !whatsWebWindow.closed){
                                        whatsWebWindow.location.replace("https://web.whatsapp.com/send?phone="+whatsNumber+"&text="+urlmensaje);
                                        whatsWebWindow.focus();
                                    }
                                    else
                                        whatsWebWindow = window.open("https://web.whatsapp.com/send?phone="+whatsNumber+"&text="+urlmensaje);

                                    bootbox.confirm({
                                        size: "small",
                                        title: "La página de Whatsapp Web ha sido cargada",
                                        message: "¿Ha concluido la notificación del cliente?",
                                        buttons: {
                                          cancel: {
                                            label: "No",
                                            className: "btn btn-md btn-warning alinear-izquierda"
                                          },
                                          confirm: {
                                              label: "Si",
                                              className: "btn btn-md btn-success alinear-derecha"
                                          }
                                        },
                                        callback: function(res){
                                            if (res){
                                                var t = $("#inventario").DataTable();
                                                t.rows(".selected").nodes().to$().removeClass("selected");
                                                t.draw(false);
                                            }
                                            else{
                                                bootbox.confirm({
                                                    message: "En caso de que whatsapp no haya reconocido el número del cliente, recuerda que puedes notificarle por correo electrónico",
                                                    buttons: {
                                                      cancel: {
                                                        label: "OK, regresar",
                                                        className: "btn btn-md btn-default alinear-izquierda"
                                                      },
                                                      confirm: {
                                                          label: "Si, enviar por correo electrónico",
                                                          className: "btn btn-md btn-success alinear-derecha"
                                                      }
                                                    },
                                                    callback: function(res){
                                                        if (res){
                                                            notificarViaEmail(uidCliente);
                                                        }
                                                        else{
                                                            document.getElementById("divBotones").style.visibility = "visible";
                                                        }
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
                error: function() {
                    bootbox.alert("Ocurrió un problema al intentar conectarse al servidor y no se pudo obtener el número de celular del cliente. Intentalo nuevamente luego.");
                }

            });
        }
        else{
            var uid = data[0][3].split(">")[1].split("<")[0];
            var nombreCliente = data[0][4].split(">")[1].split("<")[0];

            var querysita = "SELECT celular, tarifa FROM cliente WHERE cid = '" + uid + "'";

            $.ajax({
                url: "db/DBexecQuery.php",
                type: "POST",
                data:{
                    query: querysita
                },
                cache: false,
                success: function(res){

                    var whatsNumber = "502"+JSON.parse(res)[0].celular;
                    var tarifa = JSON.parse(res)[0].tarifa;
                    urlmensaje = "Buen día " + nombreCliente  + ", de parte de Chispudito Express te informamos que los siguientes paquetes ya están disponibles en nuestra bodega de Guatemala:%0A%0A";

                    var strPaquetes = "", pesoTotal = 0;
                    for (var i = 0; i < data.length; i++){
                        strPaquetes += "*  Tracking: " + data[i][2].replace("<br>", "").split(">")[1].split("<")[0] + "%0A    Peso: " + data[i][5].split(">")[1].split("<")[0] + " lb.%0A";
                        pesoTotal += Number(data[i][5].split(">")[1].split("<")[0]);
                    }
                    var totales = "%0A - Paquetes: "+data.length+"%0A - Peso total: "+pesoTotal+" lb.%0A - Total a pagar: Q"+(pesoTotal*tarifa)+" (tarifa aplica en *efectivo*)%0A";

                    urlmensaje += strPaquetes + totales + "%0APor favor confírmanos si te los enviamos a ruta o pasas a recogerlos a oficina (recuerda que si solicitas servicio ruta, debes agregar al total el costo del envío).%0AGracias por preferirnos. Chispudito Express";
                    urlmensaje = urlmensaje.replace(" ", "%20").replace("Ã¡", "á").replace("Ã©", "é").replace("Ã³", "ó").replace("Ãº", "ú").replace("Ã¼", "ü").replace("Ã±", "ñ").replace("Ã", "í");

                    if (whatsWebWindow != null && !whatsWebWindow.closed){
                        whatsWebWindow.location.replace("https://web.whatsapp.com/send?phone="+whatsNumber+"&text="+urlmensaje);
                        whatsWebWindow.focus();
                    }
                    else
                        whatsWebWindow = window.open("https://web.whatsapp.com/send?phone="+whatsNumber+"&text="+urlmensaje);

                    bootbox.confirm({
                        size: "small",
                        title: "La página de Whatsapp Web ha sido cargada",
                        message: "¿Ha concluido la notificación del cliente?",
                        buttons: {
                          cancel: {
                            label: "No",
                            className: "btn btn-md btn-warning alinear-izquierda"
                          },
                          confirm: {
                              label: "Si",
                              className: "btn btn-md btn-success alinear-derecha"
                          }
                        },
                        callback: function(res){
                            if (res){
                                var t = $("#inventario").DataTable();
                                t.rows(".selected").nodes().to$().removeClass("selected");
                                t.draw(false);
                            }
                            else{
                                bootbox.confirm({
                                    message: "En caso de que whatsapp no haya reconocido el número del cliente, recuerda que puedes notificarle por correo electrónico",
                                    buttons: {
                                      cancel: {
                                        label: "OK, regresar",
                                        className: "btn btn-md btn-default alinear-izquierda"
                                      },
                                      confirm: {
                                          label: "Si, enviar por correo electrónico",
                                          className: "btn btn-md btn-success alinear-derecha"
                                      }
                                    },
                                    callback: function(res){
                                        if (res){
                                            notificarViaEmail(uidCliente);
                                        }
                                        else{
                                            document.getElementById("divBotones").style.visibility = "visible";
                                        }
                                    }
                                });
                            }
                        }
                    });
                },
                error: function() {
                    bootbox.alert("Ocurrió un problema al intentar conectarse al servidor y no se pudo obtener el número de celular del cliente. Intentalo nuevamente luego.");
                }
            });

        }
    }

    function notificarViaEmail(uidCliente){

        bootbox.hideAll();
        var data = $("#inventario").DataTable().rows(".selected").data().toArray();
        var numero = "";
        if (uidCliente){
            var nombres = new Array();
            for (var i = 0; i < data.length; i++){
                if (nombres.indexOf(data[i][4].split(">")[1].split("<")[0]) == -1) {
                    nombres.push(data[i][4].split(">")[1].split("<")[0]);
                }
            }

            var querysita = "SELECT email, tarifa, CONCAT(nombre, ' ', apellido) AS usuario FROM cliente WHERE ";
            for (var i = 0; i < nombres.length; i++)
                querysita = querysita + "CONCAT(nombre, ' ', apellido) = '" + nombres[i] + "' OR ";
            querysita = querysita.substring(0, querysita.length-3);

            $.ajax({
                url: "db/DBexecQuery.php",
                type: "POST",
                data:{
                    query: querysita
                },
                cache: false,
                success: function(arr){
                    var rows = JSON.parse(arr);
                    var encontrados = rows.length;

                    var msg = "";
                    if (encontrados == 0){
                        msg = "<div class='row' style='background-color: #dadada'>"+
                            "<div class='col-lg-12 col-md-12 col-sm-12 col-xs-12'>"+
                            "<p style='color: black'>Por favor ingresa los siguientes datos del cliente para poder enviar la notificación.</p>"+
                            "<form novalidate>"+
                            "<div class='control-group form-group col-lg-6 col-md-6 col-sm-6 col-xs-6'><div class='controls'><label style='color: #337ab7; text-align:center; width:100%'>Correo electrónico:</label><input align='middle' style='text-align:center; width: 100%;' type='email' id='inputNotifEmailCorreo' placeholder='Email del cliente'></div></div>"+
                            "<div class='control-group form-group col-lg-6 col-md-6 col-sm-6 col-xs-6'><div class='controls'><label style='color: #337ab7; text-align:center; width:100%'>Nombre y apellido:</label><input align='middle' style='text-align:center; width: 100%;' type='text' id='inputNotifEmailCliente' placeholder='Nombre y apellido del cliente'></div></div>"+
                            "</form>"+
                            "</div>"+
                            "</div>";
                    }
                    else if (encontrados == 1){
                        msg = "Los paquetes poseen un ID de Cliente auxiliar. Por medio de uno de los nombres de clientes que figuran en los registro de los paquetes, se encontró el siguiente correo electrónico en la base de datos:<br><br><div class='row'><div class='col-lg-2 col-md- col-sm-2 col-xs-2'></div><label class='col-lg-8 col-md-8 col-sm-8 col-xs-8' align='middle' style='font-size: 20px; background-color: #178ac4; text-align: center; color: white; border-radius: 5px'>"+rows[0]["email"]+"</label></div><br><div class='row'><label class='col-lg-12 col-md-12 col-sm-12 col-xs-12' align='middle' style='text-align: center; color: black;'>¿Enviar notificación al cliente usando este correo electrónico?</label></div>";
                    }
                    else{
                        bootbox.alert("Se detectaron nombres de cliente diferentes entre los paquetes seleccionados, y estos están asociados a diferentes correos electrónicos. Por favor cerciorate de seleccionar los paquetes de un solo cliente a la vez.");
                        return;
                    }
                    bootbox.dialog({
                        size: 'medium',
                        closeButton: false,
                        title: (encontrados == 0 ? "¡No se encontró un correo electrónico asociado!" : "Confirmar correo electrónico"),
                        message: msg,
                        buttons: {
                            regresar: {
                                label: 'Regresar',
                                className: "btn-default alinear-izquierda",
                                callback: function(){
                                    document.getElementById("divBotones").style.visibility = "visible";
                                }
                            },

                            ingresar: {
                                label: "Ingresar email manualmente",
                                className: (encontrados == 0 ? "gone" : "btn-primary alinear-izquierda"),
                                callback: function(){
                                    msg = "<div class='row' style='background-color: #dadada'>"+
                                        "<div class='col-lg-12 col-md-12 col-sm-12 col-xs-12'>"+
                                        "<p style='color: black'>Por favor ingresa los siguientes datos del cliente para poder enviar la notificación.</p>"+
                                        "<form novalidate>"+
                                        "<div class='control-group form-group col-lg-6 col-md-6 col-sm-6 col-xs-6'><div class='controls'><label style='color: #337ab7; text-align:center; width:100%'>Correo electrónico:</label><input align='middle' style='text-align:center; width: 100%;' type='email' id='inputNotifEmailCorreo' placeholder='Email del cliente'></div></div>"+
                                        "<div class='control-group form-group col-lg-6 col-md-6 col-sm-6 col-xs-6'><div class='controls'><label style='color: #337ab7; text-align:center; width:100%'>Nombre y apellido:</label><input align='middle' style='text-align:center; width: 100%;' type='text' id='inputNotifEmailCliente' placeholder='Nombre y apellido del cliente'></div></div>"+
                                        "</form>"+
                                        "</div>"+
                                        "</div>";
                                    bootbox.dialog({
                                        size: 'medium',
                                        closeButton: false,
                                        title: "Ingreso de correo electrónico",
                                        message: msg,
                                        buttons: {
                                            regresar: {
                                                label: 'Regresar',
                                                className: "btn-default alinear-izquierda",
                                                callback: function(){
                                                    document.getElementById("divBotones").style.visibility = "visible";
                                                }
                                            },
                                            confirm: {
                                                label: 'Listo, continuar',
                                                className: "btn-success alinear-derecha",
                                                callback: function(){
                                                    var urlmensaje = "";
                                                    var email = document.getElementById("inputNotifEmailCorreo").value;
                                                    var cliente = document.getElementById("inputNotifEmailCliente").value;
                                                    if (email.length == 0 || cliente.length == 0){
                                                        bootbox.alert("Por favor llena correctamente los campos.");
                                                        return false;
                                                    }

                                                    urlmensaje = "Buen día " + cliente + ".<br><br><strong>Chispudito Express</strong> te informa que los siguientes paquetes ya están disponibles en nuestra bodega de Guatemala:<br><br>";

                                                    var strPaquetes = "", pesoTotal = 0;
                                                    for (var i = 0; i < data.length; i++){
                                                        strPaquetes += "*  Tracking: " + data[i][2].replace("<br>", "").split(">")[1].split("<")[0] + " - Peso: " + data[i][5].split(">")[1].split("<")[0] + " lb.<br>";
                                                        pesoTotal += Number(data[i][5].split(">")[1].split("<")[0]);
                                                    }
                                                    var totales = "<br> - Paquetes: "+data.length+"<br> - Peso total: "+pesoTotal+" lb.<br> - Total a pagar: Q"+(pesoTotal*60)+" (tarifa aplica en <b>efectivo</b>)<br>";
                                                    urlmensaje += strPaquetes + totales + "<br>Por favor confírmanos si te los enviamos a ruta o pasas a recogerlos a oficina (recuerda que si solicitas servicio ruta, debes agregar al total el costo del envío).<br><br>Gracias por preferirnos. Chispudito Express";

                                                    $.ajax({
                                                        url: "PHPMailer/notificarViaEmail.php",
                                                        type: "POST",
                                                        data: {
                                                            email: email,
                                                            cliente: cliente,
                                                            mensaje: urlmensaje
                                                        },
                                                        cache: false,
                                                        success: function(res){
                                                            if (res == "Enviado"){
                                                                bootbox.alert("La notificación por correo electrónico ha sido enviada exitosamente.")
                                                                var t = $("#inventario").DataTable();
                                                                t.rows(".selected").nodes().to$().removeClass("selected");
                                                                t.draw(false);
                                                            }
                                                            else{
                                                                bootbox.alert("Hubo un problema en el servidor de envío de correo electrónico. Se obtuvo el siguiente mensaje: <br><br> \"" + res + "\"");
                                                            }
                                                        },
                                                        error: function(){
                                                            alert("Ocurrió un problema al intentar enviar el correo electrónico. Intenta nuevamente.");
                                                        }
                                                    });
                                                }
                                            }
                                        }
                                    });
                                }
                            },
                            confirm: {
                                label: (encontrados === 0 ? 'Listo, enviar' : "Si, continuar"),
                                className: (encontrados  > 1 ? "gone" : "btn-success alinear-derecha"),
                                callback: function(){
                                    let tarifa = 60;
                                    if (encontrados === 0){
                                        var email = document.getElementById("inputNotifEmailCorreo").value;
                                        var cliente = document.getElementById("inputNotifEmailCliente").value;
                                        if (email.length == 0 || cliente.length == 0){
                                            bootbox.alert("Por favor llena correctamente los campos.");
                                            return false;
                                        }
                                    }
                                    else{
                                        var email = rows[0]["email"];
                                        var cliente = rows[0]["usuario"];
                                        tarifa = Number(rows[0]["tarifa"]);
                                    }

                                    var urlmensaje = "";
                                    urlmensaje = "Buen día " + cliente + ".<br><br><strong>Chispudito Express</strong> te informa que los siguientes paquetes ya están disponibles en nuestra bodega de Guatemala:<br><br>";

                                    var strPaquetes = "", pesoTotal = 0;
                                    for (var i = 0; i < data.length; i++){
                                        strPaquetes += "*  Tracking: " + data[i][2].replace("<br>", "").split(">")[1].split("<")[0] + " - Peso: " + data[i][5].split(">")[1].split("<")[0] + " lb.<br>";
                                        pesoTotal += Number(data[i][5].split(">")[1].split("<")[0]);
                                    }
                                    var totales = "<br> - Paquetes: "+data.length+"<br> - Peso total: "+pesoTotal+" lb.<br> - Total a pagar: Q"+(pesoTotal*tarifa)+" (tarifa aplica en <b>efectivo</b>)<br>";

                                    urlmensaje += strPaquetes + totales + "<br>Por favor confírmanos si te los enviamos a ruta o pasas a recogerlos a oficina (recuerda que si solicitas servicio ruta, debes agregar al total el costo del envío).<br><br>Gracias por preferirnos. Chispudito Express";

                                    $.ajax({
                                        url: "PHPMailer/notificarViaEmail.php",
                                        type: "POST",
                                        data: {
                                            email: email,
                                            cliente: cliente,
                                            mensaje: urlmensaje
                                        },
                                        cache: false,
                                        success: function(res){
                                            if (res == "Enviado"){
                                                bootbox.alert("La notificación por correo electrónico ha sido enviada exitosamente.")
                                                var t = $("#inventario").DataTable();
                                                t.rows(".selected").nodes().to$().removeClass("selected");
                                                t.draw(false);
                                            }
                                            else{
                                                bootbox.alert("Hubo un problema en el servidor de envío de correo electrónico. Se obtuvo el siguiente mensaje: <br><br> \"" + res + "\"");
                                            }
                                        },
                                        error: function(){
                                            alert("Ocurrió un problema al intentar enviar el correo electrónico. Intenta nuevamente.");
                                        }
                                    });
                                }
                            }
                        }
                    });
                },
                error: function() {
                    bootbox.alert("Ocurrió un problema al intentar conectarse al servidor y no se pudo obtener el email del cliente. Intentalo nuevamente luego.");
                }

            });
        }
        else{
            var uid = data[0][3].split(">")[1].split("<")[0];
            var cliente = data[0][4].split(">")[1].split("<")[0];

            var querysita = "SELECT email, tarifa FROM cliente WHERE cid = '" + uid + "'";

            $.ajax({
                url: "db/DBexecQuery.php",
                type: "POST",
                data:{
                    query: querysita
                },
                cache: false,
                success: function(res){

                    var tarifa = JSON.parse(res)[0].tarifa;
                    var urlmensaje = "";
                    urlmensaje = "Buen día " + cliente + ".<br><br><strong>Chispudito Express</strong> te informa que los siguientes paquetes ya están disponibles en nuestra bodega de Guatemala:<br><br>";

                    var strPaquetes = "", pesoTotal = 0;
                    for (var i = 0; i < data.length; i++){
                        strPaquetes += "*  Tracking: " + data[i][2].replace("<br>", "").split(">")[1].split("<")[0] + " - Peso: " + data[i][5].split(">")[1].split("<")[0] + " lb.<br>";
                        pesoTotal += Number(data[i][5].split(">")[1].split("<")[0]);
                    }
                    var totales = "<br> - Paquetes: "+data.length+"<br> - Peso total: "+pesoTotal+" lb.<br> - Total a pagar: Q"+(pesoTotal*tarifa)+" (tarifa aplica en <b>efectivo</b>)<br>";

                    urlmensaje += strPaquetes + totales + "<br>Por favor confírmanos si te los enviamos a ruta o pasas a recogerlos a oficina (recuerda que si solicitas servicio ruta, debes agregar al total el costo del envío).<br><br>Gracias por preferirnos. Chispudito Express";

                    $.ajax({
                        url: "PHPMailer/notificarViaEmail.php",
                        type: "POST",
                        data: {
                            email: JSON.parse(res)[0].email,
                            cliente: cliente,
                            mensaje: urlmensaje
                        },
                        cache: false,
                        success: function(res){
                            if (res == "Enviado"){
                                bootbox.alert("La notificación por correo electrónico ha sido enviada exitosamente.")
                                var t = $("#inventario").DataTable();
                                t.rows(".selected").nodes().to$().removeClass("selected");
                                t.draw(false);
                            }
                            else{
                                bootbox.alert("Hubo un problema en el servidor de envío de correo electrónico. Se obtuvo el siguiente mensaje: <br><br> \"" + res + "\"");
                            }
                        },
                        error: function(){
                            alert("Ocurrió un problema al intentar enviar el correo electrónico. Intenta nuevamente.");
                        }
                    });
                },
                error: function() {
                    bootbox.alert("Ocurrió un problema al intentar conectarse al servidor y no se pudo obtener el correo electrónico del cliente. Intenta nuevamente.");
                }
            });
        }
    }


    function planificarEntrega(){
        document.getElementById("divBotones").style.visibility = "hidden";
        var data = $("#inventario").DataTable().rows(".selected").data().toArray();

        var nombre = data[0][3].toUpperCase();
        var continuar = true;
        for (var i = 1; i < data.length; i++){
            if (nombre != data[i][3].toUpperCase()){
                continuar = false;
                break;
            }
        }

        if (!continuar){
            bootbox.confirm({
                title: "¡Atención!",
                message: "Parece que la mercadería seleccionada pertenece a diferentes clientes, ¿desea continuar?",
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
                    if (res)
                        planEntregaVarios(data, "failErrorFalso");
                    else document.getElementById("divBotones").style.visibility = "visible";
                }
            });
            return;
        }
        planEntregaVarios(data, data[0][4].split(">")[1].split("<")[0]);
    }

    function planEntregaVarios(arr, nombre){
        var titulo = "Plan de Entrega para " + arr.length + " paquetes de " + nombre;
        var checkLabel = "Aplicar a todos los paquetes de " + nombre;
        if (nombre == "failErrorFalso"){
            titulo = "Plan de Entrega para " + arr.length + " paquetes";
            checkLabel = "Aplicar a todos los paquetes de cada cliente";
        }

        var uids = "(";
        for (var i = 0; i < arr.length; i++)
            uids = uids + (i == 0 ? "'":", '")+arr[i][3].split(">")[1].split("<")[0]+"'";
        uids = uids+")";

        var anonimo = false;
        var arreglo = ["Cliente", "CLIENTE", "cliente", "Anónimo", "ANÓNIMO", "anónimo", "Anonimo", "ANONIMO", "anonimo"];
        for (var j = 0; j < arreglo.length; j++){
            if (uids.includes(arreglo[j])){
                anonimo = true;
                break;
            }
        }

        bootbox.dialog({
            size: (isMobile ? "small" : "medium"),
            closeButton: false,
            title: titulo,
            message:"<div class='row' style='background-color: #dadada'>"+
                "<div class='col-lg-12 col-md-12 col-sm-12 col-xs-12'>"+
                    "<form novalidate>"+
                        "<br><br>"+
                        "<div class='control-group form-group col-lg-6 col-md-6 col-sm-6 col-xs-12'>"+
                            "<label style='color: #337ab7; width:100%; text-align: center'>Plan de Entrega</label>"+
                                "<button onclick='toggleActivadito(this)' id='btnOficina' style='width:50%; color:#337ab7' type='button' class='btn btn-default'>Oficina</button>"+
                                "<button onclick='toggleActivadito(this)' id='btnRuta' style='width:50%; color:#337ab7' type='button' class='btn btn-default'>En Ruta</button>"+
                                "<button onclick='toggleActivadito(this)' id='btnGuatex' style='width:50%; color:#337ab7' type='button' class='btn btn-default'>Guatex</button>"+
                                "<button onclick='toggleActivadito(this)' id='btnEsperando' style='width:50%; color:#337ab7' type='button' class='btn btn-default'>Esperando</button>"+
                            //"</div>"+
                        "</div>"+
                        "<div class='col-lg-6 col-md-6 col-sm-6 col-xs-12'>"+
                            "<div id='divFechaRuta' style='display:none'>"+
                                "<label style='color: #696969; width:100%; text-align: center'>Fecha de Ruta</label>"+
                                //"<input type='text' id='form_carga_fecha' value='"+tomor+"' style='display:none' class='form-control'/>"+
                            "<br></div>"+
                            "<div id='divEsperandoCantidad' style='display:none'>"+
                                "<label style='color: #696969; width:100%; text-align: center'>Cantidad de Paquetes Faltantes</label>"+
                                "<input placeholder='Paquetes Faltantes' onkeyup='this.value=this.value.replace(/^0+/, \"\");' onkeypress='return integersonly(this, event);' type='text' maxlength='2' style='text-align:center;'  class='form-control' id='form_carga_esperando'/>"+
                            "</div>"+
                            "<label style='"+(anonimo? "display:none;":"")+" color: #696969; font-size:12px; padding-left:20%; padding-right: 20%; width:100%; text-align: center'>"+checkLabel+"</label><input type='checkbox' "+(anonimo? "style='display:none;'":"")+" class='form-control' id='form_carga_check_esperando'/><br>"+
                        "</div>"+
                        "<br>"+
                        "</div>"+
                    "</form>"+
                "</div>"+
            "</div>",
            buttons: {
                cancel: {
                    label: "Cancelar Plan de Entrega",
                    className: "btn btn-md btn-danger alinear-izquierda",
                    callback: function(){
                        document.getElementById("divBotones").style.visibility = "visible";
                    }
                },
                confirm: {
                    label: "<div id='spanLlenarCamposCarga' style='display:none'><span class='dialog-text'>Ingrese correctamente el campo solicitado.</span></div>Asignar Plan de Entrega",
                    className: "btn btn-md btn-success alinear-derecha",
                    callback: function() {
                        var esp = document.getElementById("form_carga_esperando").value;
                        var plan = "";
                        if (document.getElementById("btnOficina").style.color == "white")
                            plan = "Oficina";
                        else if (document.getElementById("btnGuatex").style.color == "white"){
                            var f = $("#divFechaRuta").datepicker("getDate");
                            plan = "Guatex:"+(f.getDate() < 10 ? "0" : "") + f.getDate() + "/" + (f.getMonth()+1 < 10 ? "0" : "") + (f.getMonth()+1) + "/" + f.getFullYear() ;
                        }
                        else if (document.getElementById("btnEsperando").style.color == "white"){
                            if (esp.replace(/\s/g,'').length === 0 || esp < 1){
                                document.getElementById("spanLlenarCamposCarga").style.display="inline";
                                setTimeout(function() {$('#spanLlenarCamposCarga').fadeOut('slow');}, 3000);
                                return false;
                            }
                            plan = document.getElementById("form_carga_esperando").value;
                        }
                        else if (document.getElementById("btnRuta").style.color === "white"){
                            var f = $("#divFechaRuta").datepicker("getDate");
                            plan = (f.getDate() < 10 ? "0" : "") + f.getDate() + "/" + (f.getMonth()+1 < 10 ? "0" : "") + (f.getMonth()+1) + "/" + f.getFullYear() ;
                        }

                        var trackStr = "(";
                        for (var i = 0; i < arr.length; i++)
                            trackStr = trackStr + (i === 0 ? "'":", '")+arr[i][2].replace("<br>", "").split(">")[1].split("<")[0]+"'";
                        trackStr = trackStr+")";

                        var wher = "tracking IN "+trackStr;
                        var todos = false;
                        if (document.getElementById("form_carga_check_esperando").checked){
                            todos = true;
                            wher = "estado IS NULL AND uid IN " + uids;
                        }

                        $.ajax({
                            url: "db/DBsetPaquete.php",
                            type: "POST",
                            data: {
                                set: "plan = '"+plan+"'",
                                where: wher
                            },
                            cache: false,
                            success: function(res){
                                if (res.includes("ERROR")){
                                    bootbox.alert("Ocurrió un error al consultar la base de datos. Se recibió el siguiente mensaje: <i><br>" + res + "</i>");
                                }
                                else if (Number(res) < 1){
                                    bootbox.alert("No se pudo efectuar el cambio en la base de datos, intente nuevamente");
                                }
                                else{
                                    bootbox.hideAll();
                                    var t = $("#inventario").DataTable();
                                    if (todos){
                                        if (isChrome)
                                            loadInventario();
                                        else{
                                            t.rows().every( function(rowIdx, tableLoop, rowLoop) {
                                                var dataRow = String(this.data()).split(",");
                                                if (uids.toUpperCase().includes(dataRow[3].split(">")[1].split("<")[0].toUpperCase())) {
                                                    dataRow[6] = plan == "" ? "<h5 class='popup-notif sin-plan plan btn-sm btn-danger'>Sin Especificar<div class='popupicon'><div class='row'><label class='col-lg-12 col-md-12 col-sm-12 col-xs-12' style='text-align:center;'>Notificar</label><img class='icon-email' src='images/email35px.png'/>&nbsp&nbsp<img class='icon-whatsapp' src='images/whatsapp35px.png'/></div></div></h5>" :
                                                    plan == "Oficina" ? "<h5 class='plan btn-sm btn-success'>En Oficina</h5>" :
                                                    plan.includes("Guatex") ? "<h5 class='popup plan btn-sm' style='text-align:center; background-color: #f4cb38'>Guatex<span class='popuptext'>"+plan.split(":")[1]+"</span></h5>" :
                                                    plan.length < 3 ? "<h5 class='popup plan btn-sm' style='background-color: #ff8605'>Esperando<span class='popuptext'>"+plan+" Paquetes</span></h5>":
                                                    "<h5 class='popup plan btn-sm btn-primary' style='text-align:center'>En Ruta<span class='popuptext'>-"+plan+"-</span></h5>";
                                                    t.row(rowIdx).data(dataRow).draw(false);
                                                }
                                            });
                                            t.rows(".selected").nodes().to$() .removeClass("selected");
                                            t.draw(false);
                                            document.getElementById("divBotones").style.visibility = "hidden";
                                        }
                                    }
                                    else if (isChrome)
                                        loadInventario();
                                    else{
                                        t.rows(".selected").every( function(rowIdx, tableLoop, rowLoop) {
                                                var dataRow = String(this.data()).split(",");
                                                dataRow[6] = plan == "" ? "<h5 class='popup-notif sin-plan plan btn-sm btn-danger'>Sin Especificar<div class='popupicon'><div class='row'><label class='col-lg-12 col-md-12 col-sm-12 col-xs-12' style='text-align:center;'>Notificar</label><img class='icon-email' src='images/email35px.png'/>&nbsp&nbsp<img class='icon-whatsapp' src='images/whatsapp35px.png'/></div></div></h5>" :
                                                        plan == "Oficina" ? "<h5 class='plan btn-sm btn-success'>En Oficina</h5>" :
                                                        plan.includes("Guatex") ? "<h5 class='popup plan btn-sm' style='text-align:center; background-color: #f4cb38'>Guatex<span class='popuptext'>"+plan.split(":")[1]+"</span></h5>" :
                                                        plan.length < 3 ? "<h5 class='popup plan btn-sm' style='background-color: #ff8605'>Esperando<span class='popuptext'>"+plan+" Paquetes</span></h5>":
                                                        "<h5 class='popup plan btn-sm btn-primary' style='text-align:center'>En Ruta<span class='popuptext'>-"+plan+"-</span></h5>";
                                                t.row(rowIdx).data(dataRow);
                                        });
                                        t.rows(".selected").nodes().to$() .removeClass("selected");
                                        t.draw(false);
                                        document.getElementById("divBotones").style.visibility = "hidden";
                                    }
                                    bootbox.alert("Se ha actualizado la información de los paquetes seleccionados.");
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

        $("#divFechaRuta").datepicker({
            showOtherMonths: true,
            selectOtherMonths: true,
            showAnim: "slideDown",
            minDate: 0,
            maxDate: "+1M"
        });

        var tom = new Date();
        tom.setTime(tom.getTime() + 86400000);
        $('#divFechaRuta').datepicker("setDate", tom);
    }

    function entregarSeleccionados(){
        document.getElementById("divBotones").style.visibility = "hidden";
        var data = $("#inventario").DataTable().rows(".selected").data().toArray();

        var nombre = data[0][3].toUpperCase();
        var plan = data[0][6].toUpperCase();
        var continuar = true;
        let razon = true;
        let msgError;
        for (var i = 1; i < data.length; i++){
            if (!data[i][3].toUpperCase().includes(nombre)){
                continuar = false;
                msgError = "La mercadería seleccionada pertenece a diferentes clientes, solo se puede entregar la mercadería de un cliente a la vez.";
                break;
            }
            if (plan != data[i][6].toUpperCase()){
                continuar = false;
                msgError = "Los planes de entrega de los paquetes seleccionados no coinciden, verifique que no haya seleccionado paquetes de más.";
                break;
            }
        }

        if (continuar && plan.includes(">ESPERANDO<")||plan.includes(">AVISAR<")||plan.includes(">SIN ESPECIFICAR<")){
            msgError = "Debe especificar un plan de entrega para los paquetes (Oficina, Por Ruta o Guatex)";
            continuar = false;
        }

        if (!continuar){
            bootbox.dialog({
                closeButton: false,
                title: "¡Atención!",
                message: msgError,
                buttons: {
                    confirm: {
                        label: 'Entendido',
                        className: "btn-primary",
                        callback: function(){
                            document.getElementById("divBotones").style.visibility = "visible";
                        }
                    }
                }
            });
            return;
        }
        $.ajax({
            url: "db/DBgetUserTarifa.php",
            type: "POST",
            data: {
                uid: data[0][3].split(">")[1].split("<")[0]
            },
            cache: false,
            success: function(res){
                if (res == 0)
                    res = 60;
                cobrarEntrega(data, "Entregando mercadería a " + data[0][4].split(">")[1].split("<")[0], Number(res));
            },
            error: function() {
                bootbox.alert("Ocurrió un problema al intentar conectarse al servidor.");
            }
        });
    }

    function cobrarEntrega(data, titulo, tarifa) {
        var paquetes = data.length, libras = 0, celulares = 0, extras = 0;
        var uids = data[0][3].split(">")[1].split("<")[0];
        var unombre = data[0][4].split(">")[1].split("<")[0];
        var plan = "";

        if (data[0][6].includes("Oficina"))
            plan = "Oficina";
        else if (data[0][6].includes("Guatex"))
            plan = "Guatex: " + data[0][6].split(">")[2].split("<")[0];
        if (data[0][6].includes("Esperando"))
            plan = data[0][6].split(">")[2].split(" Paquetes")[0];
        if (data[0][6].includes("En Ruta"))
            plan = "Por Ruta: " + data[0][6].split(">")[2].split("<")[0].replace("-", "").replace("-", "");

        for (var i = 0; i < data.length; i++) {
            libras += Number(data[i][5].split(">")[1].split("<")[0]);
            celulares += Number(data[i][0].split('data-celulares=')[1].split(' ')[0]);
            extras += Number(data[i][0].split('data-cobro-extra=')[1].split(' ')[0]);
        }

        let costoRutaClass = 'col-lg-offset-4 col-md-offset-4 col-sm-offset-4 col-xs-offset-4 col-lg-4 col-md-4 col-sm-4 col-xs-4';
        let celularesAddedClass = 'col-lg-4 col-md-4 col-sm-4 col-xs-4';
        let extrasAddedClass = 'col-lg-4 col-md-4 col-sm-4 col-xs-4';

        if (celulares > 0 && extras > 0) {
            if (plan.includes("/")) {
                costoRutaClass = 'col-lg-offset-1 col-md-offset-1 col-sm-offset-1 col-xs-offset-1 col-lg-3 col-md-3 col-sm-3 col-xs-3';
                extrasAddedClass = 'col-lg-3 col-md-3 col-sm-3 col-xs-3'
            }
            else {
                celularesAddedClass = 'col-lg-offset-2 col-md-offset-2 col-sm-offset-2 col-xs-offset-2  col-lg-4 col-md-4 col-sm-4 col-xs-4';
                extrasAddedClass = 'col-lg-4 col-md-4 col-sm-4 col-xs-4';
            }
        }
        else if (celulares > 0) {
            if (plan.includes("/")) {
                costoRutaClass = 'col-lg-offset-2 col-md-offset-2 col-sm-offset-2 col-xs-offset-2 col-lg-4 col-md-4 col-sm-4 col-xs-4';
            }
            else {
                celularesAddedClass = 'col-lg-offset-4 col-md-offset-4 col-sm-offset-4 col-xs-offset-4 col-lg-4 col-md-4 col-sm-4 col-xs-4';
            }
        }
        else if (extras > 0){
            if (plan.includes("/")) {
                costoRutaClass = 'col-lg-offset-2 col-md-offset-2 col-sm-offset-2 col-xs-offset-2 col-lg-4 col-md-4 col-sm-4 col-xs-4';
            }
            else {
                extrasAddedClass = 'col-lg-offset-4 col-md-offset-4 col-sm-offset-4 col-xs-offset-4 col-lg-4 col-md-4 col-sm-4 col-xs-4';
            }
        }

        var tarifaTitulo = "Cliente con tarifa corriente.";
        if (tarifa != 60)
            tarifaTitulo = "Tarifa especial de cliente: Q " + tarifa;
        tarifa = "Q "+tarifa;

        bootbox.dialog({
            closeButton: false,
            title: titulo,
            message:
                "<div class='row' style='background-color: #dadada'>"+
                    "<div class='row'>"+
                        "<div class='col-lg-1 col-md-1 col-sm-1 col-xs-1'></div>"+
                        "<div class='control-group form-group col-lg-2 col-md-2 col-sm-2 col-xs-5'><div class='controls'><label align='center'  style='color: #337ab7; text-align:center; width:100%'>Paquetes</label><input style='text-align:center;' value='"+paquetes+"' type='text' class='form-control' disabled/></div></div>"+
                        "<div class='control-group form-group col-lg-2 col-md-2 col-sm-2 col-xs-5'><div class='controls'><label align='center'  style='color: #337ab7; text-align:center; width:100%'>Libras</label><input id='librasEntrega' style='text-align:center;' value='"+libras+"' type='text' class='form-control' disabled/></div></div>"+
                        (isMobile ? "<div class='col-xs-1'></div></div><div class='row'><div class='col-xs-1'></div>" : "") +
                        "<div class='control-group form-group col-lg-2 col-md-2 col-sm-2 col-xs-5'><div class='controls'><label align='center'  style='color: #337ab7; text-align:center; width:100%'>Tarifa</label><input id='tarifaEntrega' title='"+tarifaTitulo+"' value='"+tarifa+"' type='text' class='form-control' style='text-align:center;' disabled/></div></div>"+
                        "<div class='control-group form-group col-lg-4 col-md-4 col-sm-4 col-xs-5'><div class='controls'><label align='center' style='color: #337ab7; text-align:center; width:100%'>Subtotal</label><input id='subTotalEntrega' type='text' class='form-control' style='text-align:center' disabled/></div></div>"+
                        "<div class='col-lg-1 col-md-1 col-sm-1 col-xs-1'></div>"+
                    "</div>"+
                    "<div class='row control-group form-group'>"+
                        "<div class='col-lg-1 col-md-1 col-sm-1 col-xs-1'></div>"+
                        "<button onclick='toggleMetodoPago(this)' id='btnEfectivo' style='color:#337ab7' type='button' class='btn btn-default col-lg-2 col-md-2 col-sm-2 col-xs-3''>Efectivo</button>"+
                        "<button onclick='toggleMetodoPago(this)' id='btnCredito' style='color:#337ab7' type='button' class='btn btn-default col-lg-2 col-md-2 col-sm-2 col-xs-4''>Tarjeta C.</button>"+
                        "<button onclick='toggleMetodoPago(this)' id='btnCheque' style='color:#337ab7' type='button' class='btn btn-default col-lg-2 col-md-2 col-sm-2 col-xs-3''>Cheque</button>"+
                        (isMobile ? "<div class='col-xs-1'></div></div><div class='row control-group form-group'><div class='col-xs-2'></div>" : "") +
                        "<button onclick='toggleMetodoPago(this)' id='btnTransferencia' style='color:#337ab7' type='button' class='btn btn-default col-lg-2 col-md-2 col-sm-2 col-xs-4''>Transferencia</button>"+
                        (isMobile ? "<div class='col-xs-1'></div><div class='col-xs-4'></div>" : "") +
                        "<button onclick='toggleMetodoPago(this)' id='btnPendiente' style='color:#337ab7' type='button' class='btn btn-default col-lg-2 col-md-2 col-sm-2 col-xs-4''>Pendiente</button>"+
                        "<div class='col-lg-1 col-md-1 col-sm-1 col-xs-2'></div>"+
                    "</div>" +
                    (plan.includes("/") || celulares > 0 || extras > 0 ? (
                        "<div class='row'>"+ (plan.includes("/") ?
                            "<div id='divCostoRuta' class='"+ costoRutaClass +"'>"+
                                "<div class='control-group form-group'><div class='controls'><label style='color: #337ab7; text-align:center; width:100%'>Costo de Envío (Q)</label><input onfocusout='roundField(this); calcularTotalEntrega()' onkeypress='return numbersonly(this, event, \"\")' onkeyup='this.value=this.value.replace(/^0+/, \"\");' id='costoRutaEntrega' type='text' class='form-control' style='width:100%; text-align:center;'/></div></div>"+
                            "</div>" : "" ) + (celulares > 0 ?
                            "<div id='divCostoCelulares' class='"+ celularesAddedClass +"'>" +
                                "<div class='control-group form-group'><div class='controls'><label style='color: #337ab7; text-align:center; width:100%'>Costo por "+celulares+" Celulares</label><input data-cantidad='"+celulares+"' disabled value='Q "+numberWithCommas(celulares*100)+"' onfocusout='roundField(this); calcularTotalEntrega()' onkeypress='return numbersonly(this, event, \"\")' onkeyup='this.value=this.value.replace(/^0+/, \"\");' id='costoCelulares' type='text' class='form-control' style='width:100%; text-align:center;'/></div></div>"+
                            "</div>" : "" ) + (extras > 0 ?
                            "<div id='divCostoExtras' class='"+ extrasAddedClass +"'>" +
                                "<div class='control-group form-group'><div class='controls'><label style='color: #337ab7; text-align:center; width:100%'>Costos Extras</label><input data-monto='"+extras+"' disabled value='Q "+numberWithCommas(extras)+"' onfocusout='roundField(this); calcularTotalEntrega()' onkeypress='return numbersonly(this, event, \"\")' onkeyup='this.value=this.value.replace(/^0+/, \"\");' id='costosExtras' type='text' class='form-control' style='width:100%; text-align:center;'/></div></div>"+
                            "</div>" : "" ) +
                        "</div>" ) : "" )+
                    "<div class='row'>"+
                        "<div class='col-lg-1 col-md-1 col-sm-1'></div>"+
                        "<div class='control-group form-group col-lg-2 col-md-2 col-sm-2 col-xs-2'><div class='controls'>"+
                            "<button onclick='toggleDescuento()' id='btnDescuento' style='color:#337ab7; margin-top: 2px;' type='button' class='btn btn-default'>Descuento<br>Especial</button>"+
                        "</div></div>"+
                        "<div style='pointer-events:none; opacity:0.4;' id='divDescuentoEspecial' class='col-lg-8 col-md-8 col-sm-8 col-xs-10'>"+
                            "<div class='control-group form-group col-lg-5 col-md-5 col-sm-5 col-xs-6'><div class='controls'><label style='color: #337ab7; text-align:center; width: 100%;'>Descuento (Q)</label><input onfocusout='roundField(this); aplicarDescuento();' onkeypress='return numbersonly(this, event, \"-\")' onkeyup='this.value=this.value.replace(/^0+/, \"\");' id='descuentoEntrega' type='text' class='form-control' style='text-align:center;'/></div></div>"+
                            "<div id='divComentarioDescuento' class='control-group form-group col-lg-7 col-md-7 col-sm-7 col-xs-6'><div class='controls'><label style='color: #337ab7; text-align:center; width: 100%;'>Comentario</label><input id='comentarioEntrega' type='text' class='form-control'/></div></div>"+
                        "</div>"+
                        "<div class='col-lg-1 col-md-1 col-sm-1'></div>"+
                    "</div>"+
                    "<div class='row-same-height'>"+
                        "<div class='col-lg-2 col-md-2 col-sm-2 col-xs-1'></div>"+
                        "<div id='divDetalle' style='display:none' class='col-lg-4 col-md-4 col-sm-4 col-xs-5'><div class='controls'><label align='center' style='color: #337ab7; text-align:center; width:100%'>Detalle</label>"+
                            "<label style='color: gray; font-size: 11px;' id='detalleEntrega'></label>"+
                        "</div></div>"+
                        "<div id='divRelleno' class='col-lg-2 col-md-2 col-sm-2 col-xs-3'></div>"+
                        "<div class='control-group form-group col-lg-4 col-md-4 col-sm-4 col-xs-4'><div class='controls'><label align='center' style='color: #337ab7; text-align:center; width:100%'>Total</label><input id='totalEntrega' type='text' style='text-align:center' class='form-control' disabled/></div></div>"+
                        "<div class='col-lg-2 col-md-2 col-sm-2 col-xs-2'></div>"+
                    "</div>"+
                    "<div class='row' id='divSpanInputEntrega' style='display: none;'>"+
                        "<div class='col-lg-2 col-md-2 col-sm-2 col-xs-2'></div>"+
                        "<div class='row col-lg-8 col-md-8 col-sm-8 col-xs-8'>"+
                            "<span id='spanInputEntrega' class='dialog-text'></span>"+
                        "</div>"+
                        "<div class='col-lg-2 col-md-2 col-sm-2 col-xs-2'></div>"+
                    "</div>"+
                "</div>",
            buttons: {
                cancel: {
                    label: "Cancelar Entrega",
                    className: "btn btn-md btn-danger alinear-izquierda",
                    callback: function(){
                        document.getElementById("divBotones").style.visibility = "visible";
                    }
                },
                confirm: {
                    label: "Terminar Entrega",
                    className: "btn btn-md btn-success alinear-derecha",
                    callback: function() {
                        var metodo = "";
                        if (document.getElementById("btnEfectivo").style.color == "white")
                            metodo = "Efectivo";
                        else if (document.getElementById("btnCredito").style.color == "white")
                            metodo = "Tarjeta C.";
                        else if (document.getElementById("btnCheque").style.color == "white")
                            metodo = "Cheque";
                        else if (document.getElementById("btnTransferencia").style.color == "white")
                            metodo = "Transferencia";
                        else if (document.getElementById("btnPendiente").style.color == "white")
                            metodo = "Pendiente";
                        else {
                            activateSpanEntrega("Por favor especifique una forma de pago.");
                            return false;
                        }

                        var ruta = "NULL";
                        if (plan.includes("/")){
                            ruta = document.getElementById("costoRutaEntrega").value;
                            if (ruta.replace(/\s/g,'').length === 0){
                                activateSpanEntrega("Por favor ingrese el costo del envío de mercadería.");
                                return false;
                            }
                            ruta = "'"+ruta+"'";
                        }

                        var pressed = document.getElementById("btnDescuento").style.color == "white";
                        var de = "NULL", desc = "", coment = "";
                        if (pressed){
                            desc = document.getElementById("descuentoEntrega").value;
                            coment = document.getElementById("comentarioEntrega").value;

                            if (desc.replace(/\s/g,'').length === 0 && coment.replace(/\s/g,'').length === 0){
                                activateSpanEntrega("Por favor llene los campos correspondientes al descuento especial.");
                                return false;
                            }
                            else if (coment.replace(/\s/g,'').length === 0){
                                activateSpanEntrega("Por favor ingrese el motivo del descuento en el campo 'Comentario'.");
                                return false;
                            }
                            else if (desc.replace(/\s/g,'').length === 0){
                                activateSpanEntrega("Por favor ingrese el descuento a aplicar.");
                                return false;
                            }
                            de = "'"+desc+"@@@"+coment+"'";
                        }


                        var trackStr = "(";
                        for (var i = 0; i < data.length; i++)
                            trackStr = trackStr + (i == 0 ? "'":", '")+data[i][2].replace("<br>", "").split(">")[1].split("<")[0]+"'";
                        trackStr = trackStr+")";
                        var tarif = document.getElementById("tarifaEntrega").value;
                        var total = document.getElementById("totalEntrega").value;
                        var subtotal = document.getElementById("subTotalEntrega").value;
                        var detalle = document.getElementById("detalleEntrega").innerHTML;
                        if (detalle == "")
                            detalle = "NULL";
                        else detalle = "'"+detalle+"'";

                        if (plan == "")
                            plan = "No Especificado";

                        var hoy = new Date();
                        var fecha = hoy.getFullYear() + "-" + (hoy.getMonth()+1) + "-" + hoy.getDate() + " " + hoy.getHours() + ":" + hoy.getMinutes() + ":" + hoy.getSeconds();

                        $.ajax({
                            url: "db/DBsetPaquete.php",
                            type: "POST",
                            data: {
                                set: "estado = '" + fecha + "'",
                                where: "tracking IN "+trackStr
                            },
                            cache: false,
                            success: function(res){
                                if (res.includes("ERROR")){
                                    bootbox.alert("Ocurrió un error al consultar la base de datos. Se recibió el siguiente mensaje: <i><br>" + res + "</i>");
                                }
                                else if (Number(res) < 1){
                                    bootbox.alert("No se pudo efectuar el cambio en la base de datos, intente nuevamente");
                                }
                                else if (Number(res) != data.length){
                                    $.ajax({
                                        url: "db/DBsetPaquete.php",
                                        type: "POST",
                                        data: {
                                            set: "estado = NULL",
                                            where: "tracking IN "+trackStr
                                        },
                                        cache: false,
                                        success: function(res){

                                        }
                                    });
                                    bootbox.alert("Uno de los paquetes de la entrega no pudo ser marcado como entregado (verifique los trackings), por favor realize la entrega nuevamente.");
                                }
                                else{
                                    $.ajax({
                                        url: "db/DBinsertEntrega.php",
                                        type: "POST",
                                        data: {
                                            d: fecha,
                                            p: paquetes,
                                            ui: uids,
                                            un: unombre,
                                            to: total,
                                            lbs: libras,
                                            tar: tarif,
                                            st: subtotal,
                                            m: metodo,
                                            r: ruta,
                                            des: de,
                                            det: detalle,
                                            pl: plan
                                        },
                                        cache: false,
                                        success: function(res){
                                            if (res.includes("ERROR")){
                                                $.ajax({
                                                    url: "db/DBsetPaquete.php",
                                                    type: "POST",
                                                    data: {
                                                        set: "estado = NULL",
                                                        where: "tracking IN "+trackStr
                                                    }
                                                });
                                                bootbox.alert("Ocurrió un error al consultar la base de datos. Se recibió el siguiente mensaje: <i><br>" + res + "</i>");
                                            }
                                            else if (Number(res) < 1){
                                                $.ajax({
                                                    url: "db/DBsetPaquete.php",
                                                    type: "POST",
                                                    data: {
                                                        set: "estado = NULL",
                                                        where: "tracking IN "+trackStr
                                                    }
                                                });
                                                bootbox.alert("No se pudo agregar la boleta a la base de datos, intente nuevamente");
                                            }
                                            else{
                                                var fec = fecha.split(" ")[0].split("-");
                                                var hora = fecha.split(" ")[1].split(":");
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
                                                $("#inventario").DataTable().rows('.selected').remove().draw(false);
                                                document.getElementById("divBotones").style.visibility = "hidden";
                                                bootbox.alert("La mercadería ha sido entregada con éxito. Se registró una nueva boleta virtual, con fecha " + fec[2] + "/" + fec[1] + "/" + fec[0] + " a las " + h + ":" + m + ":" + s + " " + apm + ".");
                                            }
                                        },
                                        error: function() {
                                            bootbox.alert("Ocurrió un problema al intentar conectarse al servidor.");
                                        }
                                    });
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
        calcularTotalEntrega();
    }

    function activateSpanEntrega(str){
        document.getElementById("spanInputEntrega").innerHTML = str;
        document.getElementById("divSpanInputEntrega").style.display="inline";
        setTimeout(function() {$('#divSpanInputEntrega').fadeOut('slow');}, 3000);
    }

    function calcularSubTotal(){
        var credito = document.getElementById("btnCredito").style.color == "white";
        var tarifa = Number(document.getElementById("tarifaEntrega").value.replace(/[Q,\s]/g, ""));
        var libras = Number(document.getElementById("librasEntrega").value);
        if (credito)
            document.getElementById("subTotalEntrega").value = "Q " + numberWithCommas(libras*68);
        else
            document.getElementById("subTotalEntrega").value = "Q " + numberWithCommas(libras*tarifa);
    }

    function calcularTotalEntrega(){
        calcularSubTotal();

        var total = Number(document.getElementById("subTotalEntrega").value.replace(/[Q,\s]/g, ""));
        if (document.getElementById("btnDescuento").style.color == "white")
            total -= Number(document.getElementById("descuentoEntrega").value);

        if ($("#divCostoRuta").length){
            total += Number(document.getElementById("costoRutaEntrega").value);
        }

        if ($("#divCostoCelulares").length){
            total += Number(document.getElementById("costoCelulares").value.replace(/[Q,\s]/g, ""));
        }

        if ($("#divCostoExtras").length){
            total += Number(document.getElementById("costosExtras").value.replace(/[Q,\s]/g, ""));
        }

        document.getElementById("totalEntrega").value = "Q " + numberWithCommas(total);

        var detalle = document.getElementById("detalleEntrega");
        var divDetalle = document.getElementById("divDetalle");
        var divRelleno = document.getElementById("divRelleno");
        var tarifa = document.getElementById("tarifaEntrega");
        var libras = Number(document.getElementById("librasEntrega").value);

        var detalleStr = "";
        if (document.getElementById("btnCredito").style.color == "white"){
            divDetalle.style.display = "block";
            divRelleno.style.display = "none";
            var extra = Number(document.getElementById("subTotalEntrega").value.replace(/[Q,\s]/g, ""));
            var tarifAumnt = numberWithCommas(4), comision = 68*libras;
            if (tarifa.title != "Cliente con tarifa corriente."){
                var t = Number(tarifa.title.split(": ")[1].replace(/[Q,\s]/g, ""));
                tarifAumnt = numberWithCommas(68 - t);
                extra -= t*libras;
            }
            else
                extra -= 60*libras;
            detalleStr = "Pago con Tarjeta de Crédito:<br> &nbsp&nbsp* Aumento de Tarifa: Q "+tarifAumnt
                // +"<br> &nbsp&nbsp* Comisión: Q "+numberWithCommas(comision)
                +"<br> &nbsp&nbsp* Monto total agregado:<br> &nbsp&nbsp&nbsp&nbsp&nbsp&nbspQ " + numberWithCommas(extra);
        }
        else if (tarifa.title == "Cliente con tarifa corriente."){
            divDetalle.style.display = "none";
            divRelleno.style.display = "block";
        }
        else{
            divDetalle.style.display = "block";
            divRelleno.style.display = "none";
            var tarif = Number(tarifa.title.split(":")[1].replace(/[Q,\s]/g, ""));
            var libras = Number(document.getElementById("librasEntrega").value);
            detalleStr = "* El cliente posee una tarifa especial, su ahorro es de:<br> Q " + numberWithCommas((60 - tarif)*libras);
        }

        detalle.innerHTML = detalleStr;
    }

    function aplicarDescuento(){
        var desc = document.getElementById("descuentoEntrega").value;
        if (desc.replace(/\s/g,'').length === 0){
            activateSpanEntrega("Por favor ingrese el descuento a aplicar.");
            return false;
        }
        calcularTotalEntrega();
    }

    function toggleDescuento(){
        var boton = document.getElementById("btnDescuento");
        var div = document.getElementById("divDescuentoEspecial");

        if (boton.style.color == "white"){
            div.style.pointerEvents = "none";
            div.style.opacity = "0.4";
            boton.style.backgroundColor = "#fff";
            boton.style.color = "#337ab7";
            calcularTotalEntrega();
        }
        else{
            div.style.pointerEvents = "all";
            div.style.opacity = "1.0";
            boton.style.backgroundColor = "#337ab7";
            boton.style.color = "white";
            calcularTotalEntrega();
        }
    }

    function toggleMetodoPago(boton){
        var tarifa = document.getElementById("tarifaEntrega");
        var costoCelulares = $('#costoCelulares');
        var costosExtras= $('#costosExtras');
        var cantCelulares = null;
        var montoExtras = null;
        if (costoCelulares.length)
            cantCelulares = costoCelulares.data('cantidad');
        if (costosExtras.length)
            montoExtras = costosExtras.data('monto');

        if (boton.style.color == "white"){
            boton.style.backgroundColor = "#fff";
            boton.style.color = "#337ab7";

            if (boton.innerHTML == "Tarjeta C."){
                if (tarifa.title == "Cliente con tarifa corriente.")
                    tarifa.value = "Q 60";
                else
                    tarifa.value = tarifa.title.split(": ")[1];

                if (cantCelulares !== null)
                    costoCelulares.val('Q ' + numberWithCommas(cantCelulares*100));
                if (montoExtras !== null)
                    costosExtras.val('Q ' + numberWithCommas(montoExtras));

                calcularTotalEntrega();
            }
        }
        else{
            if (tarifa.title == "Cliente con tarifa corriente.")
                tarifa.value = "Q 60";
            else
                tarifa.value = tarifa.title.split(": ")[1];

            if (boton.innerHTML == "Tarjeta C."){
                tarifa.value = "Q 68";
                if (cantCelulares !== null)
                    costoCelulares.val('Q ' + numberWithCommas(cantCelulares*115));
                if (montoExtras !== null)
                    costosExtras.val('Q ' + numberWithCommas(montoExtras*1.12));
            }

            document.getElementById("btnEfectivo").style.backgroundColor = "#fff";
            document.getElementById("btnEfectivo").style.color = "#337ab7";
            document.getElementById("btnCredito").style.backgroundColor = "#fff";
            document.getElementById("btnCredito").style.color = "#337ab7";
            document.getElementById("btnCheque").style.backgroundColor = "#fff";
            document.getElementById("btnCheque").style.color = "#337ab7";
            document.getElementById("btnTransferencia").style.backgroundColor = "#fff";
            document.getElementById("btnTransferencia").style.color = "#337ab7";
            document.getElementById("btnPendiente").style.backgroundColor = "#fff";
            document.getElementById("btnPendiente").style.color = "#337ab7";

            boton.style.backgroundColor = "#337ab7";
            boton.style.color = "white";

            calcularTotalEntrega();
        }
    }

</script>
