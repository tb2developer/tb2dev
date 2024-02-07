<?php
include '../../db.php';
include '../check_sess_1.php';

//~ $day = 0;
//~ $now = 0;
//~ $lasthour = 0;
//~ $week =0;
//~ $min =0;
//~ 
//~ for($i=0; $i<24; $i++){
	//~ $query = "SELECT * FROM `devices` WHERE TIMESTAMPDIFF(HOUR, `last_connect`, CURRENT_TIMESTAMP) = {$i}";
	//~ $result = $mysqli->query($query);
	//~ $day = $day + $result->num_rows;
//~ }
//~ 
//~ 
//~ for($i=0; $i<60; $i++){
	//~ $query = "SELECT * FROM `devices` WHERE TIMESTAMPDIFF(MINUTE, `last_connect`, CURRENT_TIMESTAMP) = {$i}";
	//~ $result = $mysqli->query($query);
	//~ $lasthour = $lasthour + $result->num_rows;
//~ }
//~ 
//~ 
//~ for($i=0; $i<7; $i++){
	//~ $query = "SELECT * FROM `devices` WHERE TIMESTAMPDIFF(DAY, `last_connect`, CURRENT_TIMESTAMP) = {$i}";
	//~ $result = $mysqli->query($query);
	//~ $week = $week + $result->num_rows;
//~ }


//~ for($i=0; $i<5; $i++){
	//~ $query = "SELECT * FROM `devices` WHERE TIMESTAMPDIFF(MINUTE, `last_connect`, CURRENT_TIMESTAMP) = {$i}";
	//~ $result = $mysqli->query($query);
	//~ $min =  $min + $result->num_rows;
//~ }


$result = $mysqli->query("SELECT count(DATE(`last_connect`)) as c FROM devices WHERE last_connect > (DATE_SUB(CURDATE(), INTERVAL 1 HOUR))");
$hour = $result->fetch_assoc()['c'];

$result = $mysqli->query("SELECT count(DATE(`last_connect`)) as c FROM devices WHERE last_connect > (DATE_SUB(CURDATE(), INTERVAL 1 DAY))");
$day = $result->fetch_assoc()['c'];

$result = $mysqli->query("SELECT count(DATE(`last_connect`)) as c FROM devices WHERE last_connect > (DATE_SUB(CURDATE(), INTERVAL 7 DAY))");
$week = $result->fetch_assoc()['c'];

$responce =	array(
			"hour" => $hour,
			"day" => $day,
			"week" => $week,
		);

echo json_encode($responce);


