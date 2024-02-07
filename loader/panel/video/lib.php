<?php

function check_landing($url)
{
	$result = true;
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	//~ curl_setopt($ch, CURLOPT_POST, 1);
	//~ curl_setopt($ch, CURLOPT_POSTFIELDS, "");
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	//~ $header = array('Accept-Encoding: gzip, deflate','Content-Type: text/xml; charset=utf-8');
	//~ curl_setopt($ch, CURLOPT_HTTPHEADER, $header );
	curl_setopt($ch, CURLOPT_VERBOSE, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	$output = curl_exec($ch);
	if($output === false) // echo curl_error($ch);
		$result = false;

	if(curl_getinfo($ch, CURLINFO_HTTP_CODE) != 200)
		$result = false;
	
	if(!stristr(curl_getinfo($ch, CURLINFO_CONTENT_TYPE), 'text/html'))
		$result = false;
		
	curl_close($ch);

	return $result;
}

function download_apk($url)
{
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	//~ curl_setopt($ch, CURLOPT_POST, 1);
	//~ curl_setopt($ch, CURLOPT_POSTFIELDS, "");
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	//~ $header = array('Accept-Encoding: gzip, deflate','Content-Type: text/xml; charset=utf-8');
	//~ curl_setopt($ch, CURLOPT_HTTPHEADER, $header );
	curl_setopt($ch, CURLOPT_VERBOSE, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	$output = curl_exec($ch);
	if($output === false) // echo curl_error($ch);
		return false;
		
	curl_close($ch);
	
	file_put_contents('../tmp/apk', $output);

	$cmd = "../aapt/aapt dump badging ../tmp/apk | grep package:\ name";

	$code = exec($cmd, $responce);
	preg_match("#package: name='([^']+)#", $responce[0], $res);
	if($res)
		$package = $res[1];
	else 
		$package = false;

	unlink('../tmp/apk');
	
	$data = array('size'=>strlen($output), 'pack'=>$package);
	return $data;
}
