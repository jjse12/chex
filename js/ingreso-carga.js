const ingresoCargaIndexes = {
  servicio: 0, guideNumber: 1, tracking: 2, uid: 3, uname: 4, peso: 5
};

$(document).ready( function () {
  var tablilla = $('#tablaNuevaCarga').DataTable({
    "bSort" : false,
    "retrieve": true,
    "responsive": true,
    "scrollY": "500px",
    "scrollCollapse": true,
    "paging": false,
    "fixedColumns": true,
    "language": {
      "lengthMenu": "Display _MENU_ records per page",
      "search": "Buscar:",
      "zeroRecords": "No hay paquetes que coincidan con la búsqueda",
      "info": "Mostrando paquetes del _START_ al _END_ de _TOTAL_ paquetes totales.",
      "infoEmpty": "No se han ingresado paquetes.",
      "infoFiltered": "(Filtrando sobre _MAX_ paquetes)",
      "paginate": {
        "first":      "Primera",
        "last":       "Última",
        "next":       "Siguiente",
        "previous":   "Anterior"
      },
      "loadingRecords": "Cargando Paquetes...",
      "processing":     "Procesando...",
    },
    "footerCallback": function ( row, data, start, end, display ) {
      var api = this.api(), data;
      if (this.fnSettings().fnRecordsDisplay() === 0){
        api.column(5).footer().style.visibility = "hidden";
        return;
      }
      else
        api.column(5).footer().style.visibility = "visible";

      var intVal = function ( i ) {
        return typeof i === 'string' ?
          i.replace(/[\$,]/g, '')*1 :
          typeof i === 'number' ?
            i : 0;
      };

      $(api.column(5).footer()).html(
        "<h5>Total: " + numberWithCommasNoFixed(api.column(5, { page: 'current'} ).data().reduce( function (a, b) {
          return intVal(a) + intVal(b.includes(">") ? b.split(">")[1].split("<")[0] : b);
        }, 0)) + " Libras</h5>"
      );
    }
  });

  $("#divServices").load('db/servicio/DBgetIngresoCargaServices.php', {}, () => {});

  $('#tablaNuevaCarga tbody').on("click", "img.icon-delete", function () {
    tablilla.row( $(this).parents('tr'))
    .remove()
    .draw(false);
    addedTrackings = addedTrackings.filter(tracking => tracking != $(this).data('tracking'));
    addedGuideNumbers = addedGuideNumbers.filter(guideNumber => guideNumber != $(this).data('guide-number'));
    document.getElementById("paquetes").innerHTML = "Paquetes: " + tablilla.rows().data().length;
    if (tablilla.rows().data().length == 0)
      document.getElementById("libras").innerHTML = "Libras: 0";
    else
      document.getElementById("libras").innerHTML = "Libras: " + calcularLibras();
  });

  $('#tablaNuevaCarga tbody').on("click", "h5.ingCargaGuideNumber", function () {
    var index = tablilla.row($(this).closest('tr')).index();
    var arr = tablilla.rows(index).data().toArray();
    var currentGuideNumber = arr[0][ingresoCargaIndexes.guideNumber].split(">")[1].split("<")[0];
    bootbox.prompt({
      title: "Ingrese el número de guía para el paquete.",
      size: "small",
      inputType: 'number',
      callback: function (result) {
        if (result == null)
          return true;
        if (result == "")
          bootbox.alert("Debe ingresar un número de guía para el paquete.");
        else if (result.includes(","))
          bootbox.alert("El número de guía no puede contener comas.");
        else if (result.includes("."))
          bootbox.alert("El número de guía no puede contener punto decimal.");
        else if (result.includes("-"))
          bootbox.alert("El número de guía no puede ser negativo.");
        else if (result.length > 10)
          bootbox.alert("El número de guía no puede exceder los 10 caracteres.");
        else {
          if (addedGuideNumbers.includes(result)) {
            bootbox.alert({
              message: "Ya ha ingresado un paquete con este número de guía en el actual registro de carga.",
              size: 'small',
              backdrop: true
            });
            return false;
          }

          $.ajax({
            url: "db/DBgetPaquete.php",
            type: "POST",
            data: {
              select: "COUNT(guide_number) AS cant",
              where: "guide_number = '"+result+"'"
            },
            cache: false,
            success: function(res) {
              if (!res.includes("\"0\"")){
                bootbox.alert({
                  message: "Ya existe un paquete registrado con este número de guía, asegúrese de haber ingresado bien el dato. ",
                  size: 'small',
                  backdrop: true
                });
              }
              else {
                addedGuideNumbers = addedGuideNumbers.filter(guideNumber => guideNumber != currentGuideNumber);
                addedGuideNumbers.push(result.toUpperCase());
                arr[0][ingresoCargaIndexes.guideNumber] = "<h5 class='seleccionado ingCargaGuideNumber'>"+result+"</h5>";
                tablilla.row(index).data(arr[0]).draw(false);
                bootbox.hideAll();
              }
            },
            error: function() {
              bootbox.alert("Ocurrió un problema al intentar conectarse al servidor.");
            }
          });
        }

        return false;
      }
    });
  });

  $('#tablaNuevaCarga tbody').on("click", "h5.ingCargaTracking", function () {
    var index = tablilla.row($(this).closest('tr')).index();
    var arr = tablilla.rows(index).data().toArray();
    var currentTracking = arr[0][ingresoCargaIndexes.tracking].split(">")[1].split("<")[0];
    bootbox.prompt({
      title: "Ingrese el tracking para el paquete.",
      size: "small",
      inputType: 'text',
      callback: function (result) {
        if (result == null)
          return true;
        if (result == "")
          bootbox.alert("Debe ingresar un tracking para el paquete.");
        else if (result.includes("<"))
          bootbox.alert("El tracking no puede contener el caracter '<'");
        else if (result.includes(">"))
          bootbox.alert("El tracking no puede contener el caracter '>'");
        else if (result.includes(","))
          bootbox.alert("El tracking no puede contener comas.");
        else if (result.length > 50)
          bootbox.alert("El tracking no puede exceder los 50 caracteres.");
        else {
          if (addedTrackings.includes(result)) {
              bootbox.alert({
                message: "Ya ha ingresado un paquete con este número de tracking en el actual registro de carga.",
                size: 'small',
                backdrop: true
              });
              return false;
          }

          $.ajax({
            url: "db/DBgetPaquete.php",
            type: "POST",
            data: {
              select: "COUNT(tracking) AS cant",
              where: "tracking = '"+result+"'"
            },
            cache: false,
            success: function(res) {
              if (!res.includes("\"0\"")){
                bootbox.alert({
                  message: "Ya existe un paquete registrado con este número de tracking, asegúrese de haber ingresado bien el dato. ",
                  size: 'small',
                  backdrop: true
                });
              }
              else {
                addedTrackings = addedTrackings.filter(tracking =>  tracking != currentTracking);
                addedTrackings.push(result.toUpperCase());
                arr[0][ingresoCargaIndexes.tracking] = "<h5 class='seleccionado ingCargaTracking'>"+result.toUpperCase()+"</h5>";
                tablilla.row(index).data(arr[0]).draw(false);
                bootbox.hideAll();
              }
            },
            error: function() {
              bootbox.alert("Ocurrió un problema al intentar conectarse al servidor.");
            }
          });
        }

        return false;
      }
    });
  });


  $('#tablaNuevaCarga tbody').on("click", "h5.ingCargaUid", function () {
    var index = tablilla.row($(this).closest('tr')).index();
    var arr = tablilla.rows(index).data().toArray();
    bootbox.prompt({
      title: "Ingrese el ID del cliente.",
      size: "small",
      inputType: 'text',
      callback: function (result) {
        if (result == null)
          return true;
        if (result == "")
          bootbox.alert("Debe ingresar un ID de cliente para el paquete.");
        else if (result.includes("<"))
          bootbox.alert("El ID no puede contener el caracter '<'");
        else if (result.includes(">"))
          bootbox.alert("El ID no puede contener el caracter '>'");
        else if (result.includes(","))
          bootbox.alert("El ID no puede contener comas.");
        else if (result.length > 7)
          bootbox.alert("El ID no puede exceder los 7 caracteres.");
        else {
          arr[0][ingresoCargaIndexes.uid] = "<h5 class='seleccionado ingCargaUid'>"+result.toUpperCase()+"</h5>";
          $.ajax({
            url: "db/DBgetUserNamePostUid.php",
            type: "POST",
            data: {
              uid: result
            },
            cache: false,
            success: function(name){
              bootbox.hideAll();
              var arreglo = ["Cliente", "CLIENTE", "cliente", "Anónimo", "ANÓNIMO", "anónimo", "Anonimo", "ANONIMO", "anonimo"];
              if (arreglo.indexOf(result) == -1 && name.replace(/\s/g,'').length == 0) {
                bootbox.alert({title: "¡Atención!", message: "Al parecer no existe ningún cliente asociado a este ID."});
              }
              else if (name != null && name.length > 1) {
                bootbox.confirm({
                  title: "Nombre de Cliente encontrado",
                  message: "El ID ingresado está asociado al cliente '" + name + "', ¿desea actualizar también el nombre de cliente del paquete?",
                  callback: function(resito){
                    if (resito){
                      arr[0][ingresoCargaIndexes.uname] = "<h5 class='seleccionado ingCargaUname'>"+name+"</h5>";
                      tablilla.row(index).data(arr[0]).draw(false);
                    }
                  }
                });
              }
              tablilla.row(index).data(arr[0]).draw(false);
            },
            error: function() {
              bootbox.hideAll();
              tablilla.row(index).data(arr[0]).draw(false);
            }
          });
        }
        return false;
      }
    });
  });

  $('#tablaNuevaCarga tbody').on("click", "h5.ingCargaUname", function () {
    var index = tablilla.row($(this).closest('tr')).index();
    var arr = tablilla.rows(index).data().toArray();
    bootbox.prompt({
      title: "Ingrese el nombre del cliente para el paquete.",
      size: "small",
      inputType: 'text',
      callback: function (result) {
        if (result == null)
          return true;
        if (result == "")
          bootbox.alert("Debe ingresar un nombre de cliente para el paquete.");
        else if (result.includes("<"))
          bootbox.alert("El nombre de cliente no puede contener el caracter '<'");
        else if (result.includes(">"))
          bootbox.alert("El nombre de cliente no puede contener el caracter '>'");
        else if (result.includes(","))
          bootbox.alert("El nombre de cliente no puede contener comas.");
        else if (result.length > 50)
          bootbox.alert("El nombre de cliente no puede exceder los 50 caracteres.");
        else {
          arr[0][ingresoCargaIndexes.uname] = "<h5 class='seleccionado ingCargaUname'>"+result+"</h5>";
          tablilla.row(index).data(arr[0]).draw(false);
          return true;
        }
        return false;
      }
    });
  });

  $('#tablaNuevaCarga tbody').on("click", "h5.ingCargaPeso", function () {
    var index = tablilla.row($(this).closest('tr')).index();
    var arr = tablilla.rows(index).data().toArray();
    bootbox.prompt({
      title: "Ingrese el peso del paquete.",
      size: "small",
      inputType: 'number',
      callback: function (result) {
        if (result == null)
          return true;
        if (result == "")
          bootbox.alert("Debe ingresar un peso para el paquete.");
        else if (result.includes(","))
          bootbox.alert("El peso no puede contener comas.");
        else if (result.includes("."))
          bootbox.alert("El peso no puede contener punto decimal.");
        else if (result.includes("-"))
          bootbox.alert("El peso no puede ser negativo.");
        else {
          arr[0][ingresoCargaIndexes.peso] = "<h5 class='seleccionado ingCargaPeso'>"+result+"</h5>";
          tablilla.row(index).data(arr[0]).draw(false);
          document.getElementById("libras").innerHTML = "Libras: " + calcularLibras();
          return true;
        }
        return false;
      }
    });
  });

});

var corres = 0;
var addedTrackings = [];
var addedGuideNumbers = [];

function initTablaIngresoCarga(){
  $("#tablaNuevaCarga").DataTable().columns.adjust().responsive.recalc();
  $.ajax({
    url: "db/DBgetAndInsertNewUsers.php",
    cache: false,
    success: function(res){
      if (res.includes("EXITO")){
        var cant = Number(res.split(": ")[1]);
        bootbox.alert("La tabla de clientes ha sido actualizada, se agregaron " + cant + " clientes nuevos.");
      }
      else if (res.includes("INCOMPLETO")){
        var cantInsertados = Number(res.split(": ")[1].split("@")[1]);
        var cantFaltantes = Number(res.split(": ")[1].split("@")[0])-cantInsertados;
        bootbox.alert("Se intentó actualizar la tabla de clientes, pero solo " + cantInsertados + " clientes nuevos pudieron ser agregados. Hacen falta " + cantFaltantes + " aún por agregar.");
      }
      else if (res.includes("ERROR"))
        bootbox.alert("Ocurrió un error al consultar la base de datos. Se recibió el siguiente mensaje: <i><br>" + res + "</i>");
    },
    error: function(){
      bootbox.alert("No se pudo verificar actualización de la tabla de clientes debido a un problema de conexión con el servidor.");
    }
  });
}

function clearInputs() {
  document.getElementById("uid").value = "";
  document.getElementById("uname").value = "";
  document.getElementById("peso").value = "";
  document.getElementById("tracking").value = "";
  document.getElementById("guide_number").value = "";
  document.getElementById("guide_number").focus();
}

function agregarPaquete(paquete) {
  const { servicio, guideNumber, tracking, uid, uname, peso } = paquete;
  var t = $('#tablaNuevaCarga').DataTable();
  t.row.add( [
    "<h5 class='seleccionado ingCargaServicio'>"+servicio+"</h5>",
    "<h5 class='seleccionado ingCargaGuideNumber'>"+guideNumber+"</h5>",
    "<h5 class='seleccionado ingCargaTracking'>"+tracking+"</h5>",
    "<h5 class='seleccionado ingCargaUid'>"+uid+"</h5>",
    "<h5 class='seleccionado ingCargaUname'>"+uname+"</h5>",
    "<h5 class='seleccionado ingCargaPeso'>"+peso+"</h5>",
    `<img alt='eliminar' style='cursor: pointer;' data-tracking='${tracking}' data-guide-number='${guideNumber}' class='icon-delete'  src='images/remove.png'/>`
  ]).draw(false);

  document.getElementById("paquetes").innerHTML = "Paquetes: " + t.rows().data().length;
  document.getElementById("libras").innerHTML = "Libras: " + calcularLibras();
  addedGuideNumbers.push(guideNumber);
  addedTrackings.push(tracking);
  clearInputs();
}

function agregarCarga(but) {
  but.disabled = true;
  var servicio = document.getElementById("servicio").value;
  var guideNumber = document.getElementById("guide_number").value;
  var tracking = document.getElementById("tracking").value.toUpperCase();
  var uid = document.getElementById("uid").value.toUpperCase();
  var uname = document.getElementById("uname").value;
  var peso = document.getElementById("peso").value;

  if (servicio.replace(/\s/g,'').length === 0 ||
      guideNumber.replace(/\s/g,'').length === 0 ||
      tracking.replace(/\s/g,'').length === 0 ||
      uid.replace(/\s/g,'').length === 0 ||
      uname.replace(/\s/g,'').length === 0 || peso.length === 0 || peso <= 0){
    document.getElementById("spanAgregarCarga").style.display="inline";
    setTimeout(function() {
      $('#spanAgregarCarga').fadeOut('slow');
    }, 3000);
    but.disabled = false;
    return;
  }

  var nt = ["NT", "NOTRACKING", "NO", "nt", "notracking", "no", "NoTracking"];
  if (nt.indexOf(tracking) !== -1) {
    $.ajax({
      url: "db/DBgetPaquete.php",
      type: "POST",
      data: {
        select: "MAX(tracking) AS max",
        where: "tracking LIKE 'NO\_TRACKING\_%'"
      },
      cache: false,
      success: function(arr) {
        var corre = Number(JSON.parse(arr.replace("[","").replace("]","")).max.split("\_")[2])+1+corres;
        if (corre < 10)
          corre = "000"+corre;
        else if (corre < 100)
          corre = "00"+corre;
        else if (corre < 1000)
          corre = "0"+corre;

        tracking = "NO_TRACKING_"+corre;
        corres += 1;

        agregarPaquete({ servicio, guideNumber, tracking, peso, uid, uname });
        but.disabled = false;
      },
      error: function() {
        clearInputs();
        bootbox.alert("Ocurrió un problema al intentar conectarse al servidor. Intente agregar nuevamente el paquete.");
        but.disabled = false;
      }
    });

    return;
  }


  if (addedTrackings.includes(tracking)){
    bootbox.alert({
      message: "Ya ha ingresado un paquete con este número de tracking en el actual registro de carga.",
      size: 'small',
      backdrop: true
    });
    but.disabled = false;
    return;
  }
  if (addedGuideNumbers.includes(guideNumber)){
    bootbox.alert({
      message: "Ya ha ingresado un paquete con este número de guía en el actual registro de carga.",
      size: 'small',
      backdrop: true
    });
    but.disabled = false;
    return;
  }

  $.ajax({
    url: "db/DBgetPaquete.php",
    type: "POST",
    data: {
      select: "COUNT(tracking) AS cant",
      where: "tracking = '"+tracking+"'"
    },
    cache: false,
    success: function(arr) {
      var row = JSON.parse(arr.replace("[","").replace("]",""));
      but.disabled = false;
      if (row.cant != 0) {
        document.getElementById("tracking").value = '';
        document.getElementById("tracking").focus();

        bootbox.alert({
          message: "Ya existe un paquete registrado con este número de tracking, por favor ingrese el dato correctamente.",
          size: 'small',
          backdrop: true,
        });
        return;
      }
      agregarPaquete({ servicio, guideNumber, tracking, peso, uid, uname });
    },
    error: function() {
      bootbox.alert("Ocurrió un problema al intentar conectarse al servidor.");
      but.disabled = false;
    }
  });
}

function calcularLibras(){
  var t = $('#tablaNuevaCarga').DataTable();
  var data = t.rows().data().toArray();
  var total = 0;
  var str = "";
  for (var i = 0; i < data.length; i++){
    str = data[i][ingresoCargaIndexes.peso].split(">")[1].split("<")[0];
    total = total + Number(str);
  }
  return total;
}

function agregarRegistro(){

  var t = $('#tablaNuevaCarga').DataTable();
  if (t.rows().data().length == 0){
    document.getElementById("spanAgregarRegistro").style.display="inline";
    setTimeout(function() {
      $('#spanAgregarRegistro').fadeOut('slow');
    }, 3000);
    return;
  }
  var tdata = [], arr = t.rows().data().toArray();
  for (var i = 0; i < arr.length; i++)
    tdata[i] = arr[i].slice();

  var libras = calcularLibras();
  var paquetes = tdata.length;
  bootbox.confirm({
    title: "Registrar Carga",
    message: "Se ingresará al sistema un nuevo registro de carga con " + paquetes + " paquetes, para un total de " + libras + " libras, ¿desea continuar?",
    size: 'medium',
    buttons: {
      cancel: {
        label: 'Regresar',
        className: "btn-default"
      },
      confirm: {
        label: 'Continuar',
        className: "btn-success"
      }
    },
    callback: function (result) {
      if (result) {
        var arreglo = ["Cliente", "CLIENTE", "cliente", "Anónimo", "ANÓNIMO", "anónimo", "Anonimo", "ANONIMO", "anonimo"];
        var invent = $("#inventario").DataTable();
        invent.search("Esperando");
        var arr = invent.rows({search:'applied'}).data().toArray();
        var uids = "";
        for (let i = 0; i < arr.length; i++) {
          var id = arr[i][inventarioIndexes.uid].split(">")[1].split("<")[0].toUpperCase();
          if ((arreglo.indexOf(id) == -1) && (uids.split(",").indexOf(id) == -1))
            uids += id+",";
        }

        uids = uids.substr(0, uids.length-1).split(",");

        for (let i = 0; i < tdata.length; i++){
          for (var j = 0; j < tdata[i].length-1; j++)
            tdata[i][j] = tdata[i][j].split(">")[1].split("<")[0];
        }
        var rcid;
        $.ajax({
          url: "db/DBinsertCarga.php",
          type: "POST",
          data: {
            peso: libras,
            data: tdata
          },
          cache: false,
          success: function(response){
            const { success, data } = response;
            if (!success){
              if (data === null) {
                bootbox.alert({
                  title: "¡Error!",
                  message: "No se pudieron agregar los paquetes al sistema",
                  size: 'medium',
                  backdrop: true
                });
              }
              else {
                var cant = Number(data.added);
                var error = data.error;
                if (cant === 0){
                  bootbox.dialog({
                    title: "¡Error en el ingreso de carga!",
                    message: "No se pudo ingresar la carga debido a un error de base de datos. El servidor indicó el siguiente error: <br><i>"+error+"</i><br><b>(El primer paquete provocó el problema)</b><br><br> ¿Desea remover de la tabla el primer paquete?)",
                    size: 'medium',
                    backdrop: true,
                    buttons: {
                      guardar: {
                        label: "Si",
                        className: "btn btn-md btn-success alinear-derecha",
                        callback: function(){
                          var trackingsin = tdata[0][ingresoCargaIndexes.tracking];
                          t.row(0).remove().draw(false);
                          document.getElementById("libras").innerHTML = "Libras: " + calcularLibras();
                          document.getElementById("paquetes").innerHTML = "Paquetes: " + t.rows().data().length;
                          invent.search("");
                          invent.draw(false);
                          bootbox.alert("Se ha removido de la tabla el paquete que provocó el problema (el paquete con #tracking = '"+trackingsin+"').");
                        }
                      },
                      regresar: {
                        label: "No",
                        className: "btn btn-md btn-info alinear-izquierda",
                        callback: function(){
                        }
                      }
                    }
                  });
                }
                else{
                  rcid = data.rcid;
                  bootbox.dialog({
                    title: "¡Carga Incompleta!",
                    message: "Solamente se pueden agregar los primeros " + cant + " paquetes debido a un error de base de datos. El servidor indicó el siguiente error: <br><i>"+error+"</i><br><b>(El " + (cant+1) + "° paquete provocó el problema)</b><br><br> ¿Desea guardar un registro de carga con los primeros " + cant + " paquetes? (Los paquetes restantes seguirán en la tabla para poder ser agregados en otro registro de carga)",
                    size: 'medium',
                    backdrop: true,
                    buttons: {
                      guardar: {
                        label: "Si",
                        className: "btn btn-md btn-success alinear-derecha",
                        callback: function(){
                          const fecha = convertToHumanDate(data.date);
                          bootbox.alert({
                            title: "¡Carga Ingresada!",
                            message: "Los primeros " + cant + " paquetes fueron ingresados al sistema de inventario bajo el registro de carga #" + rcid + " con fecha " + fecha + ".",
                            size: 'medium',
                            backdrop: true,
                            callback: function(res){
                              for (var i = 0; i < cant+1; i++){
                                t.row(0).remove();
                              }
                              t.draw(false);
                              document.getElementById("libras").innerHTML = "Libras: " + calcularLibras();
                              document.getElementById("paquetes").innerHTML = "Paquetes: " + t.rows().data().length;
                              invent.search("");
                              invent.draw(false);
                              bootbox.alert("Para facilitar el ingreso de los paquetes restantes, se removieron de la tabla los " + cant + " paquetes recien ingresados, junto con el paquete que provocó el problema.");
                            }
                          });
                        }
                      },
                      regresar: {
                        label: "No",
                        className: "btn btn-md btn-info alinear-izquierda",
                        callback: function(){
                          $.ajax({
                            url: "db/DBexecMultiQuery.php",
                            type: "POST",
                            data:{
                              query: "DELETE FROM paquete WHERE rcid = "+rcid+"; DELETE FROM carga WHERE rcid = " + rcid
                            }
                          });
                          var trackingsin = tdata[cant][ingresoCargaIndexes.tracking];
                          t.row(cant).remove().draw(false);
                          document.getElementById("libras").innerHTML = "Libras: " + calcularLibras();
                          document.getElementById("paquetes").innerHTML = "Paquetes: " + t.rows().data().length;
                          invent.search("");
                          invent.draw(false);
                          bootbox.alert("Se ha removido de la tabla el paquete que provocó el problema (el paquete con #tracking = '"+trackingsin+"').");
                        }
                      }
                    }
                  });
                }
              }
            }
            else{
              rcid = data.rcid;
              if (uids.length !== 0){
                var arreglo = [];
                for (var j = 0; j < tdata.length; j++){
                  var uidisito = tdata[j][ingresoCargaIndexes.uid].toUpperCase();
                  if (uids.indexOf(uidisito) != -1) {
                    if (uidisito in arreglo)
                      arreglo[uidisito] += 1;
                    else
                      arreglo[uidisito] = 1;
                  }
                }

                for (var i = 0; i < uids.length; i++){
                  if (uids[i] in arreglo){
                    var sete = "plan = (plan-"+arreglo[uids[i]]+")";
                    var wher = "uid = '"+uids[i]+"' AND estado IS NULL AND LENGTH(plan) < 3 AND LENGTH(plan) > 0;";
                    var j = i;
                    $.ajax({
                      url: "db/DBsetPaquete.php",
                      type: "POST",
                      data: {
                        set: sete,
                        where: wher
                      },
                      cache: false,
                      success: function(res){
                        if (res.includes("ERROR")){
                          bootbox.alert("Ocurrió un error al consultar la base de datos. Se recibió el siguiente mensaje: " + res);
                        }
                        else if (Number(res) > 0){
                          var quer = "UPDATE paquete P, (SELECT plan FROM paquete WHERE uid = '" + uids[j] + "' AND estado IS NULL AND LENGTH(plan) < 3 AND LENGTH(plan) > 0 ORDER BY plan LIMIT 1) i SET P.plan = i.plan WHERE estado IS NULL AND uid = '" + uids[j] + "' AND rcid = '" + rcid + "'";
                          $.ajax({
                            url: "db/DBexecQuery.php",
                            type: "POST",
                            data: {
                              query: quer
                            },
                            cache: false,
                            success: function(res){
                            }
                          });
                        }
                      }
                    });
                  }
                }
              }

              const fecha = convertToHumanDate(data.date);
              bootbox.alert({
                title: "¡Carga Ingresada!",
                message: "Los paquetes fueron ingresados al sistema de inventario bajo el registro de carga #" + rcid + " con fecha " + fecha + ".",
                size: 'medium',
                backdrop: true,
                callback: function(res){
                  t.clear().draw(false);
                  document.getElementById("libras").innerHTML = "Libras: 0";
                  document.getElementById("paquetes").innerHTML = "Paquetes: 0";
                  switchContent(2);
                  invent.search("");
                  invent.draw(false);
                  corres = 0;
                }
              });
            }
          }
        });
      }
    }
  });
}

function checkTrackingExists(track){
  if (addedTrackings.includes(track)) {
    bootbox.alert({
      message: "Ya ha ingresado un paquete con este número de tracking en el actual registro de carga.",
      size: 'small',
      backdrop: true
    });
    return;
  }

  $.ajax({
    url: "db/DBgetPaquete.php",
    type: "POST",
    data: {
      select: "COUNT(tracking) AS cant",
      where: "tracking = '"+track+"'"
    },
    cache: false,
    success: function(arr) {
      var row = JSON.parse(arr.replace("[","").replace("]",""));
      if (row.cant != 0){
        bootbox.alert({
          message: "Ya existe un paquete registrado con este número de tracking, asegúrese de haber ingresado bien el dato. ",
          size: 'small',
          backdrop: true
        });
      }
    }
  });
}

function getUserName(id){
  var arreglo = ["Cliente", "CLIENTE", "cliente", "Anónimo", "ANÓNIMO", "anónimo", "Anonimo", "ANONIMO", "anonimo"];
  $.ajax({
    url: "db/DBgetUserNamePostUid.php",
    type: "POST",
    data: {
      uid: id
    },
    cache: false,
    success: function(name) {
      if (arreglo.indexOf(id) == -1 && name.replace(/\s/g,'').length == 0){
        document.getElementById("spanID").style.display="inline";
        setTimeout(function() {
          $('#spanID').fadeOut('slow');
        }, 3000);
      }
      else if (name != " ")
        document.getElementById("uname").value=name;
    },
    error: function(){
      if (arreglo.indexOf(id) == -1 && name.replace(/\s/g,'').length == 0){
        document.getElementById("spanID").style.display="inline";
        setTimeout(function() {
          $('#spanID').fadeOut('slow');
        }, 3000);
      }
    }
  });
}
