<div style="position: fixed; z-index: 1029; left: 0; right: 0; background-color: #fff;"
     class='row-same-height col-lg-12 col-md-12 col-sm-12 col-xs-12'>
  <div class="col-lg-3 col-md-3 col-sm-3">
    <br>
    <h3 id="currentUserRealName" style="color: orange;"><?php echo $_SESSION['user_real_name']; ?></h3>
  </div>
  <div class='col-lg-6 col-md-6 col-sm-6 col-xs-12'>
    <section style="background: #eaeaea;" class="tabbable"
    ">
    <nav class="course-tabs">
      <ul class="nav nav-pills">
        <li style="width: 33%" onclick="scroll0(); loadFacturas();"><a href="#tab1" style="text-align: center;"
                                                                       align="center" data-toggle="tab">Facturas
            Clientes</a></li>
        <li style="width: 33%" onclick="scroll0()" class="active alpha"><a href="#tab2" style="text-align: center;"
                                                                           align="center" data-toggle="tab">Registros
            Internos</a></li>
        <li id="clientesChexTab" style="width: 33%" onclick="onChexClientsTabClicked();"><a href="#tab3" data-toggle="tab"
                                                                                                          style="text-align: center;" align="center">Clientes
            ChispuditoExpress</a></li>
      </ul>
    </nav>
    </section>
  </div>
  <div class="mt-3 col-lg-3 col-md-3 col-sm-3">
    <div class="pull-right mr-3 mt-3 btn-group">
      <button class="btn btn-default btn-sm dropdown-toggle glyphicon glyphicon-wrench"
              type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <span class="caret"></span>
      </button>
      <ul class="dropdown-menu options-dropdown-menu">
        <li>
          <a href="#" id="btnLogout">
            <span class="glyphicon glyphicon-off"></span>&nbsp;Salir
          </a>
        </li>
        <li>
          <a href="#" id="btnObtenerUsuariosNuevos">
            <span class="glyphicon glyphicon-user"></span>&nbsp;Actualizar Tabla Clientes
          </a>
        </li>
        <li>
          <a href="#" id="btnImportarTarifaciones">
            <span class="glyphicon glyphicon-import"></span>&nbsp;Importar Tarifaciones
          </a>
        </li>
        <?php if ($isAdmin): ?>
          <li>
            <a style="margin-left: 2px" href="#" id="btnVerCostoMercaderia">
              <i class="fas fa-dollar-sign"></i>&nbsp;&nbsp;Ver Costos Inventario
            </a>
          </li>
        <?php endif ?>
      </ul>
    </div>
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

<script src="js/nav-options.js?v=1.1.0"></script>
<script src="js/logout.js?v=1.1.0"></script>
<script src="js/tarifaciones.js?v=1.1.0"></script>
<script src="js/templates/tarifaciones-templates.js?v=1.1.0"></script>
<script type="text/javascript">

  $.datepicker.regional['es'] = {
    closeText: 'Cerrar',
    prevText: '< Ant',
    nextText: 'Sig >',
    currentText: 'Hoy',
    monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
    monthNamesShort: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
    dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
    dayNamesShort: ['Dom', 'Lun', 'Mar', 'Mié', 'Juv', 'Vie', 'Sáb'],
    dayNamesMin: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sá'],
    weekHeader: 'Sm',
    dateFormat: 'dd/mm/yy',
    firstDay: 1,
    isRTL: false,
    showMonthAfterYear: false,
    yearSuffix: ''
  };
  $.datepicker.setDefaults($.datepicker.regional['es']);

  function scroll0() {
    $('html,body').animate({scrollTop: 0}, 1000);
  }
</script>