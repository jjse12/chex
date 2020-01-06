function showLogoutDialog () {
    bootbox.confirm({
        size: "small",
        message: "Se cerrará la sesión actual...",
        buttons: {
            cancel: {
                label: "Regresar",
                className: "btn btn-md btn-default alinear-izquierda"
            },
            confirm: {
                label: "Continuar",
                className: "btn btn-md btn-warning alinear-derecha"
            }
        },
        callback: function(res){
            if (res){
                $.ajax({
                    url: "views/session.php",
                    type: "POST",
                    data: {
                        vaciar: 1
                    },
                    cache: false,
                    success: function(res){
                        window.location.replace("?logout");
                    },
                    error: function(){
                        bootbox.alert("Ocurrió un problema al intentar conectarse al servidor. Intente cerrar sesión nuevamente");
                    }
                });
            }
        }
    });
}