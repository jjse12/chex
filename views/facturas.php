<link href="/css/jquery.dataTables.css" rel="stylesheet" type="text/css">
<link href="/css/dataTables.tableTools.css" rel="stylesheet" type="text/css">
<link href="/css/dataTables.bootstrap.css" rel="stylesheet" type="text/css">
<link href="/css/dataTables.colVis.css" rel="stylesheet" type="text/css">
<link href="/css/dataTables.responsive.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="/js/jquery.dataTables.js"></script>
<script type="text/javascript" src="/js/dataTables.tableTools.js"></script>
<script type="text/javascript" src="/js/dataTables.bootstrap.js"></script>
<script type="text/javascript" src="/js/dataTables.colVis.js"></script>
<script type="text/javascript" src="/js/dataTables.responsive.js"></script>

<!--<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.5.3/jspdf.debug.js" integrity="sha384-NaWTHo/8YCBYJ59830LTz/P4aQZK1sS0SneOgAvhsIl3zBu8r9RevNg5lHCHAuQ/" crossorigin="anonymous"></script>-->
<script src="/js/facturas.js"></script>
<link href="/css/facturaStyles.css" rel="stylesheet">

<br><br>
<br><br>

<div class="row" id="divTablaFacturas">
    <table id="facturas" class="display" width="100%" cellspacing="0" style="width: 100%;">
        <thead>
        <tr>
            <th class="dt-head-center"><h5 style="color:black">Estado</h5></th>
            <th class="dt-head-center"><h5 style="color:black">Fecha</h5></th>
            <th class="dt-head-center"><h5 style="color:black"># Tracking</h5></th>
            <th class="dt-head-center"><h5 style="color:black">ID Cliente</h5></th>
            <th class="dt-head-center"><h5 style="color:black">Nombre Cliente</h5></th>
            <th class="dt-head-center"><h5 style="color:black">Costo</h5></th>
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
        </tr>
        </tfoot>
        <tbody>
        </tbody>
    </table>
</div>

<div class="container" align="center" id="divFacturaOpciones"
     style="background-color: white; position: fixed; left: 0; right: 0; bottom: 0; padding-bottom: 6px; z-index: 10026; visibility: hidden;">
    <div class='col-lg-12 col-md-12 col-sm-12 col-xs-12' style="padding-top: 6px;">
        <div class='col-lg-2 col-md-2 col-sm-2'></div>
        <div class='col-lg-8 col-md-8 col-sm-8 col-xs-12'>
            <button id="btnPDF" onclick="generarPDF()" class="btn-lg btn-primary" align="center" style="width:60%; text-align: center ;">Generar PDF</button>
        </div>
    </div>
</div>