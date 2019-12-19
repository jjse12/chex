
<style>
/* Popup container - can be anything you want */
.ui-datepicker-current-day{
    background-color: orange !important;
}

.alinear-izquierda {
  float:left;
  margin-left: 10px;
}

.alinear-derecha {
  float:right;
  margin-right: 10px;
}

.date-cell.selected {
    -fx-background-color: #3c983c !important;
    -fx-font-family: "Andalus";
    -fx-font-size: 16.0;
    -fx-text-fill: -fx-main-back;
}

.plan{
    font-size: 12px;
    padding: 0;
    margin: 0;
}

.plan, .icon-update, .seleccionado{
    cursor: pointer;
    text-align: center;
}
.sin-plan:hover{ 
    -webkit-transform: scale(1) !important;
}

.plan:hover{ 
    -webkit-transform: scale(1.25);
}
.icon-update:hover{ 
    -webkit-transform: scale(2); 
}

.popup, .popup-notif{
    position: relative;
    cursor: pointer;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
}

/* The actual popup */
.popup .popuptext {
    display: none;
    width: auto;
    background-color: #555;
    color: #fff;
    font-size: 18px;
    text-align: center;
    border-radius: 6px;
    padding-left: 15px;
    padding-right: 15px;
    position: absolute;
    z-index: 1;
    right: 102%;
    bottom: 9%;
    white-space: nowrap;
}

.popup-notif .popupicon {
    display: none;
    width: auto;
    background-color: transparent;
    padding-left: 12px;
    padding-right: 12px;
    position: absolute;
    z-index: 1;
    right: 100%;
    bottom: 0;
}

/* Popup arrow */
.popup .popuptext::after{
    content: "";
    position: absolute;
    top: 48%;
    left: 97%;
    border-width: 5px;
    border-style: solid;
    border-color: #555 transparent transparent transparent;
}

/* Add animation (fade in the popup) */
@-webkit-keyframes fadeIn {
    from {opacity: 0;} 
    to {opacity: 1;}
}

@keyframes fadeIn {
    from {opacity: 0;}
    to {opacity:1 ;}
}
</style>

<script>
    $(document).ready( function () {
        if (!isAdmin) document.getElementById("liBtnBoletas").style.display = "none";

        $("#divBtnHist").hover(function() {
          $(this).find('#dropmenusito').stop(true, true).delay(200).fadeIn(500);
        }, function() {
          $(this).find('#dropmenusito').stop(true, true).delay(200).fadeOut(500);
        });

        $("#liBtnBoletas").hover(function() {
          $('#dropsubmenusito').stop(true, true).delay(200).fadeIn(500);
        }, function() {
          $('#dropsubmenusito').stop(true, true).delay(200).fadeOut(500);
        });

        $("#liHistPaquetes").hover(function() {
            $('#dropsubmenusito1').stop(true, true).delay(200).fadeIn(500);
        }, function() {
            $('#dropsubmenusito1').stop(true, true).delay(200).fadeOut(500);
        });
    });
</script>

<div class="container" style="position: fixed; z-index: 1028; left: 0; right: 0; background-color: #fff;">
    <br>
    <br>
    <br>
    <hr>
    <div class='row col-lg-12 col-md-12 col-sm-12 col-xs-12'>
        <div class='col-lg-1 col-md-1 col-sm-1'></div>
        <div class='col-lg-3 col-md-3 col-sm-3 col-xs-4'>
            <a id="btnIngresar" style="width:100%; background: #eaeaea; color: #337ab7;" class="btn btn-md" onclick="switchContent(2)">Ingresar Carga</a>
        </div>
        <div class='col-lg-4 col-md-4 col-sm-4 col-xs-4'>
            <a id="btnInventario" style="width:100%; background: #5cb85c; color: white;" class="btn btn-md" onclick="switchContent(1)">Inventario</a>
        </div>
        <div id="divBtnHist" class='col-lg-3 col-md-3 co3-sm-3 col-xs-4' style="text-align: center;">
            <a  id="btnHistorico" style="width:100%; background: #eaeaea; color: #337ab7;" class="btn btn-md dropdown-toggle" data-toggle="dropdown">Histórico<span style="margin-left: 10px" class="caret"></span></a>
             <ul id='dropmenusito' class="dropdown-menu" style="text-align: center; background-color: #dadada; margin-left: 21%;">
                <li id="liHistPaquetes"><a id="btnHistPaquetes" onmouseenter="btnFocus(this, true)" onmouseleave="btnFocus(this, false)" style="text-align: center; background-color: #dadada; color: #337ab7; cursor: pointer;">Paquetes<span style="margin-left: 5px" class="caret"></span></a>
                    <ul id="dropsubmenusito1" class="dropdown-menu" style="text-align: center; background-color: #f1f1f1; top: 0; left: 100%;">
                        <li><a id="btnPaquetesBusqueda" onclick="switchContent(3,1)" onmouseenter="btnFocus(this, true)" onmouseleave="btnFocus(this, false)" style="text-align: center; background-color: #f1f1f1; color: #337ab7; cursor: pointer;">Buscar Paquete</a></li>
                        <li><a id="btnPaquetesFecha" onclick="switchContent(3,2)" onmouseenter="btnFocus(this, true)" onmouseleave="btnFocus(this, false)" style="text-align: center; background-color: #f1f1f1; color: #337ab7; cursor: pointer;">Paquetes por Fechas</a></li>
                        <li><a id="btnPaquetesHistorico" onclick="switchContent(3,3)" onmouseenter="btnFocus(this, true)" onmouseleave="btnFocus(this, false)" style="text-align: center; background-color: #f1f1f1; color: #337ab7; cursor: pointer;">Todos los Paquetes</a></li>
                    </ul>
                </li>
                <li><a id="btnHistRegistros" onclick="switchContent(4)" onmouseenter="btnFocus(this, true)" onmouseleave="btnFocus(this, false)" style="text-align: center; background-color: #dadada; color: #337ab7; cursor: pointer;">Cargas</a></li>
                <li id="liBtnBoletas"><a id="btnHistBoletas" onmouseenter="btnFocus(this, true)" onmouseleave="btnFocus(this, false)" style="text-align: center; background-color: #dadada; color: #337ab7; cursor: pointer;">Boletas<span style="margin-left: 5px" class="caret"></span></a>
                    <ul id="dropsubmenusito" class="dropdown-menu" style="text-align: center; background-color: #f1f1f1; top: 60%; left: 100%;">
                        <li><a id="btnBoletasPorLiquidar" onclick="switchContent(5)" onmouseenter="btnFocus(this, true)" onmouseleave="btnFocus(this, false)" style="text-align: center; background-color: #f1f1f1; color: #337ab7; cursor: pointer;">Por Liquidar</a></li>
                        <li><a id="btnBoletasLiquidadas" onclick="switchContent(6)" onmouseenter="btnFocus(this, true)" onmouseleave="btnFocus(this, false)" style="text-align: center; background-color: #f1f1f1; color: #337ab7; cursor: pointer;">Liquidadas</a></li>
                    </ul>
                </li>
            </ul>
        </div>
        
    </div>
    <br>
    <hr>
</div>

<div id="divInventario">
    <?php
        include("inventario.php");
    ?>
</div>
<div id="divRegistrarCarga" style="display: none">
    <?php
        include("ingresarCarga.php");
    ?>
</div>
<div id="divHistoricoRegistros" style="display: none">
    <?php
        include("historico.php");
    ?>
</div>

<script type="text/javascript">

    function btnFocus(boton, focus){
        if (boton.style.color != "white"){
            if (focus){
                if (boton.innerHTML == "Por Liquidar" || boton.innerHTML == "Liquidadas"){
                    if (document.getElementById("btnHistBoletas").style.backgroundColor == "#f1f1f1")
                        document.getElementById("btnHistBoletas").style.color = "orange";
                }
                boton.style.color = "orange";
            }
            else {
                if (boton.innerHTML == "Por Liquidar" || boton.innerHTML == "Liquidadas"){
                    if (document.getElementById("btnHistBoletas").style.backgroundColor == "#f1f1f1")
                    document.getElementById("btnHistBoletas").style.color = "#337ab7";
                }
                boton.style.color = "#337ab7";
            }
        }
    }

    function switchContent(num, opcion = 0){
        var ingreso = document.getElementById("btnIngresar");
        var invent = document.getElementById("btnInventario");
        var hist = document.getElementById("btnHistorico");
        var histPaquetes = document.getElementById("btnHistPaquetes");
        var histPaquetesBusqueda = document.getElementById("btnPaquetesBusqueda");
        var histPaquetesFecha = document.getElementById("btnPaquetesFecha");
        var histPaquetesTodos = document.getElementById("btnPaquetesHistorico");
        var histRegistros = document.getElementById("btnHistRegistros");
        var histBoletas = document.getElementById("btnHistBoletas");
        var histBoletasPorLiquidar = document.getElementById("btnBoletasPorLiquidar");
        var histBoletasLiquidadas = document.getElementById("btnBoletasLiquidadas");

        var divIngreso = document.getElementById("divRegistrarCarga");
        var divInventario =document.getElementById("divInventario");
        var divHistorico = document.getElementById("divHistoricoRegistros");
        
        divIngreso.style.display = "none";
        divInventario.style.display = "none";
        divHistorico.style.display = "none";

        ingreso.style.background = "#eaeaea";
        ingreso.style.color = "#337ab7";
        invent.style.background = "#eaeaea";
        invent.style.color = "#337ab7";
        hist.style.background = "#eaeaea";
        hist.style.color = "#337ab7";


        histPaquetes.style.background = "#dadada";
        histPaquetes.style.color = "#337ab7";
        histPaquetesBusqueda.style.background = "#f1f1f1";
        histPaquetesBusqueda.style.color = "#337ab7";
        histPaquetesFecha.style.background = "#f1f1f1";
        histPaquetesFecha.style.color = "#337ab7";
        histPaquetesTodos.style.background = "#f1f1f1";
        histPaquetesTodos.style.color = "#337ab7";

        histRegistros.style.background = "#dadada";
        histRegistros.style.color = "#337ab7";

        histBoletas.style.background = "#dadada";
        histBoletas.style.color = "#337ab7";
        histBoletasPorLiquidar.style.background = "#f1f1f1";
        histBoletasPorLiquidar.style.color = "#337ab7";
        histBoletasLiquidadas.style.background = "#f1f1f1";
        histBoletasLiquidadas.style.color = "#337ab7";

        $("#dropmenusito").stop(true, true).fadeOut(200);
        if (num === 1){
            loadInventario();
            divInventario.style.display = "block";
            invent.style.background = "#5cb85c";
            invent.style.color = "white";
        }
        else if (num === 2){
            divIngreso.style.display = "block";
            ingreso.style.background = "#5cb85c";
            ingreso.style.color = "white";
            document.getElementById("servicio").focus();
            initTablaIngresoCarga();
        }
        else {
            divHistorico.style.display = "block";
            hist.style.background = "#5cb85c";
            hist.style.color = "white";

            if (num == 3){
                histPaquetes.style.background = "#5cb85c";
                histPaquetes.style.color = "white";

                document.getElementById("divHistoricoCargas").style.display = "none";
                document.getElementById("divBoletas").style.display = "none";

                if (opcion === 1){
                    histPaquetesBusqueda.style.background = "#5cb85c";
                    histPaquetesBusqueda.style.color = "white";
                    historicoBusquedaPaquete();
                }
                else if (opcion === 2){
                    histPaquetesFecha.style.background = "#5cb85c";
                    histPaquetesFecha.style.color = "white";
                    historicoPaquetesFecha();
                }
                else if (opcion === 3){
                    histPaquetesTodos.style.background = "#5cb85c";
                    histPaquetesTodos.style.color = "white";
                    loadHistoricoPaquetes();
                }

            }
            else if (num == 4){
                histRegistros.style.background = "#5cb85c";
                histRegistros.style.color = "white";
                loadHistoricoCargas();
            }
            else{
                histBoletas.style.background = "#5cb85c";
                histBoletas.style.color = "white";

                if (num == 5){
                    histBoletasPorLiquidar.style.background = "#5cb85c";
                    histBoletasPorLiquidar.style.color = "white";
                    loadBoletasPorLiquidar();
                }
                else{
                    histBoletasLiquidadas.style.background = "#5cb85c";
                    histBoletasLiquidadas.style.color = "white";
                    loadBoletasLiquidadas();
                }
            }
        }
        $('html,body').animate({scrollTop: 0}, 1000);
    }

</script>