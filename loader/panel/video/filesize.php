<?php 
die('disabled');
if (isset($_FILES['fileToUpload']) && $_FILES['fileToUpload'] != ""){
$uploaddir = 'files_for_check_size/';

$uploadfile = $uploaddir . basename($_FILES['fileToUpload']['name']);

$size = $_FILES['fileToUpload']['size'];

$cmd = "aapt/aapt dump badging {$_FILES['fileToUpload']['tmp_name']} | grep package:\ name";

$code = exec($cmd, $responce);
preg_match("#package: name='([^']+)#", $responce[0], $res);
if($res)
	$package = $res[1];
else 
	$package = "PARSING ERROR";
	
echo "
<h4>APK Info
<table border='0' cellpadding='5'>
<tr>
	<td>Size in bytes</td>
	<td><input type='text' onclick='this.select()' value='{$size}' /></td>
</tr>
<tr>
	<td>Package name</td>
	<td><input type='text' onclick='this.select()' value='{$package}' /></td>
</tr>
</table>
</h4>";

die;
}
?>
<center>
<form action="" id="file_form" method="post" enctype="multipart/form-data" style="margin-top: 100px;">
    Select APK (max size 2,5MB):
    <input style="display: inline" type="file" name="fileToUpload" id="fileToUpload">
	
	
	<button onclick="$('#file_form').submit();" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect">
  Upload
</button>

</form>
</center>
