
async function createTarifacionesPDF(guideNumbers) {

    const table = await $.ajax({
        url: "views/getTableCostoMercaderia.php",
        type: "POST",
        data: {
            guideNumbers,
            isTarifacion: true,
        },
        cache: false,
    });

    $.ajax({
        url: 'utils/saveTarifacion.php',
        type: 'post',
        data: {
            table
        },
        cache: false,
        success: function (res, status, xhr) {
            if (xhr.status === 200){
                let fileName = res.fileName;
                Swal.fire({
                    title: 'Tabla de Tarifaciones Guardada',
                    html: `Se ha creado un archivo con el contenido HTML de la tabla de las tarifaciones ingresadas.<br><br><br><b>${fileName}</b>`,
                    type: 'success',
                    allowEscapeKey : false,
                    allowOutsideClick: false,
                    confirmButtonText: 'Ok',
                });
                window.open('tarifaciones-ingresadas/'+fileName);
            }
            /*
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
            */
        },
        error: function() {
            bootbox.alert("Ocurrió un error al intentar generar el PDF de las tarifaciones ingresadas.");
        }
    });
}

function importTarifaciones() {
    let inputTarifaciones = $('#inputImportTarifaciones');
    let inputCambioDolar = $('#inputCambioDolar')[0]
    let data = inputTarifaciones.val();
    if (data.length > 0 && (inputCambioDolar.disabled || inputCambioDolar.value.length > 0)) {
        $.ajax({
            url: 'db/tarifaciones/DBinsertTarifacionesExpress.php',
            type: 'post',
            data: {
                data,
                cambioDolar: inputCambioDolar.value
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
                    if (data){
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
                    }
                    else {
                        Swal.fire({
                            title: 'Error',
                            text: message,
                            type: 'error',
                            focusConfirm: true,
                            confirmButtonText: 'Ok',
                            confirmButtonClass: 'btn-success'
                        });
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
            error: (xhr,b,c) => {
                console.log("xhr: ", xhr);
                console.log("b: ", b);
                console.log("c: ", c);
                Swal.fire({
                    title: 'Error',
                    html: "El servidor indica lo siguiente: <br><br><b>" + xhr.responseText + "</b>",
                    type: 'error',
                    allowEscapeKey : false,
                    allowOutsideClick: false,
                    confirmButtonText: 'Ok',
                });
            }
        });
        return;
    }

    inputTarifaciones.focus();
    alert('¡Debes ingresar la información solicitada!');
    return false;
}

function onTipoCambioCheckboxChange(checked) {
    document.getElementById("inputCambioDolar").setAttribute("disabled", !checked);
    document.getElementById("inputCambioDolar").disabled = !checked;
}

async function showImportTarifacionesDialog () {
    const res = await $.ajax({
        url: 'db/tarifaciones/DBgetCurrentCoeficientes.php',
        type: 'get',
        cache: false,
    });

    bootbox.dialog({
        backdrop: true,
        closeButton: true,
        title: 'Importar Tarifaciones de Paquetes Express',
        message: getImportTarifacionesDialogContent(res.data.cambio_dolar),
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