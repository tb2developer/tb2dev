<?php
function check_imei ($imei) {
	if (!preg_match('/^[0-9]{15}$/', $imei)) return false;
	$sum = 0;
	for ($i = 0; $i < 14; $i++)
	{
		$num = $imei[$i];
		if (($i % 2) != 0)
		{
			$num = $imei[$i] * 2;
			if ($num > 9)
			{
				$num = (string) $num;
				$num = $num[0] + $num[1];
			}
		}
		$sum += $num;
	}
	if ((($sum + $imei[14]) % 10) != 0) return false;
	return true;
}

function check_country ($country) {
	if (!preg_match('/^[A-Z]{2}$/iu', $country)) return false;
	
	return true;
}

function check_int ($task_id) {
	if (!preg_match('/^([0-9]+)$/iu', $task_id)) return false;
	
	return true;
}

function check_status ($status) {
	if (!preg_match('/^(1|2)$/iu', $status)) return false;
	
	return true;
}

?>