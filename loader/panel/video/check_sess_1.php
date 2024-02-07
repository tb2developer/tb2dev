<?php
@session_start();
$key = 'gxPDE8AeX23432432#@$#@$l9SpqUWGMn';

if (!isset($_SESSION['session']) || $_SESSION['session'] != $key){
	
	//~ header("HTTP/1.0 404 Not Found");
	header("Location: login.php");
	exit;
}

?>
