
<div class="container" style="padding-top: 4.5cm">
    <br>
    <div class="row" >
        <div class="row-same-height">
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 col-lg-height col-md-height col-sm-height col-xs-height">
                <div class="row">
                    <div class="col-md-6 col-xs-6">
                        <label class="text-color-gray">Tipo de Servicio:</label>
                        <div id="divServices"><span>Cargando...</span></div>
                    </div>
                    <div class="col-md-6 col-xs-6">
                        <label class="text-color-gray" for="guide_number">No. de Guía:</label>
                        <input type="text" class="form-control" id="guide_number" required
                           placeholder="Número de Guía" maxlength="10" max="2147483647" onkeypress="return integersonly(this, event)"
                               onkeyup="this.value=this.value.replace(/^0+/, '');">
                    </div>
                </div>
                    <div class="row">
                        <div class="col-md-6 col-xs-6">
                            <label class="text-color-gray">Tracking:</label>
                            <div>
                                <input onfocusout="checkTrackingExists(this.value)" class="text-field form-control validate-field required" id="tracking" required placeholder="# Tracking" maxlength="50" onkeypress="return onlyLettersAndNumbers(this, event)" data-validation-required-message="Ingresa el numero de tracking del paquete.">
                            </div>
                        </div>
                        <div class="col-md-6 col-xs-6">
                            <label class="text-color-gray">Peso en libras:</label>
                            <div>
                                <input type="text" class="form-control" id="peso" required placeholder="Peso" maxlength="3" onkeypress="return integersonly(this, event)" onkeyup="this.value=this.value.replace(/^0+/, '');">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 col-xs-6">
                            <label class="text-color-gray">ID del Cliente:</label>
                            <div>
                                <input onfocusout="getUserName(this.value)" class="text-field form-control validate-field required" id="uid" required placeholder="ID Cliente" maxlength="7" data-validation-required-message="Ingresa el ID del cliente." onkeypress="return onlyLettersAndNumbers(this, event)">
                                <div id="spanID" style="display:none">
                                    <span class="dialog-text"> Atención: No existe ningún cliente asociado a este ID.</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-xs-6">
                            <label class="text-color-gray">Nombre del Cliente:</label>
                            <div>
                                <input class="text-field form-control validate-field required" id="uname" required placeholder="Nombre Cliente" data-validation-required-message="Ingresa el nombre del cliente." maxlength="50" onkeypress="return notAllow(this, event, ',<>\'')">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 col-xs-6">
                            <br>
                            <button class="btn btn-lg btn-success" onclick="agregarCarga(this)" style="text-align: center; width:100%; ">Agregar Paquete</button>
                            <div id="spanAgregarCarga" style="display:none">
                                <span class="dialog-text"> Por favor asegúrate de llenar todos los campos.</span>
                            </div>
                        </div>
                        <div class="col-md-12 col-xs-6">
                            <br>
                            <button class="btn btn-sm btn-warning" onclick="agregarRegistro()" style="text-align: center; width:100%; ">Guardar Registro de Carga</button>
                            <div id="spanAgregarRegistro" style="display:none">
                                <span class="dialog-text"> Se debe agregar por lo menos un paquete.</span>
                            </div>
                        </div>
                    </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 col-lg-height col-md-height col-sm-height col-xs-height" id="divResCotizacion">
                <br>
                <div class="panel panel-bordeado flex-col">
                    <div class="row">
                        <strong><h2 id="paquetes" class="header-title" style="color:#444">Paquetes: 0</h2></strong>
                    </div>
                    <div class="row">
                        <strong><h2 id="libras" class="header-title" style="color:#338">Libras: 0</h2></strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="emptyrow"></div>
    <div>
        <table id="tablaNuevaCarga" class="display">
            <thead>
                <tr>
                    <th class="dt-head-center"><h5 style="color:black">Servicio</h5></th>
                    <th class="dt-head-center"><h5 style="color:black">No. de Guía</h5></th>
                    <th class="dt-head-center"><h5 style="color:black">Tracking</h5></th>
                    <th class="dt-head-center"><h5 style="color:black">ID Cliente</h5></th>
                    <th class="dt-head-center"><h5 style="color:black">Nombre Cliente</h5></th>
                    <th class="dt-head-center"><h5 style="color:black">Peso</h5></th>
                    <th></th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th colspan="2" class="dt-head-left"></th>
                </tr>
            </tfoot>
            <tbody>
            </tbody>
        </table>
    </div>
</div>

<script src="js/ingreso-carga.js?v=1.0.2"></script>