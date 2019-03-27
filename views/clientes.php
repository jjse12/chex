<link href="css/jquery.dataTables.css" rel="stylesheet" type="text/css">
<link href="css/dataTables.tableTools.css" rel="stylesheet" type="text/css">
<link href="css/dataTables.bootstrap.css" rel="stylesheet" type="text/css">
<link href="css/dataTables.colVis.css" rel="stylesheet" type="text/css">
<link href="css/dataTables.responsive.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="js/jquery.dataTables.js"></script>
<script type="text/javascript" src="js/dataTables.tableTools.js"></script>
<script type="text/javascript" src="js/dataTables.bootstrap.js"></script>
<script type="text/javascript" src="js/dataTables.colVis.js"></script>
<script type="text/javascript" src="js/dataTables.responsive.js"></script>

<script>
    $(document).ready( function () {

        var table = $('#clientes').DataTable({
            "bSort" : false,
            "retrieve": true,
            "dom": 'CT<"clear">lfrtip',
            "tableTools": {
                "sSwfPath": "./swf/copy_csv_xls_pdf.swf"
            },
            "select": true,
            "responsive": true,
            "scrollY": "500px",
            "scrollCollapse": true,
            "paging": true,
            "language": {
                "lengthMenu": "Mostrando _MENU_ clientes por página",
                "search": "Buscar:",
                "zeroRecords": "No hay clientes que coincidan con la búsqueda",
                "info": "Mostrando clientes del _START_ al _END_ de _TOTAL_ clientes totales.",
                "infoEmpty": "No se encontraron clientes.",
                "infoFiltered": "(Filtrando sobre _MAX_ clientes)",
                "paginate": {
                    "first":      "Primera",
                    "last":       "Última",
                    "next":       "Siguiente",
                    "previous":   "Anterior"
                },
                "loadingRecords": "Cargando clientes...",
                "processing":     "Procesando...",
            },
        });

        $("#clientes tbody").on("click", "div.tarifa", function () {
            var index = table.row($(this).closest('tr')).index();
            var arr = table.rows(index).data().toArray();
            var cliente = arr[0][1] + " " + arr[0][2];
            var clienteID = arr[0][0];
            bootbox.prompt({
                title: "Nueva tarifa para " + cliente + " (en Quetzales)",
                inputType: 'number',
                callback: function (result) {
                    if (result){
                        $.ajax({
                                url: "db/DBsetCliente.php",
                                type: "POST",
                                data: {
                                    set: "tarifa = " + result,
                                    where: "cid = '" + clienteID + "'"
                                },
                                cache: false,
                                success: function(res){
                                    if (res == 1){
                                        table.cell(index, 3).data("<div style='cursor:pointer;' class='tarifa'>Q " + result + "</div>",);
                                        table.draw(false);
                                    }
                                    else{
                                        bootbox.alert("No se pudo efectuar el cambio de tarifa, verifique que haya ingresado un valor correcto.");
                                        return false;
                                    }
                                },
                                error: function() {
                                    bootbox.alert("Ocurrió un error al conectarse a la base de datos.");
                                }
                            });
                    }
                    else{
                        bootbox.alert("No se pudo efectuar el cambio de tarifa, verifique que haya ingresado un valor correcto.");
                    }
                }
            });
        });
    } );
</script>

<br><br>
<br><br>

<div class="row" id="divTablaClientes">
    <table id="clientes" class="display" width="100%" cellspacing="0" style="width: 100%;">
        <thead>
            <tr>
                <th class="dt-head-center"><h5 style="color:black">ID</h5></th>
                <th class="dt-head-center"><h5 style="color:black">Nombre</h5></th>
                <th class="dt-head-center"><h5 style="color:black">Apellido</h5></th>
                <th class="dt-head-center"><h5 style="color:black">Tarifa</h5></th>
                <th class="dt-head-center"><h5 style="color:black">Email</h5></th>
                <th class="dt-head-center"><h5 style="color:black">Celular</h5></th>
                <th class="dt-head-center"><h5 style="color:black">Teléfono</h5></th>
                <th class="dt-head-center"><h5 style="color:black">Dirección</h5></th>
                <th class="dt-head-center"><h5 style="color:black">Género</h5></th>
                <th class="dt-head-center"><h5 style="color:black">Cumpleaños</h5></th>
                <th class="dt-head-center"><h5 style="color:black">Notas</h5></th>
                <th class="dt-head-center"><h5 style="color:black">Fecha Registro</h5></th>
                <th class="dt-head-center"><h5 style="color:black"></h5></th>
                <th class="dt-head-center"><h5 style="color:black"></h5></th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
            </tr>
        </tfoot>
        <tbody>
        </tbody>
    </table>
</div>

<script>

    function initClientesChex(){
        $.ajax({
            url: "db/DBgetAllClientes.php",
            cache: false,
        })
        .then(res => {
            var table = $('#clientes').DataTable();
            table.clear();
            var rows = res.data;

            for (var i = 0; i < rows.length; i++){
                table.row.add([
                    rows[i]["cid"],
                    rows[i]["nombre"],
                    rows[i]["apellido"],
                    "<div style='cursor:pointer;' class='tarifa'>Q " + rows[i]["tarifa"] + "</div>",
                    rows[i]["email"],
                    rows[i]["celular"],
                    rows[i]["telefono"],
                    rows[i]["direccion"],
                    rows[i]["genero"],
                    rows[i]["cumple"],
                    rows[i]["comentario"],
                    rows[i]["fecha_registro"],
                    "<img src='images/edit.png' onclick=\"modifyUserData('" + rows[i]["cid"]+ "')\" />",
                    "<img src='images/mail.png' onclick=\"messageUser('" + rows[i]["cid"]+ "')\" />"
                ]);
            }
            table.draw(false);
            table.columns.adjust().responsive.recalc();
        },
        () => bootbox.alert("Ocurrió un problema al intentar conectarse al servidor."));
    }

    function modifyUserData(myUserData) {
        $.ajax({
            url: "db/getDBData.php",
            type: "POST",
            data: {
                cid: myUserData
            },
            cache: false,
            success: function(myData) {
                bootbox.dialog({
                title: "Modificar Información Usuario: " + myUserData,
                message:myData
                });
            },
            error: function() {
                bootbox.dialog({
                title: "Modificar Información Usuario: " + myUserData,
                message:"Error, por favor volver a intentar!"
                });
            }
        });
    };

    function messageUser(myUserData) {
        $.ajax({
            url: "views/messageUser.php",
            type: "POST",
            data: {
                cid: myUserData
            },
            cache: false,
            success: function(myData) {
                bootbox.dialog({
                title: "Enviar Mensaje de Recepción de Paquete al usuario: " + myUserData,
                message:myData
                });
            },
            error: function() {
                bootbox.dialog({
                title: "Enviar Mensaje de Recepción de Paquete al usuario: " + myUserData,
                message:"Error, por favor volver a intentar!"
                });
            }
        });
    };
</script>