
const facturaImage = fm => {
    return `<hr><img class="factura-image" src="data:${fm.image_type};base64, ${fm.image}" />`;
};

$(document).ready( function () {
    var table = $('#facturas').DataTable({
        "retrieve": true,
        "select": true,
        "responsive": false,
        "scrollY": "500px",
        "scrollCollapse": true,
        "oSearch": {"sSearch": "Pendiente "},
        "paging": false,
        "fixedColumns": true,
        "language": {
            "lengthMenu": "Mostrando _MENU_ facturas por página",
            "search": "Buscar:",
            "zeroRecords": "No hay facturas que coincidan con la búsqueda",
            "info": "Mostrando facturas del _START_ al _END_ de _TOTAL_ facturas totales.",
            "infoEmpty": "No se encontraron facturas.",
            "infoFiltered": "(Filtrando sobre _MAX_ facturas)",
            "paginate": {
                "first":      "Primera",
                "last":       "Última",
                "next":       "Siguiente",
                "previous":   "Anterior"
            },
            "loadingRecords": "Cargando Facturas...",
            "processing":     "Procesando...",
        },
        "order": [[4, 'asc']],
        "columnDefs": [
            {
                "targets": [0, 2, 6],
                "orderable": false
            }
        ],
        /*"footerCallback": function ( row, data, start, end, display ) {
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

        }*/
    });

    $("#facturas tbody").on("click", ".seleccionado", function () {
        $(this).closest('tr').toggleClass("selected");
        table.draw(false);
        if (table.rows('.selected').data().toArray().length === 0)
            document.getElementById("divFacturaOpciones").style.visibility = "hidden";
        else document.getElementById("divFacturaOpciones").style.visibility= "visible";
    });

    let showFacturaDialog = factura => {

        let status = factura.pendiente === '1' ? 'fa fa-times fa-1x fa-lg' : 'fa fa-check fa-1x fa-lg';
        let statusColor = factura.pendiente === '1' ? 'orange' : 'lime';

        let images = '';
        factura.images.map(img => {
            images += facturaImage(img);
        });

        let content =
            `<div class="container-flex">
                <div>
                    <b>Enviada: </b><i style="color: ${statusColor}" class='${status}'></i><br>
                    <b>Id Cliente:</b> ${factura.uid}<br>
                    <b>Tracking:</b> ${factura.tracking}<br>
                    <b>Monto:</b> US$ ${factura.amount}<br>
                    <b>Descripción:</b> ${factura.description}
                </div>
                <div class="factura-content">${images}</div>
            </div>`;

        bootbox.dialog({
            title: "Detalles de factura de " + factura.uname + ":",
            message: `${content}`
        });
    };

    $("#facturas tbody").on("click", "div.factura-data", function () {
        // var index = table.row($(this).closest('tr')).index();
        let factura = $(this).data('factura');

        $.ajax({
            url: "db/DBgetFacturasImage.php",
            data: {
                facturasId : [factura.id]
            },
            type: "POST",
        }).then(response => {
            if (response.data && response.data[factura.id]) {
                factura.images = response.data[factura.id];
                showFacturaDialog(factura);
            }
            else if (response.message) {
                bootbox.alert(response.message);
            }
            else {
                bootbox.alert("No se encontraron capturas de pantalla asociadas.");
            }
        },
        () => bootbox.alert("Ocurrió un error al conectarse a la base de datos."));
    });
});

function loadFacturas(){
    let table = $('#facturas').DataTable();
    table.clear();
    $.ajax({
        url: "db/DBgetFacturas.php",
        type: 'GET',
        dataType: 'json',
        contentType: "application/json; charset=utf-8",
        cache: false,
        success: function(response){
            if (response.data.length === 0){
                bootbox.alert("No se encontraron facturas.");
            }
            else {
                for (let i = 0; i < response.data.length; i++){
                    let factura = response.data[i];
                    let enviado = 'Enviado', color = 'lime', icon = 'fa-paper-plane';
                    if (factura['pendiente'] === '1'){
                        enviado = 'Pendiente';
                        color = 'gold';
                        icon = 'fa-clock';
                    }

                    table.row.add([
                        `<div class='seleccionado' title="${enviado}" style='color: ${color}; align-self: center; text-align: center;'><i class='fa ${icon} fa-2x fa-lg'></i><small style='display:none;'>${enviado}</small></div>`,
                        `<h6 class='seleccionado'>${factura['date_created']}<span style="display: none">${enviado}</span></h6>`,
                        `<h6 class='seleccionado'>${factura['tracking']}</h6>`,
                        `<h6 class='seleccionado'>${factura['uid']}</h6>`,
                        `<h6 class='seleccionado'>${factura['uname']}</h6>`,
                        `<h6 class='seleccionado'>US$ ${factura['amount']}</h6>`,
                        `<div style='cursor:pointer; text-align: center; color: darkslategray' class='factura-data' data-factura='${JSON.stringify(factura)}'><i class='fa fa-eye fa-2x fa-lg'></div>`
                    ]);
                }
                table.draw(false);
                table.columns.adjust().responsive.recalc();
            }
        },
        error: function(){
            bootbox.alert("Ocurrió un problema al intentar conectarse al servidor.");
        }
    });
}

function generarPDF()
{
    let table = $("#facturas").DataTable();
    let selectedRows = table.rows(".selected").data().toArray();
    let facturas = {};
    selectedRows.map(row => {
        let factura = $(row[6]).data('factura');
        facturas[factura.id] = {
            date_created: factura.date_created,
            clientId: factura.uid,
            tracking: factura.tracking,
            description: factura.description,
            amount: factura.amount,
            images: []
        };
    });

    let ids = Object.keys(facturas);

    $.ajax({
        url: "db/DBgetFacturasImage.php",
        data: {
            facturasId : ids
        },
        type: "POST",
        cache: false,
        success: function(response){
            if (response.data){
                let images = response.data;
                if (images.length !== ids.length){
                    // TODO: Show alert for missing factura images
                }

                Object.keys(images).map(id => {
                    images[id].map(fm => {
                        facturas[id].images.push({
                            image: fm.image,
                            image_type: fm.image_type
                        });
                    });
                });

                $.ajax({
                    url: 'utils/facturasPDF.php',
                    type: 'post',
                    data: {
                        facturas: facturas
                    },
                    cache: false,
                    success: function (res, status, xhr) {
                        var filename = "";
                        var disposition = xhr.getResponseHeader('Content-Disposition');
                        if (disposition && disposition.indexOf('attachment') !== -1) {
                            var filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
                            var matches = filenameRegex.exec(disposition);
                            if (matches != null && matches[1]) {
                                filename = matches[1].replace(/['"]/g, '');
                                window.open('/reportesFacturas/'+filename);
                            }

                            Swal.fire({
                                title: 'Documento Creado',
                                text: 'PDF creado exitosamente',
                                type: 'success',
                                showCancelButton: true,
                                allowEscapeKey : false,
                                allowOutsideClick: false,
                                focusConfirm: false,
                                confirmButtonText: 'Continuar',
                                cancelButtonText: 'Marcar facturas como "Enviadas"',
                                cancelButtonColor: 'limegreen',
                            }).then(res => {
                                if (!res.value){
                                    setearPendientes(Object.keys(facturas));
                                }
                            });

                            table.rows(".selected").nodes().to$().removeClass("selected");
                            table.draw(false);
                            document.getElementById("divFacturaOpciones").style.visibility = "hidden";
                        }
                        else {
                            bootbox.alert("No se pudo abrir el PDF de facturas. Por favor contacta al administrador para verificar si el archivo fue creado exitosamente.");
                        }
                    },
                    error: function() {
                        bootbox.alert("Ocurrió un error al intentar generar el PDF de facturas.");
                    }
                });
            }
            else if (response.message) {
                bootbox.alert(response.message);
            }
            else {
                bootbox.alert("No se encontraron capturas de pantalla asociadas.");
            }
        },
        error: function() {
            bootbox.alert("Ocurrió un error al conectarse a la base de datos.");
        }
    });
}

function setearPendientes(ids) {
    let where = '';
    ids.map(id => {
       where += id + ',';
    });
    where = where.substr(0, where.length -1);

    $.ajax({
        url: 'db/DBsetFacturas.php',
        type: 'post',
        data: {
            set: 'pendiente = 0',
            where: 'id IN ('+  where +')'
        },
        cache: false,
        success: function (res) {
            if (res.success){
                loadFacturas();
                Swal.fire({
                    title: 'Facturas actualizadas',
                    text: 'La tabla de facturas ha sido actualizada',
                    type: 'success',
                    focusConfirm: true,
                    confirmButtonText: 'Ok',
                    confirmButtonClass: 'btn-success'
                });
            }
            else {
                Swal.fire({
                    title: 'Error',
                    text: 'Ocurrió un error, no se pudo actualizar el estado de las facturas',
                    type: 'error',
                    focusConfirm: true,
                    confirmButtonText: 'Ok',
                    confirmButtonClass: 'btn-success'
                });
            }
        },
        error: () => bootbox.alert("Ocurrió un error al conectarse a la base de datos.")
    });
}

function eliminarFacturasConfirmado() {
    let table = $("#facturas").DataTable();
    let selectedRows = table.rows(".selected").data().toArray();
    let facturas = [];
    selectedRows.map(row => {
        let factura = $(row[6]).data('factura');
        facturas.push(factura.id);
    });

    $.ajax({
        url: "db/DBdeleteFacturas.php",
        data: {
            where: `id IN (${facturas.join(', ')})`
        },
        type: "POST",
        cache: false,
        success: function (res) {
            if (res.success){
                loadFacturas();
                Swal.fire({
                    title: 'Facturas eliminadas',
                    text: 'La tabla de facturas ha sido actualizada',
                    type: 'success',
                    focusConfirm: true,
                    confirmButtonText: 'Ok',
                    confirmButtonClass: 'btn-success'
                });

                table.rows(".selected").nodes().to$().removeClass("selected");
                table.draw(false);
                document.getElementById("divFacturaOpciones").style.visibility = "hidden";
            }
            else {
                Swal.fire({
                    title: 'Error',
                    text: 'Ocurrió un error, no se pudieron eliminar las facturas',
                    type: 'error',
                    focusConfirm: true,
                    confirmButtonText: 'Ok',
                    confirmButtonClass: 'btn-success'
                });
            }
        },
        error: () => bootbox.alert("Ocurrió un error al conectarse a la base de datos.")
    });
}

function eliminarFacturas()
{
    Swal.fire({
        title: '¡Atención!',
        text: '¿Seguro que deseas eliminar las facturas seleccionadas?',
        type: 'warning',
        showCancelButton: true,
        allowEscapeKey : false,
        allowOutsideClick: false,
        focusConfirm: false,
        confirmButtonText: 'Continuar',
        cancelButtonText: 'Cancelar',
    }).then(res => {
        if (res.value){
            eliminarFacturasConfirmado();
        }
    });
}