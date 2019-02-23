<script type="text/javascript">
    
    $(document).ready(function (){
        $("#formLogin").on("submit", function(event){
            event.preventDefault();
            var nombre = document.getElementById("login_input_username").value;
            var password = document.getElementById("login_input_password").value;
            $.ajax({
                url: "db/DBgetEmpleadoPermisos.php",
                type: "POST",
                datatype: 'json',
                data: {
                    user: nombre,
                    pass: password
                },
                cache: false,
                success: function(response){
                    if (response.data) {
                        $.ajax({
                            url: "views/session.php",
                            type: "POST",
                            data: {
                                username: nombre,
                                user_login_status: 1,
                                user_admin: response.data
                            },
                            cache: false,
                            success: function(res){
                                bootbox.alert('Ingreso correcto, redireccionando...');
                                setTimeout(function() {
                                    window.location.replace("http://<?php require_once('db/db_vars.php'); echo URL_REDIRECT; ?>");
                                }, 1000);
                            }
                        });

                    }
                    else {
                        bootbox.alert(response.message ?
                            response.message : 'Ocurrió un problema al conectarse a la base de datos para verificar usuario. Intente nuevamente.');
                    }
                },
                error: function(){
                    bootbox.alert('Ocurrió un problema al conectarse a la base de datos para verificar usuario. Intente nuevamente.');
                }
            });

        });
    });
</script>


<div class="row" id="divLoginAdmin">
    <br>
    <br>
    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 col-lg-offset-3 col-md-offset-3 col-sm-offset-3">
        <div class="panel panel-default">
            <div class="panel-fill-head color-darkblue">
                <h2 class="header-title">Ingresar al Sistema</h2>
            </div>
            <div class="panel-body panel-fill-body color-blue">
                <form method="post" id="formLogin">
                    <div class="row">
                        <div class="control-group form-group col-lg-6 col-md-6 col-sm-6 col-xs-6">
                            <div class="controls">
                                <label for="login_input_username">Usuario </label>
                            <input id="login_input_username" class="login_input form-control" type="text" name="user_name" autocomplete="off" required />
                            </div>
                        </div>
                        <div class="control-group form-group col-lg-6 col-md-6 col-sm-6 col-xs-6">
                            <div class="controls">
                                <label for="login_input_password">Constraseña </label>
                                <input id="login_input_password" class="login_input form-control" type="password" name="user_password" autocomplete="off" required />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <br>
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3"></div>
                        <div class="control-group form-group  col-lg-6 col-md-6 col-sm-6 col-xs-6">
                            <input style="width: 100%;" type="submit" id="btnLoginAdmin" value="Iniciar Sesión" class="btn btn-md btn-success"/>
                        </div>
                    </div>
                </form>
            </div>
            <div class="panel-footer panel-fill-foot color-blue"></div>
        </div>
    </div>
</div>

