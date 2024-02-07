<?php
include '../../db.php';
include '../check_sess_1.php';

if(!isset($_POST['todo']))
	die;
	
$todo = $_POST['todo'];
$number = (int)$_POST['number'];

if ($todo == 'remove'){
	
	
	$query = "UPDATE `task` SET `active` = '2' WHERE `number` = '{$number}'";
	$result = $mysqli->query($query);
	
	if ($result){
		echo 'remove_ok';
	}else{
		echo 'remove_error';
	}

}else if ($todo == 'repeat'){
	$query= "SELECT * FROM `task` WHERE `active` = '1'";
	$result2 = $mysqli->query($query);
	
	if ($result2->num_rows > 0){ // if active task is present already
		echo 'repeat_error';
	}else if ($result2->num_rows == 0){
		$query = "UPDATE `task` SET `active` = '1' WHERE `number` = '{$number}'";
		$result = $mysqli->query($query);
		
		$query = "UPDATE `statistics` SET `run` = '0', `start` = '0'  WHERE `task_id` = '{$number}'";
		$result = $mysqli->query($query);
		
		if ($result){
			echo 'repeat_ok';
		}
		else{
			echo 'repeat_error';
		}
	}
}
