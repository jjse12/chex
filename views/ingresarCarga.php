<div class="container" style="padding-top: 4.5cm">
    <br>
    <div class="row" >
        <div class="row-same-height">
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 col-lg-height col-md-height col-sm-height col-xs-height">
                    <div class="row">
                        <div class="col-md-6 col-xs-6">
                            <label class="text-color-gray"># Tracking:</label>
                            <div>
                                <input onfocusout="checkTrackingExists(this.value)" class="text-field form-control validate-field required" id="tracking" required placeholder="# Tracking" maxlength="50" onkeypress="return onlyLettersAndNumbers(this, event)" data-validation-required-message="Ingresa el numero de tracking del paquete.">
                            </div>
                        </div>
                        <div class="col-md-6 col-xs-6">
                            <label class="text-color-gray">Peso en libras:</label>
                            <div>
                                <input type="text" class="form-control" id="peso" required placeholder="Peso" maxlength="3" onkeypress="return integersonly(this, event)" onkeyup="this.value=this.value.replace(/^0+/, '');">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 col-xs-6">
                            <label class="text-color-gray">ID del Cliente:</label>
                            <div>
                                <input onfocusout="getUserName(this.value)" class="text-field form-control validate-field required" id="uid" required placeholder="ID Cliente" maxlength="7" data-validation-required-message="Ingresa el ID del cliente." onkeypress="return onlyLettersAndNumbers(this, event)">
                                <div id="spanID" style="display:none">
                                    <span class="dialog-text"> Atención: No existe ningún cliente asociado a este ID.</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-xs-6">
                            <label class="text-color-gray">Nombre del Cliente:</label>
                            <div>
                                <input class="text-field form-control validate-field required" id="uname" required placeholder="Nombre Cliente" data-validation-required-message="Ingresa el nombre del cliente." maxlength="50" onkeypress="return notAllow(this, event, ',<>\'')">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 col-xs-6">
                            <br>
                            <button class="btn btn-lg btn-success" onclick="agregarCarga()" style="text-align: center; width:100%; ">Agregar Paquete</button>
                            <div id="spanAgregarCarga" style="display:none">
                                <span class="dialog-text"> Por favor asegúrate de llenar todos los campos.</span>
                            </div>
                        </div>
                        <div class="col-md-12 col-xs-6">
                            <br>
                            <button class="btn btn-sm btn-warning" onclick="agregarRegistro()" style="text-align: center; width:100%; ">Guardar Registro de Carga</button>
                            <div id="spanAgregarRegistro" style="display:none">
                                <span class="dialog-text"> Se debe agregar por lo menos un paquete.</span>
                            </div>
                        </div>
                    </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 col-lg-height col-md-height col-sm-height col-xs-height" id="divResCotizacion">
                <br>
                <div class="panel panel-bordeado flex-col">
                    <div class="row">
                        <strong><h2 id="paquetes" class="header-title" style="color:#444">Paquetes: 0</h2></strong>
                    </div>
                    <div class="row">
                        <strong><h2 id="libras" class="header-title" style="color:#338">Libras: 0</h2></strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="emptyrow"></div>
    <div>
        <table id="tablaNuevaCarga" class="display">
            <thead>
                <tr>
                    <th class="dt-head-center"><h5 style="color:black"># Tracking</h5></th>
                    <th class="dt-head-center"><h5 style="color:black">ID Cliente</h5></th>
                    <th class="dt-head-center"><h5 style="color:black">Nombre Cliente</h5></th>
                    <th class="dt-head-center"><h5 style="color:black">Peso</h5></th>
                    <th></th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th colspan="2" class="dt-head-left"></th>
                </tr>
            </tfoot>
            <tbody>
            </tbody>
        </table>
    </div>
</div>

<script type="text/javascript">

    $(document).ready( function () {
        var tablilla = $('#tablaNuevaCarga').DataTable({
            "bSort" : false,
            "retrieve": true,
            "select": true,
            "responsive": true,
            "scrollY": "500px",
            "scrollCollapse": true,
            "paging": false,
            "fixedColumns": true,
            "language": {
                "lengthMenu": "Display _MENU_ records per page",
                "search": "Buscar:",
                "zeroRecords": "No hay paquetes que coincidan con la búsqueda",
                "info": "Mostrando paquetes del _START_ al _END_ de _TOTAL_ paquetes totales.",
                "infoEmpty": "No se han ingresado paquetes.",
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
                    api.column(3).footer().style.visibility = "hidden";
                    return;
                }
                else
                    api.column(3).footer().style.visibility = "visible";

                var intVal = function ( i ) {
                    return typeof i === 'string' ?
                        i.replace(/[\$,]/g, '')*1 :
                        typeof i === 'number' ?
                            i : 0;
                };

                $(api.column(3).footer()).html(
                    "<h5>Total: " + numberWithCommasNoFixed(api.column(3, { page: 'current'} ).data().reduce( function (a, b) {
                                        return intVal(a) + intVal(b.includes(">") ? b.split(">")[1].split("<")[0] : b);
                                        }, 0)) + " Libras</h5>"
                );
            }
        });

        $('#tablaNuevaCarga tbody').on("click", "img.icon-delete", function () {
            tablilla.row( $(this).parents('tr'))
                        .remove()
                        .draw(false);
            document.getElementById("paquetes").innerHTML = "Paquetes: " + tablilla.rows().data().length;
            if (tablilla.rows().data().length == 0)
                document.getElementById("libras").innerHTML = "Libras: 0";
            else
                document.getElementById("libras").innerHTML = "Libras: " + calcularLibras();
        });

        $('#tablaNuevaCarga tbody').on("click", "h5.ingCargaTracking", function () {
            var index = tablilla.row($(this).closest('tr')).index();
            var arr = tablilla.rows(index).data().toArray();
            bootbox.prompt({
                title: "Ingrese el tracking para el paquete.",
                size: "small",
                inputType: 'text',
                callback: function (result) {
                    if (result == null)
                        return true;
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
                        var data = tablilla.rows().data().toArray();
                        for (var i = 0; i < data.length; i++){
                            if (data[i][0].split(">")[1].split("<")[0] == result){
                                bootbox.alert({
                                    message: "Ya ha ingresado un paquete con este número de tracking en el actual registro de carga.",
                                    size: 'small',
                                    backdrop: true
                                });
                                return false;
                            }
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
                                    arr[0][0] = "<h5 class='seleccionado ingCargaTracking'>"+result.toUpperCase()+"</h5>";
                                    tablilla.row(index).data(arr[0]).draw(false);
                                    bootbox.hideAll();
                                }
                            },
                            error: function() {
                                bootbox.alert("Ocurrió un problema al intentar conectarse al servidor.");
                            }
                        });
                    }

                    return false;
                }
            });
        });


        $('#tablaNuevaCarga tbody').on("click", "h5.ingCargaUid", function () {
            var index = tablilla.row($(this).closest('tr')).index();
            var arr = tablilla.rows(index).data().toArray();
            bootbox.prompt({
                title: "Ingrese el ID del cliente.",
                size: "small",
                inputType: 'text',
                callback: function (result) {
                    if (result == null)
                        return true;
                    if (result == "")
                        bootbox.alert("Debe ingresar un ID de cliente para el paquete.");
                    else if (result.includes("<"))
                        bootbox.alert("El ID no puede contener el caracter '<'");
                    else if (result.includes(">"))
                        bootbox.alert("El ID no puede contener el caracter '>'");
                    else if (result.includes(","))
                        bootbox.alert("El ID no puede contener comas.");
                    else if (result.length > 7)
                        bootbox.alert("El ID no puede exceder los 7 caracteres.");
                    else {
                        arr[0][1] = "<h5 class='seleccionado ingCargaUid'>"+result.toUpperCase()+"</h5>";
                        $.ajax({
                            url: "db/DBgetUserNamePostUid.php",
                            type: "POST",
                            data: {
                                uid: result
                            },
                            cache: false,
                            success: function(name){
                                bootbox.hideAll();
                                var arreglo = ["Cliente", "CLIENTE", "cliente", "Anónimo", "ANÓNIMO", "anónimo", "Anonimo", "ANONIMO", "anonimo"];
                                if (arreglo.indexOf(result) == -1 && name.replace(/\s/g,'').length == 0) {
                                    bootbox.alert({title: "¡Atención!", message: "Al parecer no existe ningún cliente asociado a este ID."});
                                }
                                else if (name != null && name.length > 1) {
                                    bootbox.confirm({
                                        title: "Nombre de Cliente encontrado",
                                        message: "El ID ingresado está asociado al cliente '" + name + "', ¿desea actualizar también el nombre de cliente del paquete?",
                                        callback: function(resito){
                                            if (resito){
                                                arr[0][2] = "<h5 class='seleccionado ingCargaUname'>"+name+"</h5>";
                                                tablilla.row(index).data(arr[0]).draw(false);
                                            }
                                        }
                                    });
                                }
                                tablilla.row(index).data(arr[0]).draw(false);
                            },
                            error: function() {
                                bootbox.hideAll();
                                tablilla.row(index).data(arr[0]).draw(false);
                            }
                        });
                    }
                    return false;
                }
            });
        });

        $('#tablaNuevaCarga tbody').on("click", "h5.ingCargaUname", function () {
            var index = tablilla.row($(this).closest('tr')).index();
            var arr = tablilla.rows(index).data().toArray();
            bootbox.prompt({
                title: "Ingrese el nombre del cliente para el paquete.",
                size: "small",
                inputType: 'text',
                callback: function (result) {
                    if (result == null)
                        return true;
                    if (result == "")
                        bootbox.alert("Debe ingresar un nombre de cliente para el paquete.");
                    else if (result.includes("<"))
                        bootbox.alert("El nombre de cliente no puede contener el caracter '<'");
                    else if (result.includes(">"))
                        bootbox.alert("El nombre de cliente no puede contener el caracter '>'");
                    else if (result.includes(","))
                        bootbox.alert("El nombre de cliente no puede contener comas.");
                    else if (result.length > 50)
                        bootbox.alert("El nombre de cliente no puede exceder los 50 caracteres.");
                    else {
                        arr[0][2] = "<h5 class='seleccionado ingCargaUname'>"+result+"</h5>";
                        tablilla.row(index).data(arr[0]).draw(false);
                        return true;
                    }
                    return false;
                }
            });
        });

        $('#tablaNuevaCarga tbody').on("click", "h5.ingCargaPeso", function () {
            var index = tablilla.row($(this).closest('tr')).index();
            var arr = tablilla.rows(index).data().toArray();
            bootbox.prompt({
                title: "Ingrese el peso del paquete.",
                size: "small",
                inputType: 'number',
                callback: function (result) {
                    if (result == null)
                        return true;
                    if (result == "")
                        bootbox.alert("Debe ingresar un peso para el paquete.");
                    else if (result.includes(","))
                        bootbox.alert("El peso no puede contener comas.");
                    else if (result.includes("."))
                        bootbox.alert("El peso no puede contener punto decimal.");
                    else if (result.includes("-"))
                        bootbox.alert("El peso no puede ser negativo.");
                    else {
                        arr[0][3] = "<h5 class='seleccionado ingCargaPeso'>"+result+"</h5>";
                        tablilla.row(index).data(arr[0]).draw(false);
                        document.getElementById("libras").innerHTML = "Libras: " + calcularLibras();
                        return true;
                    }
                    return false;
                }
            });
        });

    });

    var corres = 0;

    function initTablaIngresoCarga(){
        $("#tablaNuevaCarga").DataTable().columns.adjust().responsive.recalc();
    }

    function agregarCarga() {
        var tracking = document.getElementById("tracking").value.toUpperCase();
        var uid = document.getElementById("uid").value.toUpperCase();
        var uname = document.getElementById("uname").value;
        var peso = document.getElementById("peso").value;

        if (tracking.replace(/\s/g,'').length === 0 || uid.replace(/\s/g,'').length === 0 || uname.replace(/\s/g,'').length === 0 || peso.length === 0 || peso <= 0){
            document.getElementById("spanAgregarCarga").style.display="inline";
            setTimeout(function() {
                $('#spanAgregarCarga').fadeOut('slow');
            }, 3000);
            return;
        }

        var nt = ["NT", "NOTRACKING", "NO", "nt", "notracking", "no", "NoTracking"];
        if (nt.indexOf(tracking) != -1){
            $.ajax({
                url: "db/DBgetPaquete.php",
                type: "POST",
                data: {
                    select: "MAX(tracking) AS max",
                    where: "tracking LIKE 'NO\_TRACKING\_%'"
                },
                cache: false,
                success: function(arr) {
                    var corre = Number(JSON.parse(arr.replace("[","").replace("]","")).max.split("\_")[2])+1+corres;
                    if (corre < 10)
                        corre = "000"+corre;
                    else if (corre < 100)
                        corre = "00"+corre;
                    else if (corre < 1000)
                        corre = "0"+corre;

                    tracking = "NO_TRACKING_"+corre;
                    corres += 1;

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

        var data = $("#tablaNuevaCarga").DataTable().rows().data().toArray();
        for (var i = 0; i < data.length; i++){
            if (data[i][0].split(">")[1].split("<")[0] == tracking) {
                bootbox.alert({
                    message: "Ya ha ingresado un paquete con este número de tracking en el actual registro de carga.",
                    size: 'small',
                    backdrop: true
                });
                return;
            }
        }

        $.ajax({
            url: "db/DBgetPaquete.php",
            type: "POST",
            data: {
                select: "COUNT(tracking) AS cant",
                where: "tracking = '"+tracking+"'"
            },
            cache: false,
            success: function(arr) {
                var row = JSON.parse(arr.replace("[","").replace("]",""));
                if (row.cant != 0){
                    bootbox.alert({
                        message: "Ya existe un paquete registrado con este número de tracking, por favor ingrese el dato correctamente.",
                        size: 'small',
                        backdrop: true,
                    });
                }
                else {
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
                }
            },
            error: function() {
                bootbox.alert("Ocurrió un problema al intentar conectarse al servidor.");
            }
        });
    }

    function calcularLibras(){
        var t = $('#tablaNuevaCarga').DataTable();
        var data = t.rows().data().toArray();
        var total = 0;
        var str = "";
        for (var i = 0; i < data.length; i++){
            str = data[i][3].split(">")[1].split("<")[0];
            total = total + Number(str);
        }
        return total;
    }

    function agregarRegistro(){

        var t = $('#tablaNuevaCarga').DataTable();
        if (t.rows().data().length == 0){
            document.getElementById("spanAgregarRegistro").style.display="inline";
            setTimeout(function() {
                $('#spanAgregarRegistro').fadeOut('slow');
            }, 3000);
            return;
        }
        var tdata = [], arr = t.rows().data().toArray();
        for (var i = 0; i < arr.length; i++)
            tdata[i] = arr[i].slice();

        var libras = calcularLibras();
        var paquetes = tdata.length;
        bootbox.confirm({
            title: "Registrar Carga",
            message: "Se ingresará al sistema un nuevo registro de carga con " + paquetes + " paquetes, para un total de " + libras + " libras, ¿desea continuar?",
            size: 'medium',
            buttons: {
                cancel: {
                    label: 'Regresar',
                    className: "btn-default"
                },
                confirm: {
                    label: 'Continuar',
                    className: "btn-success"
                }
            },
            callback: function (result) {
                if (result == true){
                    var hoy = new Date();
                    var fecha = hoy.getFullYear() + "-" + (hoy.getMonth()+1) + "-" + hoy.getDate() + " " + hoy.getHours() + ":" + hoy.getMinutes() + ":" + hoy.getSeconds();

                    var arreglo = ["Cliente", "CLIENTE", "cliente", "Anónimo", "ANÓNIMO", "anónimo", "Anonimo", "ANONIMO", "anonimo"];
                    var invent = $("#inventario").DataTable();
                    invent.search("Esperando");
                    var arr = invent.rows({search:'applied'}).data().toArray();
                    var uids = "";
                    for (var i = 0; i < arr.length; i++){
                        var id = arr[i][2].split(">")[1].split("<")[0].toUpperCase();
                        if ((arreglo.indexOf(id) == -1) && (uids.split(",").indexOf(id) == -1))
                            uids += id+",";
                    }
                    
                    uids = uids.substr(0, uids.length-1).split(",");

                    for (var i = 0; i < tdata.length; i++){
                        for (var j = 0; j < tdata[i].length-1; j++)
                            tdata[i][j] = tdata[i][j].split(">")[1].split("<")[0];
                    }
                    $.ajax({
                        url: "db/DBinsertCarga.php",
                        type: "POST",
                        data: {
                            peso: libras,
                            date: fecha,
                            data: tdata
                        },
                        cache: false,
                        success: function(res){
                            if (res == "errorRegistro"){
                                bootbox.alert({
                                    title: "¡Error!",
                                    message: "No se pudieron agregar los paquetes al sistema",
                                    size: 'medium',
                                    backdrop: true
                                });
                            }
                            else if (res.includes("|")){
                                var cant = Number(res.split("|")[0].split("@")[0]);
                                var error = res.split("|")[0].split("@")[1];
                                if (cant == 0){
                                    bootbox.dialog({
                                        title: "¡Error en el ingreso de carga!",
                                        message: "No se pudo ingresar la carga debido a un error de base de datos. El servidor indicó el siguiente error: <br><i>"+error+"</i><br><b>(El primer paquete provocó el problema)</b><br><br> ¿Desea remover de la tabla el primer paquete?)",
                                        size: 'medium',
                                        backdrop: true,
                                        buttons: {
                                            guardar: {
                                                label: "Si",
                                                className: "btn btn-md btn-success alinear-derecha",
                                                callback: function(){
                                                    var trackingsin = tdata[0][0];
                                                    t.row(0).remove().draw(false);
                                                    document.getElementById("libras").innerHTML = "Libras: " + calcularLibras();
                                                    document.getElementById("paquetes").innerHTML = "Paquetes: " + t.rows().data().length;
                                                    invent.search("");
                                                    invent.draw(false);
                                                    bootbox.alert("Se ha removido de la tabla el paquete que provocó el problema (el paquete con #tracking = '"+trackingsin+"').");
                                                }
                                            },
                                            regresar: {
                                                label: "No",
                                                className: "btn btn-md btn-info alinear-izquierda",
                                                callback: function(){
                                                }
                                            }
                                        }
                                    });
                                }
                                else{
                                    var rcid = res.split("|")[1].split("@")[0];
                                    bootbox.dialog({
                                        title: "¡Carga Incompleta!",
                                        message: "Solamente se pueden agregar los primeros " + cant + " paquetes debido a un error de base de datos. El servidor indicó el siguiente error: <br><i>"+error+"</i><br><b>(El " + (cant+1) + "° paquete provocó el problema)</b><br><br> ¿Desea guardar un registro de carga con los primeros " + cant + " paquetes? (Los paquetes restantes seguirán en la tabla para poder ser agregados en otro registro de carga)",
                                        size: 'medium',
                                        backdrop: true,
                                        buttons: {
                                            guardar: {
                                                label: "Si",
                                                className: "btn btn-md btn-success alinear-derecha",
                                                callback: function(){
                                                    var fec = res.split("|")[1].split("@")[1].split(" ")[0].split("-");
                                                    var hora = res.split("|")[1].split("@")[1].split(" ")[1].split(":");
                                                    var h = hora[0];
                                                    var m = hora[1];
                                                    if (m < 10 && m.length == 1)
                                                        m = "0"+m;
                                                    var apm = "PM";
                                                    if (h > 12)
                                                        h = h-12;
                                                    else if (h < 12){
                                                        if (h == 0)
                                                            h = 12;
                                                        apm = "AM";
                                                    }
                                                    bootbox.alert({
                                                        title: "¡Carga Ingresada!",
                                                        message: "Los primeros " + cant + " paquetes fueron ingresados al sistema de inventario bajo el registro de carga #" + rcid + " con fecha " + fec[2] + "/" + fec[1] + "/" + fec[0] + " a las " + h + ":" + m + " " + apm + ".",
                                                        size: 'medium',
                                                        backdrop: true,
                                                        callback: function(res){
                                                            for (var i = 0; i < cant+1; i++){
                                                                t.row(0).remove();
                                                            }
                                                            t.draw(false);
                                                            document.getElementById("libras").innerHTML = "Libras: " + calcularLibras();
                                                            document.getElementById("paquetes").innerHTML = "Paquetes: " + t.rows().data().length;
                                                            invent.search("");
                                                            invent.draw(false);
                                                            bootbox.alert("Para facilitar el ingreso de los paquetes restantes, se removieron de la tabla los " + cant + " paquetes ingresados, junto con el paquete que provocó el problema.");
                                                        }
                                                    });
                                                }
                                            },
                                            regresar: {
                                                label: "No",
                                                className: "btn btn-md btn-info alinear-izquierda",
                                                callback: function(){
                                                    $.ajax({
                                                        url: "db/DBexecMultiQuery.php",
                                                        type: "POST",
                                                        data:{
                                                            query: "DELETE FROM carga WHERE rcid = "+rcid+"; DELETE FROM registro_carga WHERE rcid = " + rcid
                                                        }
                                                    });
                                                    var trackingsin = tdata[cant][0];
                                                    t.row(cant).remove().draw(false);
                                                    document.getElementById("libras").innerHTML = "Libras: " + calcularLibras();
                                                    document.getElementById("paquetes").innerHTML = "Paquetes: " + t.rows().data().length;
                                                    invent.search("");
                                                    invent.draw(false);
                                                    bootbox.alert("Se ha removido de la tabla el paquete que provocó el problema (el paquete con #tracking = '"+trackingsin+"').");
                                                }
                                            }
                                        }
                                    });
                                }
                            }
                            else{
                                var rcid = res.split("@")[0];
                                if (uids.length != 0){
                                    var arreglo = new Array();
                                    for (var j = 0; j < tdata.length; j++){
                                        var uidisito = tdata[j][1].toUpperCase();
                                        if (uids.indexOf(uidisito) != -1) {
                                            if (uidisito in arreglo)
                                                arreglo[uidisito] += 1;
                                            else
                                                arreglo[uidisito] = 1;
                                        }
                                    }

                                    for (var i = 0; i < uids.length; i++){
                                        if (uids[i] in arreglo){
                                            var sete = "plan = (plan-"+arreglo[uids[i]]+")";
                                            var wher = "uid = '"+uids[i]+"' AND estado IS NULL AND LENGTH(plan) < 3 AND LENGTH(plan) > 0;";
                                            var j = i;
                                            $.ajax({
                                                url: "db/DBsetPaquete.php",
                                                type: "POST",
                                                data: {
                                                    set: sete,
                                                    where: wher
                                                },
                                                cache: false,
                                                success: function(res){
                                                    if (res.includes("ERROR")){
                                                        bootbox.alert("Ocurrió un error al consultar la base de datos. Se recibió el siguiente mensaje: " + res);
                                                    }
                                                    else if (Number(res) > 0){
                                                        var quer = "UPDATE carga C, (SELECT plan FROM carga WHERE uid = '" + uids[j] + "' AND estado IS NULL AND LENGTH(plan) < 3 AND LENGTH(plan) > 0 ORDER BY plan LIMIT 1) i SET C.plan = i.plan WHERE estado IS NULL AND uid = '" + uids[j] + "' AND rcid = '" + rcid + "'";
                                                        $.ajax({
                                                            url: "db/DBexecQuery.php",
                                                            type: "POST",
                                                            data: {
                                                                query: quer
                                                            },
                                                            cache: false,
                                                            success: function(res){
                                                            }
                                                        });
                                                    }
                                                }
                                            });
                                        }
                                    }
                                }

                                var fec = res.split("@")[1].split(" ")[0].split("-");
                                var hora = res.split("@")[1].split(" ")[1].split(":");
                                var h = hora[0];
                                var m = hora[1];
                                if (m < 10 && m.length == 1)
                                    m = "0"+m;
                                var apm = "PM";
                                if (h > 12)
                                    h = h-12;
                                else if (h < 12){
                                    if (h == 0)
                                        h = 12;
                                    apm = "AM";
                                }
                                bootbox.alert({
                                    title: "¡Carga Ingresada!",
                                    message: "Los paquetes fueron ingresados al sistema de inventario bajo el registro de carga #" + rcid + " con fecha " + fec[2] + "/" + fec[1] + "/" + fec[0] + " a las " + h + ":" + m + " " + apm + ".",
                                    size: 'medium',
                                    backdrop: true,
                                    callback: function(res){
                                        t.clear().draw(false);
                                        document.getElementById("libras").innerHTML = "Libras: 0";
                                        document.getElementById("paquetes").innerHTML = "Paquetes: 0";
                                        switchContent(2);
                                        invent.search("");
                                        invent.draw(false);
                                        corres = 0;
                                    }
                                });
                            }
                        }
                    });
                }
            }
        });
    }

    function checkTrackingExists(track){
        var data = $("#tablaNuevaCarga").DataTable().rows().data().toArray();
        for (var i = 0; i < data.length; i++){
            if (data[i][0].split(">")[1].split("<")[0].toUpperCase() == track.toUpperCase()){
                bootbox.alert({
                    message: "Ya ha ingresado un paquete con este número de tracking en el actual registro de carga.",
                    size: 'small',
                    backdrop: true
                });
                return;
            }
        }

        $.ajax({
            url: "db/DBgetPaquete.php",
            type: "POST",
            data: {
                select: "COUNT(tracking) AS cant",
                where: "tracking = '"+track+"'"
            },
            cache: false,
            success: function(arr) {
                var row = JSON.parse(arr.replace("[","").replace("]",""));
                if (row.cant != 0){
                    bootbox.alert({
                        message: "Ya existe un paquete registrado con este número de tracking, asegúrese de haber ingresado bien el dato. ",
                        size: 'small',
                        backdrop: true
                    });
                }
            }
        });
    }

    function getUserName(id){
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
                    document.getElementById("spanID").style.display="inline";
                    setTimeout(function() {
                    $('#spanID').fadeOut('slow');
                    }, 3000);
                }
                else if (name != " ")
                    document.getElementById("uname").value=name;
            },
            error: function(){
                if (arreglo.indexOf(id) == -1 && name.replace(/\s/g,'').length == 0){
                    document.getElementById("spanID").style.display="inline";
                    setTimeout(function() {
                    $('#spanID').fadeOut('slow');
                    }, 3000);
                }
            }
        });
    }

</script>