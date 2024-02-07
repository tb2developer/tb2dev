<?php 
include 'check_sess_1.php';
include '../db.php';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"
    xml:lang="en"
    lang="en"
    dir="ltr">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="A front-end template that helps you build fast, modern mobile web apps.">
    <meta name="viewport" content="width=device-width, initial-scale=1">
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
	
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>


	<script src="scripts.js"></script>
    <link rel="shortcut icon" href="images/favicon.png">

    <!-- SEO: If your mobile URL is different from the desktop URL, add a canonical link to the desktop page https://developers.google.com/webmasters/smartphone-sites/feature-phones -->
    <!--
    <link rel="canonical" href="http://www.example.com/">
    -->

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:regular,bold,italic,thin,light,bolditalic,black,medium&amp;lang=en">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="https://code.getmdl.io/1.1.2/material.cyan-light_blue.min.css">
    <link rel="stylesheet" href="styles.css">
	<link rel="stylesheet" href="normalize.css">
    <style>
    #view-source {
      position: fixed;
      display: block;
      right: 0;
      bottom: 0;
      margin-right: 40px;
      margin-bottom: 40px;
      z-index: 900;
    }
    </style>
  </head>
  <body>
 <div class="mdl-layout mdl-js-layout mdl-layout--fixed-header">
  <header class="mdl-layout__header"  style="background: rgb(55, 71, 79); color: white;">
    <div class="mdl-layout__header-row">
      <!-- Title -->
      <span class="mdl-layout-title"><a href='index.php' style='text-decoration: none'>Admin Panel</a></span>
      <!-- Add spacer, to align navigation to the right -->
      <div class="mdl-layout-spacer"></div>
      <!-- Navigation. We hide it in small screens. -->
      <nav class="mdl-navigation mdl-layout--large-screen-only">
<!--
	      <a style="color: white;" class="mdl-navigation__link" href="?command=filesize"><i class="mdl-color-text--blue-grey-400 material-icons" role="presentation">people</i>APK Info</a>
-->
          
          <a style="color: white;" class="mdl-navigation__link" href="../stats/"><i class="mdl-color-text--blue-grey-400 material-icons" role="presentation">info</i>&nbsp;Stats</a>
          <a style="color: white;" class="mdl-navigation__link" href="?command=tasks"><i class="mdl-color-text--blue-grey-400 material-icons" role="presentation">today</i>&nbsp;Tasks</a>
          <a style="color: white;" class="mdl-navigation__link" href="?command=botlist"><i class="mdl-color-text--blue-grey-400 material-icons" role="presentation">people</i>&nbsp;Bots</a>
          <a style="color: white;" class="mdl-navigation__link" onclick="logout();"><i class="mdl-color-text--blue-grey-400 material-icons" role="presentation">launch</i>&nbsp;Logout</a>
          
         
          
<!--
 <button class="mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--icon" id="hdrbtn">
            <i class="material-icons">more_vert</i>
          </button>
          <ul class="mdl-menu mdl-js-menu mdl-js-ripple-effect mdl-menu--bottom-right" for="hdrbtn">
            <a onclick="logout();"><li class="mdl-menu__item">Logout</li></a>
     
          </ul>
-->
      </nav>
    </div>
  </header>
<!--
  <div class="mdl-layout__drawer">
    <span class="mdl-layout-title">Admin Panel</span>
    <nav class="mdl-navigation" style="text-align: left; ">
          <a  class="mdl-navigation__link" href="?command=bots_online"><i class="mdl-color-text--blue-grey-400 material-icons" role="presentation">place</i> Bots Online</a>
          <a  class="mdl-navigation__link" href="?command=tasks"><i class="mdl-color-text--blue-grey-400 material-icons" role="presentation">today</i> Tasks</a>
          <a  class="mdl-navigation__link" href="?command=botlist"><i class="mdl-color-text--blue-grey-400 material-icons" role="presentation">people</i> All Bots</a>

    </nav>
  </div>
-->
  
  <main class="mdl-layout__content">
       
<?php 
$cmd = (isset($_GET['command']))? $_GET['command'] : 'botlist';

//~ if ($cmd == 'bots_online'){
	//~ 
	//~ echo('<div id="task"  class="mdl-grid demo-content">');
	//~ include "botsonline.php";
	//~ echo('</div>');
//~ } else if ($cmd == 'tasks'){
if ($cmd == 'tasks'){
	
	echo('<div id="task"  class="mdl-grid demo-content">');
	include "tasks.php";
	echo('</div>');
//~ } else if ($cmd == 'filesize'){
	//~ 
	//~ echo('<div id="botlist"  class="mdl-grid demo-content">');
	//~ include "filesize.php";
	//~ echo('</div>');
}else if ($cmd == 'botlist'){
	
	echo('<div id="botlist"  class="mdl-grid demo-content">');
	include "botlist.php";
	echo('</div>');
}
?>
      </main>

    </div>
      
     
    <script src="material.min.js"></script>
  </body>
</html>
