<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">

        <title>Chispudito Express - Base de Datos</title>

        <!-- Bootstrap Core CSS -->
        <link href="css/bootstrap.min.css" rel="stylesheet">

        <!-- Custom CSS -->
        <link href="css/modern-business.css" rel="stylesheet">

        <!-- Custom Fonts -->
        <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
        <link rel="stylesheet" href="css/sansita-stylesheet.css" type="text/css" charset="utf-8" />

        <link href="css/custom.css?v=1.0.1" rel="stylesheet">
    </head>
    <body>

        <?php
        require_once("db_vars.php");
        $nuevodb_connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        $cid = $_POST['cid'];
        $sql = "SELECT * FROM cliente WHERE cid = '" . $cid . "'";
        $result = $nuevodb_connection->query($sql);
        $row = mysqli_fetch_row($result);

        $formdata  = "<div class='row modalbox'><div class='col-lg-12 col-md-12 col-sm-12'><form name='modifyUser' id='userModifyForm' novalidate>";

        $formdata .= "<div class='control-group form-group col-lg-3 col-md-3 col-sm-3'><div class='controls'><label>Usuario No. </label><input value='" . $row[0] . "' type='text' class='form-control' id='form_user_number_id' disabled /></div></div>";

        $formdata .= "<div class='control-group form-group col-lg-3 col-md-3 col-sm-3'><div class='controls'><label>Usuario</label><input value='" . $row[1] . "' type='text' class='form-control' id='form_cid' /></div></div>";

        $formdata .= "<div class='control-group form-group col-lg-6 col-md-6 col-sm-6'><div class='controls'><label>Correo Electrónico</label><input value='" . $row[4] . "' type='email' class='form-control' id='form_email' /></div></div>";

        $formdata .= "<div class='control-group form-group col-lg-4 col-md-4 col-sm-4'><div class='controls'><label>Nombre</label><input value='" . $row[2] . "' type='text' class='form-control' id='form_nombre' /></div></div>";

        $formdata .= "<div class='control-group form-group col-lg-4 col-md-4 col-sm-4'><div class='controls'><label>Apellido</label><input value='" . $row[3] . "' type='text' class='form-control' id='form_apellido' /></div></div>";

        $formdata .= "<div class='control-group form-group col-lg-4 col-md-4 col-sm-4'><div class='controls'><label>Fecha de Nacimiento</label><input value='" . $row[9] . "' type='text' class='form-control' id='form_cumple' placeholder='yyyy-mm-dd' /></div></div>";

        $formdata .= "<div class='control-group form-group col-lg-4 col-md-4 col-sm-4'><div class='controls'><label>Teléfono Móvil</label><input value='" . $row[5] . "' type='text' class='form-control' id='form_celular' /></div></div>";

        $formdata .= "<div class='control-group form-group col-lg-4 col-md-4 col-sm-4'><div class='controls'><label>Teléfono Adicional</label><input value='" . $row[6] . "' type='text' class='form-control' id='form_telefono' /></div></div>";

        if($row[8] == 'M')
        {
            $formdata .= "<div class='col-lg-4 col-md-4 col-sm-4'><div class='controls'><label>Género</label><div class='radio col-lg-offset-1 col-md-offset-1 col-sm-offset-1'><input type='radio' class='login_input' id='form_genero_male' value='M' name='radiogroup' checked />Masculino</div><div class='radio col-lg-offset-1 col-md-offset-1 col-sm-offset-1'><input type='radio' class='login_input' id='form_genero_female' value='F' name='radiogroup'/>Femenino</div></div></div>";
        }
        elseif($row[8] == 'F')
        {
            $formdata .= "<div class='col-lg-4 col-md-4 col-sm-4'><div class='controls'><label>Género</label><div class='radio col-lg-offset-1 col-md-offset-1 col-sm-offset-1'><input type='radio' class='login_input' id='form_genero_male' value='M' name='radiogroup'/>Masculino</div><div class='radio col-lg-offset-1 col-md-offset-1 col-sm-offset-1'><input type='radio' class='login_input' id='form_genero_female' value='F' name='radiogroup' checked />Femenino</div></div></div>";
        }
        else
        {
            $formdata .= "<div class='col-lg-4 col-md-4 col-sm-4'><div class='controls'><label>Género</label><div class='radio col-lg-offset-1 col-md-offset-1 col-sm-offset-1'><input type='radio' class='login_input' id='form_genero_male' value='M' name='radiogroup'/>Masculino</div><div class='radio col-lg-offset-1 col-md-offset-1 col-sm-offset-1'><input type='radio' class='login_input' id='form_genero_female' value='F' name='radiogroup' />Femenino</div></div></div>";
        }

        $formdata .= "<div class='control-group form-group col-lg-6 col-md-6 col-sm-6'><div class='controls'><label>Dirección<br></label><textarea rows='3' id='form_direccion' class='textarea-form'>" . $row[7] . "</textarea></div></div>";

        $formdata .= "<div class='control-group form-group col-lg-6 col-md-6 col-sm-6'><div class='controls'><label>Comentarios<br></label><textarea rows='3' id='form_comentario' class='textarea-form'>" . $row[10] . "</textarea></div></div>";

        $formdata .= "<div class='control-group form-group col-lg-6 col-md-6 col-sm-6'><input id='submitbutton' type='submit' class='btn btn-primary' value='Guardar Cambios' /></div>";
        $formdata .= "</form></div></div>";

        echo $formdata;
        ?>

        <script>
            $("#submitbutton").click(function(e) {
                e.preventDefault();
                var fname = $("input#form_nombre").val();
                var lname = $("input#form_apellido").val();
                var userid = $("input#form_cid").val();
                var email = $("input#form_email").val();
                var mobile = $("input#form_celular").val();
                var phone = $("input#form_telefono").val();

                if(document.getElementById('form_genero_male').checked)
                    var gender = $("input#form_genero_male").val();
                else if(document.getElementById('form_genero_female').checked)
                    var gender = $("input#form_genero_female").val();

                var bday = $("input#form_cumple").val();
                var address = $("textarea#form_direccion").val();
                var note = $("textarea#form_comentario").val();
                var useridnumber = $("input#form_user_number_id").val();
                $.ajax({
                    url: "db/saveDBData.php",
                    type: "POST",
                    data: {
                        fname: fname,
                        lname: lname,
                        userid: userid,
                        email: email,
                        mobile: mobile,
                        phone: phone,
                        gender: gender,
                        bday: bday,
                        address: address,
                        note: note,
                        useridnumber: useridnumber
                    },
                    cache: false,
                    success: function(){
                        window.location.reload(true);
                        },
                    error: function() {
                        }
                });
            });
        </script>
    </body>
</html>