<?php
include '../check_sess_1.php';
include '../../db.php';

if(!isset($_POST['number']))
	die;

$task = (int)$_POST['number'];

$query = "SELECT * FROM `tasks_stat`  WHERE `task_id` = '$task' AND `take` = '1'";
$result = $mysqli->query($query);
$take = $result->num_rows;

$query = "SELECT * FROM `tasks_stat`  WHERE `task_id` = '$task' AND `run` = '1'";
$result = $mysqli->query($query);
$run = $result->num_rows;

$responce =	array(
	"number" => $task,
	"run" => $run,
	"take" => $take
);

echo json_encode($responce);
