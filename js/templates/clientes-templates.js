const renderClientDataModal = (client, resourcesOptionsLists, clientTableIndex) => {
  return `
    <div>
      <form id="clienteForm">  
        <div class="container-flex">
          <div class="row background-whitesmoke">
            <span class="col-sm-12 h4 text-left mt-0">Datos Generales</span>
            <div class="col-sm-6">
              <div class="form-group col-sm-6">
                <label class="text-color-gray" for="clienteNombre">Nombre</label>
                <input id="clienteNombre" name="nombre" type="text" class="form-control" placeholder="Nombre" value="${client.nombre}">
              </div>
              <div class="form-group col-sm-6">
                <label class="text-color-gray" for="clienteApellido">Apellido</label>
                <input id="clienteApellido" name="apellido" type="text" class="form-control" placeholder="Apellido" value="${client.apellido}">
              </div>
              <div class="form-group col-sm-6">
                <label class="text-color-gray" for="clienteTelefono">Teléfono</label>
                <input id="clienteTelefono" name="celular" type="text" class="form-control" placeholder="Teléfono" value="${client.celular}">
              </div>
              <div class="form-group col-sm-6">
                <label class="text-color-gray" for="clienteTelefonoAlt">Teléfono Alternativo</label>
                <input id="clienteTelefonoAlt" name="telefono_secundario" type="text" class="form-control" placeholder="Teléfono Alternativo" value="${client.telefono_secundario ?? ''}">
              </div>
              <div class="form-group col-sm-12">
                <label class="text-color-gray" for="clienteEmail">Email</label>
                <input id="clienteEmail" name="email" type="text" class="form-control" placeholder="Email" value="${client.email}">
              </div>
            </div>
            <div class="col-sm-6">
              <div class="form-group col-sm-12">
                <label class="text-color-gray" for="clienteDireccionEntrega">Dirección de Entrega</label>
                <input id="clienteDireccionEntrega" name="direccion_entrega" type="text" class="form-control" placeholder="Dirección de Entrega" value="${client.direccion_entrega ?? ''}">
              </div>
              <div class="form-group col-sm-12">
                <label class="text-color-gray" for="clienteDireccion">Dirección</label>
                <input id="clienteDireccion" name="direccion" type="text" class="form-control" placeholder="Dirección" value="${client.direccion}">
              </div>
              <div class="form-group col-sm-5">
                <label class="text-color-gray" for="clienteDepartamento">Departamento</label>
                <select class="form-control" id="clienteDepartamento" name="departamento">
                  ${resourcesOptionsLists.departamento}
                </select>
              </div>
              <div class="form-group col-sm-4">
                <label class="text-color-gray" for="clienteMunicipio">Municipio</label>
                <select class="form-control" id="clienteMunicipio" name="municipio" ${client.departamento === '' ? 'disabled' : ''}>
                  ${resourcesOptionsLists.municipio}
                </select>
              </div>
              <div class="form-group col-sm-3">
                <label class="text-color-gray" for="clienteZona">Zona</label>
                <select class="form-control" id="clienteZona" name="zona" ${client.departamento === '' || client.municipio === '' ? 'disabled' : ''}>
                  ${resourcesOptionsLists.zona}
                </select>
              </div>
            </div>
            <div class="col-sm-12">
              <span class="col-sm-12 h6 text-left mt-1">Datos de Facturación</span>
              <div class="form-group col-sm-6">
                  <label class="text-color-gray" for="clienteNitNombre">Nombre</label>
                  <input id="clienteNitNombre" name="nit_nombre" type="text" class="form-control" placeholder="Nombre de Facturación" value="${client.nit_nombre ?? ''}">
                </div>
                <div class="form-group col-sm-6">
                  <label class="text-color-gray" for="clienteNitNumero">Nit</label>
                  <input id="clienteNitNumero" name="nit_numero" type="text" class="form-control" placeholder="Nit de Facturación" value="${client.nit_numero ?? ''}" maxlength="10">
                </div>
            </div>
          </div>
          <div class="row background-whitesmoke mt-3">
            <span class="col-sm-12 h4 text-left">Datos Internos</span>
            <div class="col-sm-6">
              <div class="form-group col-sm-6">
                <label class="text-color-gray" for="clienteReferencia">Cómo Nos Conoció</label>
                <select class="form-control" id="clienteReferencia" name="referencia">
                  ${resourcesOptionsLists.referencia}
                </select>
              </div>
              <div class="form-group col-sm-6">
                <label class="text-color-gray" for="clienteTipo">Tipo Cliente</label>
                <select class="form-control" id="clienteTipo" name="tipo">
                  ${resourcesOptionsLists.tipo}
                </select>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="col-sm-12">
                <label class="text-color-gray" for="clienteComentario">Notas Adicionales</label>
                <textarea id="clienteComentario" name="comentario" class="form-control">${client.comentario ?? ''}</textarea>
              </div>
            </div>
          </div>
          <div class="row background-whitesmoke">
            <div class="col-sm-6 mt-3">
              <span class="col-sm-12 h6 text-left mt-1">Configuración de Costos</span>
              <div class="form-group col-sm-4">
                <label class="text-color-gray" for="clienteCostoLibra">Libra</label>
                <div class="input-group">
                  <span class="input-group-addon">Q</span>
                  <input id="clienteCostoLibra" name="tarifa_express" type="text" class="form-control"
                    value="${client.tarifa_express}" ${!isAdmin ? 'disabled' : ''}>
                </div>
              </div>
              <div class="form-group col-sm-4">
                <label class="text-color-gray" for="clienteCostoDesaduanaje">Desaduanaje</label>
                <div class="input-group">
                  <span class="input-group-addon">Q</span>
                  <input id="clienteCostoDesaduanaje" name="desaduanaje_express" type="text"
                    class="form-control" value="${client.desaduanaje_express}" ${!isAdmin ? 'disabled' : ''}>
                </div>
              </div>
              <div class="form-group col-sm-4">
                <label class="text-color-gray" for="clienteCostoLibra">Seguro</label>
                <div class="input-group">
                  <input id="clienteCostoSeguro" name="seguro" type="text" class="form-control text-right"
                    value="${(client.seguro * 100).toFixed(2)}" ${!isAdmin ? 'disabled' : ''}>
                  <span class="input-group-addon">%</span>
                </div>
              </div>
            </div>
            <div class="col-sm-6 mt-3">
              <span class="col-sm-12 h6 text-left mt-1">Datos de Vendedor</span>
              <div class="form-group col-sm-12">
                <label class="text-color-gray" for="clienteNombreVendedor">Nombre Vendedor</label>
                <select class="form-control" id="clienteNombreVendedor" name="vendedor_id" ${!isAdmin ? 'disabled' : ''}>
                  ${resourcesOptionsLists.vendedor}
                </select>
              </div>
              ` + (!isAdmin ? '' : `
              <div class="form-group col-sm-6">
                <label class="text-color-gray" for="clienteComisionLibraVendedor">Comisión Libra</label>
                <div class="input-group">
                  <input id="clienteComisionLibraVendedor" name="vendedor_comision_libra" type="text" class="form-control" value="${client.vendedor_comision_libra ?? ''}">
                  <span class="input-group-addon">%</span>
                </div>
              </div>
              <div class="form-group col-sm-6">
                <label class="text-color-gray" for="clienteComisionPaqueteVendedor">Comisión Paquete</label>
                <div class="input-group">
                  <input id="clienteComisionPaqueteVendedor" name="vendedor_comision_paquete" type="text" class="form-control" value="${client.vendedor_comision_paquete ?? ''}">
                  <span class="input-group-addon">%</span>
                </div>
              </div>
              `) + `
            </div>
          </div>
        </div>
      </form>
      <div class="row mt-3">
        <div class="col-sm-4 text-left">
          <button onclick='bootbox.hideAll();' class="btn btn-default">Regresar</button>
        </div>
        <div class="col-sm-8 text-right">
          <button onclick='discardChanges(${JSON.stringify(client)})' id="clienteBtnDescartarCambios" class="btn btn-default" disabled>Descartar Cambios</button>
          <button onclick='saveChanges(${JSON.stringify(client)}, ${clientTableIndex})' id="clienteBtnGuardarCambios" class="btn btn-success" disabled>Guardar Cambios</button>
        </div>
      </div>
    </div>
  `;
}
