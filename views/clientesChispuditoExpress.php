<link href="css/jquery.dataTables.css" rel="stylesheet" type="text/css">
<link href="css/dataTables.tableTools.css" rel="stylesheet" type="text/css">
<link href="css/dataTables.bootstrap.css" rel="stylesheet" type="text/css">
<link href="css/dataTables.colVis.css" rel="stylesheet" type="text/css">
<link href="css/dataTables.responsive.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="js/jquery.dataTables.js"></script>
<script type="text/javascript" src="js/dataTables.tableTools.js"></script>
<script type="text/javascript" src="js/dataTables.bootstrap.js"></script>
<script type="text/javascript" src="js/dataTables.colVis.js"></script>
<script type="text/javascript" src="js/dataTables.responsive.js"></script>

<script>
    $(document).ready( function () {

        var table = $('#users').DataTable({
            "dom": 'CT<"clear">lfrtip',
            "tableTools": {
                "sSwfPath": "./swf/copy_csv_xls_pdf.swf"
            },
            "responsive": true,
            "scrollY": "500px",
            "scrollCollapse": true
        });

        $("#users tbody").on("click", "td.tarifa", function () {
            var index = table.row($(this).closest('tr')).index();
            var arr = table.rows(index).data().toArray();
            var cliente = arr[0][2] + " " + arr[0][3];
            var clienteID = arr[0][1];
            bootbox.prompt({
                title: "Nueva tarifa para " + cliente,
                inputType: 'number',
                callback: function (result) {
                    if (result){
                        $.ajax({
                                url: "db/DBsetUser.php",
                                type: "POST",
                                data: {
                                    set: "user_tarifa = " + result,
                                    where: "user_id = '" + clienteID + "'"
                                },
                                cache: false,
                                success: function(res){
                                    if (res == 1){
                                        table.cell(index, 4).data("Q " + result);
                                        table.draw(false);
                                    }
                                    else{
                                        bootbox.alert("No se pudo efectuar el cambio de tarifa, verifique que haya ingresado un valor correcto.");
                                        return false;
                                    }
                                },
                                error: function() {
                                    bootbox.alert("Ocurrió un error al conectarse a la base de datos.");
                                }
                            });
                    }
                    else{
                        bootbox.alert("No se pudo efectuar el cambio de tarifa, verifique que haya ingresado un valor correcto.");
                        return false;
                    }
                }
            });
        });
    } );
</script>

<br><br>
<br><br>

<?php
require_once("db/db_vars.php");
$newdb_connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$newsql = "SELECT user_number_id, user_id, user_fname, user_lname, user_tarifa, user_email, user_mobile, user_phone, user_address, user_gender, user_bday, user_note, user_created FROM users ORDER BY users.user_lname ASC";
$newsqlquery = $newdb_connection->query($newsql);
$numrows = mysqli_num_rows($newsqlquery);
$numfields = mysqli_num_fields($newsqlquery);

$table_data = '<table id="users" class="display">';
$table_data .= '<thead>';
$table_data .= '<tr>';
$table_data .= '<th>No.</th>';
$table_data .= '<th>Usuario</th>';
$table_data .= '<th>Nombre</th>';
$table_data .= '<th>Apellido</th>';
$table_data .= '<th>Tarifa</th>';
$table_data .= '<th>Correo</th>';
$table_data .= '<th>Celular</th>';
$table_data .= '<th>Teléfono</th>';
$table_data .= '<th>Dirección</th>';
$table_data .= '<th>Género</th>';
$table_data .= '<th>Cumpleaños</th>'; 
$table_data .= '<th>Notas</th>';
$table_data .= '<th>Fecha Creación</th>';
$table_data .= '<th> </th><th> </th></tr></thead><tbody>';
$i = 1;
while($row = mysqli_fetch_row($newsqlquery))
{   
    $table_data .= '<tr>';
    $j = 0;
    $row_user_id = "";
    foreach($row as $cell)
    {
        if ($j === 4)
            $table_data .= "<td style='cursor:pointer;' class='tarifa'>Q " . $cell . "</td>";
        else{
            $table_data .= '<td>' . $cell . '</td>';
            if($j === 1)
                $row_user_id = $cell;
        }
        $j++;
    }
    $table_data .= '<td><img src="images/edit.png" onclick="modifyUserData(\'' . $row_user_id . '\')" /></td>';
    $table_data .= '<td><img src="images/mail.png" onclick="messageUser(\'' . $row_user_id . '\')" /></td>';
    $table_data .= '</tr>';
    $i++;
}
$table_data .= '</tbody></table>';

echo $table_data;
?>

<script>
    function modifyUserData(myUserData) {
        $.ajax({
            url: "db/getDBData.php",
            type: "POST",
            data: {
                user_id: myUserData
            },
            cache: false,
            success: function(myData) {
                bootbox.dialog({
                title: "Modificar Información Usuario: " + myUserData,
                message:myData
                });
            },
            error: function() {
                bootbox.dialog({
                title: "Modificar Información Usuario: " + myUserData,
                message:"Error, por favor volver a intentar!"
                });
            }
        });
    };

    function messageUser(myUserData) {
        $.ajax({
            url: "views/messageUser.php",
            type: "POST",
            data: {
                user_id: myUserData
            },
            cache: false,
            success: function(myData) {
                bootbox.dialog({
                title: "Enviar Mensaje de Recepción de Paquete al usuario: " + myUserData,
                message:myData
                });
            },
            error: function() {
                bootbox.dialog({
                title: "Enviar Mensaje de Recepción de Paquete al usuario: " + myUserData,
                message:"Error, por favor volver a intentar!"
                });
            }
        });
    };
</script>