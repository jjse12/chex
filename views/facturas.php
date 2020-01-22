<script src="js/facturas.js?v=1.0.2"></script>
<script src="js/templates/facturas-templates.js?v=1.0.1"></script>
<script src="factura_logistica.js?v=1.0.1"></script>
<link href="css/facturaStyles.css?v=1.0.1" rel="stylesheet">

<br><br>
<br><br>

<div class="row" id="divTablaFacturas">
    <table id="facturas" class="display" width="100%" cellspacing="0" style="width: 100%;">
        <thead>
        <tr>
            <th class="dt-head-center"><h5 style="color:black">Tipo de Servicio</h5></th>
            <th class="dt-head-center"><h5 style="color:black">Fecha de Creación</h5></th>
            <th class="dt-head-center"><h5 style="color:black">Avisado</h5></th>
            <th class="dt-head-center"><h5 style="color:black">Estado</h5></th>
            <th class="dt-head-center"><h5 style="color:black">Fecha Delivery</h5></th>
            <th class="dt-head-center"><h5 style="color:black">Fecha Miami Delivery</h5></th>
            <th class="dt-head-center"><h5 style="color:black"># Tracking</h5></th>
            <th class="dt-head-center"><h5 style="color:black">ID Cliente</h5></th>
            <th class="dt-head-center"><h5 style="color:black">Nombre Cliente</h5></th>
            <th class="dt-head-center"><h5 style="color:black">Costo</h5></th>
            <th class="dt-head-center"><h5 style="color:black"></h5></th>
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

<div class="container" align="center" id="divFacturaOpciones"
     style="background-color: white; position: fixed; left: 0; right: 0; bottom: 0; padding-bottom: 6px; z-index: 100; visibility: hidden;">
    <div class='col-lg-12 col-md-12 col-sm-12 col-xs-12' style="padding-top: 6px;">
        <div class='col-lg-2 col-md-2 col-sm-2'></div>
        <div class='col-lg-8 col-md-8 col-sm-8 col-xs-12'>
            <button id="btnPDF" onclick="generarPDF()" class="btn-lg" align="center" style="background: limegreen; color: white; width:50%; text-align: center ;">Generar PDF</button>
            <button id="btnEliminarFacturas" class="btn-lg btn-danger" align="center" style="width:49%; text-align: center ;">Eliminar Facturas</button>
        </div>
    </div>
</div>