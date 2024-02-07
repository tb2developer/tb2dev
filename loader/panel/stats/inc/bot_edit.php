<?php
include '../../db.php';
include '../check_sess_1.php';

if(!isset($_POST['todo']))
	die;

$todo = $_POST['todo'];

if ($todo == 'remove'){
	$id = (int)$_POST['id'];
	$query = "DELETE FROM `devices` WHERE `id` ='{$id}'";
	$result = $mysqli->query($query);

}else if ($todo == 'edit_get'){
	$id = (int)$_POST['id'];

	$query = "SELECT `id`, `uniqnum`, `model`, `country`, `group_id` FROM `devices` WHERE `id` = '{$id}'";	
	$result = $mysqli->query($query);
	$row = $result->fetch_array(MYSQL_ASSOC);
	$uniqnumber = $row['uniqnum'];

	$query = "SELECT * FROM `tasks_stat` WHERE `uniqnum` = '{$uniqnumber}' AND `run` ='1'";	
	$result = $mysqli->query($query);
	$row1 = $result->fetch_array(MYSQL_ASSOC);
	$number_installs = $result->num_rows;


	$query1 = "SELECT * FROM `tasks_stat` WHERE `uniqnum` = '{$uniqnumber}' AND `take` ='1'";
	$result1 = $mysqli->query($query1);
	$number_taken = $result1->num_rows;

	$answer = array(
		"number_installs" => $number_installs,
		"id" => $row['id'],
		"uniqnum" => $row['uniqnum'],
		"country" => $row['country'],
		"model" => $row['model'],
		"group_id" => $row['group_id'],
		"taken" => $number_taken
	);	

	echo json_encode($answer);
	
}else if ($todo == 'edit_save'){

	$id = (int)$_POST['id'];
	$group = mysql_escape_string($_POST['group']);

	$query = "UPDATE `devices` SET `group_id` = '{$group}' WHERE `id` = '{$id}'";
	$result = $mysqli->query($query);
}
