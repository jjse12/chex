<div style="position: fixed; z-index: 1029; left: 0; right: 0; background-color: #fff;" class='row-same-height col-lg-12 col-md-12 col-sm-12 col-xs-12'>
  <div class='col-lg-4 col-md-4 col-sm-4'></div>
  <div class='col-lg-4 col-md-4 col-sm-4 col-xs-12'>
    <section  style="background: #eaeaea;" class="tabbable"">
      <nav class="course-tabs">
        <ul class="nav nav-pills">
          <li style="width: 49%" onclick="scroll0()" class="active alpha"><a href="#tab1" style="text-align: center;" align="center" data-toggle="tab">Registros Internos</a></li>
          <li style="width: 50%" onclick="scroll0()"><a href="#tab2" data-toggle="tab" style="text-align: center;" align="center">Clientes ChispuditoExpress</a></li>
        </ul>
      </nav>
    </section>
  </div>
  <div class='col-lg-4 col-md-4 col-sm-4'></div>
</div>

<div class="tab-content box">
  <div class="tab-pane fade active in" id="tab1">
    <?php 
      include("cargas.php");
    ?>
  </div>
  <div class="tab-pane fade" id="tab2">
      <div class="container">
        <?php 
          include("clientesChispuditoExpress.php");
        ?>
      </div>
  </div>
</div>

<a style="position: fixed; z-index: 1030; bottom: 0; right:0; margin-right: 10px; margin-bottom: 5px" onclick="logout()" class="btn-lg btn-danger header-title">Cerrar Sesión</a>

<script type="text/javascript">

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
                        window.location.replace("fonts.php?logout");
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