
async function createTarifacionesPDF(guideNumbers) {

    const table = await $.ajax({
        url: "views/getTableCostoMercaderia.php",
        type: "POST",
        data: {
            guideNumbers,
            isEntrega: false,
        },
        cache: false,
    });

    $.ajax({
        url: 'pdf/createTarifacionesPDF.php',
        type: 'post',
        data: {
            table
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
                    window.open('tarifaciones-ingresadas/'+filename);
                }

                Swal.fire({
                    title: 'Documento Creado',
                    text: 'El PDF con las tarifaciones ingresadas ha sido creado exitosamente',
                    type: 'success',
                    allowEscapeKey : false,
                    allowOutsideClick: false,
                    confirmButtonText: 'Ok',
                });
            }
            else {
                bootbox.alert("No se pudo abrir el PDF de las tarifaciones ingresadas. Por favor contacta al administrador para verificar si el archivo fue creado exitosamente.");
            }
        },
        error: function() {
            bootbox.alert("Ocurrió un error al intentar generar el PDF de las tarifaciones ingresadas.");
        }
    });
}

function importTarifaciones() {
    let input = $('#inputImportTarifaciones');
    let data = input.val();
    if (data.length > 0) {
        $.ajax({
            url: 'db/tarifaciones/DBinsertTarifacionesExpress.php',
            type: 'post',
            data: {
                data
            },
            cache: false,
            success: function (response) {
                const { success, message, data } = response;
                if (success) {
                    Swal.fire({
                        title: '¡Paquetes Tarifados!',
                        type: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    const { insertedTarifacionesGuideNumbers } = data;
                    if (insertedTarifacionesGuideNumbers.length > 0){
                        createTarifacionesPDF(insertedTarifacionesGuideNumbers);
                    }
                } else if (message) {
                    const { failedQueriesData, insertedTarifacionesGuideNumbers } = data;
                    bootbox.dialog({
                        backdrop: true,
                        closeButton: true,
                        title: 'Error al Importar Tarifaciones de Paquetes Express',
                        message: getFailedImportedTarifacionesDialogContent(message, failedQueriesData),
                    });
                    if (insertedTarifacionesGuideNumbers.length > 0){
                        createTarifacionesPDF(insertedTarifacionesGuideNumbers);
                    }
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: 'Ocurrió un error inesperado, no se pudieron importar las tarifaciones',
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
    alert('¡Debes ingresar la información solicitada!');
    return false;
}

function showImportTarifacionesDialog () {
    bootbox.dialog({
        backdrop: true,
        closeButton: true,
        title: 'Importar Tarifaciones de Paquetes Express',
        message: getImportTarifacionesDialogContent(),
        buttons: {
            regresar: {
                label: 'Cancelar',
                className: "btn-default alinear-izquierda",
            },
            confirm: {
                label: 'Importar',
                className: "btn-success alinear-derecha",
                callback: importTarifaciones
            }
        }
    });
}