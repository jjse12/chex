<link href="css/jquery.dataTables.css" rel="stylesheet" type="text/css">
<link href="css/dataTables.tableTools.css" rel="stylesheet" type="text/css">
<link href="css/dataTables.bootstrap.css" rel="stylesheet" type="text/css">
<link href="css/dataTables.colVis.css" rel="stylesheet" type="text/css">
<link href="css/dataTables.select.min.css" rel="stylesheet" type="text/css">
<link href="css/dataTables.responsive.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="js/jquery.dataTables.js"></script>
<script type="text/javascript" src="js/dataTables.tableTools.js"></script>
<script type="text/javascript" src="js/dataTables.bootstrap.js"></script>
<script type="text/javascript" src="js/dataTables.colVis.js"></script>
<script type="text/javascript" src="js/dataTables.responsive.js"></script>
<script type="text/javascript" src="js/dataTables.select.min.js"></script>
<script type="text/javascript" src="js/clientes.js?v=1.2.1"></script>
<script src="js/templates/clientes-templates.js?v=1.2.1"></script>

<br><br>
<br><br>


<div class="col-sm-12 text-center">
  <span id="clientLastSyncDatetime" class="h5">Última Actualización...</span>
</div>

<div class="row" id="divTablaClientes">
    <table id="clientes" class="display text-center" width="100%" cellspacing="0" style="width: 100%;">
        <thead>
            <tr>
                <th class="dt-head-center"><h5 style="color:black">No.</h5></th>
                <th class="dt-head-center"><h5 style="color:black">Código Chex</h5></th>
                <th class="dt-head-center"><h5 style="color:black">Nombre</h5></th>
                <th class="dt-head-center"><h5 style="color:black">Apellido</h5></th>
                <th class="dt-head-center"><h5 style="color:black">Teléfono</h5></th>
                <th class="dt-head-center"><h5 style="color:black">Email</h5></th>
                <th class="dt-head-center"><h5 style="color:black">Dirección</h5></th>
                <th class="dt-head-center"><h5 style="color:black"></h5></th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
            </tr>
        </tfoot>
        <tbody>
        </tbody>
    </table>
</div>