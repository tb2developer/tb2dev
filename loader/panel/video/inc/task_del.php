<?php
include '../../db.php';
include '../check_sess_1.php';

if(!isset($_POST['number']))
	die;
	
$number = (int)$_POST['number'];

$query = "DELETE FROM `task` WHERE `number` ='{$number}'";
$result1 = $mysqli->query($query);

$query = "DELETE FROM `statistics` WHERE `task_id` ='{$number}'";
$result2 = $mysqli->query($query);
