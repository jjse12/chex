Number.prototype.toMoney = function toMoney() {
    return "US$ " + this.toFixed(2);
};

const facturaImage = fm => {
    return `<hr><img class="factura-image" src="data:${fm.image_type};base64, ${fm.image}" />`;
};

const couriersSelectBox = selectedCourier => {
    let select = `<select id="factura-courier" data-original="${selectedCourier}" name="factura-courier" class="disabable form-control"><option value=""></option>`;
    couriers.map(courier => {
        let selected = courier === selectedCourier ? ' selected' : '';
        select += `<option value="${courier}" ${selected}>${courier}</option>`;
    });
    select += '</select>';
    return select;
};

const signersSelectBox = selectedSigner => {
    let select = `<select id="factura-signer" data-original="${selectedSigner}" name="factura-signer" class="disabable form-control"><option value=""></option>`;
    signers.map(signer => {
        let selected = signer === selectedSigner ? ' selected' : '';
        select += `<option value="${signer}" ${selected}>${signer}</option>`;
    });
    select += '</select>';
    return select;
};

function resetLogisticaInputsToOriginals() {
    let $dateDelivery = $('#factura-date-delivered');
    let $courier = $('#factura-courier');
    let $signer = $('#factura-signer');
    let $miamiReceived = $('#factura-miami-received');
    let $dateReceived = $('#factura-date-received');
    let $comment = $('#factura-comment');

    $dateDelivery.val($dateDelivery.data('original'));
    $courier.val($courier.data('original'));
    $signer.val($signer.data('original'));
    let received = $miamiReceived.data('original');
    let $divDateReceived = $('#divDateReceived');
    $miamiReceived.prop('checked', received);
    $dateReceived.val($dateReceived.data('original'));
    if (received && !$divDateReceived.hasClass('in') || !received && $divDateReceived.hasClass('in')){
        $miamiReceived.trigger('click');
    }
    $comment.val($comment.data('original'));
}

function toggleLogistica() {
    let divLogisticaContent = $('#divFacturaLogisticaContent');
    let btnToggleLogistica = $('#btnToggleLogistica');
    let btnUpdateLogistica = $('.btnUpdateFacturaLogistica');

    // HABILITAR FORMULARIO
    if (divLogisticaContent.hasClass('disabled-element')){
        $('#divFacturaLogisticaContent :input').removeAttr('disabled');
        divLogisticaContent.removeClass('disabled-element');
        btnToggleLogistica.removeClass('btn-primary');
        btnToggleLogistica.addClass('btn-danger');
        btnToggleLogistica.text('Cancelar');
        btnUpdateLogistica.show();
    }
    // DESHABILITAR FORMULARIO
    else {
        resetLogisticaInputsToOriginals();
        $('#divFacturaLogisticaContent :input').attr('disabled', true);
        divLogisticaContent.addClass('disabled-element');
        btnToggleLogistica.removeClass('btn-danger');
        btnToggleLogistica.addClass('btn-primary');
        btnToggleLogistica.text('Modificar');
        btnUpdateLogistica.hide();
    }
}

function activateLogisticaDatePickers(logistica) {
    if (logistica !== null){
        let dateDelivered = $("#factura-date-delivered");
        dateDelivered.datepicker({
            showOtherMonths: true,
            selectOtherMonths: true,
            showAnim: "slideDown",
        });
        let dateReceived = $("#factura-date-received");
        dateReceived.datepicker({
            showOtherMonths: true,
            selectOtherMonths: true,
            showAnim: "slideDown",
        });
    }
}

const facturaLogistica = (logistica, factura) => {
    let content = '';
    if (logistica === null){
        content = `
            <div class="text-center">
                <h5>Seguimiento de Paquete en Bodega</h5>
            </div>
            <br>
            <div class="text-center">
                ¡Aún no existe registro!&nbsp;&nbsp;&nbsp;&nbsp;
                <button data-factura-id='${JSON.stringify(factura)}' class='btn btn-success btn-sm btnCreateFacturaLogistica'>Crear Registro</button></>
            </div>
            <br>
        `;
    }
    else {
        let received = logistica.miami_received === 1;
        content = `
            <div class="text-center">
                <h5>Seguimiento de Paquete en Bodega</h5>
            </div>
            <div id="divFacturaLogisticaContent">
                <div class="form-row">
                    <div class="form-group">
                        <label for="factura-date-delivered" style='color: #696969;'>Fecha de Delivery :</label>
                        <input type="text" data-original="${logistica.date_delivered}" id="factura-date-delivered"
                                class="disabable form-control text-center" value="${logistica.date_delivered}">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="factura-courier" style="color: #696969">Courier :</label>
                        ${couriersSelectBox(logistica.courier)}
                    </div>
                    <div class="form-group col-md-6">
                        <label for="factura-signer" style="color: #696969">Firmado por :</label>
                        ${signersSelectBox(logistica.signer)}
                    </div>
                </div>
                <div class="form-row text-center">
                    <div class="form-group">
                        <label class="form-check-label" for="factura-miami-received" style="color: #696969">Recibido en Miami :&nbsp;&nbsp;&nbsp;&nbsp;</label>
                        <input type="checkbox" class="disabable form-check-input" data-original="${received}" 
                               id="factura-miami-received" data-toggle="collapse" data-target="#divDateReceived" 
                               aria-expanded="false" aria-controls="divDateReceived" ${received ? 'checked' : ''}>
                    </div>
                </div>
                <div id="divDateReceived" class="form-row collapse ${logistica.miami_received === 1 ? 
                    'in" aria-expanded="true' : '" aria-expanded="false" style="height: 0px;'}">
                    <div class="form-group">
                        <label for="factura-date-received" style='color: #696969;'>Fecha de Recibido :</label>
                        <input type="text" data-original="${logistica.date_received}" id="factura-date-received" 
                                class="disabable form-control text-center" value="${logistica.date_received}">
                    </div>
                </div>
                <div class="form-group">
                    <label for="factura-comment" style='color: #696969;'>Comentario :</label>
                    <textarea class="disabable form-control" id="factura-comment" maxlength="512"
                            data-original="${logistica.comment}" placeholder="Ingresa un comentario">${logistica.comment}
                    </textarea>
                </div>
            </div>
            <div class="text-center">
                <button class="btn btn-sm btn-primary" id="btnToggleLogistica" onclick="toggleLogistica()">Modificar</button>
                <button class="btn btn-sm btn-success btnUpdateFacturaLogistica" data-factura-id='${JSON.stringify(factura)}'>Guardar</button>
            </div>
            <br>
        `;
    }

    return content;
};

function toggleNewSeguimiento() {
    let $btnNewSeguimiento = $('#btnNewSeguimiento');
    let $btnCreateSeguimiento = $('button.btnCreateSeguimiento');
    if ($btnNewSeguimiento.hasClass('btn-success')){
        $btnNewSeguimiento.removeClass('btn-success');
        $btnNewSeguimiento.addClass('btn-danger');
        $btnNewSeguimiento.text('Cancelar');
        $btnCreateSeguimiento.show();
    }
    else if ($btnNewSeguimiento.hasClass('btn-danger')){
        $btnNewSeguimiento.removeClass('btn-danger');
        $btnNewSeguimiento.addClass('btn-success');
        $btnNewSeguimiento.text('Nueva Nota de Seguimiento');
        $btnCreateSeguimiento.hide();
    }
}

const seguimientoNote = seguimiento => {
    let date = moment(seguimiento.date_created);
    date = date.format('[El&nbsp;&nbsp;]DD/MM/YYYY[&nbsp;&nbsp;a&nbsp;&nbsp;las&nbsp;&nbsp;]hh:mm A');

    let note = `
        <div class="seguimiento-register">
            <div class="row">
                <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 text-left">
                    &nbsp;&nbsp;&nbsp;${seguimiento.creator} :
                </div>
                <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 text-right">
                    ${date}&nbsp;&nbsp;&nbsp;
                </div>
            </div>
            <div class="row" style="margin-top: 8px !important;">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <textarea class="form-control" disabled>${seguimiento.note}</textarea>
                </div>
            </div>
        </div>
    `;
    return note;
};

const facturaSeguimiento = (seguimiento, factura) => {
    let seguimientosContent = '<div class="text-center">- Sin notas de seguimiento de cliente -</div>';
    if (seguimiento.length){
        seguimientosContent = `<div class="text-center"><b>- ${seguimiento.length} ${seguimiento.length === 1 ? 'Nota' : 'Notas'} de Seguimiento -</b>`;
        seguimiento.map(seg => {
            seguimientosContent += seguimientoNote(seg);
        });
        seguimientosContent += '</div>';
    }

    let content = `
        <div class="text-center">
            <h5>Seguimiento de Cliente</h5>
        </div>
        <br>
        <div class="text-center">
            <button class="btn btn-sm btn-success" id="btnNewSeguimiento" onclick="toggleNewSeguimiento()" data-toggle="collapse" 
                    data-target="#divNewSeguimiento" aria-expanded="false" aria-controls="divDateReceived">
                Nueva Nota de Seguimiento
            </button>
            <button class="btn btn-sm btn-success btnCreateSeguimiento" style="display: none;" data-factura='${JSON.stringify(factura)}'>Guardar</button>
        </div>
        <div class="collapse" id="divNewSeguimiento">
            <br>
            <div class="form-group">
                <textarea class="disabable form-control" id="txt-new-seguimiento" maxlength="512" placeholder="Ingresa la nota de seguimiento..."></textarea>
            </div>
        </div>
        <hr>
        ${seguimientosContent}
        <br>
    `;

    return content;
};



const facturaDetails = (details) => {
    let logistica = facturaLogistica(details.logistica, details.factura);
    let seguimiento = facturaSeguimiento(details.seguimiento, details.factura);
    let content =
        `<div id="divFacturaDetails" class="row">
            <div class="col-md-5" id="divFacturaLogistica">${logistica}</div>
            <div class="col-md-7 fill" id="divFacturaSeguimiento">${seguimiento}</div>
        </div>`;
    return content;
};

function loadFacturaDetailsAndShowDialog(factura) {
    $.ajax({
        url: 'db/DBgetFacturaDetails.php',
        data: {
            facturaId : factura.id
        },
        type: "POST",
    })
    .then(response => {
        if (response.data !== null) {
            let details = response.data;
            details.factura = factura;
            let content = facturaDetails(details);
            bootbox.dialog({
                title: `Detalles Factura tracking #${factura.tracking}`,
                size: 'large',
                message: `${content}`
            });

            if (details.logistica !== null){
                toggleLogistica();
                activateLogisticaDatePickers(details.logistica);
            }
            $('.modal-body').css({paddingTop: 0, paddingBottom: 0});
        }
        else if (response.message) {
            bootbox.alert(response.message);
        }
        else {
            bootbox.alert("No se encontraron capturas de pantalla asociadas.");
        }
    },
    () => bootbox.alert("Ocurrió un error al conectarse a la base de datos."));
}

function loadFacturas(){
    let table = $('#facturas').DataTable();
    table.clear();
    $.ajax({
        url: 'db/DBgetFacturas.php',
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

                    let date = factura.date_delivered ? factura.date_delivered : 'Sin Especificar';

                    table.row.add([
                        `<div class='seleccionado' title="${enviado}" style='color: ${color}; align-self: center; text-align: center;'><i class='fa ${icon} fa-2x fa-lg'></i><small style='display:none;'>${enviado}</small></div>`,
                        `<h6 class='seleccionado'>${date}<span style="display: none">${enviado}</span></h6>`,
                        `<h6 class='seleccionado'>${factura.tracking}</h6>`,
                        `<h6 class='seleccionado'>${factura.uid}</h6>`,
                        `<h6 class='seleccionado'>${factura.uname}</h6>`,
                        `<h6 class='seleccionado'>${Number(factura.amount).toMoney()}</h6>`,
                        `<div style='cursor:pointer; text-align: center; color: darkslategray' class='factura-see-image' data-factura='${JSON.stringify(factura)}'><i class='fa fa-eye fa-2x fa-lg'></div>`,
                        `<div style='cursor:pointer; text-align: center; color: greenyellow' class='factura-see-details' data-factura='${JSON.stringify(factura)}'><i class="fas fa-address-card fa-2x fa-lg"></i></div>`
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
        url: 'db/DBgetFacturasImage.php',
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
                    url: 'facturasPDF.php',
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
                                window.open('reportesFacturas/'+filename);
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
    let where = ids.join(', ');

    $.ajax({
        url: 'db/DBsetFactura.php',
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
        url: 'db/DBdeleteFacturas.php',
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

    getFacturaFieldEditDialog = (value, id, field, extra) => {
        return `
            <div class='row' style='background-color: #dadada'>
                <div class='col-lg-12 col-md-12 col-sm-12 col-xs-12'>
                    <p style='color: black'>Ingresa el nuevo valor para el campo <b>${field}</b>.</p>
                    <div class='control-group form-group col-sm-offset-3 col-md-offset-3 col-lg-offset-3 col-sm-6 col-md-6 col-lg-6 col-xs-12'>
                        <div class='controls'>
                            <input align='middle' style='text-align:center; width: 100%;' 
                                    placeholder="Nuevo valor" id='${field+'-'+id}' value="${value}" ${extra}>
                        </div>
                    </div>
                </div>
            </div>`;
    };

    $(document).on('click', 'span.factura-editable', ev => {
        let span = $(ev.target);
        let value = span.text();
        let id = span.data('id');
        let field = span.data('field');
        let column = span.data('column');

        let extra = '';
        if (column === 'amount'){
            extra = 'type="number" step="0.01" onKeyPress="return integersonly(this, event)"';
            value = value.replace('US$ ', '');
        }

        bootbox.dialog({
            title: `Modificar Factura`,
            message: getFacturaFieldEditDialog(value, id, field, extra),
            buttons: {
                regresar: {
                    label: 'Cancelar',
                    className: "btn-default alinear-izquierda",
                },
                confirm: {
                    label: 'Guardar',
                    className: "btn-success alinear-derecha",
                    callback: () => {
                        let input = $(`#${field+'-'+id}`);
                        let newValue = input.val();
                        if (newValue.length > 0 && newValue !== value) {
                            let set = column + ' = ' + (column === 'amount' ? newValue : `'${newValue}'`);
                            $.ajax({
                                url: 'db/DBsetFactura.php',
                                type: 'post',
                                data: {
                                    set: set,
                                    where: `id = ${id}`
                                },
                                cache: false,
                                success: function (res) {
                                    if (res.success){
                                        if (column === 'amount'){
                                            span.text(Number(newValue).toMoney());
                                        }
                                        else {
                                            span.text(input.val());
                                        }
                                        loadFacturas();
                                        Swal.fire({
                                            title: 'Factura actualizada',
                                            type: 'success',
                                            timer: 2000,
                                            showConfirmButton: false
                                        });
                                    }
                                    else {
                                        Swal.fire({
                                            title: 'Error',
                                            text: 'Ocurrió un error, no se pudo actualizar la factura',
                                            type: 'error',
                                            focusConfirm: true,
                                            confirmButtonText: 'Ok',
                                            confirmButtonClass: 'btn-success'
                                        });
                                    }
                                },
                                error: () => bootbox.alert("Ocurrió un error al conectarse a la base de datos.")
                            });

                            return;
                        }

                        input.focus();
                        return false;
                    }
                }
            }
        });

    });

    let showFacturaDialog = factura => {

        let status = factura.pendiente === '1' ? 'fa fa-times fa-1x fa-lg' : 'fa fa-check fa-1x fa-lg';
        let statusColor = factura.pendiente === '1' ? 'orange' : 'lime';

        let images = '';
        factura.images.map(img => {
            images += facturaImage(img);
        });

        let date = moment(factura.date_created);
        date = date.format('LLL');

        let content =
            `<div class="container-flex">
                <div>
                    <b>Enviada: </b><i style="color: ${statusColor}" class='${status}'></i><br>
                    <b>Fecha de Creación: </b><span /*class="factura-editable" data-column="date_created" data-id="${factura.id}" data-field="Fecha de Creación"*/>${date}</span><br>
                    <b>Id Cliente:</b> <span /*class="factura-editable" data-column="uid" data-id="${factura.id}" data-field="Id Cliente"*/>${factura.uid}</span><br>
                    <b>Tracking:</b> <span class="factura-editable" data-column="tracking" data-id="${factura.id}" data-field="Tracking">${factura.tracking}</span><br>
                    <b>Monto:</b> <span class="factura-editable" data-column="amount" data-id="${factura.id}" data-field="Monto">US$ ${factura.amount}</span><br>
                    <b>Descripción:</b> <span class="factura-editable" data-column="description" data-id="${factura.id}" data-field="Descripción">${factura.description}</span>
                </div>
                <div class="factura-content">${images}</div>
            </div>`;

        bootbox.dialog({
            title: "Detalles de factura de " + factura.uname + ":",
            message: `${content}`
        });
    };

    let tableBody = $("#facturas tbody");

    tableBody.on("click", ".seleccionado", function () {
        $(this).closest('tr').toggleClass("selected");
        table.draw(false);
        if (table.rows('.selected').data().toArray().length === 0)
            document.getElementById("divFacturaOpciones").style.visibility = "hidden";
        else document.getElementById("divFacturaOpciones").style.visibility= "visible";
    });

    tableBody.on("click", "div.factura-see-image", function () {
        let factura = $(this).data('factura');
        $.ajax({
            url: 'db/DBgetFacturasImage.php',
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

    tableBody.on("click", "div.factura-see-details", function () {
        let factura = $(this).data('factura');
        loadFacturaDetailsAndShowDialog({id: factura.id, tracking: factura.tracking});
    });

    $(document).on("click", ".btnCreateFacturaLogistica", function () {
        let factura = $(this).data('factura-id');
        let facturaId = factura.id;
        if (!facturaId) {
            bootbox.alert("No se encontró el ID de la factura para crear el registro.");
            return
        }

        $.ajax({
            url: 'db/DBserverInsertFacturaLogistica.php',
            data: {
                facturaId: facturaId
            },
            type: "POST"
        })
        .then(response => {
            if (response.success) {
                if (response.data === true){
                    bootbox.hideAll();
                    loadFacturaDetailsAndShowDialog(factura);
                }
                else {
                    $('#divFacturaLogistica').html(facturaLogistica(response.data, factura));
                    toggleLogistica();
                    activateLogisticaDatePickers(response.data);
                }
            } else if (response.message) {
                bootbox.alert(response.message);
            } else {
                bootbox.alert("No se pudo crear el registro para la factura.");
            }
        },
        () => bootbox.alert("Ocurrió un error al conectarse a la base de datos."));
    });

    $(document).on("click", ".btnUpdateFacturaLogistica", function () {
        let factura = $(this).data('factura-id');
        let facturaId = factura.id;
        if (!facturaId) {
            bootbox.alert("No se encontró el ID de la factura para modificar el registro.");
            return
        }

        let $dateDelivery = $('#factura-date-delivered');
        let $courier = $('#factura-courier');
        let $signer = $('#factura-signer');
        let $miamiReceived = $('#factura-miami-received');
        let received = $miamiReceived.prop('checked');
        let $dateReceived = $('#factura-date-received');
        if (!received){
            $dateReceived.val('');
        }
        let $comment = $('#factura-comment');

        let query = `
            UPDATE factura_logistica 
            SET
                date_delivered = '${$dateDelivery.val()}',
                courier = '${$courier.val()}',
                signer = '${$signer.val()}',
                miami_received = ${received ? 1 : 0},
                date_received = '${$dateReceived.val()}',
                comment = '${$comment.val()}'
            WHERE fid = '${facturaId}';
            `;

        $.ajax({
            url: 'db/DBserverExecQuery.php',
            data: {
                query: query
            },
            type: "POST"
        })
        .then(response => {
            if (response.success && response.data === true){
                Swal.fire({
                    title: 'Datos de factura actualizados',
                    type: 'success',
                    timer: 2000,
                    showConfirmButton: false
                });
                if ($dateDelivery.val() !== $dateDelivery.data('original')){
                    loadFacturas();
                }
                $dateDelivery.data('original', $dateDelivery.val());
                $courier.data('original', $courier.val());
                $signer.data('original', $signer.val());
                $miamiReceived.data('original', received);
                $dateReceived.data('original', $dateReceived.val());
                $comment.data('original', $comment.val());
                toggleLogistica();

            } else if (response.message) {
                bootbox.alert(response.message);
            } else {
                bootbox.alert("No se pudo crear el registro para la factura.");
            }
        },
        () => bootbox.alert("Ocurrió un error al conectarse a la base de datos."));
    });

    $(document).on('click', 'button.btnCreateSeguimiento', function() {
        var factura = $(this).data('factura');
        var facturaId = factura.id;
        var $noteTextArea = $('#txt-new-seguimiento');
        var note = $noteTextArea.val();
        var creator = $('#currentUserRealName').text();
        if (!facturaId) {
            bootbox.alert("No se encontró el ID de la factura para crear la nota de seguimiento.");
            return
        }
        if (!note.length) {
            bootbox.alert("¡El contenido de la nota no puede estar vacío!");
            return
        }

        let closure = () => {
            $.ajax({
                url: 'db/DBserverInsertFacturaSeguimiento.php',
                data: {
                    facturaId: facturaId,
                    note: note,
                    creator: creator
                },
                type: "POST"
            })
            .then(response => {
                if (response.success) {
                    if (response.data === true){
                        Swal.fire({
                            title: 'Nota de Seguimiento Creada',
                            type: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            bootbox.hideAll();
                            loadFacturaDetailsAndShowDialog(factura);
                        });
                    }
                    else {
                        Swal.fire({
                            title: 'Nota de Seguimiento Creada',
                            type: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        });
                        $('#divFacturaSeguimiento').html(facturaSeguimiento(response.data, factura));
                    }
                } else if (response.message) {
                    bootbox.alert(response.message);
                } else {
                    bootbox.alert("No se pudo crear la nota de seguimiento de cliente.");
                }
            },
            () => bootbox.alert("Ocurrió un error al conectarse a la base de datos."));
        };

        if (!creator.length) {
            bootbox.prompt({
                title: "Ingrese su nombre:",
                size: "small",
                inputType: 'text',
                required: true,
                callback: result => {
                    if (result !== null){
                        creator = result;
                        closure();
                    }
                }
            });
            $('.modal-body').css({paddingTop: 0, paddingBottom: 0});
            return;
        }

        closure();
    });

    $('#btnEliminarFacturas').on('click', () => {
        eliminarFacturas();
    });
});
