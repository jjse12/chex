<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta http-equiv="cache-control" content="max-age=0" />
        <meta http-equiv="cache-control" content="no-cache" />
        <meta http-equiv="expires" content="0" />
        <meta http-equiv="expires" content="Tue, 01 Jan 1980 1:00:00 GMT" />
        <meta http-equiv="pragma" content="no-cache" />
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="msapplication-TileColor" content="#2d89ef">
        <meta name="msapplication-TileImage" content="/mstile-144x144.png">
        <meta name="theme-color" content="#ffffff">


        <title>Chispudito Express - Administración</title>

        <!-- Bootstrap Core CSS -->
        <link href="css/bootstrap.css" rel="stylesheet">

        <!-- Custom CSS -->
        <link href="css/modern-business.css" rel="stylesheet">

        <!-- Custom Fonts -->
        <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
        <!-- jQuery -->
        <link href="./css/custom.css" rel="stylesheet">
        <link href="./css/tableStyles.css" rel="stylesheet">
        <link href="./css/general.css" rel="stylesheet">
        <link href="./css/loader.css" rel="stylesheet">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
        <script type="text/javascript" src="js/jquery.js"></script>
        <script type="text/javascript" src="js/jquery.session.js"></script>
        <script type="text/javascript" src="js/bootbox.js"></script>
        <script type="text/javascript" src="js/bootstrap.min.js"></script>

        <script type="text/javascript" src="js/moment.min.js"></script>
        <script type="text/javascript" src="js/loader.js"></script>
        <script type="text/javascript" src="js/utils.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@8"></script>
    </head>
    <!--
    <body style="overflow: hidden; height: 100%">
    -->
    <body>
        <nav class="navbar navbar-orange navbar-fixed-top">
            <h1 align="center" style="text-align: center;" class="header-title">Chispudito Express
                <small>Administración</small>
            </h1>
        </nav>
        <div>
            <?php
                session_start();

                if (isset($_GET["logout"]))
                echo "<script type='text/javascript'>$(document).ready(function (){ bootbox.alert('La sesión ha sido cerrada exitosamente.'); }); </script>";

                if (isset($_SESSION["user_login_status"]) AND ($_SESSION["user_login_status"] == 1)) {
                    define("ADMIN", (isset($_SESSION["user_admin"]) AND ($_SESSION["user_admin"] == 1)));
                    include("views/adminTabSelector.php");
                }
                else
                    include("views/not_logged_in.php");
            ?>
        </div>

        <script type="text/javascript">
            moment.locale('es');

            $(document).ajaxStart(function() {
                Pace.restart();
            });

            function numberWithCommas(num) {
                return Number(num).toFixed(2).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            }
            function numberWithCommasNoFixed(num) {
                return Number(num).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            }

            function notAllow(myfield, e, restringidos){
                var key;
                var keychar;

                if (window.event)
                    key = window.event.keyCode;
                else if (e)
                    key = e.which;
                else
                    return true;
                keychar = String.fromCharCode(key);

                // control keys
                if ((key==null) || (key==0) || (key==8) ||
                    (key==9) || (key==13) || (key==27) )
                    return true;

                // numbers
                else if ((""+restringidos).indexOf(keychar) > -1)
                    return false;
                return true;
            }

            function numbersonly(myfield, e, extras){
                var key;
                var keychar;

                if (window.event)
                    key = window.event.keyCode;
                else if (e)
                    key = e.which;
                else
                    return true;
                keychar = String.fromCharCode(key);

                // control keys
                if ((key==null) || (key==0) || (key==8) ||
                    (key==9) || (key==13) || (key==27) )
                    return true;

                // numbers
                else if ((("0123456789."+extras).indexOf(keychar) > -1))
                    return true;
                return false;
            }

            function onlyLettersAndNumbers(myfield, e, dec){
                var key;
                var keychar;

                if (window.event)
                    key = window.event.keyCode;
                else if (e)
                    key = e.which;
                else
                    return true;
                keychar = String.fromCharCode(key);

                // control keys
                if ((key==null) || (key==0) || (key==8) ||
                    (key==9) || (key==13) || (key==27) )
                    return true;

                // numbers
                else if ((("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ").indexOf(keychar) > -1))
                    return true;

                return false;
            }

            function integersonly(myfield, e, dec){
                var key;
                var keychar;

                if (window.event)
                    key = window.event.keyCode;
                else if (e)
                    key = e.which;
                else
                    return true;
                keychar = String.fromCharCode(key);

                // control keys
                if ((key==null) || (key==0) || (key==8) ||
                    (key==9) || (key==13) || (key==27) )
                    return true;

                // numbers
                else if ((("0123456789").indexOf(keychar) > -1))
                    return true;

                return false;
            }

            function roundField(myfield){
                if (myfield.value.length == 0)
                    return;
                if (myfield.value.split("-").length > 2){
                    myfield.value = "-" + myfield.value.split("-")[1];
                }
                if (myfield.value.split(".").length > 2)
                    myfield.value = myfield.value.split(".")[0]+"."+myfield.value.split(".")[1].replace(".","");
                var res = Number(myfield.value).toFixed(2);
                myfield.value=res;
            }
        </script>
    </body>
</html>