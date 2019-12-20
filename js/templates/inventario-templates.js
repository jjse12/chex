const renderPlanSelectionDialogContent = (uname, anonimo) => {
    return `
    <div class='row' style='background-color: #dadada'>
        <div class='col-lg-12 col-md-12 col-sm-12 col-xs-12'>
            <form novalidate>
                <br>
                <div class='control-group form-group col-lg-6 col-md-6 col-sm-6 col-xs-12'>
                    <label style='color: #337ab7; width:100%; text-align: center'>Plan de Entrega</label>
                    <button onclick='toggleActivadito(this)' id='btnOficina' style='width:49%; color:#337ab7'
                        type='button' class='btn btn-default'>Oficina</button>
                    <button onclick='toggleActivadito(this)' id='btnRuta' style='width:49%; color:#337ab7'
                        type='button' class='btn btn-default'>En Ruta</button>
                    <button onclick='toggleActivadito(this)' id='btnGuatex' style='width:49%; color:#337ab7'
                        type='button' class='btn btn-default'>Guatex</button>
                    <button onclick='toggleActivadito(this)' id='btnEsperando' style='width:49%; color:#337ab7'
                        type='button' class='btn btn-default'>Esperando</button>
                </div>
                <div class='col-lg-6 col-md-6 col-sm-6 col-xs-12'>
                    <div id='divFechaRuta' style='display:none'>
                        <label style='color: #696969; width:100%; text-align: center'>Fecha de Ruta</label>
                        <br>
                    </div>
                    <div id='divEsperandoCantidad' style='display:none'>
                        <label style='color: #696969; width:100%; text-align: center'>Cantidad de Paquetes Faltantes</label>
                        <input placeholder='Paquetes Faltantes' onkeyup='this.value=this.value.replace(/^0+/, \"\");'
                            onkeypress='return integersonly(this, event);' type='text' maxlength='2' style='text-align:center;'
                            class='form-control' id='form_carga_esperando'/>
                    </div>
                    <label style='${anonimo ? "display:none;":""} color: #696969; font-size:12px; padding-left:20%; 
                        padding-right: 20%; width:100%; text-align: center'>Aplicar a todos los paquetes de ${uname}
                    </label>
                    <input type='checkbox' ${anonimo ? "style='display:none;'":""}
                        class='form-control' id='form_carga_check_esperando'/>
                    <br>
                </div>
                <br>
            </form>
        </div>
    </div>`;
};

const renderMultiplePlanSelectionDialogContent = (checkLabel, anonimo) => {
    return `
        <div class='row' style='background-color: #dadada'>
            <div class='col-lg-12 col-md-12 col-sm-12 col-xs-12'>
                <form novalidate>
                    <br><br>
                    <div class='control-group form-group col-lg-6 col-md-6 col-sm-6 col-xs-12'>
                    <label style='color: #337ab7; width:100%; text-align: center'>Plan de Entrega</label>
                        <button onclick='toggleActivadito(this)' id='btnOficina' style='width:49%; color:#337ab7'
                            type='button' class='btn btn-default'>Oficina</button>
                        <button onclick='toggleActivadito(this)' id='btnRuta' style='width:49%; color:#337ab7'
                            type='button' class='btn btn-default'>En Ruta</button>
                        <button onclick='toggleActivadito(this)' id='btnGuatex' style='width:49%; color:#337ab7'
                            type='button' class='btn btn-default'>Guatex</button>
                        <button onclick='toggleActivadito(this)' id='btnEsperando' style='width:49%; color:#337ab7'
                            type='button' class='btn btn-default'>Esperando</button>
                    </div>
                    <div class='col-lg-6 col-md-6 col-sm-6 col-xs-12'>
                        <div id='divFechaRuta' style='display:none'>
                            <label style='color: #696969; width:100%; text-align: center'>Fecha de Ruta</label>
                            <br>
                        </div>
                        <div id='divEsperandoCantidad' style='display:none'>
                            <label style='color: #696969; width:100%; text-align: center'>Cantidad de Paquetes Faltantes</label>
                            <input placeholder='Paquetes Faltantes' onkeyup='this.value=this.value.replace(/^0+/, \"\");'
                                onkeypress='return integersonly(this, event);' type='text' maxlength='2'
                                style='text-align:center;'  class='form-control' id='form_carga_esperando'/>
                        </div>
                        <label style="${anonimo ? "display:none;":""} color: #696969; font-size:12px; padding-left:20%; padding-right: 20%; width:100%; text-align: center">${checkLabel}</label>
                        <input type='checkbox' ${anonimo ? "style='display:none;'":""} class='form-control' id='form_carga_check_esperando'/>
                        <br>
                    </div>
                    <br>
                </form>
            </div>
        </div>`;
};

const renderModificarPaqueteDialogContent = (paquete) => {
    const {
        celulares, extras, fechaIng, rcid, tracking, uid, uname, peso
    } = paquete;
    return `
    <div class='row' style='background-color: #dadada'>
        <div class='col-lg-12 col-md-12 col-sm-12 col-xs-12'>
            <form novalidate>
                <div class='control-group form-group col-lg-4 col-md-4 col-sm-4 col-xs-4'>
                    <div class='controls'>
                        <label style='color: #337ab7; text-align:center; width:100%'># Tracking </label>
                        <input value='${tracking}' type='text' style='text-align: center;' class='form-control' readonly />
                    </div>
                </div>
                <div class='control-group form-group col-lg-4 col-md-4 col-sm-4 col-xs-4'>
                    <div class='controls'>
                        <label style='color: #337ab7; text-align:center; width:100%'># Registro de Cargas</label>
                        <input readonly value='${rcid}' type='text' style='width:100%; text-align: center;' class='form-control'/>
                    </div>
                </div>
                <div class='control-group form-group col-lg-4 col-md-4 col-sm-4 col-xs-4'>
                    <div class='controls'>
                        <label style='color: #337ab7; text-align:center; width:100%'>Fecha de Ingreso</label>
                        <input readonly value='${fechaIng}' type='text' style='width:100%; text-align: center;' class='form-control'/>
                    </div>
                </div>
                <div class='control-group form-group col-lg-3 col-md-3 col-sm-3 col-xs-3'>
                    <div class='controls'>
                        <label style='color: #337ab7; text-align:center; width:100%'>Peso</label>
                        <input placeholder='Peso' value='${peso}' onkeyup='this.value=this.value.replace(/^0+/, \"\");' 
                            onkeypress='return integersonly(this, event);' type='text' maxlength='3' 
                            style='text-align:center;' class='form-control' id='form_carga_libras'/>
                    </div>
                </div>
                <div class='control-group form-group col-lg-3 col-md-3 col-sm-3 col-xs-3'>
                    <div class='controls'>
                        <label style='color: #337ab7; text-align:center; width:100%'>ID Cliente</label>
                        <input onfocusout='getUserName2(this.value)' value='${uid}' style='text-align: center;' 
                            type='text' maxlength='7' class='form-control' placeholder='ID Cliente'
                            id='form_carga_uid'/>
                        <div id='spanIDCliente' style='display:none'>
                            <span class='dialog-text'> Atención: No existe ningún cliente asociado a este ID.</span>
                        </div>
                    </div>
                </div>
                <div class='control-group form-group col-lg-6 col-md-6 col-sm-6 col-xs-6'>
                    <div class='controls'>
                        <label style='color: #337ab7; text-align:center; width:100%'>Nombre Cliente</label>
                        <input placeholder='Nombre Cliente' value='${uname}' style='text-align: center;' 
                            type='email' maxlength='50' class='form-control' id='form_carga_uname' />
                    </div>
                </div>
                <div class='control-group form-group col-lg-3 col-md-3 col-sm-3 col-xs-3'>
                    <div class='controls'>
                        <label style='color: #337ab7; text-align:center; width:100%'>Celulares</label>
                        <input placeholder='Cantidad' value='${celulares > 0 ? celulares : ""}'
                            onkeyup='this.value=this.value.replace(/^0+/, \"\");' onkeypress='return integersonly(this, event);'
                            type='text' maxlength='3' style='text-align:center;' class='form-control' id='form_carga_celulares'/>
                    </div>
                </div>
                <div class='control-group form-group col-lg-3 col-md-3 col-sm-3 col-xs-3'>
                    <div class='controls'>
                        <label style='color: #337ab7; text-align:center; width:100%'>Cobro Extra</label>
                        <input placeholder='Monto (Q)' value='${extras > 0 ? extras : ""}'
                            onkeyup='this.value=this.value.replace(/^0+/, \"\");' onkeypress='return integersonly(this, event);'
                            type='text' maxlength='5' style='text-align:center;' class='form-control' id='form_carga_cobro_extra'/>
                    </div>
                </div>
                <div class='control-group form-group col-lg-6 col-md-6 col-sm-6 col-xs-6'>
                    <label style='color: #337ab7; width:100%; text-align: center'>Plan de Entrega</label>
                    <button onclick='toggleActivadito(this)' id='btnOficina' style='width:49%; color:#337ab7' type='button' class='btn btn-default'>Oficina</button>
                    <button onclick='toggleActivadito(this)' id='btnRuta' style='width:49%; color:#337ab7' type='button' class='btn btn-default'>En Ruta</button>
                    <button onclick='toggleActivadito(this)' id='btnGuatex' style='width:49%; color:#337ab7' type='button' class='btn btn-default'>Guatex</button>
                    <button onclick='toggleActivadito(this)' id='btnEsperando' style='width:49%; color:#337ab7' type='button' class='btn btn-default'>Esperando</button>
                </div>
                <div class='col-lg-offset-3 col-md-offset-3 col-sm-offset-3 col-xs-offset-3 col-lg-6 col-md-6 col-sm-6 col-xs-6' style='margin-bottom: 10px;'>
                    <div id='divFechaRuta' style='display:none'>
                        <label style='color: #696969; width:100%; text-align: center'>Fecha de Ruta</label>
                        <br>
                    </div>
                    <div id='divEsperandoCantidad' style='display:none'>
                        <label style='color: #696969; width:100%; text-align: center'>Cantidad de Paquetes Faltantes</label>
                        <input placeholder='Paquetes Faltantes' onkeyup='this.value=this.value.replace(/^0+/, \"\");'
                            onkeypress='return integersonly(this, event);' type='text' maxlength='2' style='text-align:center;'
                            class='form-control' id='form_carga_esperando'/>
                    </div>
                </div>
                <br>
            </form>
        </div>
    </div>`;
};

const renderNotificationOptionsDialogContent = (searchByUid) => {
    return `
        <div class='row'>
            <div class='row'>
                <div class='row'>
                    <img class='col-xs-offset-1 col-sm-offset-1 col-md-offset-1 col-lg-offset-1 col-lg-5 col-md-5 col-sm-5 col-xs-5' align='middle'
                         style='cursor: pointer;' src='images/whatsapp128px.png' onclick='notificarViaWhatsApp(${searchByUid})' alt="Notificar vía WhatsApp"/>
                    <img class='col-lg-5 col-md-5 col-sm-5 col-xs-5' align='middle' style='cursor: pointer;' src='images/email128px.png' onclick='notificarViaEmail(${searchByUid})' alt="Notifica via Email"/>
                </div>
                <div class='row'>
                    <label class='col-xs-offset-1 col-sm-offset-1 col-md-offset-1 col-lg-offset-1 col-lg-5 col-md-5 col-sm-5 col-xs-5'
                           style='text-align: center; color: black; cursor: pointer;' onclick='notificarViaWhatsApp(${searchByUid})'>
                        Vía Whatsapp
                    </label>
                    <label class='col-lg-5 col-md-5 col-sm-5 col-xs-5' style='text-align: center; color: black; cursor: pointer;' onclick='notificarViaEmail(${searchByUid})'>
                        Vía Correo Electrónico
                    </label>
                </div>
            </div>
        </div>`;
};