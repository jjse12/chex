
const facturaImage = data => {
    let type =  data.image_type, src = data.image;
    return `<hr><img class="factura-image" src="data:${type};base64, ${src}" />`;
};

$(document).ready( function () {
    var table = $('#facturas').DataTable({
        "retrieve": true,
        "select": true,
        "responsive": false,
        "scrollY": "500px",
        "scrollCollapse": true,
        "paging": false,
        "fixedColumns": true,
        "language": {
            "lengthMenu": "Mostrando _MENU_ facturas por página",
            "search": "Buscar:",
            "zeroRecords": "No hay facturas que coincidan con la búsqueda",
            "info": "Mostrando facturas del _START_ al _END_ de _TOTAL_ facturas totales.",
            "infoEmpty": "No se encontraron facturas.",
            "infoFiltered": "(Filtrando sobre _MAX_ facturas)",
            "paginate": {
                "first":      "Primera",
                "last":       "Última",
                "next":       "Siguiente",
                "previous":   "Anterior"
            },
            "loadingRecords": "Cargando Facturas...",
            "processing":     "Procesando...",
        },
        /*"columnDefs": [
            {
                "targets": [0, 7],
                "orderable": false
            }
        ],*/
        /*"footerCallback": function ( row, data, start, end, display ) {
            var api = this.api(), data;
            if (this.fnSettings().fnRecordsDisplay() == 0){
                api.column(4).footer().style.visibility = "hidden";
                return;
            }
            else
                api.column(4).footer().style.visibility = "visible";

            var intVal = function ( i ) {
                return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '')*1 :
                    typeof i === 'number' ?
                        i : 0;
            };

            $(api.column(4).footer() ).html(
                "<h5>Total: " + numberWithCommasNoFixed(api.column(5, { page: 'current'} ).data().reduce( function (a, b) {
                    return intVal(a) + intVal(b.split(">")[1].split("<")[0]);
                }, 0)) + " Libras</h5>"
            );

        }*/
    });

    $("#facturas tbody").on("click", "h6.seleccionado", function () {
        $(this).closest('tr').toggleClass("selected");
        table.draw(false);
        if (table.rows('.selected').data().toArray().length == 0)
            document.getElementById("divFacturaOpciones").style.visibility = "hidden";
        else document.getElementById("divFacturaOpciones").style.visibility= "visible";
    });

    $("#facturas tbody").on("click", "div.factura-data", function () {
        // var index = table.row($(this).closest('tr')).index();
        let factura = $(this).data('factura');
        let id = factura.id;
        let cliente = factura.uname;
        let descripcion = factura.description;

        $.ajax({
            url: "db/DBgetFacturasImage.php",
            data: {
                facturasId : [id]
            },
            type: "POST",
            cache: false,
            success: function(response){
                if (response.data) {
                    let images = '';
                    response.data[id].map(img => {
                       images += facturaImage(img);
                    });
                    let content =
                        `<div class="container-flex">
                            <div>Descripcion:
                            <small>${descripcion}</small>
                            </div>
                            <div class="factura-content">${images}</div></div>`;
                    bootbox.dialog({
                        title: "Detalles de factura de " + cliente + ":",
                        message: `${content}`
                    });
                }
                else if (response.message) {
                    bootbox.alert(response.message);
                }
                else {
                    bootbox.alert("No se encontraron capturas de pantalla asociadas.");
                }
            },
            error: function() {
                bootbox.alert("Ocurrió un error al conectarse a la base de datos.");
            }
        });
    });
});

function loadFacturas(){
    let table = $('#facturas').DataTable();
    table.clear();
    $.ajax({
        url: "db/DBgetFacturas.php",
        type: 'GET',
        dataType: 'json',
        contentType: "application/json; charset=utf-8",
        cache: false,
        success: function(response){
            if (response.data.length === 0){
                bootbox.alert("No se encontraron facturas.");
            }
            else {
                for (let i = 0; i < response.data.length; i++){
                    let factura = response.data[i];
                    table.row.add([
                        `<h6 class='seleccionado'>${factura['tracking']}</h6>`,
                        `<h6 class='seleccionado'>${factura['uid']}</h6>`,
                        `<h6 class='seleccionado'>${factura['uname']}</h6>`,
                        `<h6 class='seleccionado'>${factura['amount']}</h6>`,
                        `<div style='cursor:pointer;' class='factura-data' data-factura='${JSON.stringify(factura)}'><h6>EXPLORAR</h6></div>`,
                    ]);
                }
                table.draw(false);
                table.columns.adjust().responsive.recalc();
            }
        },
        error: function(){
            bootbox.alert("Ocurrió un problema al intentar conectarse al servidor.");
        }
    });
}

function generarPDF() {
    let selectedRows = $("#facturas").DataTable().rows(".selected").data().toArray();
    let facturas = {};
    selectedRows.map(row => {
        let factura = $(row[4]).data('factura');
        facturas[factura.id] = {
            name: factura.uname,
            tracking: factura.tracking,
            description: factura.description,
            amount: factura.amount,
            images: []
        };
    });

    $.ajax({
        url: "db/DBgetFacturasImage.php",
        data: {
            facturasId : Object.keys(facturas)
        },
        type: "POST",
        cache: false,
        success: function(response){
            if (response.data){
                let images = response.data;
                Object.keys(images).map(id => {
                    images[id].map(fm => {
                        facturas[id].images.push({
                            image: fm.image,
                            image_type: fm.image_type
                        });
                    });
                });

                console.log(facturas);
            }
            else if (response.message) {
                bootbox.alert(response.message);
            }
            else {
                bootbox.alert("No se encontraron capturas de pantalla asociadas.");
            }
        },
        error: function() {
            bootbox.alert("Ocurrió un error al conectarse a la base de datos.");
        }
    });
}

function setearVisibles() {

}