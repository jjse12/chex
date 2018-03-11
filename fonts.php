<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">
        <meta name="google-site-verification" content="Mv8LjSSeN7Y9foYL0jati7qHBMEBA_1QAEmBuwOlQr8" />
        <link rel="apple-touch-icon" sizes="57x57" href="/apple-touch-icon-57x57.png">
        <link rel="apple-touch-icon" sizes="60x60" href="/apple-touch-icon-60x60.png">
        <link rel="apple-touch-icon" sizes="72x72" href="/apple-touch-icon-72x72.png">
        <link rel="apple-touch-icon" sizes="76x76" href="/apple-touch-icon-76x76.png">
        <link rel="apple-touch-icon" sizes="114x114" href="/apple-touch-icon-114x114.png">
        <link rel="apple-touch-icon" sizes="120x120" href="/apple-touch-icon-120x120.png">
        <link rel="apple-touch-icon" sizes="144x144" href="/apple-touch-icon-144x144.png">
        <link rel="apple-touch-icon" sizes="152x152" href="/apple-touch-icon-152x152.png">
        <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon-180x180.png">
        <link rel="icon" type="image/png" href="/favicon-32x32.png" sizes="32x32">
        <link rel="icon" type="image/png" href="/android-chrome-192x192.png" sizes="192x192">
        <link rel="icon" type="image/png" href="/favicon-96x96.png" sizes="96x96">
        <link rel="icon" type="image/png" href="/favicon-16x16.png" sizes="16x16">
        <link rel="manifest" href="/manifest.json">
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
        <link rel="stylesheet" href="css/sansita-stylesheet.css" type="text/css" charset="utf-8" />        
        <!-- jQuery -->
        <link href="css/custom.css" rel="stylesheet">

        <script type="text/javascript" src="js/jquery.js"></script>
        <script type="text/javascript" src="js/jquery.session.js"></script>
        <script type="text/javascript" src="js/bootbox.js"></script>
        <script type="text/javascript" src="js/bootstrap.min.js"></script>

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