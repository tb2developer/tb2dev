
<?php
session_start();
$key = 'gxPDE8AeX23432432#@$#@$l9SpqUWGMn';


if(isset($_POST['type']) && $_POST['type'] == 'login'){

	$login = mysql_escape_string($_POST['login']);
	$password = md5(md5($_POST['password']));

	include '../db.php';

	$query = "SELECT * FROM `user` WHERE `login` = '{$login}' AND `password` = '{$password}'";
	$result = $mysqli->query($query);

	$users = $result->fetch_assoc();
	
	if($users){
		$_SESSION['session'] = $key;
		header('location: index.php');
	}else{
		header('location: login.php');
	}

}

if(isset($_POST['type']) && $_POST['type'] == 'logout'){
	$_SESSION['session'] = '';
}


else{
	echo ('
	<hrml>
	<head>
	<title>Admin Panel</title>

    <!-- Add to homescreen for Chrome on Android -->
    <meta name="mobile-web-app-capable" content="yes">
    <link rel="icon" sizes="192x192" href="images/android-desktop.png">

    <!-- Add to homescreen for Safari on iOS -->
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="apple-mobile-web-app-title" content="Material Design Lite">
    <link rel="apple-touch-icon-precomposed" href="images/ios-desktop.png">

    <!-- Tile icon for Win8 (144x144 + tile color) -->
    <meta name="msapplication-TileImage" content="images/touch/ms-touch-icon-144x144-precomposed.png">
    <meta name="msapplication-TileColor" content="#3372DF">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:regular,bold,italic,thin,light,bolditalic,black,medium&amp;lang=en">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="material.cyan-light_blue.min.css">
	<script src="jquery.min.js"></script>
	<script src="material.min.js"></script>

	<script src="scripts.js"></script>
    <link rel="shortcut icon" href="images/favicon.png">
	<style>
	.form_login{
		padding: 20px;
		margin-top: 100px;
		border-radius: 5px;
	}
	</style>
	</head>
	<body onload="$(\'#sample1\').focus()">
			<center><div class="mdl-cell mdl-cell--6-col form_login">
				<form action="login.php" method="post" id="login_form">
				<input type="hidden" name="type" value="login">
				
				
				<div class="mdl-textfield mdl-js-textfield">
					<input name="login" class="mdl-textfield__input" type="text" id="sample1">
					<label class="mdl-textfield__label" for="sample1">Login</label>
				</div>
				<br>
				<div class="mdl-textfield mdl-js-textfield">
					<input name="password" class="mdl-textfield__input" type="password" id="password">
					<label class="mdl-textfield__label" for="sample1">Password</label>
				</div>
				<br>
				
				
				
				<button onclick="$( "#login_form" ).submit();" class="mdl-button mdl-js-button mdl-button--raised">
					Login
				</button>
				
				</form>
			</div></center>
	</body>
				
	</hrml>			
	
	');
}
