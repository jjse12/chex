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
                callback: () => {
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
                                const {success, message, data} = response;
                                if (success) {
                                    Swal.fire({
                                        title: '¡Paquetes Tarifados!',
                                        type: 'success',
                                        timer: 2000,
                                        showConfirmButton: false
                                    });
                                } else if (message) {
                                    const {failedQueriesData} = data;
                                    bootbox.dialog({
                                        backdrop: true,
                                        closeButton: true,
                                        title: 'Error al Importar Tarifaciones de Paquetes Express',
                                        message: getFailedImportedTarifacionesDialogContent(message, failedQueriesData),
                                    });
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
            }
        }
    });
}