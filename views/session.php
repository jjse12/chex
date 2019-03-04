<?php 
	session_start();
	if (isset($_POST["vaciar"])){
		$_SESSION = array();
	}
	else{
		if (isset($_POST["user_admin"]))
			$_SESSION["user_admin"] = $_POST["user_admin"];
		if (isset($_POST["user_login_status"]))
			$_SESSION["user_login_status"] = $_POST["user_login_status"];
        if (isset($_POST["username"]))
            $_SESSION["username"] = $_POST["username"];
	}
	echo "status=".$_SESSION['user_login_status']." , admin=". $_SESSION['user_admin']; 
?>