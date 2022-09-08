const clientesIndexes = {
  id: 0,
  chexCode: 1,
  firstName: 2,
  lastName: 3,
  phone: 4,
  email: 5,
  address: 6,
  moreButton: 7,
};

const DepartamentosYMunicipios = {
  "Alta Verapaz": [
    "Cahabón",
    "Chahal",
    "Chisec",
    "Cobán",
    "Fray Bartolomé de las Casas",
    "Lanquín",
    "Panzós",
    "Raxruha",
    "San Cristóbal Verapaz",
    "San Juan Chamelco",
    "San Pedro Carchá",
    "Santa Cruz Verapaz",
    "Senahú",
    "Tactic",
    "Tamahú",
    "Tucurú",
    "Santa Catarina La Tinta"
  ],
  "Baja Verapaz": [
    "Cubulco",
    "Granados",
    "Purulhá",
    "Rabinal",
    "Salamá",
    "San Jerónimo",
    "San Miguel Chicaj",
    "Santa Cruz El Chol"
  ],
  "Chimaltenango": [
    "Acatenango",
    "Chimaltenango",
    "El Tejar",
    "Parramos",
    "Patzicía",
    "Patzún",
    "Pochuta",
    "San Andrés Itzapa",
    "San José Poaquil",
    "San Juan Comalapa",
    "San Martín Jilotepeque",
    "Santa Apolonia",
    "Santa Cruz Balanyá",
    "Tecpán Guatemala",
    "Yepocapa",
    "Zaragoza"
  ],
  "Chiquimula": [
    "Camotán",
    "Chiquimula",
    "Concepción Las Minas",
    "Esquipulas",
    "Ipala",
    "Jocotán",
    "Olopa",
    "Quezaltepeque",
    "San Jacinto",
    "San José La Arada",
    "San Juan Ermita"
  ],
  "El Progreso": [
    "El Jícaro",
    "Guastatoya",
    "Morazán",
    "San Agustín Acasaguastlán",
    "San Antonio La Paz",
    "San Cristóbal Acasaguastlán",
    "Sanarate"
  ],
  "Escuintla": [
    "Escuintla",
    "Guanagazapa",
    "Iztapa",
    "La Democracia",
    "La Gomera",
    "Masagua",
    "Nueva Concepción",
    "Palín",
    "San José",
    "San Vicente Pacaya",
    "Santa Lucía Cotzumalguapa",
    "Siquinalá",
    "Tiquisate"
  ],
  "Guatemala": [
    "Amatitlán",
    "Chinautla",
    "Chuarrancho",
    "Fraijanes",
    "Ciudad de Guatemala",
    "Mixco",
    "Palencia",
    "Petapa",
    "San José del Golfo",
    "San José Pinula",
    "San Juan Sacatepéquez",
    "San Pedro Ayampuc",
    "San Pedro Sacatepéquez",
    "San Raymundo",
    "Santa Catarina Pinula",
    "Villa Canales"
  ],
  "Huehuetenango": [
    "Aguacatán",
    "Chiantla",
    "Colotenango",
    "Concepción Huista",
    "Cuilco",
    "Huehuetenango",
    "Ixtahuacán",
    "Jacaltenango",
    "La Democracia",
    "La Libertad",
    "Malacatancito",
    "Nentón",
    "San Antonio Huista",
    "San Gaspar Ixchil",
    "San Juan Atitán",
    "San Juan Ixcoy",
    "San Mateo Ixtatán",
    "San Miguel Acatán",
    "San Pedro Necta",
    "San Rafael La Independencia",
    "San Rafael Petzal",
    "San Sebastián Coatán",
    "San Sebastián Huehuetenango",
    "Santa Ana Huista",
    "Santa Bárbara",
    "Santa Cruz Barillas",
    "Santa Eulalia",
    "Santiago Chimaltenango",
    "Soloma",
    "Tectitán",
    "Todos Santos Cuchumatan"
  ],
  "Izabal": [
    "El Estor",
    "Livingston",
    "Los Amates",
    "Morales",
    "Puerto Barrios"
  ],
  "Jalapa": [
    "Jalapa",
    "Mataquescuintla",
    "Monjas",
    "San Carlos Alzatate",
    "San Luis Jilotepeque",
    "San Pedro Pinula",
    "San Manuel Chaparrón"
  ],
  "Jutiapa": [
    "Agua Blanca",
    "Asunción Mita",
    "Atescatempa",
    "Comapa",
    "Conguaco",
    "El Adelanto",
    "El Progreso",
    "Jalpatagua",
    "Jerez",
    "Jutiapa",
    "Moyuta",
    "Pasaco",
    "Quezada",
    "San José Acatempa",
    "Santa Catarina Mita",
    "Yupiltepeque",
    "Zapotitlán"
  ],
  "Petén": [
    "Dolores",
    "El Chal",
    "Flores",
    "La Libertad",
    "Melchor de Mencos",
    "Poptún",
    "San Andrés",
    "San Benito",
    "San Francisco",
    "San José",
    "San Luis",
    "Santa Ana",
    "Sayaxché",
    "Las Cruces",
  ],
  "Quetzaltenango": [
    "Almolonga",
    "Cabricán",
    "Cajolá",
    "Cantel",
    "Coatepeque",
    "Colomba",
    "Concepción Chiquirichapa",
    "El Palmar",
    "Flores Costa Cuca",
    "Génova",
    "Huitán",
    "La Esperanza",
    "Olintepeque",
    "Ostuncalco",
    "Palestina de Los Altos",
    "Quetzaltenango",
    "Salcajá",
    "San Carlos Sija",
    "San Francisco La Unión",
    "San Martín Sacatepéquez",
    "San Mateo",
    "San Miguel Sigüilá",
    "Sibilia",
    "Zunil"
  ],
  "Quiché": [
    "Canillá",
    "Chajul",
    "Chicamán",
    "Chiché",
    "Chichicastenango",
    "Chinique",
    "Cunén",
    "Ixcán",
    "Joyabaj",
    "Nebaj",
    "Pachalum",
    "Patzité",
    "Sacapulas",
    "San Andrés Sajcabajá",
    "San Antonio Ilotenango",
    "San Bartolomé Jocotenango",
    "San Juan Cotzal",
    "San Pedro Jocopilas",
    "Santa Cruz del Quiché",
    "Uspantán",
    "Zacualpa"
  ],
  "Retalhuleu": [
    "Champerico",
    "El Asintal",
    "Nuevo San Carlos",
    "Retalhuleu",
    "San Andrés Villa Seca",
    "San Felipe",
    "San Martín Zapotitlán",
    "San Sebastián",
    "Santa Cruz Muluá"
  ],
  "Sacatepéquez": [
    "Alotenango",
    "Antigua",
    "Ciudad Vieja",
    "Jocotenango",
    "Magdalena Milpas Altas",
    "Pastores",
    "San Antonio Aguas Calientes",
    "San Bartolomé Milpas Altas",
    "San Lucas Sacatepéquez",
    "San Miguel Dueñas",
    "Santa Catarina Barahona",
    "Santa Lucía Milpas Altas",
    "Santa María de Jesús",
    "Santiago Sacatepéquez",
    "Santo Domingo Xenacoj",
    "Sumpango"
  ],
  "San Marcos": [
    "Ayutla",
    "Catarina",
    "Comitancillo",
    "Concepción Tutuapa",
    "El Quetzal",
    "El Rodeo",
    "El Tumbador",
    "Esquipulas Palo Gordo",
    "Ixchiguan",
    "La Reforma",
    "Malacatán",
    "Nuevo Progreso",
    "Ocos",
    "Pajapita",
    "Río Blanco",
    "San Antonio Sacatepéquez",
    "San Cristóbal Cucho",
    "San José Ojetenam",
    "San Lorenzo",
    "San Marcos",
    "San Miguel Ixtahuacán",
    "San Pablo",
    "San Pedro Sacatepéquez",
    "San Rafael Pie de La Cuesta",
    "San Sibinal",
    "Sipacapa",
    "Tacaná",
    "Tajumulco",
    "Tejutla"
  ],
  "Santa Rosa": [
    "Barberena",
    "Casillas",
    "Chiquimulilla",
    "Cuilapa",
    "Guazacapán",
    "Nueva Santa Rosa",
    "Oratorio",
    "Pueblo Nuevo Viñas",
    "San Juan Tecuaco",
    "San Rafael Las Flores",
    "Santa Cruz Naranjo",
    "Santa María Ixhuatán",
    "Santa Rosa de Lima",
    "Taxisco"
  ],
  "Sololá": [
    "Concepción",
    "Nahualá",
    "Panajachel",
    "San Andrés Semetabaj",
    "San Antonio Palopó",
    "San José Chacaya",
    "San Juan La Laguna",
    "San Lucas Tolimán",
    "San Marcos La Laguna",
    "San Pablo La Laguna",
    "San Pedro La Laguna",
    "Santa Catarina Ixtahuacan",
    "Santa Catarina Palopó",
    "Santa Clara La Laguna",
    "Santa Cruz La Laguna",
    "Santa Lucía Utatlán",
    "Santa María Visitación",
    "Santiago Atitlán",
    "Sololá"
  ],
  "Suchitepéquez": [
    "Chicacao",
    "Cuyotenango",
    "Mazatenango",
    "Patulul",
    "Pueblo Nuevo",
    "Río Bravo",
    "Samayac",
    "San Antonio Suchitepéquez",
    "San Bernardino",
    "San Francisco Zapotitlán",
    "San Gabriel",
    "San José El Idolo",
    "San Juan Bautista",
    "San Lorenzo",
    "San Miguel Panán",
    "San Pablo Jocopilas",
    "Santa Bárbara",
    "Santo Domingo Suchitepequez",
    "Santo Tomas La Unión",
    "Zunilito"
  ],
  "Totonicapán": [
    "Momostenango",
    "San Andrés Xecul",
    "San Bartolo",
    "San Cristóbal Totonicapán",
    "San Francisco El Alto",
    "Santa Lucía La Reforma",
    "Santa María Chiquimula",
    "Totonicapán"
  ],
  "Zacapa": [
    "Cabañas",
    "Estanzuela",
    "Gualán",
    "Huité",
    "La Unión",
    "Río Hondo",
    "San Diego",
    "Teculután",
    "Usumatlán",
    "Zacapa"
  ]
}


const departamentos = Object.keys(DepartamentosYMunicipios);
const zonas = [...Array(26).keys()];
zonas.shift();

const tiposUsuario = ['Constante', 'Emprendedor', 'Empresa', 'Eventual', 'Vip']

const referencias = ['Recomendado', 'Publicidad', 'Redes Sociales', 'Google', 'Otros'];

const mapResourceListToOptions = (resourceList, client, resourceName, defaultWithSelectText = true) => {
  return [`<option value=''>${defaultWithSelectText ? 'Seleccionar ' : ''}${toPascalCase(resourceName)}</option>`,
    ...resourceList.map(r => {
      return `<option value="${r}"${r.toString() === client[resourceName] ? ' selected' : ''}>${r}</option>`
    })]
}

$(document).ready(function () {

  const table = $('#clientes').DataTable({
    "bSort": false,
    "retrieve": true,
    "dom": 'CT<"clear">lfrtip',
    "tableTools": {
      "sSwfPath": "./swf/copy_csv_xls_pdf.swf"
    },
    "responsive": true,
    "scrollY": "500px",
    "scrollCollapse": true,
    "paging": true,
    "language": {
      "lengthMenu": "Mostrando _MENU_ clientes por página",
      "search": "Buscar:",
      "zeroRecords": "No hay clientes que coincidan con la búsqueda",
      "info": "Mostrando clientes del _START_ al _END_ de _TOTAL_ clientes totales.",
      "infoEmpty": "No se encontraron clientes.",
      "infoFiltered": "(Filtrando sobre _MAX_ clientes)",
      "paginate": {
        "first": "Primera",
        "last": "Última",
        "next": "Siguiente",
        "previous": "Anterior"
      },
      "loadingRecords": "Cargando clientes...",
      "processing": "Procesando...",
    },
  });

  $("#clientes tbody").on("click", "button.client-btn-more", async function () {
    try {
      const response = await $.ajax({
        url: "db/DBgetAllVendedores.php",
        cache: false,
      });
      const vendedores = response.data;

      let client = $(this).data('client');
      const index = table.row($(this).closest('tr')).index();

      const resourcesOptionsLists = {
        departamento: mapResourceListToOptions(departamentos, client, 'departamento', false),
        municipio: client.departamento !== '' ?
          mapResourceListToOptions(DepartamentosYMunicipios[client.departamento] ?? [], client, 'municipio', false) :
          [`<option value=''>Municipio</option>`],
        zona: mapResourceListToOptions(zonas, client, 'zona', false),
        referencia: mapResourceListToOptions(referencias, client, 'referencia'),
        tipo: mapResourceListToOptions(tiposUsuario, client, 'tipo'),
        vendedor: [`<option value=''>Seleccionar Vendedor</option>`,
          ...vendedores.map(v => {
            return `<option value="${v.id}"${v.id === (client['vendedor_id'] ?? 0) ? ' selected' : ''}>${v.nombre}</option>`
          })]
      }

      bootbox.dialog({
        size: 'large',
        closeButton: true,
        title: `<b>Client No: ${client.ccid}  -  ${client.nombre} ${client.apellido}</b>  -  CHEX ${client.cid}`,
        message: renderClientDataModal(client, resourcesOptionsLists, index),
      });

      initClientFormInputsTriggers(client);
    } catch (err) {
      Swal.fire({
        title: 'Error',
        text: "Ocurrió un problema al intentar conectarse al servidor para obtener el listado de vendedores.",
        type: 'error',
        focusConfirm: true,
        confirmButtonText: 'Cerrar',
      });
    }
  });
});

const getUpdatedClientFields = (client) => {
  const formValuesArray = $("#clienteForm").serializeArray();
  const updatedValues = {};
  for (let i = 0; i < formValuesArray.length; i++) {
    const formValue = formValuesArray[i];

    if (formValue.name === 'seguro') {
      if ((client['seguro'] * 100).toFixed(2) !== formValue.value)
        updatedValues['seguro'] = formValue.value / 100;

      continue;
    }

    if ((client[formValue.name] ?? '') !== formValue.value) {
      updatedValues[formValue.name] = formValue.value;
    }
  }

  return updatedValues;
}

const initClientFormInputsTriggers = (client) => {

  const refreshButtonsStated = (client) => {
    const valuesUpdated = Object.keys(getUpdatedClientFields(client)).length > 0;
    $('#clienteBtnDescartarCambios, #clienteBtnGuardarCambios').attr('disabled', !valuesUpdated);
  };

  $('#clienteNombre, #clienteApellido, #clienteTelefono, #clienteTelefonoAlt, #clienteEmail, #clienteDireccionEntrega, #clienteDireccion, #clienteDepartamento' +
    ', #clienteMunicipio, #clienteZona, #clienteNitNombre, #clienteNitNumero, #clienteReferencia, #clienteTipo' +
    ', #clienteComentario, #clienteCostoLibre, #clienteCostoDesaduanaje, #clienteCostoSeguro, #clienteNombreVendedor' +
    ', #clienteComisionPaqueteVendedor, #clienteComisionLibraVendedor').on('input', () => refreshButtonsStated(client));

  $('#clienteTelefono, #clienteTelefonoAlt, #clienteNitNumero').on('keypress', (e) => integersonly(this, e));

  const $departamento = $('#clienteDepartamento');
  const $municipio = $('#clienteMunicipio');
  const $zona = $('#clienteZona');

  $departamento.change(e => {
    const newDepartamento = e.target.value;

    if (newDepartamento === '') {
      $municipio.val("").change();
      $municipio.attr('disabled', true);
      return;
    }

    $municipio.attr('disabled', false);
    $municipio
      .find('option')
      .remove()
      .end()
      .append(mapResourceListToOptions(DepartamentosYMunicipios[newDepartamento], client, 'municipio', false));

    if (newDepartamento === client.departamento) {
      $municipio.val(client.municipio).change();
      return
    }

    $zona.val("").change();
    $zona.attr('disabled', true);
  })

  $municipio.change(e => {
    const newMunicipio = e.target.value;
    if (newMunicipio === '') {
      $zona.val("").change();
      $zona.attr('disabled', true);
      return;
    }

    $zona.attr('disabled', false);
    if (newMunicipio === client.municipio) {
      $zona.val(client.zona).change();
    } else {
      $zona.val("").change();
    }
  })


  $('#clienteNombreVendedor').change((e) => {
    const newVendedorId = e.target.value;
    if (newVendedorId !== client.vendedor_id) {
      $('#clienteComisionLibraVendedor').val('').trigger('input');
      $('#clienteComisionPaqueteVendedor').val('').trigger('input');
    } else {
      $('#clienteComisionLibraVendedor').val(client.vendedor_comision_libra).trigger('input');
      $('#clienteComisionPaqueteVendedor').val(client.vendedor_comision_paquete).trigger('input');
    }
  });
}

const discardChanges = (client) => {
  const $municipio = $('#clienteMunicipio');
  const $zona = $('#clienteZona');
  if (client.departamento === '') {
    $municipio.attr('disabled', true);
  } else {
    $municipio.attr('disabled', false);
  }

  $municipio
    .find('option')
    .remove()
    .end()
    .append(mapResourceListToOptions(DepartamentosYMunicipios[client.departamento] ?? [], client, 'municipio', false));

  if (client.municipio === '')
    $zona.attr('disabled', true);
  else {
    $zona.attr('disabled', false);
  }

  $("#clienteForm")[0].reset();
  const $buttons = $('#clienteBtnDescartarCambios, #clienteBtnGuardarCambios');
  $buttons.attr('disabled', true);
}

const saveChanges = async (client, clientTableIndex) => {
  const updatedFields = getUpdatedClientFields(client);

  let vendedorDetails = {
    vendedor_id: updatedFields.vendedor_id ?? client.vendedor_id,
    comision_libra: updatedFields.vendedor_comision_libra ?? client.vendedor_comision_libra,
    comision_paquete: updatedFields.vendedor_comision_paquete ?? client.vendedor_comision_paquete
  }


  const {vendedor_comision_libra, vendedor_comision_paquete, vendedor_id, ...allButVendedorDetails} = updatedFields;

  const data = {
    clientId: client.ccid,
    clientDetails: allButVendedorDetails
  };

  if (vendedorDetails.vendedor_id !== client.vendedor_id ||
      vendedorDetails.comision_libra !== client.vendedor_comision_libra ||
      vendedorDetails.comision_paquete !== client.vendedor_comision_paquete) {

    data.clientVendedorDetails = vendedorDetails;
  }

  try {
    await $.ajax({
      url: 'db/DBsetCliente.php',
      type: 'post',
      data,
      cache: false,
    });

    Swal.fire({
      title: 'Cliente Actualizado',
      text: 'El cliente ha sido actualizado correctamente.',
      type: 'success',
      focusConfirm: true,
      confirmButtonText: 'Ok',
    });

    const vendedor_nombre = $("#clienteNombreVendedor option:selected").text();
    const newClientData = {
      ...client,
      ...allButVendedorDetails,
      vendedor_nombre,
      vendedor_id: vendedorDetails.vendedor_id,
      vendedor_comision_libra: vendedorDetails.comision_libra,
      vendedor_comision_paquete: vendedorDetails.comision_paquete
    };

    const table = $('#clientes').DataTable();
    table.cell(clientTableIndex, 2).data(newClientData.nombre);
    table.cell(clientTableIndex, 3).data(newClientData.apellido);
    table.cell(clientTableIndex, 4).data(newClientData.celular);
    table.cell(clientTableIndex, 5).data(newClientData.email);
    table.cell(clientTableIndex, 6).data(newClientData.direccion);
    table.cell(clientTableIndex, 7).data(`<button data-client='${JSON.stringify(newClientData)}' class='text-color-gray client-btn-more'>MÁS</button>`);
    table.draw(false);
    bootbox.hideAll();
  } catch (err) {
    Swal.fire({
      title: 'Error',
      html: `
        <div>Ocurrió un error al intentar modificar los datos de cliente.
          <br>
          El servidor retornó el siguiente mensaje de error:
          <br><br>
          <b>${err.responseText}</b>
        </div>`,
      type: 'error',
      focusConfirm: true,
      confirmButtonText: 'Cerrar',
    });
  }
}

async function onChexClientsTabClicked() {
  scroll0();
  await insertarNuevosClientes();
  await getClientsAndPopulateTable();
}

async function getClientsAndPopulateTable() {
  try {
    const response = await $.ajax({
      url: "db/DBgetAllClientes.php",
      cache: false,
    });

    const lastSyncDatetime = moment(response.data.lastSyncDatetime)
      .format('DD/MM/YYYY [a las] HH:mm')
    $('#clientLastSyncDatetime').text('Última Actualización: ' + lastSyncDatetime);
    var table = $('#clientes').DataTable();
    table.clear();
    var rows = response.data.clients;

    for (var i = 0; i < rows.length; i++) {
      table.row.add([
        rows[i]["ccid"],
        rows[i]["cid"],
        rows[i]["nombre"],
        rows[i]["apellido"],
        rows[i]["celular"],
        rows[i]["email"],
        rows[i]["direccion"],
        "<button data-client='" + JSON.stringify(rows[i]) + "' class='text-color-gray client-btn-more'>MÁS</button>",
      ]);
    }
    table.draw(false);
    table.columns.adjust().responsive.recalc();
  } catch (err) {
    bootbox.alert("Ocurrió un problema al intentar conectarse al servidor. Error: " + err.message);
  }
}


async function insertarNuevosClientes(manuallyCalled = false) {
  try {
    const response = await $.ajax({
      url: "db/DBgetAndInsertNewUsers.php",
      cache: false,
    });

    if (manuallyCalled) {
      if (response.numberOfClientsInserted === 0) {
        Swal.fire({
          title: 'Nada Por Actualizar',
          html: "<div>No se encontraron clientes nuevos.<br><br>La tabla de clientes se encuentra actualizada.</div>",
          type: 'success',
          focusConfirm: true,
          confirmButtonText: 'Ok',
        });
      } else {
        Swal.fire({
          title: 'Tabla de Clientes Actualizada',
          html: `<div>La tabla de clientes ha sido actualizada.<br><br>Se han agregado <b>${response.numberOfClientsInserted}</b> clientes nuevos a la tabla.</div>`,
          type: 'success',
          focusConfirm: true,
          confirmButtonText: 'Ok',
        });

        if ($('#clientesChexTab').hasClass('active')) {
          await getClientsAndPopulateTable();
        }
      }
      return;
    }

    if (response.numberOfClientsInserted > 0) {
      Swal.fire({
        title: 'Tabla de Clientes Actualizada',
        html: `<div>La tabla de clientes ha sido actualizada.<br><br>Se han agregado <b>${response.numberOfClientsInserted}</b> clientes nuevos a la tabla.</div>`,
        type: 'success',
        focusConfirm: true,
        confirmButtonText: 'Ok',
      });
    }
  } catch (err) {
    const {responseJSON: {errorMessage}} = err;
    Swal.fire({
      title: 'Error en Actualización',
      text: "No se pudo actualizar la tabla de clientes. ",
      html: `
        <div>Ocurrió un error al intentar actualizar la tabla de clientes.
          <br>
          El servidor retornó el siguiente mensaje de error:
          <br><br>
          <b>${errorMessage}</b>
        </div>`,
      type: 'error',
      focusConfirm: true,
      confirmButtonText: 'Ok',
    });
  }
}