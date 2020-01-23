<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">

        <title>Chispudito Express - Enviar Mensaje</title>

        <!-- Bootstrap Core CSS -->
        <link href="css/bootstrap.min.css" rel="stylesheet">

        <!-- Custom CSS -->
        <link href="css/modern-business.css" rel="stylesheet">

        <!-- Custom Fonts -->
        <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
        <link rel="stylesheet" href="css/sansita-stylesheet.css" type="text/css" charset="utf-8" />

        <link href="css/custom.css?v=1.0.2" rel="stylesheet">
    </head>
    <body>

        <?php
        require_once("db/db_vars.php");
        $nuevodb_connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        $cid = $_POST['cid'];
        $sql = "SELECT * FROM cliente WHERE cid = '" . $cid . "'";
        $result = $nuevodb_connection->query($sql);
        $row = mysqli_fetch_row($result);

        $formdata  = "<div class='row modalbox'><div class='col-lg-12 col-md-12 col-sm-12'><form name='modifyUser' id='userModifyForm' novalidate>";

        $formdata .= "<div class='control-group form-group col-lg-2 col-md-2 col-sm-2'><div class='controls'><label>Código</label><input value='CHEX " . $row[1] . "' type='text' class='form-control' id='form_cid' disabled /></div></div>";

        $formdata .= "<div class='control-group form-group col-lg-3 col-md-3 col-sm-3'><div class='controls'><label>Nombre</label><input value='" . $row[2] . "' type='text' class='form-control' id='form_nombre' disabled/></div></div>";

        $formdata .= "<div class='control-group form-group col-lg-3 col-md-3 col-sm-3'><div class='controls'><label>Apellido</label><input value='" . $row[3] . "' type='text' class='form-control' id='form_apellido' disabled/></div></div>";

        $formdata .= "<div class='control-group form-group col-lg-4 col-md-4 col-sm-4'><div class='controls'><label>Correo del Cliente</label><input value='" . $row[4] . "' type='text' class='form-control' id='form_email' disabled/></div></div>";

        $formdata .= "<div class='control-group form-group col-lg-6 col-md-6 col-sm-6'><div class='controls'><label>Dirección del cliente<br></label><textarea rows='3' id='form_address' class='textarea-form' disabled>" . $row[7] . "</textarea></div></div>";

        $formdata .= "<div class='control-group form-group col-lg-6 col-md-6 col-sm-6'><div class='controls'><label>Notas<br></label><textarea rows='3' id='form_address' class='textarea-form' disabled>" . $row[10] . "</textarea></div></div>";

        $formdata .= "<div id='newitemspace'><div class='control-group form-group col-lg-12 col-md-12 col-sm-12'><div class='controls'><img src='images/add.png' style='padding:5px;' onclick='addRow()'><img src='images/remove.png' style='padding:5px;' onclick='removeRow()'></div></div>";

        $i = 1;
        $formdata .= "<div class='control-group form-group col-lg-4 col-md-4 col-sm-4'><div class='controls'><label>Paquete #" . $i . " </label><input type='text' class='form-control' id='form_user_package_desc_1' /></div></div>";

        $formdata .= "<div class='control-group form-group col-lg-4 col-md-4 col-sm-4'><div class='controls'><label>Peso(Lbs.)</label><input type='number' class='form-control' id='form_user_package_weight_1' value='0' min='0' max='100' onchange='calculateCost(\"form_user_package_cost_". $i . "\", \"". $i. "\")' /></div></div>";

        $formdata .= "<div class='control-group form-group col-lg-4 col-md-4 col-sm-4'><div class='controls'><label>Monto(Q.)</label><input type='text' class='form-control' id='form_user_package_cost_1' value='0' disabled /></div></div></div>";

        $formdata .= "<div class='control-group form-group col-lg-8 col-md-8 col-sm-8'><div class='controls'><label>Monto Flete(Q)</label><select id='monto_flete' onchange='checkValueFlete(this.value)'><option value='20' selected>Capital (Q20)</option><option value='25'>Aledaño capital (Q25)</option><option value='30'>Municipio aledaño (Q30)</option><option value='otro'>Otro</option></select><input type='text' name='other_flete' id='other_flete' class='form-control' style='display:none'/></div></div>";

        $formdata .= "<div class='control-group form-group col-lg-4 col-md-4 col-sm-4'><div class='controls'><label>Total(Q)</label><input type='text' class='form-control' id='form_user_packages_total' value='0' disabled /></div></div>";

        $formdata .= "<div class='control-group form-group col-lg-12 col-md-12 col-sm-12'><div class='controls'><label>Comentarios Adicionales<br></label><textarea rows='3' id='form_comentario' class='textarea-form'></textarea></div></div>";

        $formdata .= "<div class='control-group form-group col-lg-6 col-md-6 col-sm-6'><input id='messagesubmitbutton' type='submit' class='btn btn-primary' value='Enviar Correo' /></div>";
        $formdata .= "</form></div></div>";

        echo $formdata;
        ?>

        <script>
            var max_rows = 10; //Cantidad maxima de filas para mandar por correo
            var min_rows = 1;
            var row_amount = 1; //Inicia con 1 fila

            function calculateCost(elementID, elementNumber) {
                var elementIDToCalc = "input#form_user_package_weight_";
                elementIDToCalc = elementIDToCalc + elementNumber;
                var result = Math.ceil($(elementIDToCalc).val()) * 64.00;// Libras x Costo de Q64.00
                result = result.toFixed(2);
                var elementIDToGive = "input#" + elementID;
                $(elementIDToGive).val(result);
                calculateTotalCost();
            }

            function addRow() {
                if(row_amount < max_rows)
                {
                    row_amount++;
                    $("#newitemspace").append("<div class='control-group form-group col-lg-4 col-md-4 col-sm-4'><div class='controls'><label>Paquete #" + row_amount + "</label><input type='text' class='form-control' id='form_user_package_desc_" + row_amount + "' value='' /></div></div>");
                    $("#newitemspace").append("<div class='control-group form-group col-lg-4 col-md-4 col-sm-4'><div class='controls'><label>Peso(Lbs)</label><input type='number' class='form-control' id='form_user_package_weight_" + row_amount + "'value='0' min='1' max='100' onchange='calculateCost(\"form_user_package_cost_" + row_amount + "\", \"" + row_amount + "\")' /></div></div>");
                    $("#newitemspace").append("<div class='control-group form-group col-lg-4 col-md-4 col-sm-4'><div class='controls'><label>Monto($)</label><input type='text' class='form-control' id='form_user_package_cost_" + row_amount + "' value='0' disabled /></div></div>");
                }
            }

            function removeRow() {
              if(row_amount > 1)
              {
                  $("input#form_user_package_desc_" + row_amount).parent('div').parent('div').remove();
                  $("input#form_user_package_weight_" + row_amount).parent('div').parent('div').remove();
                  $("input#form_user_package_cost_" + row_amount).parent('div').parent('div').remove();
                  row_amount--;
              }
                  calculateTotalCost();
            }


            function calculateTotalCost() {
                var i = 1
                var subtotal = 0;
                while(i <= row_amount)
                {
                    subtotal += parseFloat($("input#form_user_package_cost_" + i).val());
                    i++;
                }
                subtotal = subtotal.toFixed(2);
                $("input#form_user_packages_total").val(subtotal);
            }

            function checkValueFlete(val)
            {
                if(val==="otro")
                    document.getElementById('other_flete').style.display='block';
                else
                    document.getElementById('other_flete').style.display='none';
            }


            $("#messagesubmitbutton").click(function() {
                event.preventDefault();
                var firstName = $("input#form_nombre").val();
                var lastName = $("input#form_apellido").val();
                var userID = $("input#form_cid").val();
                var email = $("input#form_email").val();
                var package_desc = [];
                var package_weight = [];
                var package_cost = [];
                var amount_send = $("select#monto_flete").val();
                if(amount_send === 'otro')
                {
                    amount_send = $('input#other_flete').val();
                }

                var address = $("textarea#form_address").val();

                var x = 0;
                while(x < row_amount)
                {
                    var y = x+1;
                    package_desc[x] = $("input#form_user_package_desc_" + y).val();
                    package_weight[x] = $("input#form_user_package_weight_" + y).val();
                    package_cost[x] = $("input#form_user_package_cost_" + y).val();
                    x++;
                }

                var totalcost = $("input#form_user_packages_total").val();
                var notes = $("textarea#form_comentario").val();

                if (firstName.indexOf(' ') >= 1) {
                    firstName = firstName.split(' ').slice(0, -1).join(' ');
                }

                if (lastName.indexOf(' ') >= 1) {
                    lastName = lastName.split(' ').slice(0, -1).join(' ');
                }

                var fullName = firstName + " " + lastName;

                $.ajax({
                    url: "views/sendPackageEmail.php",
                    type: "POST",
                    data: {
                        fullName: fullName,
                        userID: userID,
                        email: email,
                        package_desc: package_desc,
                        package_weight: package_weight,
                        package_cost: package_cost,
                        totalcost: totalcost,
                        notes: notes,
                        amount_send: amount_send,
                        address: address
                    },
                    cache: false,
                    success: function(){
                        //window.location.reload(true);
                        alert("Mensaje 'enviado'");
                        },
                    error: function() {
                        }
                });
            });
        </script>
    </body>
</html>