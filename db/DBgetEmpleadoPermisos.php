<?php
	require_once("db_vars.php");
    header('Content-Type: application/json;charset=utf-8');
	$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
   	$query = "SELECT user_admin FROM admintable WHERE user_name = '".$_POST["user"]."' AND user_password = '".$_POST["pass"]."' LIMIT 1 ";
    $result = $conn->query($query);
    if (isset($result) && $result !== false) {
        if($result->num_rows > 0)
        {
            $row = mysqli_fetch_row($result);
            echo json_encode([
                'success' => true,
                'message' => null,
                'data'    => (int) $row[0]
            ]);
        }
        else {
            echo json_encode([
                'success' => false,
                'message' => 'El usuario o la contraseña ingresados no son correctos.',
                'data'    => null
            ]);
        }
    }
    else {
        header("HTTP/1.1 500 Internal Server Error");
    }
?>