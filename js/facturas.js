const dataFacturaIndex = 11;

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
    let $clientNotified = $('#factura-client-notified');
    let $comment = $('#factura-comment');

    $dateDelivery.val($dateDelivery.data('original'));
    $courier.val($courier.data('original'));
    $signer.val($signer.data('original'));

    let received = $miamiReceived.data('original');
    let $divDateReceived = $('#divDateReceived');
    if (received){
        $miamiReceived[0].checked = true;
        if (!$divDateReceived.hasClass('in')) {
            collapseElement($divDateReceived, 74);
        }
    }
    else {
        $miamiReceived[0].checked = false;
        if (received === null){
            $miamiReceived[0].indeterminate = true;
            $miamiReceived[0].readOnly = false;
        }
        else {
          $miamiReceived[0].readOnly = true;
        }
        if ($divDateReceived.hasClass('in')){
            collapseElement($divDateReceived);
        }
    }
    $dateReceived.val($dateReceived.data('original'));
    $clientNotified.prop('checked', $clientNotified.data('original'));
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

function handleMiamiReceivedCheckBox(cb) {
    if (cb.checked){
      if (cb.readOnly){
          cb.checked=cb.readOnly=false;
          cb.indeterminate=true;
      }
      else {
        collapseElement($('#divDateReceived'), 74);
      }
    }
    else {
        collapseElement($('#divDateReceived'));
        cb.readOnly = true;
    }
}

const facturaLogistica = (logistica, factura) => {
    if (logistica === null){
        return renderFacturaLogisticaEmpty(factura);
    }
    return renderFacturaLogistica(logistica, factura);
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

const facturaDetails = (details) => {
    let logistica = facturaLogistica(details.logistica, details.factura);
    let seguimiento = renderFacturaSeguimiento(details.seguimiento, details.factura);

    return `
        <div id="divFacturaDetails" class="row">
            <div class="col-md-5" id="divFacturaLogistica">${logistica}</div>
            <div class="col-md-7 fill" id="divFacturaSeguimiento">${seguimiento}</div>
        </div>`;
};

function loadFacturaDetailsAndShowDialog(factura) {
    $.ajax({
        url: 'db/factura/DBgetFacturaDetails.php',
        data: {
            facturaId : factura.id
        },
        type: "POST",
        cache: false,
    })
    .then(response => {
        if (response.data !== null) {
            let details = response.data;
            details.factura = factura;
            let content = facturaDetails(details);
            bootbox.dialog({
                title: `<div class="text-center">&nbsp;CHEX ${factura.uid}&nbsp-&nbsp;${factura.uname}&nbsp-&nbsp;Tracking: ${factura.tracking}</div>`,
                size: 'large',
                message: `${content}`
            });

            if (details.logistica !== null){
                toggleLogistica();
                activateLogisticaDatePickers(details.logistica);
                if (details.logistica.miami_received === null){
                    $('#factura-miami-received')[0].indeterminate = true;
                }
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
        url: 'db/factura/DBgetFacturas.php',
        type: 'GET',
        cache: false,
    })
    .then(response => {
        if (response.data.length === 0) {
            bootbox.alert("No se encontraron facturas.");
        } else {
            for (let i = 0; i < response.data.length; i++) {
                let factura = response.data[i];
                let momentDateCreated = moment(factura.date_created);
                let dateCreated = momentDateCreated.format('DD/MM/YYYY[&nbsp-&nbsp;]HH:mm');

                let notificado = 'Cliente aún no notificado', notifiedColor = 'red', notifiedIcon = 'fa-times';
                if (factura['client_notified'] == 1) {
                  notifiedColor = 'lime';
                  notifiedIcon = 'fa-check';
                  notificado = 'Cliente notificado';
                }

                let enviado = 'Enviado', color = 'lime', icon = 'fa-paper-plane';
                if (factura['pendiente'] === '1') {
                    enviado = 'Pendiente';
                    color = 'gold';
                    icon = 'fa-clock';
                }

                let dateReceived = 'Pendiente';
                let date = factura.date_delivered ? factura.date_delivered : 'Sin Especificar';

                let miamiReceived = factura.miami_received;
                if (miamiReceived == 1) {
                    dateReceived = factura.date_received ? factura.date_received : 'Sin Especificar';
                }
                else if (miamiReceived == 0) dateReceived = 'No Recibido';

                if (factura.date_delivered === null){
                    date = dateReceived = 'Aún sin Seguimiento'
                }

                // Anytime the cant of columns is changed, please update variable `dataFacturaIndex`
                table.row.add([
                    `<h6 class='seleccionado'>${factura.service}</h6>`,
                    `<h6 class='seleccionado' data-sorting-date="${factura.date_created}">${dateCreated}<span style="display: none">-${dateCreated}-</span></h6>`,
                    `<div class='seleccionado' title="${notificado}" style='color: ${notifiedColor}; align-self: center; text-align: center;'><i class='fa ${notifiedIcon} fa-2x fa-lg'></i></div>`,
                    `<div class='seleccionado' title="${enviado}" style='color: ${color}; align-self: center; text-align: center;'><i class='fa ${icon} fa-2x fa-lg'></i><small style='display:none;'>${enviado}</small></div>`,
                    `<h6 class='seleccionado' data-sorting-date="${date}">${date}<span style="display: none">${enviado}</span></h6>`,
                    `<h6 class='seleccionado' data-sorting-date="${dateReceived}">${dateReceived}</h6>`,
                    `<h6 class='seleccionado'>${factura.tracking}</h6>`,
                    `<h6 class='seleccionado'>${factura.uid}</h6>`,
                    `<h6 class='seleccionado'>${factura.uname}</h6>`,
                    `<h6 class='seleccionado'>${Number(factura.amount).toMoney()}</h6>`,
                    `<div style='cursor:pointer; text-align: center; color: darkslategray' class='factura-see-image' data-factura='${JSON.stringify(factura)}'><i class='fa fa-eye fa-2x fa-lg'></div>`,
                    `<div style='cursor:pointer; text-align: center; color: greenyellow' class='factura-see-details' data-factura='${JSON.stringify(factura)}'><i class="fas fa-address-card fa-2x fa-lg"></i></div>`
                ]);
            }
            table.columns.adjust().responsive.recalc();
            table.draw(false);
        }
    },
    () => bootbox.alert("Ocurrió un problema al intentar conectarse al servidor."));
}

function generarPDF()
{
    let table = $("#facturas").DataTable();
    let selectedRows = table.rows(".selected").data().toArray();
    let facturas = {};
    selectedRows.map(row => {
        let factura = $(row[dataFacturaIndex]).data('factura');
        facturas[factura.id] = {
            date_created: factura.date_created,
            tracking: factura.tracking,
            clientName: factura.uname,
            clientId: factura.uid,
            description: factura.description,
            amount: factura.amount,
            itemCount: factura.item_count,
            service: factura.service,
            guideNumber: factura.guide_number,
            fobPrice: factura.fob_price,
            images: []
        };
    });

    let ids = Object.keys(facturas);

    $.ajax({
        url: 'db/factura/DBgetFacturasImage.php',
        data: {
            facturasId : ids
        },
        type: "POST",
        cache: false,
        success: function(response){
            if (response.data) {
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
        url: 'db/factura/DBsetFactura.php',
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
        let factura = $(row[dataFacturaIndex]).data('factura');
        facturas.push(factura.id);
    });

    $.ajax({
        url: 'db/factura/DBdeleteFacturas.php',
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
        "order": [[7, 'asc']],
        "columnDefs": [
            {
                "targets": [2, 3, 6, 10, 11],
                "orderable": false
            }
        ],
        "aoColumns": [
          null,
          { "sType": "date-time", "bSortable": true }, null, null,
          { "sType": "dd-mm-yyyy-date", "bSortable": true },
          { "sType": "dd-mm-yyyy-date", "bSortable": true },
          null, null, null, null, null, null
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

    $.fn.dataTableExt.oSort['dd-mm-yyyy-date-asc'] = (a,b) => sortddmmyyyyDate(false, a, b);
    $.fn.dataTableExt.oSort['dd-mm-yyyy-date-desc'] = (a,b) => sortddmmyyyyDate(true, a, b);
    $.fn.dataTableExt.oSort['date-time-asc'] = (a,b) => sortddmmyyyyDate(false, a, b);
    $.fn.dataTableExt.oSort['date-time-desc'] = (a,b) => sortDateTime(true, a, b);

    var onlyAdminEditableFields = [
        'service_id'
    ];

    var unquotedColumns = [
        'amount',
        'item_count',
        'service_id',
        'guide_number',
        'fob_price',
    ];

    $(document).on('click', 'span.factura-editable', ev => {
        let span = $(ev.target);
        let value = span.text();
        let id = span.data('id');
        let field = span.data('field');
        let column = span.data('column');
        if (!isAdmin && onlyAdminEditableFields.includes(column)) return;
        let dialogContent;

        const showEditionDialog = (dialogContent) => {
            bootbox.dialog({
                title: `Modificar Factura`,
                message: dialogContent,
                buttons: {
                    regresar: {
                        label: 'Cancelar',
                        className: "btn-default alinear-izquierda",
                    },
                    confirm: {
                        label: 'Guardar',
                        className: "btn-success alinear-derecha",
                        callback: () => {
                            let input = $(`#${column+'-'+id}`);
                            let newValue = input.val();
                            if (newValue.length > 0 && newValue !== value) {
                                let set = column + ' = ' +
                                  (unquotedColumns.includes(column) ? newValue : `'${newValue}'`);
                                $.ajax({
                                    url: 'db/factura/DBsetFactura.php',
                                    type: 'post',
                                    data: {
                                        set: set,
                                        where: `id = ${id}`
                                    },
                                    cache: false,
                                    success: function (res) {
                                        if (res.success){
                                            if (column === 'amount' || column === 'fob_price'){
                                                span.text(Number(newValue).toMoney());
                                            }
                                            else if (column === 'service_id'){
                                                const option = input.find('option:selected');
                                                span.text(option.data('nombre'));
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
            $('.modal-body').css({paddingTop: 0, paddingBottom: 0});
        };

        if (column === 'service_id'){
            $.ajax({
                url: 'db/servicio/DBgetServices.php',
                type: 'get',
                cache: false,
            })
            .then(result => {
                dialogContent = renderFacturaServiceEditDialog(value, column+'-'+id, field, result.data || {});
                showEditionDialog(dialogContent);
            });
        }
        else {
            let extra = '';
            if (column === 'amount' || column === 'fob_price'){
                extra = 'type="number" step="0.01" onKeyPress="return numbersonly(this, event, \'\')"';
                value = value.replace('US$ ', '');
            }
            else if (column === 'item_count') {
                extra = 'type="number" step="1" onKeyPress="return integersonly(this, event)"';
            }
            else if (column === 'guide_number') {
                extra = 'type="number" step="0" onKeyPress="return integersonly(this, event)"';
            }
            dialogContent = renderFacturaFieldEditDialog(value, column+'-'+id, field, extra);
            showEditionDialog(dialogContent);
        }

    });

    let showFacturaDialog = factura => {
        bootbox.dialog({
            title: `Detalles de factura de ${factura.uname}`,
            message: `${renderFacturaDetails(factura)}`
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

    tableBody.on("click", "div.factura-see-image", async function () {
        let factura = $(this).data('factura');
        const images = await getFacturaImagesFromDB(factura.id);
        if (images === null){
            bootbox.alert("Ocurrió un error al intentar obtener las imágenes de la factura, por favor intenta nuevamente.");
        }
        else {
            factura.images = images;
            showFacturaDialog(factura);
        }
    });

    tableBody.on("click", "div.factura-see-details", function () {
        let factura = $(this).data('factura');
        loadFacturaDetailsAndShowDialog(factura);
    });

    $(document).on("click", ".btnCreateFacturaLogistica", function () {
        let factura = $(this).data('factura-id');
        let facturaId = factura.id;
        if (!facturaId) {
            bootbox.alert("No se encontró el ID de la factura para crear el registro.");
            return
        }

        $.ajax({
            url: 'db/factura/DBserverInsertFacturaLogistica.php',
            data: {
                facturaId: facturaId
            },
            type: "POST",
            cache: false
        })
        .then(response => {
            const { success, data, message } = response;
            if (success) {
                if (data === true){
                    bootbox.hideAll();
                    loadFacturaDetailsAndShowDialog(factura);
                }
                else {
                    $('#divFacturaLogistica').html(facturaLogistica(response.data, factura));
                    toggleLogistica();
                    activateLogisticaDatePickers(data);
                    if (data.miami_received === null){
                        $('#factura-miami-received')[0].indeterminate = true;
                    }
                }
            } else if (message) {
                bootbox.alert(message);
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
        let received = $miamiReceived[0].checked ? 1 : $miamiReceived[0].indeterminate ? null : 0;
        let $dateReceived = $('#factura-date-received');
        if (received !== 1){
            $dateReceived.val('');
        }
        let $clientNotified = $('#factura-client-notified');
        let notified = $clientNotified.prop('checked');
        let $comment = $('#factura-comment');

        let query = `
            UPDATE factura_logistica 
            SET
                date_delivered = '${$dateDelivery.val()}',
                courier = '${$courier.val()}',
                signer = '${$signer.val()}',
                miami_received = ${received},
                date_received = '${$dateReceived.val()}',
                client_notified = ${notified},
                comment = '${$comment.val()}'
            WHERE fid = '${facturaId}';
            `;

        $.ajax({
            url: 'db/factura/DBfacturaExecQuery.php',
            data: {
                query: query
            },
            type: "POST",
            cache: false,
        })
        .then(response => {
            if (response.success && response.data === true){
                Swal.fire({
                    title: 'Datos de factura actualizados',
                    type: 'success',
                    timer: 2000,
                    showConfirmButton: false
                });
                if ($dateDelivery.val() !== $dateDelivery.data('original') ||
                    received !== $miamiReceived.data('original') ||
                    $dateReceived.val() !== $dateReceived.data('original') ||
                    notified !== $clientNotified.data('original')) {
                    loadFacturas();
                }
                $dateDelivery.data('original', $dateDelivery.val());
                $courier.data('original', $courier.val());
                $signer.data('original', $signer.val());
                $miamiReceived.data('original', received);
                $dateReceived.data('original', $dateReceived.val());
                $clientNotified.data('original', notified);
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
                url: 'db/factura/DBserverInsertFacturaSeguimiento.php',
                data: {
                    facturaId: facturaId,
                    note: note,
                    creator: creator
                },
                type: "POST",
                cache: false,
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
                        $('#divFacturaSeguimiento').html(renderFacturaSeguimiento(response.data, factura));
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

    function cancelFacturaImagesDeletion() {
        let $deleteButton = $('#deleteFacturaImages');
        $deleteButton.addClass('btn-danger');
        $deleteButton.removeClass('btn-default');
        $deleteButton.text('Eliminar Imágenes');
        let $confirmDeleteButton = $('#confirmDeleteFacturaImages');
        $confirmDeleteButton.attr('disabled', 'disabled');
        $confirmDeleteButton.hide();
        $('.selected-factura-image').removeClass('selected-factura-image');
        $('#addFacturaImages').removeAttr('disabled');
    }

    function initFacturaImagesDeletion() {
        let $deleteButton = $('#deleteFacturaImages');
        $deleteButton.removeClass('btn-danger');
        $deleteButton.addClass('btn-default');
        $deleteButton.text('Cancelar');
        $('#confirmDeleteFacturaImages').show();
        $('#addFacturaImages').attr('disabled', 'disabled');
    }

    $(document).on('click', '#deleteFacturaImages', function() {
        if ($(this).hasClass('btn-default')) {
            cancelFacturaImagesDeletion();
        }
        else {
            initFacturaImagesDeletion();
        }
    });

    $(document).on('click', '.factura-image', function() {
        if ($('#deleteFacturaImages').hasClass('btn-danger')) return;

        if ($(this).hasClass('selected-factura-image'))
            $(this).removeClass('selected-factura-image');
        else $(this).addClass('selected-factura-image');

        if ($('.selected-factura-image').length > 0)
            $('#confirmDeleteFacturaImages').removeAttr('disabled');
        else $('#confirmDeleteFacturaImages').attr('disabled', 'disabled');
    });

    $(document).on('click', '#confirmDeleteFacturaImages', async function() {
        $selectedImages = $('.selected-factura-image');
        if ($selectedImages.length < 1) return;
        let facturaImagesIds = [];
        for (let i = 0; i < $selectedImages.length; i++) {
            facturaImagesIds.push($($selectedImages[i]).data('id'));
        }

        try {
            const response = await $.ajax({
                url: 'db/factura/DBdeleteFacturasImages.php',
                data: {
                  where: `id IN (${facturaImagesIds.join(', ')})`
                },
                type: "POST",
                cache: false
            });

            if (!response.success) {
                bootbox.alert('Ocurrió un error, no se pudieron eliminar las imágenes seleccionadas facturas');
                return;
            }

            Swal.fire({
                title: 'Imágenes eliminadas',
                text: 'Las imágenes seleccionadas han sido eliminadas',
                type: 'success',
                focusConfirm: true,
                confirmButtonText: 'Ok',
                confirmButtonClass: 'btn-success'
            });

            var facturaId = $(this).data('id');
            bootbox.hideAll();

            const factura = await getFacturaFromDB(facturaId);
            if (factura === null) {
                bootbox.alert("Ocurrió un error al intentar recargar la información de la factura, presiona nuevamente el botón &#x1f441; para ver los detalles de la factura.");
                return;
            }
            const images = await getFacturaImagesFromDB(facturaId);
            if (images === null){
                bootbox.alert("Ocurrió un error al intentar recargar las imágenes de la factura, presiona nuevamente el botón &#x1f441; para ver los detalles de la factura.");
                return;
            }

            factura.images = images;
            showFacturaDialog(factura);
        } catch (e) {
            console.log(e);
            bootbox.alert("Ocurrió un error al conectarse a la base de datos.");
        }
    });

    function cancelFacturaImagesAddition () {
        let $addButton = $('#addFacturaImages');
        $addButton.addClass('btn-success');
        $addButton.removeClass('btn-default');
        $addButton.text('Agregar Imágenes');
        let $confirmAddButton = $('#confirmAddFacturaImages');
        $confirmAddButton.attr('disabled', 'disabled');
        $confirmAddButton.hide();
        $('#deleteFacturaImages').removeAttr('disabled');
        $('#imgs').val('');
    }

    function initFacturaImagesAddition () {
        let $addButton = $('#addFacturaImages');
        $addButton.removeClass('btn-success');
        $addButton.addClass('btn-default');
        $addButton.text('Cancelar');
        $('#confirmAddFacturaImages').show();
        $('#deleteFacturaImages').attr('disabled', 'disabled');
    }

    $(document).on('change', '#imgs', function () {
        $('#confirmAddFacturaImages').removeAttr('disabled');
    });

    $(document).on('click', '#addFacturaImages', function() {
        if ($(this).hasClass('btn-default')) {
            cancelFacturaImagesAddition();
        }
        else {
            initFacturaImagesAddition();
        }
    });


    $(document).on('click', '#confirmAddFacturaImages', async function () {
        var formData = new FormData($('#addImagesForm')[0]);
        var facturaId = $(this).data('id');

        try {
            const response = await $.ajax({
                url: 'db/factura/DBinsertFacturaImages.php',
                method: 'POST',
                data: formData,
                dataType: 'json',
                contentType: false,
                processData: false,
                cache: false
            });

            let title = '¡Error de Solicitud!';
            let text = 'Ocurrió un error al intentar hacer la solicitud';
            let type = 'error';
            if (response.success === true) {

                type = 'success';
                bootbox.hideAll();
                Swal.fire({
                    title: '¡Imágenes Agregadas!',
                    text: '¡Las imágenes han sido agregadas a la factura exitosamente!',
                    type: 'success',
                    confirmButtonText: 'Ok'
                });

                const factura = await getFacturaFromDB(facturaId);
                if (factura === null) {
                  bootbox.alert("Ocurrió un error al intentar recargar la información de la factura, presiona nuevamente el botón &#x1f441; para ver los detalles de la factura.");
                  return;
                }
                const images = await getFacturaImagesFromDB(facturaId);
                if (images === null){
                    bootbox.alert("Ocurrió un error al intentar recargar las imágenes de la factura, presiona nuevamente el botón &#x1f441; para ver los detalles de la factura.");
                }
                else {
                    factura.images = images;
                    showFacturaDialog(factura);
                }
                return;
            }
            else if (response.message) {
                title = 'Error';
                text = response.message;
            }

            Swal.fire({
                title,
                text,
                type,
                confirmButtonText: 'Ok'
            });

        } catch (e) {
            console.log(e);
            Swal.fire({
                title: '¡Error de Solicitud!',
                text: 'Ocurrió un error al intentar hacer la solicitud',
                type: 'error',
                confirmButtonText: 'Ok'
            });
        }
    });
});

async function getFacturaFromDB(id) {
    try {

    const response = await $.ajax({
        url: 'db/factura/DBgetFactura.php',
        data: {
          factura_id : id
        },
        type: "GET",
        cache: false
    });

    return response.data;
    } catch (e) {
        console.log(e);
        return null;
    }
}

async function getFacturaImagesFromDB(facturaId) {
    try {
        const response = await $.ajax({
            url: 'db/factura/DBgetFacturasImage.php',
            data: {
                facturasId : [facturaId]
            },
            type: "POST",
            cache: false
        });

        if (response.data) {
            return response.data[facturaId] || [];
        }
        return [];
    } catch (e) {
        console.log(e);
        return null;
    }
}