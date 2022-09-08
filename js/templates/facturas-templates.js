
const renderFacturaFieldEditDialog = (value, inputId, field, extra) => {
    return `
        <div class='row' style='background-color: #dadada'>
            <div class='col-lg-12 col-md-12 col-sm-12 col-xs-12'>
                <p style='color: black'>Ingresa el nuevo valor para el campo <b>${field}</b>.</p>
                <div class='control-group form-group col-sm-offset-3 col-md-offset-3 col-lg-offset-3 col-sm-6 col-md-6 col-lg-6 col-xs-12'>
                    <div class='controls'>
                        <input align='middle' style='text-align:center; width: 100%;' 
                                placeholder="Nuevo valor" id='${inputId}' value="${value}" ${extra}>
                    </div>
                </div>
            </div>
        </div>`;
};

const renderFacturaServiceEditDialog = (value, inputId, field, services) => {
    let options = '';
    Object.values(services).forEach(({ id, nombre }) => {
        options += `<option data-nombre="${nombre}" value="${id}" ${value === nombre ? 'selected' : ''}>${nombre}</option>`;
    });
    return `
        <div class='row' style='background-color: #dadada'>
            <div class='col-lg-12 col-md-12 col-sm-12 col-xs-12'>
                <p style='color: black'>Ingresa el nuevo valor para el campo <b>${field}</b>.</p>
                <div class='control-group form-group col-sm-offset-3 col-md-offset-3 col-lg-offset-3 col-sm-6 col-md-6 col-lg-6 col-xs-12'>
                    <div class='controls'>
                        <select class="form-control" id='${inputId}' style='text-align:center; width: 100%;'>
                            ${options}
                        </select>
                    </div>
                </div>
            </div>
        </div>`;
};

const facturaImage = fm => {
    if (fm.image_type === 'application/pdf'){
        return `<hr><div class="factura-image factura-image-pdf" data-id="${fm.id}"><object type="${fm.image_type}" data="data:${fm.image_type};base64, ${fm.image}" width='100%' height='500px'></object></div>`;
    }
    return `<hr><img alt="image-${fm.id}" class="factura-image" data-id="${fm.id}" src="data:${fm.image_type};base64, ${fm.image}" />`;
};

const renderFacturaDetails = (factura) => {
    const { date_created, id, tracking, amount, description, service, user_chex_code,
        item_count,fob_price, guide_number, pendiente, images
    } = factura;
    let status = pendiente === '1' ? 'fa fa-times fa-1x fa-lg' : 'fa fa-check fa-1x fa-lg';
    let statusColor = pendiente === '1' ? 'orange' : 'lime';

    let imagesHtml = '';
    images.forEach(img => {
        imagesHtml += facturaImage(img);
    });

    let date = moment(date_created);
    date = date.format('LLL');
    return `
        <div class="container-flex">
            <div>
                <b>Enviada: </b><i style="color: ${statusColor}" class='${status}'></i><br>
                <b>Fecha de Creación: </b><span /*class="factura-editable" data-column="date_created" data-id="${id}" data-field="Fecha de Creación"*/>${date}</span><br>
                <b>Id Cliente:</b> <span /*class="factura-editable" data-column="user_chex_code" data-id="${id}" data-field="Id Cliente"*/>${user_chex_code}</span><br>
                <b>Tipo de Servicio:</b> <span class="${isAdmin ? 'factura-editable' : ''}" data-column="service_id" data-id="${id}" data-field="Tipo de Servicio">${service}</span><br>
                <b>Tracking:</b> <span class="factura-editable" data-column="tracking" data-id="${id}" data-field="Tracking">${tracking}</span><br>
                <b>Cantidad de Artículos:</b> <span class="factura-editable" data-column="item_count" data-id="${id}" data-field="Artículos">${item_count || 'N/A'}</span><br>
                <b>Monto:</b> <span class="factura-editable" data-column="amount" data-id="${id}" data-field="Monto">US$ ${amount}</span><br>
                <b>Precio FOB:</b> <span class="factura-editable" data-column="fob_price" data-id="${id}" data-field="Precio FOB">${fob_price !== null ? `US$ ${fob_price}` : 'N/A'}</span><br>
                <b>Descripción:</b> <span class="factura-editable" data-column="description" data-id="${id}" data-field="Descripción">${description}</span><br>
                <b>No. Guía:</b> <span class="factura-editable" data-column="guide_number" data-id="${id}" data-field="Número de Guía">${guide_number || 'N/A'}</span>
            </div>
            <div id="divImageActions">
                <hr>
                ${images.length > 0 ?
                    `<button id="confirmDeleteFacturaImages" data-id="${id}"
                        style="display: none" class="btn btn-sm btn-danger" disabled>
                        Eliminar Seleccionadas
                    </button>
                    <button id="deleteFacturaImages" class="btn btn-sm btn-danger" data-toggle="collapse"
                        data-target="#divInstructions" aria-expanded="false" aria-controls="divInstructions">
                        Eliminar Imágenes
                    </button>` : ''
                }
                <button id="confirmAddFacturaImages" data-id="${id}" style="display: none" class="btn btn-sm btn-success" disabled>Subir Imágenes</button>
                <button id="addFacturaImages" data-id="${id}" data-toggle="collapse" 
                    data-target="#divAddImages" aria-expanded="false" aria-controls="divAddImages"
                    class="btn btn-sm btn-success"
                  >Agregar Imágenes</button>
                <div id="divAddImages" class="collapse">
                    <br>
                    <small class="mt-3">Busca y selecciona las imágenes que deseas agregar, luego presiona el botón "Subir Imágenes".</small>
                    <div class="row">
                      <form id="addImagesForm">
                          <input type="hidden" name="factura_id" value="${id}"/>
                          <input class="col-sm-offset-3 col-sm-6 mt-3" type="file" id="imgs" name="imgs[]" accept="image/jpeg,image/png,image/bmp,image/tiff,image/tif,application/pdf,image/pdf" multiple>
                      </form>
                    </div>
                </div>
                <div id="divInstructions" class="collapse"
                    <br><br>
                    <small class="mt-3">Selecciona las imágenes que deseas eliminar haciendo click sobre ellas, luego presiona el botón "Eliminar Seleccionadas".</small>
                </div>
            </div>
            <div class="factura-content">
                ${images.length > 0 ? imagesHtml : '<hr><h5>¡No hay imágenes asociadas a esta factura!</h5>'}
            </div>
        </div>`;
};

const renderFacturaLogisticaEmpty = factura => {
    return `
        <div class="text-center">
            <h5>Seguimiento de Paquete en Bodega</h5>
        </div>
        <br>
        <div class="text-center">
            ¡Aún no existe registro!&nbsp;&nbsp;&nbsp;&nbsp;
            <button data-factura-id='${JSON.stringify(factura)}' class='btn btn-success btn-sm btnCreateFacturaLogistica'>Crear Registro</button></>
        </div>
        <br>
    `;
};

const renderFacturaLogistica = (logistica, factura) => {
    let received = logistica.miami_received;
    const checkBoxAttribute = received ? 'checked="true"' : (received === 0 ? 'readonly="true"' : '');
    return `
        <div class="text-center">
            <h5>Seguimiento de Paquete en Bodega</h5>
        </div>
        <div id="divFacturaLogisticaContent">
            <div class="form-row">
                <div class="form-group">
                    <label for="factura-date-delivered" style='color: #696969;'>Fecha de Delivery :</label>
                    <input type="text" data-original="${logistica.date_delivered}" id="factura-date-delivered"
                        placeholder="Fecha de Delivery" class="disabable form-control text-center" 
                        value="${logistica.date_delivered}">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="factura-courier" style="color: #696969">Courier :</label>
                    ${couriersSelectBox(logistica.courier)}
                </div>
                <div class="form-group col-md-6">
                    <label for="factura-signer" style="color: #696969">Firmado por :</label>
                    ${signersSelectBox(logistica.signer)}
                </div>
            </div>
            <div class="form-row text-center">
                <div class="form-group">
                    <label class="form-check-label" for="factura-miami-received" style="color: #696969">
                        Recibido en Miami :&nbsp;&nbsp;&nbsp;&nbsp;
                    </label>
                    <input type="checkbox" class="disabable form-check-input" data-original="${received}" 
                        id="factura-miami-received" onclick="handleMiamiReceivedCheckBox(this)" ${checkBoxAttribute}>
                </div>
            </div>
            <div id="divDateReceived" class="form-row collapse ${logistica.miami_received === 1 ?
    'in" aria-expanded="true" style="' : '" aria-expanded="false" style="height: 0px;'}">
                <div class="form-group">
                    <label for="factura-date-received" style='color: #696969;'>Fecha de Recibido :</label>
                    <input type="text" data-original="${logistica.date_received}" id="factura-date-received"
                        placeholder="Fecha de Recibido en Miami" class="disabable form-control text-center" 
                        value="${logistica.date_received}">
                </div>
            </div>
            <div class="form-group">
                <div class="text-left">
                    <label class="form-check-label" for="factura-client-notified" style="color: #696969">
                        Cliente Notificado :&nbsp;&nbsp;&nbsp;&nbsp;
                    </label>
                    <input type="checkbox" class="form-check-input" data-original="${logistica.client_notified}" 
                        id="factura-client-notified" ${logistica.client_notified ? 'checked="true"':''}>
                </div>
                <textarea class="disabable form-control" id="factura-comment" maxlength="512"
                        data-original="${logistica.comment}" placeholder="Ingresa un comentario">${logistica.comment}
                </textarea>
            </div>
        </div>
        <div class="text-center">
            <button class="btn btn-sm btn-primary" id="btnToggleLogistica" onclick="toggleLogistica()">Modificar</button>
            <button class="btn btn-sm btn-success btnUpdateFacturaLogistica" data-factura-id='${JSON.stringify(factura)}'>Guardar</button>
        </div>
        <br>
    `;
};

const renderSeguimientoNote = seguimiento => {
    let date = moment(seguimiento.date_created);
    date = date.format('[El&nbsp;&nbsp;]DD/MM/YYYY[&nbsp;&nbsp;a&nbsp;&nbsp;las&nbsp;&nbsp;]hh:mm A');

    return `
        <div class="seguimiento-register">
            <div class="row">
                <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 text-left">
                    &nbsp;&nbsp;&nbsp;${seguimiento.creator} :
                </div>
                <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 text-right">
                    ${date}&nbsp;&nbsp;&nbsp;
                </div>
            </div>
            <div class="row" style="margin-top: 8px !important;">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <textarea class="form-control" disabled>${seguimiento.note}</textarea>
                </div>
            </div>
        </div>
    `;
};

const renderFacturaSeguimiento = (seguimiento, factura) => {
    let seguimientosContent = '<div class="text-center">- Sin notas de seguimiento de cliente -</div>';
    if (seguimiento.length){
        seguimientosContent = `<div class="text-center"><b>- ${seguimiento.length} ${seguimiento.length === 1 ? 'Nota' : 'Notas'} de Seguimiento -</b>`;
        seguimiento.map(seg => {
            seguimientosContent += renderSeguimientoNote(seg);
        });
        seguimientosContent += '</div>';
    }

    return `
        <div class="text-center">
            <h5>Seguimiento de Cliente</h5>
        </div>
        <br>
        <div class="text-center">
            <button class="btn btn-sm btn-success" id="btnNewSeguimiento" onclick="toggleNewSeguimiento()" data-toggle="collapse" 
                    data-target="#divNewSeguimiento" aria-expanded="false" aria-controls="divDateReceived">
                Nueva Nota de Seguimiento
            </button>
            <button class="btn btn-sm btn-success btnCreateSeguimiento" style="display: none;" data-factura='${JSON.stringify(factura)}'>Guardar</button>
        </div>
        <div class="collapse" id="divNewSeguimiento">
            <br>
            <div class="form-group">
                <textarea class="disabable form-control" id="txt-new-seguimiento" maxlength="512" placeholder="Ingresa la nota de seguimiento..."></textarea>
            </div>
        </div>
        <hr>
        ${seguimientosContent}
        <br>
    `;
};

