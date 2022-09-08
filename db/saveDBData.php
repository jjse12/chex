<?php
    require_once("db_vars.php");
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $sql  = "UPDATE cliente SET ";
    $sql .= "cid = '" . $_POST['userid'] . "', ";
    $sql .= "nombre = '" . $_POST['fname'] . "', ";
    $sql .= "apellido = '" . $_POST['lname'] . "', ";
    $sql .= "email = '" . $_POST['email'] . "', ";
    $sql .= "celular = '" . $_POST['mobile'] . "', ";
    $sql .= "telefono_secundario = '" . $_POST['phone'] . "', ";
    $sql .= "direccion = '" . $_POST['address'] . "', ";
    $sql .= "genero = '" . $_POST['gender'] . "', ";
    $sql .= "cumple = '" . $_POST['bday'] . "', ";
    $sql .= "comentario = '" . $_POST['note'] . "' ";
    $sql .= "WHERE id = '" . $_POST['useridnumber'] . "';";

    $sql_query = $conn->query($sql);
    echo $sql;
?>