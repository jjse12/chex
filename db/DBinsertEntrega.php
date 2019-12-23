<?php
require_once("db_vars.php");
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$query = "INSERT INTO entrega VALUES ('{$_POST['d']}', '{$_POST['p']}', '{$_POST['ui']}', '{$_POST['un']}', " .
    "'{$_POST['to']}', '{$_POST['lbs']}', NULL, NULL, '{$_POST['m']}', {$_POST['r']}, " .
    "{$_POST['des']}, NULL, NULL, '{$_POST['pl']}', '{$_POST['table']}');";
if ($conn->query($query)){
    echo 1;
}
else{
    echo "¡ERROR! - $conn->error";
}