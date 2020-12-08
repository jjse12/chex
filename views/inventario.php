<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/dt-1.10.16/r-2.2.0/datatables.min.css"/>
<script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.10.16/r-2.2.0/datatables.min.js"></script>

<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css">
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<script src="js/inventario.js?v=1.0.9"></script>
<script src="js/templates/inventario-templates.js?v=1.0.6"></script>

<div class="container" style="padding-top: 4.5cm">
    <table id="inventario" class="display compact" width="100%" cellspacing="0">
        <thead>
            <tr>
                <th></th>
                <th class="dt-head-center"><span style="color: transparent; -webkit-text-stroke-width: 2px; -webkit-text-stroke-color: gold"><i class="fa fa-star fa-2x"></i></span></th>
                <th class="dt-head-center"><h6 style="color:black">Fecha de Ingreso</h6></th>
                <th class="dt-head-center"><h6 style="color:black">Tipo de Servicio</h6></th>
                <th class="dt-head-center"><h6 style="color:black">No. de Guía</h6></th>
                <th class="dt-head-center"><h6 style="color:black"># Tracking</h6></th>
                <th class="dt-head-center"><h6 style="color:black">ID Cliente</h6></th>
                <th class="dt-head-center"><h6 style="color:black">Nombre Cliente</h6></th>
                <th class="dt-head-center"><h6 style="color:black">Peso</h6></th>
                <th class="dt-head-center"><h6 style="color:black">Plan de Entrega</h6></th>
                <th></th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <th></th>
                <th></th>
                <th class="dt-head-center"><input class="buscarIngreso" type="text" placeholder="Buscar"/></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th colspan="2" class="dt-head-right"></th>
                <th class="dt-head-center"><input class="buscarPlan" type="text" placeholder="Buscar"/></th>
                <th></th>
            </tr>
        </tfoot>
        <tbody>
        </tbody>
    </table>
</div>

<div class="container" align="center" style="background-color: white; position: fixed; left: 0; right: 0; bottom: 0; padding-bottom: 6px; z-index: 100; visibility: hidden;" id="divBotones">
    <div class='col-lg-12 col-md-12 col-sm-12 col-xs-12' style="padding-top: 6px;">
        <div class='col-sm-offset-1 col-sm-10'>
            <button onclick="notificarSeleccionados()" class="btn btn-primary" align="center" style="width:24%; text-align: center ;">Notificar</button>
            <button onclick="entregarSeleccionados()" class="btn btn-success" align="center" style="width: 25%; text-align: center ;">Entregar Mercadería</button>
            <button onclick="showCostoMercaderia(false) " class="btn btn-info" align="center" style="width: 25%; text-align: center;">Ver Costos</button>
            <button onclick="planificarEntrega()" class="btn btn-warning" align="center" style="width: 24%; text-align: center;">Plan de Entrega</button>
        </div>
    </div>
</div>