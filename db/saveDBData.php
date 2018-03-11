<?php
    require_once("db_vars.php");
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);    
    $sql  = "UPDATE users SET ";
    $sql .= "user_id = '" . $_POST['userid'] . "', ";
    $sql .= "user_fname = '" . $_POST['fname'] . "', ";
    $sql .= "user_lname = '" . $_POST['lname'] . "', ";
    $sql .= "user_email = '" . $_POST['email'] . "', ";
    $sql .= "user_mobile = '" . $_POST['mobile'] . "', ";
    $sql .= "user_phone = '" . $_POST['phone'] . "', ";
    $sql .= "user_address = '" . $_POST['address'] . "', ";
    $sql .= "user_gender = '" . $_POST['gender'] . "', ";
    $sql .= "user_bday = '" . $_POST['bday'] . "', ";
    $sql .= "user_note = '" . $_POST['note'] . "' ";
    $sql .= "WHERE user_number_id = '" . $_POST['useridnumber'] . "';";

    $sql_query = $conn->query($sql);
    echo $sql;
?>