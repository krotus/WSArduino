<?php

require("utilities/Database.php");
require("views/ViewJson.php");
require("models/Technician.php");
require("models/Points.php");

$db = new Database();

//print $db->errorCode();


// Obtenim el recurs a partir de la petició
$request = explode("/", $_GET['PATH_INFO']);
$resource = array_shift($request);
$existing_resources = array('process','technicians','points');

// COmprovem si existeix el recurs especificat
if(!in_array($resource, $existing_resources)){
	//Resposta d'error
}

$method = strtolower($_SERVER["REQUEST_METHOD"]);

/*echo "<pre>";
print_r($method);
echo "</pre>";
exit;*/

$view = new ViewJson();

set_exception_handler(function ($exception) use ($view) {
    $body = array(
        "sate" => $exception->state,
        "message" => $exception->getMessage()
    );
    if ($exception->getCode()) {
        $view->state = $exception->getCode();
    } else {
        $view->state = 500;
    }

    $view->prints($body);
}
);

switch ($method) {
	case 'get':
		if ($resource == "points") {
			echo $view->prints(Points::get($request));
		} else if ($resource == "technicians") {
			echo $view->prints(Technician::get($request));
		} else if ($resource == 'process') {
			echo $view->prints(Process::get($request));
		}
	break;
	case 'post':
		$view->prints($tech::post($request));
		break;
	case 'put':
		break;
	case 'delete':
		break;
	default:
		// mètode erroni
}