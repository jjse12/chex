const inventarioIndexes = {
  selectionCheckBox: 0,
  cobroEspecial: 1,
  fechaIngreso: 2,
  servicio: 3,
  guideNumber: 4,
  tracking: 5,
  uid: 6,
  uname: 7,
  peso: 8,
  plan: 9,
  editar: 10,
};

$(document).ready( function () {
  var table = $('#inventario').DataTable({
    "retrieve": true,
    "dom": 'CT<"clear">lfrtip',
    "tableTools": {
      "sSwfPath": "./swf/copy_csv_xls_pdf.swf"
    },
    "select": {
      "style": 'multi',
      "selector": "td:first-child"
    },
    "responsive": false,
    "scrollY": "500px",
    "scrollCollapse": true,
    "paging": false,
    "fixedColumns": true,
    "language": {
      "lengthMenu": "Display _MENU_ records per page",
      "search": "Buscar:",
      "zeroRecords": "No hay paquetes que coincidan con la búsqueda",
      "info": "Mostrando paquetes del _START_ al _END_ de _TOTAL_ paquetes totales.",
      "infoEmpty": "No se encontraron paquetes.",
      "infoFiltered": "(Filtrando sobre _MAX_ paquetes)",
      "paginate": {
        "first":      "Primera",
        "last":       "Última",
        "next":       "Siguiente",
        "previous":   "Anterior"
      },
      "loadingRecords": "Cargando Paquetes...",
      "processing":     "Procesando...",
      "select": {
        "rows": {
          "_": "Paquetes seleccionados: %d",
          "0": "",
        }
      }
    },
    "order": [[inventarioIndexes.uname, 'asc']],
    "columnDefs": [
      {
        "targets": [inventarioIndexes.selectionCheckBox, inventarioIndexes.cobroEspecial, inventarioIndexes.guideNumber, inventarioIndexes.tracking, inventarioIndexes.editar],
        "orderable": false
      },
      {
        "className": 'select-checkbox',
        "targets": inventarioIndexes.selectionCheckBox
      }
    ],
    "aoColumns": [
      null, null, null, { "sType": "date-time", "bSortable": true }, null, null, null, null, null
    ],
    "footerCallback": function ( row, data, start, end, display ) {
      var api = this.api();
      // Se usa el índice `inventarioIndexes.uname` ya que el footer que mostrará el total de libras posee colspan="2"
      if (this.fnSettings().fnRecordsDisplay() === 0){
        api.column(inventarioIndexes.uname).footer().style.visibility = "hidden";
        return;
      }
      else
        api.column(inventarioIndexes.uname).footer().style.visibility = "visible";

      var intVal = function ( i ) {
        return typeof i === 'string' ?
            i.replace(/[\$,]/g, '')*1 :
            typeof i === 'number' ?
                i : 0;
      };

      $(api.column(inventarioIndexes.uname).footer() ).html(
          "<h6>Total: " + numberWithCommasNoFixed(api.column(inventarioIndexes.peso, { page: 'current'} ).data().reduce( function (a, b) {
            return intVal(a) + intVal(b.split(">")[1].split("<")[0]);
          }, 0)) + " Libras</h6>"
      );
    }
  });

  $.fn.dataTableExt.oSort['date-time-asc'] = (a,b) => sortDateTime(false, a, b);
  $.fn.dataTableExt.oSort['date-time-desc'] = (a,b) => sortDateTime(true, a, b);

  table.on( 'select', function() {
      document.getElementById("divBotones").style.visibility= "visible";
  })
  .on( 'deselect', function() {
    if (table.rows({ selected: true }).count() === 0) {
      document.getElementById("divBotones").style.visibility = "hidden";
    }
  } );

  $(".buscarIngreso").keyup(function () {
    let val = $(this).val();
    table.column(inventarioIndexes.fechaIngreso).search(val).draw(false);
  });
  $(".buscarPlan").keyup(function () {
    let val = $(this).val();
    table.column(inventarioIndexes.plan).search(val).draw(false);
  });

  $("#inventario tbody").on("mouseover", "h6.popup", function () {
    $(this).children("span").stop(true, true).delay(200).fadeIn(500);
  });

  $("#inventario tbody").on("mouseout", "h6.popup", function () {
    $(this).children("span").stop(true, true).delay(200).fadeOut(500);
  });

  $("#inventario tbody").on("mouseover", "h6.popup-notif", function () {
    $(this).children("div").stop(true, true).delay(200).fadeIn(500);
  });

  $("#inventario tbody").on("mouseout", "h6.popup-notif", function () {
    $(this).children("div").stop(true, true).delay(200).fadeOut(500);
  });

  $("#inventario tbody").on("click", "img.icon-email", function (e) {
    e.stopPropagation();
    var index = table.row($(this).closest('tr')).index();
    var arr = table.rows(index).data().toArray();
    var sinNotificar = !arr[0][inventarioIndexes.plan].includes("Notificado por Whatsapp");
    var tracking = arr[0][inventarioIndexes.tracking].replace("<br>", "").split(">")[1].split("<")[0];
    var avisando = arr[0][inventarioIndexes.plan].includes("Avisar");
    var sete = "plan = " + (sinNotificar ? (avisando ? "'@email'" : "'email'") : (avisando ? "'@whatsmail'" : "'whatsmail'"));
    $.ajax({
      url: "db/DBsetPaquete.php",
      type: "POST",
      data: {
        set: sete,
        where: "tracking = '" + tracking + "'"
      },
      cache: false,
      success: function(res){
        if (res.includes("ERROR")){
          bootbox.alert("Ocurrió un error al consultar la base de datos. Se recibió el siguiente mensaje: <i><br>" + res + "</i>");
        }
        else if (Number(res) < 1){
          bootbox.alert("No se pudo efectuar el cambio en la base de datos, intente nuevamente");
        }
        else{
          arr[0][inventarioIndexes.plan] = sinNotificar ? (!avisando ? "<div class='row'><div class='col-lg-4 col-md-4 col-sm-4 col-xs-4'></div><img title='Notificado por Email' class='col-lg-4 col-md-4 col-sm-4 col-xs-4 plan-img' src='images/email20px.png'/></div><h6 class='popup-notif sin-plan plan btn-sm btn-danger'>Sin Especificar<div class='popupicon'><div class='row'><label class='col-lg-12 col-md-12 col-sm-12 col-xs-12' style='text-align:center;'>Notificar</label><img class='icon-whatsapp' src='images/whatsapp35px.png'/></div></div></h6>":

                  "<div class='row'><div class='col-lg-4 col-md-4 col-sm-4 col-xs-4'></div><img title='Notificado por Email' class='col-lg-4 col-md-4 col-sm-4 col-xs-4 plan-img' src='images/email20px.png'/></div><h6 class='popup-notif sin-plan plan btn-sm' style='background-color: #eaeaea; color: #444'>Avisar<div class='popupicon'><div class='row'><label class='col-lg-12 col-md-12 col-sm-12 col-xs-12' style='text-align:center;'>Notificar</label><img class='icon-whatsapp' src='images/whatsapp35px.png'/></div></div></h6>"
              ):
              (!avisando ? "<div class='row'><div class='col-lg-1 col-md-1 col-sm-1 col-xs-1'></div><img title='Notificado por Email' class='col-lg-4 col-md-4 col-sm-4 col-xs-4 plan-img' src='images/email20px.png'/><div class='col-lg-2 col-md-2 col-sm-2 col-xs-2'></div><img title='Notificado por Whatsapp' class='col-lg-4 col-md-4 col-sm-4 col-xs-4 plan-img' src='images/whatsapp20px.png'/></div><h6 class='sin-plan plan btn-sm btn-danger'>Sin Especificar</h6>":
                      "<div class='row'><div class='col-lg-1 col-md-1 col-sm-1 col-xs-1'></div><img title='Notificado por Email' class='col-lg-4 col-md-4 col-sm-4 col-xs-4 plan-img' src='images/email20px.png'/><div class='col-lg-2 col-md-2 col-sm-2 col-xs-2'></div><img title='Notificado por Whatsapp' class='col-lg-4 col-md-4 col-sm-4 col-xs-4 plan-img' src='images/whatsapp20px.png'/></div><h6 class='popup-notif sin-plan plan btn-sm' style='background-color: #eaeaea; color: #444'>Avisar"
              );
          table.row(index).data(arr[0]).draw(false);
        }
      },
      error: function() {
        bootbox.alert("Ocurrió un problema al intentar conectarse al servidor.");
      }
    });
  });

  $("#inventario tbody").on("click", "img.icon-whatsapp", function (e) {
    e.stopPropagation();
    var index = table.row($(this).closest('tr')).index();
    var arr = table.rows(index).data().toArray();
    var sinNotificar = !arr[0][inventarioIndexes.plan].includes("Notificado por Email");
    var tracking = arr[0][inventarioIndexes.tracking].replace("<br>", "").split(">")[1].split("<")[0];
    var avisando = arr[0][inventarioIndexes.plan].includes("Avisar");
    var sete = "plan = " + (sinNotificar ? (avisando ? "'@whats'" : "'whats'") : (avisando ? "'@whatsmail'" : "'whatsmail'"));
    ///*
    $.ajax({
      url: "db/DBsetPaquete.php",
      type: "POST",
      data: {
        set: sete,
        where: "tracking = '" + tracking + "'"
      },
      cache: false,
      success: function(res){
        if (res.includes("ERROR")){
          bootbox.alert("Ocurrió un error al consultar la base de datos. Se recibió el siguiente mensaje: <i><br>" + res + "</i>");
        }
        else if (Number(res) < 1){
          bootbox.alert("No se pudo efectuar el cambio en la base de datos, intente nuevamente");
        }
        else{
          arr[0][inventarioIndexes.plan] = sinNotificar ? (!avisando ? "<div class='row'><div class='col-lg-4 col-md-4 col-sm-4 col-xs-4'></div><img title='Notificado por Whatsapp' class='col-lg-4 col-md-4 col-sm-4 col-xs-4 plan-img' src='images/whatsapp20px.png'/></div><h6 class='popup-notif sin-plan plan btn-sm btn-danger'>Sin Especificar<div class='popupicon'><div class='row'><label class='col-lg-12 col-md-12 col-sm-12 col-xs-12' style='text-align:center;'>Notificar</label><img class='icon-email' src='images/email35px.png'/></div></div></h6>" :
                  "<div class='row'><div class='col-lg-4 col-md-4 col-sm-4 col-xs-4'></div><img title='Notificado por Whatsapp' class='col-lg-4 col-md-4 col-sm-4 col-xs-4 plan-img' src='images/whatsapp20px.png'/></div><h6 class='popup-notif sin-plan plan btn-sm' style='background-color: #eaeaea; color: #444'>Avisar<div class='popupicon'><div class='row'><label class='col-lg-12 col-md-12 col-sm-12 col-xs-12' style='text-align:center;'>Notificar</label><img class='icon-email' src='images/email35px.png'/></div></div></h6>"
              ) :
              (!avisando ? "<div class='row'><div class='col-lg-1 col-md-1 col-sm-1 col-xs-1'></div><img title='Notificado por Email' class='col-lg-4 col-md-4 col-sm-4 col-xs-4 plan-img' src='images/email20px.png'/><div class='col-lg-2 col-md-2 col-sm-2 col-xs-2'></div><img title='Notificado por Whatsapp' class='col-lg-4 col-md-4 col-sm-4 col-xs-4 plan-img' src='images/whatsapp20px.png'/></div><h6 class='sin-plan plan btn-sm btn-danger'>Sin Especificar</h6>" :
                      "<div class='row'><div class='col-lg-1 col-md-1 col-sm-1 col-xs-1'></div><img title='Notificado por Email' class='col-lg-4 col-md-4 col-sm-4 col-xs-4 plan-img' src='images/email20px.png'/><div class='col-lg-2 col-md-2 col-sm-2 col-xs-2'></div><img title='Notificado por Whatsapp' class='col-lg-4 col-md-4 col-sm-4 col-xs-4 plan-img' src='images/whatsapp20px.png'/></div><h6 class='popup-notif sin-plan plan btn-sm' style='background-color: #eaeaea; color: #444'>Avisar"
              );
          table.row(index).data(arr[0]).draw(false);
        }
      },
      error: function() {
        bootbox.alert("Ocurrió un problema al intentar conectarse al servidor.");
      }
    });
  });

  $("#inventario tbody").on("click", "h6.plan", function () {
    var index = table.row($(this).closest('tr')).index();
    var arr = table.rows(index).data().toArray();
    var nombre = arr[0][inventarioIndexes.uname].split(">")[1].split("<")[0];
    var uid = arr[0][inventarioIndexes.uid].split(">")[1].split("<")[0];
    var tracking = arr[0][inventarioIndexes.tracking].replace("<br>", "").split(">")[1].split("<")[0];

    var plan = "";
    if (arr[0][inventarioIndexes.plan].includes("Oficina"))
      plan = "Oficina";
    else if (arr[0][inventarioIndexes.plan].includes("Guatex"))
      plan = "Guatex:"+arr[0][inventarioIndexes.plan].split(">")[2].split("<")[0];
    if (arr[0][inventarioIndexes.plan].includes("Esperando"))
      plan = arr[0][inventarioIndexes.plan].split(">")[2].split(" Paquetes")[0];
    if (arr[0][inventarioIndexes.plan].includes("En Ruta"))
      plan = arr[0][inventarioIndexes.plan].split(">")[2].split("<")[0].replace("-", "").replace("-","");

    var arreglo = ["Cliente", "CLIENTE", "cliente", "Anónimo", "ANÓNIMO", "anónimo", "Anonimo", "ANONIMO", "anonimo"];
    var anonimo = arreglo.indexOf(uid) !== -1;

    bootbox.dialog({
      closeButton: false,
      title: "Plan de Entrega para el Paquete de " + nombre,
      message: renderPlanSelectionDialogContent(nombre, anonimo),
      buttons: {
        cancel: {
          label: "Cancelar Plan de Entrega",
          className: "btn btn-md btn-danger alinear-izquierda"
        },
        confirm: {
          label: "<div id='spanLlenarCamposCarga' style='display:none'><span class='dialog-text'>Ingrese correctamente el campo solicitado.</span></div>Asignar Plan de Entrega",
          className: "btn btn-md btn-success alinear-derecha",
          callback: function() {

            var esp = document.getElementById("form_carga_esperando").value;
            var plan = "";
            if (document.getElementById("btnOficina").style.color == "white")
              plan = "Oficina";
            else if (document.getElementById("btnGuatex").style.color == "white"){
              var f = $("#divFechaRuta").datepicker("getDate");
              plan = "Guatex:" + (f.getDate() < 10 ? "0" : "") + f.getDate() + "/" + (f.getMonth()+1 < 10 ? "0" : "") + (f.getMonth()+1) + "/" + f.getFullYear() ;
            }
            else if (document.getElementById("btnEsperando").style.color == "white"){
              if (esp.replace(/\s/g,'').length === 0 || esp < 1){
                document.getElementById("spanLlenarCamposCarga").style.display="inline";
                setTimeout(function() {$('#spanLlenarCamposCarga').fadeOut('slow');}, 3000);
                return false;
              }

              plan = document.getElementById("form_carga_esperando").value;

            }
            else if (document.getElementById("btnRuta").style.color == "white"){
              var f = $("#divFechaRuta").datepicker("getDate");
              plan = (f.getDate() < 10 ? "0" : "") + f.getDate() + "/" + (f.getMonth()+1 < 10 ? "0" : "") + (f.getMonth()+1) + "/" + f.getFullYear() ;
            }

            var wher = "tracking = '" + tracking + "'";
            var todos = false;
            if (document.getElementById("form_carga_check_esperando").checked){
              wher = "estado IS NULL AND uid = '" + uid + "'";
              todos = true;
            }

            $.ajax({
              url: "db/DBsetPaquete.php",
              type: "POST",
              data: {
                set: "plan='"+plan+"'",
                where: wher
              },
              cache: false,
              success: function(res){
                if (res.includes("ERROR")){
                  bootbox.alert("Ocurrió un error al consultar la base de datos. Se recibió el siguiente mensaje: <i><br>" + res + "</i>");
                }
                else if (Number(res) < 1){
                  bootbox.alert("No se pudo efectuar el cambio en la base de datos, intente nuevamente");
                }
                else{
                  bootbox.hideAll();
                  if (todos){
                    loadInventario();
                    bootbox.alert("Se actualizó el plan de entrega de todos los paquetes de " + nombre + ".");
                  }
                  else{
                    arr[0][inventarioIndexes.plan] = plan === "" ? "<h6 class='popup-notif sin-plan plan btn-sm btn-danger'>Sin Especificar<div class='popupicon'><div class='row'><label class='col-lg-12 col-md-12 col-sm-12 col-xs-12' style='text-align:center;'>Notificar</label><img class='icon-email' src='images/email35px.png'/>&nbsp&nbsp<img class='icon-whatsapp' src='images/whatsapp35px.png'/></div></div></h6>" :
                        plan === "Oficina" ? "<h6 class='plan btn-sm btn-success'>En Oficina</h6>" :
                            plan.includes("Guatex") ? "<h6 class='popup plan btn-sm' style='background-color: #f4cb38'>Guatex<span class='popuptext'>"+plan.split(":")[1]+"</span></h6>" :
                                plan.length < 3 ? "<h6 class='popup plan btn-sm' style='background-color: #ff8605'>Esperando<span class='popuptext'>"+plan+" Paquetes</span></h6>":
                                    "<h6 class='popup plan btn-sm btn-primary' style='text-align:center'>En Ruta<span class='popuptext'>-"+plan+"-</span></h6>";
                    table.row(index).data(arr[0]).draw(false);
                    bootbox.alert("Se actualizó la información del paquete exitosamente.");
                  }
                }
              },
              error: function() {
                bootbox.alert("Ocurrió un problema al intentar conectarse al servidor.");
              }
            });
          }
        }
      }
    });

    $('.modal-body').css({paddingTop: 0, paddingBottom: 0});

    $("#divFechaRuta").datepicker({
      showOtherMonths: true,
      selectOtherMonths: true,
      showAnim: "slideDown",
      minDate: 0,
      maxDate: "+1M"
    });
    var tom = new Date();
    tom.setTime(tom.getTime() + 86400000);
    $('#divFechaRuta').datepicker("setDate", tom);

    if (plan == "Oficina"){
      document.getElementById("btnOficina").style.backgroundColor = "#337ab7";
      document.getElementById("btnOficina").style.color = "white";
    }
    else if (plan.includes("Guatex")){
      document.getElementById("btnGuatex").style.backgroundColor = "#337ab7";
      document.getElementById("btnGuatex").style.color = "white";
      document.getElementById("divFechaRuta").style.display = "block";
      var fechaArr = plan.split(":")[1].split("/");
      $('#divFechaRuta').datepicker("setDate", new Date(Number(fechaArr[2]),Number(fechaArr[1])-1, Number(fechaArr[0])));
    }
    else if (plan.length > 0){
      if (plan.length >= 3) {
        document.getElementById("btnRuta").style.backgroundColor = "#337ab7";
        document.getElementById("btnRuta").style.color = "white";
        document.getElementById("divFechaRuta").style.display = "block";
        //document.getElementById("form_carga_fecha").value = row.plan;
        var fechaArr = plan.split("/");
        $('#divFechaRuta').datepicker("setDate", new Date(Number(fechaArr[2]),Number(fechaArr[1])-1, Number(fechaArr[0])));
      }
      else {
        document.getElementById("btnEsperando").style.backgroundColor = "#337ab7";
        document.getElementById("btnEsperando").style.color = "white";
        document.getElementById("divEsperandoCantidad").style.display = "block";
        document.getElementById("form_carga_esperando").value = plan;
      }
    }
  });


  $("#inventario tbody").on("click", "img.icon-update", function (){

    var index = table.row($(this).closest('tr')).index();
    var arr = table.rows(index).data().toArray();
    var extras = arr[0][inventarioIndexes.cobroEspecial].split('data-cobro-extra=')[1].split(' ')[0];
    var fechaIng = arr[0][inventarioIndexes.fechaIngreso].split(">")[1].split("<")[0];
    var rcid = arr[0][inventarioIndexes.fechaIngreso].split("#")[1].split("'")[0];
    var tracking = arr[0][inventarioIndexes.tracking].replace("<br>", "").split(">")[1].split("<")[0];
    var uid = arr[0][inventarioIndexes.uid].split(">")[1].split("<")[0];
    var uname = arr[0][inventarioIndexes.uname].split(">")[1].split("<")[0];
    var peso = arr[0][inventarioIndexes.peso].split(">")[1].split("<")[0];
    var plan = "";
    if (arr[0][inventarioIndexes.plan].includes("Oficina"))
      plan = "Oficina";
    else if (arr[0][inventarioIndexes.plan].includes("Guatex"))
      plan = "Guatex:"+arr[0][inventarioIndexes.plan].split(">")[2].split("<")[0];
    if (arr[0][inventarioIndexes.plan].includes("Esperando"))
      plan = arr[0][inventarioIndexes.plan].split(">")[2].split(" Paquetes")[0];
    if (arr[0][inventarioIndexes.plan].includes("En Ruta"))
      plan = arr[0][inventarioIndexes.plan].split(">")[2].split("<")[0].replace("-", "").replace("-","");

    var tom = new Date();
    tom.setTime(tom.getTime() + 86400000);
    bootbox.dialog({
      closeButton: false,
      title: "Modificar paquete de " + uname,
      message: renderModificarPaqueteDialogContent({
        extras, fechaIng, rcid, tracking, uid, uname, peso
      }),
      buttons: {
        cancel: {
          label: "Cancelar",
          className: "btn btn-md btn-danger alinear-izquierda"
        },
        confirm: {
          label: "<div id='spanLlenarCamposCarga' style='display:none'><span class='dialog-text'> Asegurese de llenar todos los campos.</span></div>Guardar Cambios",
          className: "btn btn-md btn-success alinear-derecha",
          callback: function() {
            var uid = document.getElementById("form_carga_uid").value;
            var uname = document.getElementById("form_carga_uname").value;
            var pesito = document.getElementById("form_carga_libras").value;
            var esp = document.getElementById("form_carga_esperando").value;
            var extrasN = document.getElementById("form_carga_cobro_extra").value;
            if (extrasN == '')
              extrasN = 0;

            var plan = "";

            if (uid.replace(/\s/g,'').length === 0 || uname.replace(/\s/g,'').length === 0 || pesito.length === 0){
              document.getElementById("spanLlenarCamposCarga").style.display="inline";
              setTimeout(function() {
                $('#spanLlenarCamposCarga').fadeOut('slow');
              }, 3000);
              return false;
            }


            if (document.getElementById("btnOficina").style.color == "white")
              plan = "Oficina";
            else if (document.getElementById("btnGuatex").style.color == "white"){
              var f = $("#divFechaRuta").datepicker("getDate");
              plan = "Guatex:" + (f.getDate() < 10 ? "0" : "") + f.getDate() + "/" + (f.getMonth()+1 < 10 ? "0" : "") + (f.getMonth()+1) + "/" + f.getFullYear() ;
            }
            else if (document.getElementById("btnEsperando").style.color == "white"){
              if (esp.replace(/\s/g,'').length === 0 || esp < 1){
                document.getElementById("spanLlenarCamposCarga").style.display="inline";
                setTimeout(function() {
                  $('#spanLlenarCamposCarga').fadeOut('slow');
                }, 3000);
                return false;
              }
              plan = document.getElementById("form_carga_esperando").value;
            }
            else if (document.getElementById("btnRuta").style.color == "white"){
              var f = $("#divFechaRuta").datepicker("getDate");
              plan = (f.getDate() < 10 ? "0" : "") + f.getDate() + "/" + (f.getMonth()+1 < 10 ? "0" : "") + (f.getMonth()+1) + "/" + f.getFullYear() ;
            }

            $.ajax({
              url: "db/DBsetPaquete.php",
              type: "POST",
              data: {
                set: "uid='"+uid+"', uname='"+uname+"', libras="+pesito+", plan='"+plan+"', cobro_extra="+extrasN,
                where: "tracking = '"+tracking+"'"
              },
              cache: false,
              success: function(res){
                if (res.includes("ERROR")){
                  bootbox.alert("Ocurrió un error al consultar la base de datos. Se recibió el siguiente mensaje: <i><br>" + res + "</i>");
                }
                else if (Number(res) < 1)
                  bootbox.alert("No se pudo efectuar el cambio en la base de datos, intente nuevamente");
                else{
                  var especial = extrasN > 0;
                  if (peso != pesito){
                    $.ajax({
                      url: "db/DBsetCarga.php",
                      type: "POST",
                      data: {
                        set: "total_lbs = total_lbs - (" + (peso-pesito) + ")",
                        where: "rcid = " + rcid
                      },
                      cache: false,
                      success: function(res){
                        if (res.includes("ERROR")){
                          bootbox.alert("Ocurrió un error al consultar la base de datos para actualizar el registro de cargas asociado al paquete. Se recibió el siguiente mensaje: <i><br>" + res + "</i><br>" + "Contacte con el administrador de base de datos para poder ajustar el registro de carga");
                        }
                        else if (Number(res) < 1){
                          bootbox.alert("No se pudo actualizar el peso del registro de carga asociado al paquete. Contacte con el administrador de base de datos para poder ajustar el registro de carga");
                        }
                        else{
                          bootbox.alert("La información del paquete ha sido actualizada. El total de libras del registro de carga asociado también ha sido actualizado.");
                          var table = $('#inventario').DataTable();
                          arr[0][inventarioIndexes.cobroEspecial] = `<h6 data-cobro-extra=${extrasN} >${especial ? "<span title='Cobro Extra: Q"+ numberWithCommas(extrasN) +"' style='color: gold;'><i class='fa fa-star fa-2x fa-lg'></i><small style='display: none;'>Especial</small></span>" : ""}</h6>`;
                          arr[0][inventarioIndexes.uid] = "<h6 >"+uid+"</h6>";
                          arr[0][inventarioIndexes.uname] = "<h6 >"+uname+"</h6>";
                          arr[0][inventarioIndexes.peso] = "<h6 >"+pesito+"</h6>";
                          arr[0][inventarioIndexes.plan] = plan == "" ? "<h6 class='popup-notif sin-plan plan btn-sm btn-danger'>Sin Especificar<div class='popupicon'><div class='row'><label class='col-lg-12 col-md-12 col-sm-12 col-xs-12' style='text-align:center;'>Notificar</label><img class='icon-email' src='images/email35px.png'/>&nbsp&nbsp<img class='icon-whatsapp' src='images/whatsapp35px.png'/></div></div></h6>" :
                              plan == "Oficina" ? "<h6 class='plan btn-sm btn-success'>En Oficina</h6>" :
                                  plan.includes("Guatex") ? "<h6 class='popup plan btn-sm' style='background-color: #f4cb38'>Guatex<span class='popuptext'>"+plan.split(":")[1]+"</span></h6>" :
                                      plan.length < 3 ? "<h6 class='popup plan btn-sm' style='background-color: #ff8605'>Esperando<span class='popuptext'>"+plan+" Paquetes</span></h6>":
                                          "<h6 class='popup plan btn-sm btn-primary' style='text-align:center'>En Ruta<span class='popuptext'>-"+plan+"-</span></h6>";
                          table.row(index).data(arr[0]);
                          table.order([inventarioIndexes.uname, "asc"]);
                          table.draw(false);
                        }

                      },
                      error: function(){
                        bootbox.alert("No se pudo actualizar el peso del registro de carga asociado al paquete. Contacte con el administrador de base de datos para poder ajustar el registro de carga");
                      }
                    });
                  }
                  else{
                    bootbox.alert("Se actualizó la información del paquete exitosamente.");
                    var table = $('#inventario').DataTable();
                    arr[0][inventarioIndexes.cobroEspecial] = `<h6 data-cobro-extra=${extrasN} >${especial ? "<span title='Cobro Extra: Q"+ numberWithCommas(extrasN) +"' style='color: gold;'><i class='fa fa-star fa-2x fa-lg'></i><small style='display: none;'>Especial</small></span>" : ""}</h6>`,
                    arr[0][inventarioIndexes.uid] = "<h6 >"+uid+"</h6>";
                    arr[0][inventarioIndexes.uname] = "<h6 >"+uname+"</h6>";
                    arr[0][inventarioIndexes.peso] = "<h6 >"+pesito+"</h6>";
                    arr[0][inventarioIndexes.plan] = plan == "" ? "<h6 class='popup-notif sin-plan plan btn-sm btn-danger'>Sin Especificar<div class='popupicon'><div class='row'><label class='col-lg-12 col-md-12 col-sm-12 col-xs-12' style='text-align:center;'>Notificar</label><img class='icon-email' src='images/email35px.png'/>&nbsp&nbsp<img class='icon-whatsapp' src='images/whatsapp35px.png'/></div></div></h6>" :
                        plan == "Oficina" ? "<h6 class='plan btn-sm btn-success'>En Oficina</h6>" :
                            plan.includes("Guatex") ? "<h6 class='popup plan btn-sm' style='background-color: #f4cb38'>Guatex<span class='popuptext'>"+plan.split(":")[1]+"</span></h6>" :
                                plan.length < 3 ? "<h6 class='popup plan btn-sm' style='background-color: #ff8605'>Esperando<span class='popuptext'>"+plan+" Paquetes</span></h6>":
                                    "<h6 class='popup plan btn-sm btn-primary' style='text-align:center'>En Ruta<span class='popuptext'>-"+plan+"-</span></h6>";
                    table.row(index).data(arr[0]);
                    table.order([inventarioIndexes.uname, "asc"]);
                    table.draw(false);
                  }
                }
              },
              error: function() {
                bootbox.alert("Ocurrió un problema al intentar conectarse al servidor.");
              }
            });
          }
        }
      }
    });

    $('.modal-body').css({paddingTop: 0, paddingBottom: 0});

    $("#divFechaRuta").datepicker({
      showOtherMonths: true,
      selectOtherMonths: true,
      showAnim: "slideDown",
      minDate: 0,
      maxDate: "+1M"
    });

    $('#divFechaRuta').datepicker("setDate", tom);

    if (plan == "Oficina"){
      document.getElementById("btnOficina").style.backgroundColor = "#337ab7";
      document.getElementById("btnOficina").style.color = "white";
    }
    else if (plan.includes("Guatex")){
      document.getElementById("btnGuatex").style.backgroundColor = "#337ab7";
      document.getElementById("btnGuatex").style.color = "white";
      document.getElementById("divFechaRuta").style.display = "block";
      var fechaArr = plan.split(":")[1].split("/");
      $('#divFechaRuta').datepicker("setDate", new Date(Number(fechaArr[2]),Number(fechaArr[1])-1, Number(fechaArr[0])));
    }
    else if (plan.length > 0){
      if (plan.length >= 3) {
        document.getElementById("btnRuta").style.backgroundColor = "#337ab7";
        document.getElementById("btnRuta").style.color = "white";
        document.getElementById("divFechaRuta").style.display = "block";
        //document.getElementById("form_carga_fecha").value = row.plan;
        var fechaArr = plan.split("/");
        $('#divFechaRuta').datepicker("setDate", new Date(Number(fechaArr[2]),Number(fechaArr[1])-1, Number(fechaArr[0])));
      }
      else {
        document.getElementById("btnEsperando").style.backgroundColor = "#337ab7";
        document.getElementById("btnEsperando").style.color = "white";
        document.getElementById("divEsperandoCantidad").style.display = "block";
        document.getElementById("form_carga_esperando").value = plan;
      }
    }
  });

  loadInventario();
});

function toggleActivadito(boton){
  if (boton.style.color == "white"){
    if (boton.innerHTML == "En Ruta" || boton.innerHTML == "Guatex")
      document.getElementById("divFechaRuta").style.display = "none";
    else if (boton.innerHTML == "Esperando")
      document.getElementById("divEsperandoCantidad").style.display = "none";

    boton.style.backgroundColor = "#fff";
    boton.style.color = "#337ab7";
  }
  else{
    document.getElementById("divEsperandoCantidad").style.display = "none";
    document.getElementById("divFechaRuta").style.display = "none";
    if (boton.innerHTML == "En Ruta" || boton.innerHTML == "Guatex")
      document.getElementById("divFechaRuta").style.display = "block";
    else if (boton.innerHTML == "Esperando"){
      document.getElementById("divEsperandoCantidad").style.display = "block";
      document.getElementById("form_carga_esperando").focus();
    }
    document.getElementById("btnOficina").style.backgroundColor = "#fff";
    document.getElementById("btnOficina").style.color = "#337ab7";
    document.getElementById("btnRuta").style.backgroundColor = "#fff";
    document.getElementById("btnRuta").style.color = "#337ab7";
    document.getElementById("btnEsperando").style.backgroundColor = "#fff";
    document.getElementById("btnEsperando").style.color = "#337ab7";
    document.getElementById("btnGuatex").style.backgroundColor = "#fff";
    document.getElementById("btnGuatex").style.color = "#337ab7";
    boton.style.backgroundColor = "#337ab7";
    boton.style.color = "white";
  }
}

function getUserName2(id){
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
        document.getElementById("spanIDCliente").style.display="inline";
        setTimeout(function() {
          $('#spanIDCliente').fadeOut('slow');
        }, 3000);
      }
      else
        document.getElementById("form_carga_uname").value=name;
    },
    error: function(){
      if (arreglo.indexOf(id) == -1 && name.replace(/\s/g,'').length == 0){
        document.getElementById("spanIDCliente").style.display="inline";
        setTimeout(function() {
          $('#spanID').fadeOut('slow');
        }, 3000);
      }
    }
  });
}

var isChrome = /Chrome/.test(navigator.userAgent) && /Google Inc/.test(navigator.vendor);
var dataPaqueteIndice = inventarioIndexes.fechaIngreso;

function loadInventario(){
  var table = $('#inventario').DataTable();
  table.clear();
  document.getElementById("divBotones").style.visibility = "hidden";
  $.ajax({
    url: "db/DBgetInventario.php",
    cache: false,
    success: function(paquetes) {
      paquetes.map(paquete => {
        let fechaIngreso = moment(paquete.fecha).format('DD/MM/YYYY');
        var plansito = "";
        switch (paquete.plan){
          case "":
            plansito = "<h6 class='popup-notif sin-plan plan btn-sm btn-danger'>Sin Especificar<div class='popupicon'><div class='row'><label class='col-lg-12 col-md-12 col-sm-12 col-xs-12' style='text-align:center;'>Notificar</label><img class='icon-email' src='images/email35px.png'/>&nbsp&nbsp<img class='icon-whatsapp' src='images/whatsapp35px.png'/></div></div></h6>";
            break;
          case "whats":
            plansito = "<div class='row'><div class='col-lg-4 col-md-4 col-sm-4 col-xs-4'></div><img title='Notificado por Whatsapp' class='col-lg-4 col-md-4 col-sm-4 col-xs-4 plan-img' src='images/whatsapp20px.png'/></div><h6 class='popup-notif sin-plan plan btn-sm btn-danger'>Sin Especificar<div class='popupicon'><div class='row'><label class='col-lg-12 col-md-12 col-sm-12 col-xs-12' style='text-align:center;'>Notificar</label><img class='icon-email' src='images/email35px.png'/></div></div></h6>";
            break;
          case "email":
            plansito = "<div class='row'><div class='col-lg-4 col-md-4 col-sm-4 col-xs-4'></div><img title='Notificado por Email' class='col-lg-4 col-md-4 col-sm-4 col-xs-4 plan-img' src='images/email20px.png'/></div><h6 class='popup-notif sin-plan plan btn-sm btn-danger'>Sin Especificar<div class='popupicon'><div class='row'><label class='col-lg-12 col-md-12 col-sm-12 col-xs-12' style='text-align:center;'>Notificar</label><img class='icon-whatsapp' src='images/whatsapp35px.png'/></div></div></h6>";
            break;
          case "whatsmail":
            plansito = "<div class='row'><div class='col-lg-1 col-md-1 col-sm-1 col-xs-1'></div><img title='Notificado por Email' class='col-lg-4 col-md-4 col-sm-4 col-xs-4 plan-img' src='images/email20px.png'/><div class='col-lg-2 col-md-2 col-sm-2 col-xs-2'></div><img title='Notificado por Whatsapp' class='col-lg-4 col-md-4 col-sm-4 col-xs-4 plan-img' src='images/whatsapp20px.png'/></div><h6 class='sin-plan plan btn-sm btn-danger'>Sin Especificar</h6>";
            break;
          case "@whats":
            plansito = "<div class='row'><div class='col-lg-4 col-md-4 col-sm-4 col-xs-4'></div><img title='Notificado por Whatsapp' class='col-lg-4 col-md-4 col-sm-4 col-xs-4 plan-img' src='images/whatsapp20px.png'/></div><h6 class='popup-notif sin-plan plan btn-sm' style='background-color: #eaeaea; color: #444'>Avisar<div class='popupicon'><div class='row'><label class='col-lg-12 col-md-12 col-sm-12 col-xs-12' style='text-align:center;'>Notificar</label><img class='icon-email' src='images/email35px.png'/></div></div></h6>";
            break;
          case "@email":
            plansito = "<div class='row'><div class='col-lg-4 col-md-4 col-sm-4 col-xs-4'></div><img title='Notificado por Email' class='col-lg-4 col-md-4 col-sm-4 col-xs-4 plan-img' src='images/email20px.png'/></div><h6 class='popup-notif sin-plan plan btn-sm' style='background-color: #eaeaea; color: #444'>Avisar<div class='popupicon'><div class='row'><label class='col-lg-12 col-md-12 col-sm-12 col-xs-12' style='text-align:center;'>Notificar</label><img class='icon-whatsapp' src='images/whatsapp35px.png'/></div></div></h6>";
            break;
          case "@whatsmail":
            plansito = "<div class='row'><div class='col-lg-1 col-md-1 col-sm-1 col-xs-1'></div><img title='Notificado por Email' class='col-lg-4 col-md-4 col-sm-4 col-xs-4 plan-img' src='images/email20px.png'/><div class='col-lg-2 col-md-2 col-sm-2 col-xs-2'></div><img title='Notificado por Whatsapp' class='col-lg-4 col-md-4 col-sm-4 col-xs-4 plan-img' src='images/whatsapp20px.png'/></div><h6 class='popup-notif sin-plan plan btn-sm' style='background-color: #eaeaea; color: #444'>Avisar";
            break;
          case "Oficina":
            plansito = "<h6 class='plan btn-sm btn-success'>En Oficina</h6>";
            break;
          default:
            if (paquete.plan.includes("/")){
              if (paquete.plan.includes("Guatex"))
                plansito = "<h6 class='popup plan btn-sm' style='text-align:center; background-color: #f4cb38'>Guatex<span class='popuptext'>"+paquete.plan.split(":")[1]+"</span></h6>";
              else
                plansito = "<h6 class='popup plan btn-sm btn-primary' style='text-align:center'>En Ruta<span class='popuptext'>-"+paquete.plan+"-</span></h6>";
            }
            else if (paquete.plan < 1){
              plansito = "<h6 class='popup-notif sin-plan plan btn-sm' style='background-color: #eaeaea; color: #444'>Avisar<div class='popupicon'><div class='row'><label class='col-lg-12 col-md-12 col-sm-12 col-xs-12' style='text-align:center;'>Notificar</label><img class='icon-email' src='images/email35px.png'/>&nbsp&nbsp<img class='icon-whatsapp' src='images/whatsapp35px.png'/></div></div></h6>";
            }
            else plansito = "<h6 class='popup plan btn-sm' style='background-color: #ff8605'>Esperando<span class='popuptext'>"+paquete.plan+" Paquetes</span></h6>";
            break;
        }
        var servicio = paquete.servicio;
        var guideNumber = paquete.guide_number || 'N/A';
        var extras = paquete.cobro_extra;
        var especial = extras > 0;
        var trackingsito = paquete.tracking;
        if (trackingsito.length > 20)
          trackingsito = trackingsito.substr(0, trackingsito.length/2) + "<br>" +
              trackingsito.substr(trackingsito.length/2, trackingsito.length);
        table.row.add([
          '',
          `<h6 data-cobro-extra=${extras} >${especial ? "<span title='Cobro Extra: Q" + numberWithCommas(extras) + "' style='color: gold;'><i class='fa fa-star fa-2x fa-lg'></i><small style='display:none;'>Especial</small></span>" : ""}</h6>`,
          `<h6 data-paquete='${JSON.stringify(paquete)}' title='Registro de Carga #${paquete.rcid}'  data-sorting-date="${paquete.fecha}">${fechaIngreso}</h6>`,
          "<h6>"+servicio+"</h6>",
          "<h6>"+guideNumber+"</h6>",
          `<h6 data-tracking="${paquete.tracking}">${trackingsito}</h6>`,
          "<h6>"+paquete.uid+"</h6>",
          "<h6>"+paquete.uname+"</h6>",
          "<h6>"+paquete.libras+"</h6>",
          plansito,
          "<img class='icon-update' src='images/edit.png'/>"
        ]);

      });

      table.order([inventarioIndexes.uname, "asc"]);
      table.draw(false);
      table.columns.adjust().responsive.recalc();
    },
    error: function(){
      bootbox.alert("No se pudo cargar el inventario, ocurrió un problema al intentar conectarse al servidor.");
    }
  });
}

var isMobile = (/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|ipad|iris|kindle|Android|Silk|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(navigator.userAgent)
    || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(navigator.userAgent.substr(0,4)));

function notificarSeleccionados(){
  document.getElementById("divBotones").style.visibility = "hidden";
  let selectedRows = $("#inventario").DataTable().rows({ selected: true }).data().toArray();

  let ids = [];
  selectedRows.map(row => {
    let paquete = $(row[dataPaqueteIndice]).data('paquete');
    ids.push(paquete.uid);
  });

  let comparerId = ids[0];
  let continuar = true;
  for (let i = 1; i < ids.length; i++){
    if (comparerId.toUpperCase() !== ids[i].toUpperCase()){
      continuar = false;
      break;
    }
  }

  if (!continuar){
    bootbox.dialog({
      closeButton: false,
      title: "¡Atención!",
      message: "La mercadería seleccionada pertenece a diferentes clientes, solo se puede notificar a un cliente a la vez.",
      buttons: {
        confirm: {
          label: 'Entendido',
          className: "btn-primary",
          callback: () => { document.getElementById("divBotones").style.visibility = "visible"; }
        }
      }
    });
    return;
  }

  let specialUids = ["Cliente", "CLIENTE", "cliente", "Anónimo", "ANÓNIMO", "anónimo", "Anonimo", "ANONIMO", "anonimo"];
  let searchByUid = specialUids.indexOf(comparerId) === -1;

  bootbox.dialog({
    size: 'medium',
    closeButton: false,
    title: "¿Por cuál medio desea notificar al cliente?",
    message: renderNotificationOptionsDialogContent(searchByUid),
    buttons: {
      confirm: {
        label: 'Regresar',
        className: "btn-default alinear-izquierda",
        callback: function(){
          document.getElementById("divBotones").style.visibility = "visible";
        }
      }
    }
  });
}

var whatsWebWindow = null;

clientNotificationDestFoundDialog = (dest, isWhatsAppNotification = true) => {
  let destFoundText = 'número de celular',
      destClass = 'col-sm-offset-4 col-md-offset-4 col-lg-offset-4 col-lg-4 col-md-4 col-sm-4 col-xs-12',
      finalQuestion = '¿Enviar notificación por WhatsApp a este número?';

  if (!isWhatsAppNotification){
    destFoundText = 'correo electrónico';
    destClass = 'col-sm-offset-2 col-md-offset-2 col-lg-offset-2 col-lg-8 col-md-8 col-sm-8 col-xs-12';
    finalQuestion = '¿Enviar notificación al cliente usando este correo electrónico?';
  }

  return `Los paquetes poseen un ID de Cliente auxiliar. Por medio de uno de los nombres de clientes que figuran en los registro de los paquetes, se encontró el siguiente ${destFoundText} en la base de datos:
        <br>
        <div class='row'>
            <label class='${destClass}' align='middle'
                style='font-size: 20px; background-color: #dadada; text-align: center; color: #349b25; border-radius: 7px'>
                ${dest}
            </label>
        </div>
        <div class='row'>
            <label class='col-lg-12 col-md-12 col-sm-12 col-xs-12' align='middle' style='text-align: center; color: black;'>
                ${finalQuestion}
            </label>
        </div>`;
};

askForClientDataDialog = (isWhatsAppNotification = true) => {
  let label = 'Número de celular:',
      id = 'inputNotificationPhoneNumber',
      placeholder = 'Celular del cliente',
      type = 'text',
      maxlength = 8,
      onKeyPress = 'return integersonly(this, event)',
      destClass = 'col-lg-4 col-md-4 col-sm-4 col-xs-4',
      nameClass = 'col-lg-5 col-md-5 col-sm-5 col-xs-5';
  if (!isWhatsAppNotification){
    label = 'Correo electrónico:';
    id = 'inputNotificationEmail';
    placeholder = 'Email del cliente';
    type = 'email';
    maxlength = 40;
    onKeyPress = '';
    destClass = 'col-lg-5 col-md-5 col-sm-5 col-xs-5';
    nameClass = 'col-lg-4 col-md-4 col-sm-4 col-xs-4';
  }

  return `
            <div class='row' style='background-color: #dadada'>
                <div class='col-lg-12 col-md-12 col-sm-12 col-xs-12'>
                    <br>
                    <p style='color: black'>Por favor ingresa los siguientes datos del cliente para poder enviar la notificación.</p>
                    <br>
                    <form novalidate>
                        <div class='control-group form-group ${destClass}'>
                            <div class='controls'>
                                <label style='color: #337ab7; text-align:center; width:100%'>${label}</label>
                                <input align='middle' style='text-align:center; width: 100%;' type='text' id='${id}' placeholder='${placeholder}' maxlength='${maxlength}' onkeypress='${onKeyPress}'>
                            </div>
                        </div>
                        <div class='control-group form-group col-lg-3 col-md-3 col-sm-3 col-xs-3'>
                            <div class='controls'>
                                <label style='color: #337ab7; text-align:center; width:100%'>Tarifa (Q):</label>
                                <input align='middle' style='text-align:center; width: 100%;' type='number' min='1' id='inputNotificationRate' placeholder='Tarifa a aplicar' maxlength='3' onkeypress='return integersonly(this, event)'>
                            </div>
                        </div>
                        <div class='control-group form-group ${nameClass}'>
                            <div class='controls'>
                                <label style='color: #337ab7; text-align:center; width:100%'>Nombre y apellido:</label>
                                <input align='middle' style='text-align:center; width: 100%;' type='text' id='inputNotificationClientName' placeholder='Nombre y apellido del cliente'>
                            </div>
                        </div>
                    </form>
                </div>
            </div>`;
};

async function getWhatsAppNotificationUrl(notificationData){
  let trackings = notificationData.paquetes.map(({ tracking }) => tracking );
  const response = await $.ajax({
    url: "notification/DBgetPaquetesNotificationContent.php",
    type: "POST",
    data: {
      trackings,
      uid: notificationData.uid,
      uname: notificationData.clientName,
      notificationType: 'whatsapp',
    },
    cache: false,
  });

  const { success, data, message } = response;
  if (success) {
    return "https://web.whatsapp.com/send?phone="+notificationData.phoneNumber+"&text="+data;
  }
  else {
    bootbox.alert(message);
    return false;
  }
}

async function getEmailNotificationMessage(notificationData){
  let trackings = notificationData.paquetes.map(({ tracking }) => tracking );
  const response = await $.ajax({
    url: "notification/DBgetPaquetesNotificationContent.php",
    type: "POST",
    data: {
      trackings,
      uid: notificationData.uid,
      uname: notificationData.clientName,
      notificationType: 'email',
    },
    cache: false,
  });
  const { success, data, message } = response;
  if (success) {
    return data;
  }
  else {
    bootbox.alert(message);
    return false;
  }
}

async function sendNotificationToClient(notificationData, searchAskedClientData, searchByClientUid, isWhatsAppNotification = true){
  if (searchAskedClientData){
    let dest = isWhatsAppNotification ?
        document.getElementById("inputNotificationPhoneNumber").value :
        document.getElementById("inputNotificationEmail").value;
    let nombre = document.getElementById("inputNotificationClientName").value;
    let tarifa = document.getElementById("inputNotificationRate").value;
    if (dest.length === 0 || nombre.length === 0 || tarifa.length === 0){
      bootbox.alert("Por favor llena correctamente los campos.");
      return false;
    }

    if (isWhatsAppNotification)
      notificationData.phoneNumber = "502"+dest;
    else
      notificationData.email = dest;

    notificationData.clientName = nombre;
    notificationData.rate = Number(tarifa);
  }

  if (isWhatsAppNotification)
  {
    let notificationUrl = await getWhatsAppNotificationUrl(notificationData);
    if (notificationUrl === false) return;

    if (whatsWebWindow != null && !whatsWebWindow.closed){
      whatsWebWindow.location.replace(notificationUrl);
      whatsWebWindow.focus();
    }
    else{
      whatsWebWindow = window.open(notificationUrl);
    }

    bootbox.confirm({
      size: "small",
      title: "La página de Whatsapp Web ha sido cargada",
      message: "¿Ha concluido la notificación del cliente?",
      buttons: {
        cancel: {
          label: "No",
          className: "btn btn-md btn-warning alinear-izquierda"
        },
        confirm: {
          label: "Si",
          className: "btn btn-md btn-success alinear-derecha"
        }
      },
      callback: res => {
        if (res){
          let t = $("#inventario").DataTable();
          t.rows({ selected: true }).nodes().to$().removeClass("selected");
          t.draw(false);

          setPlanForNotificatedPackages(notificationData.paquetes, 'whats');
        }
        else{
          bootbox.confirm({
            message: "En caso de que whatsapp no haya reconocido el número del cliente, recuerda que puedes notificarle por correo electrónico",
            buttons: {
              cancel: {
                label: "OK, regresar",
                className: "btn btn-md btn-default alinear-izquierda"
              },
              confirm: {
                label: "Si, enviar por correo electrónico",
                className: "btn btn-md btn-success alinear-derecha"
              }
            },
            callback: function(res){
              if (res){
                notificarViaEmail(searchByClientUid);
              }
              else{
                document.getElementById("divBotones").style.visibility = "visible";
              }
            }
          });
        }
      }
    });
  }
  else
  {
    let message = await getEmailNotificationMessage(notificationData);
    if (message === false) return;
    $.ajax({
      url: "notification/notificarViaEmail.php",
      type: "POST",
      data: {
        email: notificationData.email,
        cliente: notificationData.clientName,
        mensaje: message
      },
      cache: false,
      success: function(res){
        if (res === "Enviado"){
          bootbox.alert("La notificación por correo electrónico ha sido enviada exitosamente.");
          let t = $("#inventario").DataTable();
          t.rows({ selected: true }).nodes().to$().removeClass("selected");
          t.draw(false);

          setPlanForNotificatedPackages(notificationData.paquetes, 'email');
        }
        else{
          bootbox.alert("Hubo un problema en el servidor de envío de correo electrónico. Se obtuvo el siguiente mensaje: <br><br> \"" + res + "\"");
          document.getElementById("divBotones").style.visibility = "visible";
        }
      },
      error: function(){
        bootbox.alert("Ocurrió un error en la solicitud para enviar el correo electrónico. Intenta nuevamente.");
        document.getElementById("divBotones").style.visibility = "visible";
      }
    });
  }
}

async function notificarViaWhatsApp(searchByClientUid = true){
  bootbox.hideAll();
  let selectedRows = $("#inventario").DataTable().rows({ selected: true }).data().toArray();
  let paquetes = [];
  selectedRows.map(row => {
    let paquete = $(row[dataPaqueteIndice]).data('paquete');
    paquete.libras = Number(paquete.libras);
    paquetes.push(paquete)
  });

  let notificationData = {
    paquetes: paquetes
  };

  // Obtener datos del cliente por medio su id
  if (searchByClientUid) {
    let uid = paquetes[0].uid;
    notificationData.uid = uid;
    notificationData.clientName = paquetes[0].uname;

    let querysita = `SELECT celular, tarifa FROM cliente WHERE cid = '${uid}'`;

    try {

      const response = await $.ajax({
        url: "db/DBexecQuery.php",
        type: "POST",
        data: {query: querysita},
        cache: false,
      });
      let rows = JSON.parse(response);
      if (rows.length === 0){
        bootbox.alert("No se encontró en la base de datos los datos del cliente necesarios para enviar la notificación (celular y tarifa).");
        return;
      }
      let clientData = rows[0];
      notificationData.phoneNumber = "502"+clientData.celular;
      notificationData.rate = clientData.tarifa;
      sendNotificationToClient(notificationData, false, true)
    } catch (e) {
      bootbox.alert("Ocurrió un problema al intentar conectarse al servidor y no se pudo obtener el número de celular del cliente. Intentalo nuevamente luego.");
      document.getElementById("divBotones").style.visibility = "visible";
    }

    return;
  }

  // Obtener datos del cliente por medio del nombre de los paquetes seleccionados
  let nombres = [];
  paquetes.map(p => { nombres.push(p.uname); });
  let whereCondition = `CONCAT(nombre, ' ', apellido) IN ('${nombres.join("', '")}')`;
  let querysita = `SELECT celular, tarifa, CONCAT(nombre, ' ', apellido) AS usuario FROM cliente WHERE ${whereCondition}`;

  $.ajax({
    url: "db/DBexecQuery.php",
    type: "POST",
    data: { query: querysita },
    cache: false,
    success: function(arr){
      let rows = JSON.parse(arr);
      let encontrados = rows.length;
      if (encontrados > 1) {
        bootbox.alert("Se detectaron nombres de cliente diferentes entre los paquetes seleccionados, y estos están asociados a diferentes números de celular. Por favor cerciórate de seleccionar los paquetes de un solo cliente a la vez.");
        return;
      }
      let found = encontrados === 1;
      let title = '', msg = "";
      if (!found){
        title = '¡No se encontró un número de celular asociado!';
        msg = askForClientDataDialog();
      }
      else {
        let clientData = rows[0];
        title = 'Confirmar número de celular';
        msg = clientNotificationDestFoundDialog("+502 " + clientData.celular);
        notificationData.phoneNumber = "502"+clientData.celular;
        notificationData.clientName = clientData.usuario;
        notificationData.rate = Number(clientData.tarifa);
      }

      bootbox.dialog({
        size: 'medium',
        closeButton: false,
        title: title,
        message: msg,
        buttons: {
          regresar: {
            label: 'Regresar',
            className: "btn-default alinear-izquierda",
            callback: () => { document.getElementById("divBotones").style.visibility = "visible"; }
          },
          ingresar: {
            label: "Ingresar número manualmente",
            className: (!found ? "gone" : "btn-primary alinear-izquierda"),
            callback: function(){
              bootbox.dialog({
                size: 'medium',
                closeButton: false,
                title: "Datos del cliente para la notificación",
                message: askForClientDataDialog(),
                buttons: {
                  regresar: {
                    label: 'Cancelar',
                    className: "btn-default alinear-izquierda",
                    callback: () => { document.getElementById("divBotones").style.visibility = "visible"; }
                  },
                  confirm: {
                    label: 'Listo, continuar',
                    className: "btn-success alinear-derecha",
                    callback: () => sendNotificationToClient(notificationData, true, false)
                  }
                }
              });
              $('.modal-body').css({paddingTop: 0, paddingBottom: 0});
            }
          },
          confirm: {
            label: (found ? 'Si, continuar' : 'Listo, continuar'),
            className: "btn-success alinear-derecha",
            callback: () => sendNotificationToClient(notificationData, !found, false)
          }
        }
      });
    },
    error: () => {
      bootbox.alert("Ocurrió un problema al intentar conectarse al servidor y no se pudo obtener el número de celular del cliente. Intentalo nuevamente luego.");
      document.getElementById("divBotones").style.visibility = "visible";
    }
  });
}

async function notificarViaEmail(searchByClientUid = true){
  bootbox.hideAll();
  let selectedRows = $("#inventario").DataTable().rows({ selected: true }).data().toArray();
  let paquetes = [];
  selectedRows.map(row => {
    let paquete = $(row[dataPaqueteIndice]).data('paquete');
    paquete.libras = Number(paquete.libras);
    paquetes.push(paquete)
  });

  let notificationData = {
    paquetes: paquetes
  };

  if (searchByClientUid) {
    let uid = paquetes[0].uid, name;
    notificationData.uid = uid;
    notificationData.clientName = name = paquetes[0].uname;

    let querysita = `SELECT email, tarifa FROM cliente WHERE cid = '${uid}'`;

    try {
      const response = await $.ajax({
        url: "db/DBexecQuery.php",
        type: "POST",
        data: {
          query: querysita
        },
        cache: false,
      });
      let rows = JSON.parse(response);
      if (rows.length === 0){
        bootbox.alert("No se encontró en la base de datos los datos del cliente necesarios para enviar la notificación (email y tarifa).");
        return;
      }
      let clientData = rows[0];
      notificationData.email = clientData.email;
      notificationData.rate = clientData.tarifa;

      await sendNotificationToClient(notificationData, false, true, false);
    } catch (e) {
      bootbox.alert("Ocurrió un problema al intentar conectarse al servidor y no se pudo obtener el correo electrónico del cliente. Intenta nuevamente.");
      document.getElementById("divBotones").style.visibility = "visible";
    }
    return;
  }

  // Obtener datos del cliente por medio del nombre de los paquetes seleccionados
  let nombres = [];
  paquetes.map(p => { nombres.push(p.uname); });
  let whereCondition = `CONCAT(nombre, ' ', apellido) IN ('${nombres.join("', '")}')`;
  let querysita = `SELECT email, tarifa, CONCAT(nombre, ' ', apellido) AS usuario FROM cliente WHERE ${whereCondition}`;

  $.ajax({
    url: "db/DBexecQuery.php",
    type: "POST",
    data:{
      query: querysita
    },
    cache: false,
    success: function(arr){
      let rows = JSON.parse(arr);
      let encontrados = rows.length;
      if (encontrados > 1) {
        bootbox.alert("Se detectaron nombres de cliente diferentes entre los paquetes seleccionados, y estos están asociados a diferentes correos electrónicos. Por favor cerciórate de seleccionar los paquetes de un solo cliente a la vez.");
        return;
      }
      let found = encontrados === 1;
      let title = '', msg = "";
      if (!found){
        title = '¡No se encontró un correo electrónico asociado!';
        msg = askForClientDataDialog(false);
      }
      else {
        let clientData = rows[0];
        title = 'Confirmar correo electrónico';
        msg = clientNotificationDestFoundDialog(clientData.email, false);
        notificationData.email = clientData.email;
        notificationData.clientName = clientData.usuario;
        notificationData.rate = Number(clientData.tarifa);
      }

      bootbox.dialog({
        size: 'medium',
        closeButton: false,
        title: title,
        message: msg,
        buttons: {
          regresar: {
            label: 'Regresar',
            className: "btn-default alinear-izquierda",
            callback: () => { document.getElementById("divBotones").style.visibility = "visible"; }
          },
          ingresar: {
            label: "Ingresar email manualmente",
            className: (!found ? "gone" : "btn-primary alinear-izquierda"),
            callback: function(){
              bootbox.dialog({
                size: 'medium',
                closeButton: false,
                title: "Datos del cliente para la notificación",
                message: askForClientDataDialog(false),
                buttons: {
                  regresar: {
                    label: 'Cancelar',
                    className: "btn-default alinear-izquierda",
                    callback: () => { document.getElementById("divBotones").style.visibility = "visible"; }
                  },
                  confirm: {
                    label: 'Listo, continuar',
                    className: "btn-success alinear-derecha",
                    callback: () => sendNotificationToClient(notificationData, true, false, false)
                  }
                }
              });
              $('.modal-body').css({paddingTop: 0, paddingBottom: 0});
            }
          },
          confirm: {
            label: (found ? 'Si, continuar' : 'Listo, continuar'),
            className: "btn-success alinear-derecha",
            callback: () => sendNotificationToClient(notificationData, !found, false, false)
          }
        }
      });
    },
    error: () => {
      bootbox.alert("Ocurrió un problema al intentar conectarse al servidor y no se pudo obtener el correo electrónico del cliente. Intentalo nuevamente luego.");
      document.getElementById("divBotones").style.visibility = "visible";
    }
  });
}

function getNewPlanForNotificatedPackage(prevPlan, newPlan) {
  switch (prevPlan) {
    case '':
      break;
    case '0':
      newPlan = '@'+newPlan;
      break;
    case 'whats':
      if (newPlan === 'email')
        newPlan = 'whatsmail';
      break;
    case 'email':
      if (newPlan === 'whats')
        newPlan = 'whatsmail';
      break;
    case 'whatsmail':
      newPlan = 'whatsmail';
      break;
    case '@email':
      if (newPlan === 'whats')
        newPlan = '@whatsmail';
      break;
    case '@whats':
      if (newPlan === 'email')
        newPlan = '@whatsmail';
      break;
    case '@whatsemail':
      newPlan = '@whatsmail';
      break;
    default:
      newPlan = '';
  }

  return newPlan;
}

function setPlanForNotificatedPackages(paquetes, newPlan) {
  let plan = paquetes[0].plan;
  let trackings = [paquetes[0].tracking];
  let samePlan = true;
  for (let i = 1; i < paquetes.length; i++){
    if (paquetes[i].plan !== plan){
      samePlan = false;
      break;
    }

    trackings.push(paquetes[i].tracking);
  }

  if (samePlan){

    newPlan = getNewPlanForNotificatedPackage(plan, newPlan);
    if (newPlan === '') return;
    let where = 'tracking IN (\'' + trackings.join('\', \'') + '\')';

    $.ajax({
      url: "db/DBsetPaquete.php",
      type: "POST",
      data: {
        set: "plan = '"+newPlan+"'",
        where: where
      },
      cache: false,
      success: function(res) {
        if (res.includes("ERROR")){
          bootbox.alert("Ocurrió un error al consultar la base de datos. Se recibió el siguiente mensaje: <i><br>" + res + "</i>");
        }
        else if (Number(res) < 1){
          bootbox.alert("No se pudo efectuar el cambio en la base de datos, intente nuevamente");
        }
        else{
          Swal.fire({
            title: 'Paquetes actualizados',
            type: 'success',
            timer: 2000,
            showConfirmButton: false
          }).then(() => {
            loadInventario();
          });
        }
      },
      error: function() {
        bootbox.alert("Ocurrió un problema al intentar conectarse al servidor.");
      }
    })
  }
  else {
    paquetes.map(paquete => {

      let plan = getNewPlanForNotificatedPackage(paquete.plan, newPlan);
      if (plan === '') return;

      let where = 'tracking = \'' + paquete.tracking + '\'';

      $.ajax({
        url: "db/DBsetPaquete.php",
        type: "POST",
        data: {
          set: "plan = '"+plan+"'",
          where: where
        },
        cache: false
      })
    });

    Swal.fire({
      title: 'Paquetes actualizados',
      type: 'success',
      timer: 2000,
      showConfirmButton: false
    }).then(() => {
      loadInventario();
    });
  }
}

function planificarEntrega(){
  document.getElementById("divBotones").style.visibility = "hidden";
  var data = $("#inventario").DataTable().rows({ selected: true }).data().toArray();

  var uid = data[0][inventarioIndexes.uid].toUpperCase();
  var continuar = true;
  for (var i = 1; i < data.length; i++){
    if (uid != data[i][inventarioIndexes.uid].toUpperCase()){
      continuar = false;
      break;
    }
  }

  if (!continuar){
    bootbox.confirm({
      title: "¡Atención!",
      message: "Parece que la mercadería seleccionada pertenece a diferentes clientes, ¿desea continuar?",
      buttons: {
        cancel: {
          label: 'Cancelar',
          className: "btn-danger"
        },
        confirm: {
          label: 'Continuar',
          className: "btn-success",
        }
      },
      callback: function(res){
        if (res)
          planEntregaVarios(data, "failErrorFalso");
        else document.getElementById("divBotones").style.visibility = "visible";
      }
    });
    return;
  }
  planEntregaVarios(data, data[0][inventarioIndexes.uname].split(">")[1].split("<")[0]);
}

function planEntregaVarios(arr, nombre){
  var titulo = "Plan de Entrega para " + arr.length + " paquetes de " + nombre;
  var checkLabel = "Aplicar a todos los paquetes de " + nombre;
  if (nombre == "failErrorFalso"){
    titulo = "Plan de Entrega para " + arr.length + " paquetes";
    checkLabel = "Aplicar a todos los paquetes de cada cliente";
  }

  var uids = "(";
  for (var i = 0; i < arr.length; i++)
    uids = uids + (i == 0 ? "'":", '")+arr[i][inventarioIndexes.uid].split(">")[1].split("<")[0]+"'";
  uids = uids+")";

  var anonimo = false;
  var arreglo = ["Cliente", "CLIENTE", "cliente", "Anónimo", "ANÓNIMO", "anónimo", "Anonimo", "ANONIMO", "anonimo"];
  for (var j = 0; j < arreglo.length; j++){
    if (uids.includes(arreglo[j])){
      anonimo = true;
      break;
    }
  }

  bootbox.dialog({
    size: 'medium',
    closeButton: false,
    title: titulo,
    message: renderMultiplePlanSelectionDialogContent(checkLabel, anonimo),
    buttons: {
      cancel: {
        label: "Cancelar Plan de Entrega",
        className: "btn btn-md btn-danger alinear-izquierda",
        callback: function(){
          document.getElementById("divBotones").style.visibility = "visible";
        }
      },
      confirm: {
        label: "<div id='spanLlenarCamposCarga' style='display:none'><span class='dialog-text'>Ingrese correctamente el campo solicitado.</span></div>Asignar Plan de Entrega",
        className: "btn btn-md btn-success alinear-derecha",
        callback: function() {
          var esp = document.getElementById("form_carga_esperando").value;
          var plan = "";
          if (document.getElementById("btnOficina").style.color == "white")
            plan = "Oficina";
          else if (document.getElementById("btnGuatex").style.color == "white"){
            var f = $("#divFechaRuta").datepicker("getDate");
            plan = "Guatex:"+(f.getDate() < 10 ? "0" : "") + f.getDate() + "/" + (f.getMonth()+1 < 10 ? "0" : "") + (f.getMonth()+1) + "/" + f.getFullYear() ;
          }
          else if (document.getElementById("btnEsperando").style.color == "white"){
            if (esp.replace(/\s/g,'').length === 0 || esp < 1){
              document.getElementById("spanLlenarCamposCarga").style.display="inline";
              setTimeout(function() {$('#spanLlenarCamposCarga').fadeOut('slow');}, 3000);
              return false;
            }
            plan = document.getElementById("form_carga_esperando").value;
          }
          else if (document.getElementById("btnRuta").style.color === "white"){
            var f = $("#divFechaRuta").datepicker("getDate");
            plan = (f.getDate() < 10 ? "0" : "") + f.getDate() + "/" + (f.getMonth()+1 < 10 ? "0" : "") + (f.getMonth()+1) + "/" + f.getFullYear() ;
          }

          var trackStr = "(";
          for (var i = 0; i < arr.length; i++)
            trackStr = trackStr + (i === 0 ? "'":", '")+arr[i][inventarioIndexes.tracking].replace("<br>", "").split(">")[1].split("<")[0]+"'";
          trackStr = trackStr+")";

          var wher = "tracking IN "+trackStr;
          var todos = false;
          if (document.getElementById("form_carga_check_esperando").checked){
            todos = true;
            wher = "estado IS NULL AND uid IN " + uids;
          }

          $.ajax({
            url: "db/DBsetPaquete.php",
            type: "POST",
            data: {
              set: "plan = '"+plan+"'",
              where: wher
            },
            cache: false,
            success: function(res){
              if (res.includes("ERROR")){
                bootbox.alert("Ocurrió un error al consultar la base de datos. Se recibió el siguiente mensaje: <i><br>" + res + "</i>");
              }
              else if (Number(res) < 1){
                bootbox.alert("No se pudo efectuar el cambio en la base de datos, intente nuevamente");
              }
              else{
                bootbox.hideAll();
                loadInventario()
                bootbox.alert("Se ha actualizado la información de los paquetes seleccionados.");
              }
            },
            error: function() {
              bootbox.alert("Ocurrió un problema al intentar conectarse al servidor.");
            }
          });
        }
      }
    }
  });

  $('.modal-body').css({paddingTop: 0, paddingBottom: 0});

  $("#divFechaRuta").datepicker({
    showOtherMonths: true,
    selectOtherMonths: true,
    showAnim: "slideDown",
    minDate: 0,
    maxDate: "+1M"
  });

  var tom = new Date();
  tom.setTime(tom.getTime() + 86400000);
  $('#divFechaRuta').datepicker("setDate", tom);
}

function entregarSeleccionados(){
  document.getElementById("divBotones").style.visibility = "hidden";
  var data = $("#inventario").DataTable().rows({ selected: true }).data().toArray();

  var uid = data[0][inventarioIndexes.uid].toUpperCase();
  var plan = data[0][inventarioIndexes.plan].toUpperCase();
  var continuar = true;
  let msgError;
  for (var i = 1; i < data.length; i++){
    if (!data[i][inventarioIndexes.uid].toUpperCase().includes(uid)){
      continuar = false;
      msgError = "La mercadería seleccionada pertenece a diferentes clientes, solo se puede entregar la mercadería de un cliente a la vez.";
      break;
    }
    if (plan != data[i][inventarioIndexes.plan].toUpperCase()){
      continuar = false;
      msgError = "Los planes de entrega de los paquetes seleccionados no coinciden, verifique que no haya seleccionado paquetes de más.";
      break;
    }
  }

  if (continuar && plan.includes(">ESPERANDO<")||plan.includes(">AVISAR<")||plan.includes(">SIN ESPECIFICAR<")){
    msgError = "Debe especificar un plan de entrega para los paquetes (Oficina, Por Ruta o Guatex)";
    continuar = false;
  }

  if (!continuar){
    bootbox.dialog({
      closeButton: false,
      title: "¡Atención!",
      message: msgError,
      buttons: {
        confirm: {
          label: 'Entendido',
          className: "btn-primary",
          callback: function(){
            document.getElementById("divBotones").style.visibility = "visible";
          }
        }
      }
    });
    return;
  }

  showEntregaMercaderiaDialog(data, "Entregando mercadería a " + data[0][inventarioIndexes.uname].split(">")[1].split("<")[0]);
}

var loadingEntregaMercaderiaTable = false;
const getEntregaMercaderiaTable = async (trackings, uid, pagoTarjeta) => {
  loadingEntregaMercaderiaTable = true;
  const result = await $.ajax({
    url: "views/getTableCostoMercaderia.php",
    type: "POST",
    data: {
      trackings,
      uid,
      pagoTarjeta,
    },
    cache: false,
  });
  loadingEntregaMercaderiaTable = false;
  return result;
};

async function showEntregaMercaderiaDialog(data, titulo) {
  tipoDePagoSeleccionado = '';
  var paquetes = data.length, libras = 0;
  var uid = data[0][inventarioIndexes.uid].split(">")[1].split("<")[0];
  var unombre = data[0][inventarioIndexes.uname].split(">")[1].split("<")[0];
  var plan = "";

  if (data[0][inventarioIndexes.plan].includes("Oficina"))
    plan = "Oficina";
  else if (data[0][inventarioIndexes.plan].includes("Guatex"))
    plan = "Guatex: " + data[0][inventarioIndexes.plan].split(">")[2].split("<")[0];
  if (data[0][inventarioIndexes.plan].includes("Esperando"))
    plan = data[0][inventarioIndexes.plan].split(">")[2].split(" Paquetes")[0];
  if (data[0][inventarioIndexes.plan].includes("En Ruta"))
    plan = "Por Ruta: " + data[0][inventarioIndexes.plan].split(">")[2].split("<")[0].replace("-", "").replace("-", "");

  var trackings = [];
  let boletaEntregaPaquetes = [];
  for (var i = 0; i < data.length; i++) {
    let tracking = $(data[i][inventarioIndexes.tracking]).data('tracking');
    let peso = Number(data[i][inventarioIndexes.peso].split(">")[1].split("<")[0]);
    boletaEntregaPaquetes.push({tracking, peso});
    trackings.push(tracking);
    libras += peso;

  }

  const table = await getEntregaMercaderiaTable(trackings, uid, false);

  let customerInfo = await getCustomerInfo(uid);
  if (customerInfo === false){
    customerInfo = {
      cid: uid,
      nombre: '',
      apellido: '',
      celular: '',
      telefono: '',
      direccion: ''
    };
  }
  const callback = () => {
    const $restringirEntrega = $('#restringir-entrega');
    if ($restringirEntrega.length > 0) {
      activateSpanEntrega("No se puede entregar la mercadería. Verifica los datos de los paquetes a entregar...");
      return false;
    }

    if (loadingEntregaMercaderiaTable) {
      activateSpanEntrega("Espera a que se recalculen los costos totales para los paquetes...");
      return false;
    }

    const $tipoPagoSelect = $('#tipo-de-pago');
    var tipoPago = $tipoPagoSelect.val();
    if (tipoPago === '')  {
      activateSpanEntrega("Por favor especifique una forma de pago.");
      $tipoPagoSelect.focus();
      return false;
    }

    var costoEnvio = "NULL";
    if (plan.includes("/")) {
      const $costoEnvio = $('#costoRutaEntrega');
      costoEnvio = $costoEnvio.val();
      if (costoEnvio.replace(/\s/g, '').length === 0) {
        activateSpanEntrega("Por favor ingrese el costo para el envío de la mercadería.");
        $costoEnvio.focus();
        return false;
      }
      costoEnvio = Number(costoEnvio);
    }

    var pressed = document.getElementById("btnDescuento").style.color === "white";
    var de = "NULL";

    let desc = document.getElementById("descuentoEntrega").value;
    let comment = document.getElementById("comentarioEntrega").value;
    if (pressed) {

      if (desc.replace(/\s/g, '').length === 0 && comment.replace(/\s/g, '').length === 0) {
        activateSpanEntrega("Por favor llene los campos correspondientes al descuento especial.");
        return false;
      } else if (comment.replace(/\s/g, '').length === 0) {
        activateSpanEntrega("Por favor ingrese el motivo del descuento en el campo 'Comentario'.");
        return false;
      } else if (desc.replace(/\s/g, '').length === 0) {
        activateSpanEntrega("Por favor ingrese el descuento a aplicar.");
        return false;
      }
      de = "'" + desc + "@@@" + comment + "'";
    } else if (comment.replace(/\s/g, '').length !== 0) {
      de = "'@@@" + comment + "'";
    }

    var total = document.getElementById("totalEntrega").value;

    if (plan === "") plan = "No Especificado";
    const table = document.getElementById('table-entrega-mercaderia');
    if (!table){
      activateSpanEntrega('No se ha logrado obtener la información de la tabla, ' +
          'por favor cierra este diálogo e intenta entregar la mercadería nuevamente.');
      return false;
    }
    const tableMarkup = table.outerHTML;
    if (!tableMarkup) {
      activateSpanEntrega('No se ha logrado obtener la información de la tabla, ' +
          'por favor cierra este diálogo e intenta entregar la mercadería nuevamente.');
      return false;
    }

    return {
      total,
      tipoPago,
      costoEnvio,
      de,
      tableMarkup,
    }
  };

  const showBoletaInputsDialog = (finalCallback = null) => {
    const callBackResult = callback();
    if (callBackResult === false) return false;
    const { tipoPago } = callBackResult;
    const comentario = document.getElementById('comentarioEntrega').value;
    bootbox.dialog({
      closeButton: false,
      title: 'Datos para boleta de entrega',
      size: 'medium',
      message: renderGenerateBoletaDialog(customerInfo, plan === 'Oficina', comentario),
      buttons: {
        cancel: {
          label: "Cancelar Boleta",
          className: "btn btn-md btn-danger alinear-izquierda",
        },
        confirm: {
          label: "Crear Boleta",
          className: "btn btn-md btn-success alinear-derecha",
          callback: () => {
            let costoPaquetes = Number($('#th-total').data('total')).toGTQMoney();
            let fecha = moment().format('LL');
            let tipo = 'Oficina';
            if (plan.includes("/")) {
              tipo = 'Ruta';
              let fechaParts = plan.split(': ')[1].split('/');
              fecha = moment(`${fechaParts[2]}-${fechaParts[1]}-${fechaParts[0]}`).format( 'LL');
            }
            let costoTotal = document.getElementById("totalEntrega").value;
            const cliente = `${customerInfo.nombre} ${customerInfo.apellido} / CHEX ${customerInfo.cid}`;

            const data = {
              fecha,
              cliente,
              receptor: $('#boletaNombreReceptor').val(),
              telefono: $('#boletaTelefono').val(),
              direccion: $('#boletaDireccion').val(),
              tipo,
              metodoPago: tipoPago === 'Tarjeta' ? 'Tarjeta de crédito' : tipoPago,
              paquetes: boletaEntregaPaquetes,
              costoPaquetes,
              costoTotal,
              comentario: $('#boletaComentario').val()
            };
            $.ajax({
              url: 'views/getBoleta.php',
              type: 'POST',
              data,
              cache: false,
              success: (res, status, xhr) => {
                if (xhr.status === 200){
                  if (res.success){
                    let boletas = res.data.boletas;
                    let html = boletas.length > 1 ? 'La boleta se ha almacenado en los siguientes archivos HTML: <br><br>' :
                        'La boleta se ha almacenado en el siguiente archivo HTML: <br><br>';
                    html += `<b>${boletas.join('.html</b><br><b>')}.html</b>`;
                    bootbox.hideAll();
                    document.getElementById("divBotones").style.visibility = "none";
                    let t = $("#inventario").DataTable();
                    t.rows({ selected: true }).nodes().to$().removeClass("selected");
                    t.draw(false);
                    Swal.fire({
                      title: 'Boleta de entrega al cliente generada',
                      html,
                      type: 'success',
                      allowEscapeKey : false,
                      allowOutsideClick: false,
                      confirmButtonText: 'Ok',
                    });

                    for (let i = boletas.length -1; i >= 0; i--){
                      window.open(`boletas/${boletas[i]}.html`);
                    }

                    if (finalCallback !== null){
                      finalCallback();
                    }
                  }
                  else {
                    const { data: { errorMessage, stackTrace } } = res;
                    bootbox.dialog({
                      backdrop: true,
                      closeButton: true,
                      title: 'Error al intentar crear boleta de entrega',
                      message: `
                        <div style="overflow-y: auto;">
                          <p style='color: black'>${errorMessage}</p>
                            <div class="alert alert-warning" role="alert">
                            <span>Stack trace del error</b>:</span><br>
                            <span><b>Error</b>: <span style="color: indianred">${stackTrace}</span></span>
                          </div>
                        </div>
                      `,
                    });
                  }
                }
              },
              error: function (xhr) {
                if (xhr.status === 400){
                  bootbox.alert("Error en la solicitud enviada, revisar parámetros enviados!");
                }
                else {
                  bootbox.alert("Ocurrió un error al intentar crear la boleta de entrega.");
                }
              }
            });
          }
        }
      }
    });
    return false;
  }

  bootbox.dialog({
    closeButton: false,
    title: titulo,
    size: 'large',
    message: renderEntregaMercaderaDialog(table, plan, trackings, uid),
    buttons: {
      cancel: {
        label: "Cancelar Entrega",
        className: "btn btn-md btn-danger alinear-izquierda",
        callback: function () {
          document.getElementById("divBotones").style.visibility = "visible";
        }
      },
      confirm: {
        label: "Terminar Entrega",
        className: "btn btn-md btn-success alinear-derecha",
        callback: function () {
          const callBackResult = callback();
          if (callBackResult === false) return false;

          const { total, tipoPago, costoEnvio, de, tableMarkup } = callBackResult;
          const data = {
            trackings,
            p: paquetes,
            ui: uid,
            un: unombre,
            to: total,
            lbs: libras,
            m: tipoPago,
            r: costoEnvio,
            des: de,
            pl: plan,
            table: tableMarkup
          };

          const successCallback = (response) => {
            const { success, message, data } = response;
            if (success) {
              const fecha = convertToHumanDate(data.date);
              if (!message) {
                $("#inventario").DataTable().rows({ selected: true }).remove().draw(false);
                document.getElementById("divBotones").style.visibility = "hidden";
                bootbox.alert("La mercadería ha sido entregada con éxito. Se registró una nueva boleta virtual, con fecha " + fecha + ".");
              }
              else {
                bootbox.alert('La mercadería ha sido entregada con éxito. Se registró una nueva boleta virtual, ' +
                    `con fecha ${fecha}.<br><br>Sin embargo el servidor indicó lo siguiente: ${message}` );
              }
            } else if (message) {
              document.getElementById("divBotones").style.visibility = "visible";
              bootbox.alert(message);
            }
            else {
              document.getElementById("divBotones").style.visibility = "visible";
              bootbox.alert("Ocurrió un error inesperado al consultar la base de datos");
            }
          };

          const errorCallback = () => {
            document.getElementById("divBotones").style.visibility = "visible";
            bootbox.alert("Ocurrió un problema al intentar conectarse al servidor.");
          };

          const proceedInsertingEntrega = () => insertEntrega(data, successCallback, errorCallback);

          bootbox.dialog({
            closeButton: false,
            title: 'Crear boleta de entrega',
            size: 'small',
            message: 'Deseas generar la boleta de entrega imprimible',
            buttons: {
              confirm: {
                label: "Si",
                className: 'btn btn-md btn-success alinear-derecha',
                callback: () => {
                  showBoletaInputsDialog(proceedInsertingEntrega);
                }
              },
              cancel: {
                label: "No",
                className: 'btn btn-md btn-info alinear-derecha',
                callback: () => {
                  proceedInsertingEntrega();
                  bootbox.hideAll();
                }
              },
            }
          });
          return false;
        }
      },
      boleta: {
        label: 'Generar Boleta',
        className: 'btn btn-md btn-info alinear-derecha',
        callback: () => {
          showBoletaInputsDialog()
          return false;
        }
      },
    }
  }).find("div.modal-dialog").addClass("fullWidthDialog");

  $('.modal-body').css({paddingTop: 0, paddingBottom: 0});
  calcularTotalEntrega();
}

function insertEntrega(data, success, error)
{
  $.ajax({
    url: "db/DBinsertEntrega.php",
    type: "POST",
    data,
    cache: false,
    success,
    error
  });
}

async function getCustomerInfo(cid) {
  try {
    const response = await $.ajax({
      url: "db/DBexecQuery.php",
      type: "POST",
      data: {query: `SELECT * FROM cliente WHERE cid = '${cid}'`},
      cache: false,
    });
    let rows = JSON.parse(response);
    if (rows.length === 0){
      bootbox.alert("No se encontró en la base de datos la información del cliente.");
      return false;
    }
    const data = rows[0];
    if (data.telefono === '0') data.telefono = '';
    return data;
  } catch (e) {
    bootbox.alert("Ocurrió un problema al intentar conectarse al servidor y no se pudo obtener la información del cliente. Intentalo nuevamente luego.");
  }
}

function activateSpanEntrega(str){
  document.getElementById("spanInputEntrega").innerHTML = str;
  document.getElementById("divSpanInputEntrega").style.display="inline";
  setTimeout(function() {$('#divSpanInputEntrega').fadeOut('slow');}, 3000);
}

function calcularTotalEntrega(){

  const $tableTotalCell = $('#th-total');
  var total = Number($tableTotalCell.data('total'));
  if (document.getElementById("btnDescuento").style.color === "white"){
    total -= Number(document.getElementById("descuentoEntrega").value);
  }

  if ($("#divCostoRuta").length){
    total += Number($('#costoRutaEntrega').val());
  }

  let value = numberWithCommas(total);
  document.getElementById("totalEntrega").value = "Q " + value;
}

function toggleDescuento(){
  var boton = document.getElementById("btnDescuento");
  var div = document.getElementById("divDescuentoInput");

  if (boton.style.color === "white"){
    div.style.pointerEvents = "none";
    div.style.opacity = "0.4";
    boton.style.backgroundColor = "#fff";
    boton.style.color = "#337ab7";
    calcularTotalEntrega();
  }
  else{
    div.style.pointerEvents = "all";
    div.style.opacity = "1.0";
    boton.style.backgroundColor = "#337ab7";
    boton.style.color = "white";
    calcularTotalEntrega();
  }
}

var tipoDePagoSeleccionado = '';
async function tipoDePagoOnChange(selectBox, trackings, uid) {
  const $select = $(selectBox);
  const tipoDePagoAnterior = tipoDePagoSeleccionado;
  tipoDePagoSeleccionado = $select.val();
  let table = '';
  if (tipoDePagoAnterior === 'Tarjeta') {
    table = await getEntregaMercaderiaTable(trackings, uid, false);
  }
  else if (tipoDePagoSeleccionado === 'Tarjeta') {
    table = await getEntregaMercaderiaTable(trackings, uid, true);
  }

  if (table !== ''){
    $('#table-entrega-mercaderia').remove();
    $('#divEntregaMercaderiaTable').append($(table));
    calcularTotalEntrega();
  }
}

async function showCostoMercaderia(wholeInventory) {
  document.getElementById("divBotones").style.visibility = "hidden";
  let data = {};
  let title;
  if (wholeInventory){
    data.inventory = true;
    title = 'Costo de todos los paquetes';
  }
  else {
    const tableData = $("#inventario").DataTable().rows({ selected: true }).data().toArray();
    let trackings = [];
    let clientChexIds = [];
    let userName;
    for (var i = 0; i < tableData.length; i++) {
      let paquete = $(tableData[i][inventarioIndexes.fechaIngreso]).data('paquete');
      trackings.push(paquete.tracking);
      clientChexIds.push(paquete.uid);
      userName = paquete.uname;
    }
    let uniqueClientChexIds = [... new Set(clientChexIds)];
    title = uniqueClientChexIds.length === 1 ?
        `Costo de paquetes de: ${userName} CHEX ${uniqueClientChexIds[0]}`:
        'Costo de paquetes seleccionados';
    data.trackings = trackings;
  }

  const table = await $.ajax({
    url: "views/getTableCostoMercaderia.php",
    type: "POST",
    data,
    cache: false,
  });

  bootbox.dialog({
    closeButton: false,
    title,
    size: 'large',
    message: renderCostoMercaderaDialog(table),
    buttons: {
      cancel: {
        label: "Regresar",
        className: "btn btn-md btn-default",
        callback: function () {
          if (!wholeInventory){
            document.getElementById("divBotones").style.visibility = "visible";
          }
        }
      },
    }
  }).find("div.modal-dialog").addClass("largeWidthDialog");
}