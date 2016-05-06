<?php
/**
 *  Example API call
 */
header('Content-Type: text/html; charset=utf-8');


//id que agafara en GET i DELETE
$profileID = "1";
// urls de prova per als diferents metodes
$urlGet = "http://localhost/api.arduino.com/v1/orders/getById/".$profileID;
$urlPost = "http://localhost/api.arduino.com/v1/workers/create";
$urlDelete = "http://localhost/api.arduino.com/v1/workers/deleteById/".$profileID;

//dades de prova per POST
$data = array ("username"=> "tete",
	"password"=> "123456",
	"NIF"=> "00000000T",
	"name"=> "Chechu",
	"surname"=> "Color",
	"mobile"=> "633720214",
	"telephone"=> "938665411",
	"category"=> "Gerent",
	"id_team"=> "2",
	"is_admin"=> "0");


sendGetReq($urlGet);
//sendPostReq($urlPost,$data_string);
//sendDeleteReq($urlDelete);

function initRequestParameters($url)
{
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HEADER, false);
	return $ch;
}

function sendGetReq($url)
{
	$ch = initRequestParameters($url);
	// execute the request
	$output = curl_exec($ch);
	// output the profile information - includes the header
	echo ($output) . PHP_EOL;
	// close curl resource to free up system resources
	curl_close($ch);
}

//se li ha de passar com a parametre un json en format string
//ex:$data_string = json_encode($data); on data es un array.
function sendPostReq($url,$data)
{
	// set up the curl resource
	$ch = initRequestParameters($url);
	// s'ha de passar a string les dades abans de ser enviades en POST o PUT
	$data_string = json_encode($data); 
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
	    'Content-Type: application/json',                                                                                
	    'Content-Length: ' . strlen($data_string)                                                                       
	));       
	// execute the request
	$output = curl_exec($ch);
	// output the profile information - includes the header
	echo($output) . PHP_EOL;
	// close curl resource to free up system resources
	curl_close($ch);
}

function sendDeleteReq($url)
{
	// set up the curl resource
	$ch = initRequestParameters($url);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");

	// execute the request
	$output = curl_exec($ch);

	// output the profile information - includes the header
	echo($output) . PHP_EOL;

	// close curl resource to free up system resources
	curl_close($ch);
}




?>