<script src="../js/facturas.js"></script>

<div style="position: fixed; z-index: 1029; left: 0; right: 0; background-color: #fff;" class='row-same-height col-lg-12 col-md-12 col-sm-12 col-xs-12'>
  <div class="col-lg-3 col-md-3 col-sm-3">
      <br>
      <h3 style="color: orange;"><?php echo $_SESSION['username'];?></h3>
  </div>
  <div class='col-lg-6 col-md-6 col-sm-6 col-xs-12'>
    <section  style="background: #eaeaea;" class="tabbable"">
      <nav class="course-tabs">
        <ul class="nav nav-pills">
          <li style="width: 33%" onclick="scroll0(); loadFacturas();"><a href="#tab1" style="text-align: center;" align="center" data-toggle="tab">Facturas Clientes</a></li>
          <li style="width: 33%" onclick="scroll0()" class="active alpha"><a href="#tab2" style="text-align: center;" align="center" data-toggle="tab">Registros Internos</a></li>
          <li style="width: 33%" onclick="scroll0(); insertarNuevosClientes();"><a href="#tab3" data-toggle="tab" style="text-align: center;" align="center">Clientes ChispuditoExpress</a></li>
        </ul>
      </nav>
    </section>
  </div>
</div>

<div class="tab-content box">
    <div class="tab-pane fade" id="tab1">
        <div class="container">
            <?php
                include("facturas.php");
            ?>
        </div>
    </div>
  <div class="tab-pane fade active in" id="tab2">
    <?php 
      include("mercaderia.php");
    ?>
  </div>
  <div class="tab-pane fade" id="tab3">
      <div class="container">
        <?php 
          include("clientes.php");
        ?>
      </div>
  </div>
</div>

<a style="position: fixed; z-index: 1030; bottom: 0; right:0; margin-right: 10px; margin-bottom: 5px" onclick="logout()" class="btn-lg btn-danger header-title">Cerrar Sesión</a>
  
<script type="text/javascript">

  function insertarNuevosClientes(){
    $.ajax({
            url: "db/DBgetAndInsertNewUsers.php",
            cache: false,
            success: function(res){
                if (res.includes("EXITO")){
                    var cant = Number(res.split(": ")[1]);
                    bootbox.alert("La tabla de clientes ha sido actualizada, se agregaron " + cant + " clientes nuevos.");
                }
                else if (res.includes("INCOMPLETO")){
                    var cantInsertados = Number(res.split(": ")[1].split("@")[1]);
                    var cantFaltantes = Number(res.split(": ")[1].split("@")[0])-cantInsertados;
                    bootbox.alert("Se intentó actualizar la tabla de clientes, pero solo " + cantInsertados + " clientes nuevos pudieron ser agregados. Hacen falta " + cantFaltantes + " aún por agregar.");
                }
                else if (res.includes("ERROR"))
                    bootbox.alert("Ocurrió un error al consultar la base de datos. Se recibió el siguiente mensaje: <i><br>" + res + "</i>");
                initClientesChex();
            }, 
            error: function(){
                bootbox.alert("No se pudo verificar actualización de la tabla de clientes debido a un problema de conexión con el servidor.");
                initClientesChex();
            }
        });
  }

  function logout(){
      bootbox.confirm({
        size: "small",
        message: "Se cerrará la sesión actual...",
        buttons: {
          cancel: {
            label: "Regresar",
            className: "btn btn-md btn-info alinear-izquierda"
          },
          confirm: {
              label: "Continuar",
              className: "btn btn-md btn-danger alinear-derecha"
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

  function scroll0(){
    $('html,body').animate({scrollTop: 0}, 1000);
  }
</script>