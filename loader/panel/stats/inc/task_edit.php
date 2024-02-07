<?php
include '../check_sess_1.php';
include '../../db.php';
include '../lib.php';

if(!isset($_POST['number']))
	die;
	
$task = (int)$_POST['number'];

if ($_POST['type'] == 'get') {
	$query= "SELECT * FROM `task` WHERE `number` = '{$task}'";
	$result = $mysqli->query($query);
	$row = $result->fetch_array(MYSQL_ASSOC);

	echo json_encode($row);

}else if ($_POST['type'] == 'save') {

	$real_task = trim(mysql_escape_string($_POST['task']));
	$number = (int)$_POST['number'];

	$url = trim(mysql_escape_string($_POST['url']));
	if(!$url)
	{
		$error = "apk URL is not valid";
		die('{"result":"0","error":"'.$error.'"}');
	}

	$apk_changed = '';
	
	if($url != $_POST['url_original'])
	{
		$apk_info = download_apk($url);
		if($apk_info === false)
		{
			$error = "Can't download apk by URL";
			die('{"result":"0","error":"'.$error.'"}');
		}
		
		$size = $apk_info['size'];
		$pack = $apk_info['pack'];
		
		//~ $pack = trim(mysql_escape_string($_POST['pack']));
		//~ if(!trim($pack) or !preg_match('#[a-z0-9\.]+#', $pack))
		//~ {
			//~ $error = "package name is incorrect";
			//~ die('{"result":"0","error":"'.$error.'"}');
		//~ }
		
		$apk_changed = <<<PHP
	`url` = '{$url}', 
	`size` = '{$size}', 
	`package` = '{$pack}', 
PHP;
	}

	$times = (int)$_POST['times'];
	if($times < 1)
	{
		$error = "Install times count can not be less than 1";
		die('{"result":"0","error":"'.$error.'"}');
	}
	
	$root = $_POST['root'];
	if(!in_array($root, array(1, 2, '')))
		die('{"result":"0","error":"Root value is incorrect"}');
		
	$model = trim(mysql_escape_string($_POST['model']));
	$osver = trim(mysql_escape_string($_POST['osver']));
	$country = trim(mysql_escape_string($_POST['country']));
		$country = str_replace(' ', '', $country);
	
	$lim = (trim($_POST['lim']))? (int) $_POST['lim'] : '';
	$landing = trim(mysql_escape_string($_POST['landing']));
	if($landing && !check_landing($landing))
	{
		$error = "Landing URL is inaccessible; Server should response with 200 OK & text/html";
		die('{"result":"0","error":"'.$error.'"}');
	}
	
	$packy = trim(mysql_escape_string($_POST['packy']));
		$packy = str_replace(' ', '', $packy);
	$packn = trim(mysql_escape_string($_POST['packn']));
		$packn = str_replace(' ', '', $packn);

	$device_clear = (int) $_POST['device_clear'];

	$sql = "UPDATE `task` SET 
	`number` = '{$number}', 
{$apk_changed}
	`times` = '{$times}', 
	`root` = '{$root}', 
	`model` = '{$model}', 
	`osver` = '{$osver}', 
	`country` = '{$country}', 
	`lim` = '{$lim}', 
	`landing` = '{$landing}', 
	`packy` = '{$packy}', 
	`packn` = '{$packn}', 
	`device_clear` = '{$device_clear}' 
	WHERE `number` = '{$real_task}'";
	file_put_contents('/var/www/tmp/X', $sql);
	//~ file_put_contents('/var/www/tmp/X', $_POST['device_clear']);
	$result1 = $mysqli->query($sql);
	
	$sql = "UPDATE `tasks_stat` SET `task_id` = '{$number}' WHERE `task_id` = '{$real_task}'";
	$result2 = $mysqli->query($sql);
	
	if($result1 and $result2){
		die('{"result":"1","error":""}');
	}else{
		die('{"result":"0","error":"Save error"}');
	}

}









