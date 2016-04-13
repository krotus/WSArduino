<?php

require("utilities/Database.php");
require("views/ViewJson.php");
require("models/Order.php");

$db = new Database();

//print $db->errorCode();


// Obtenim el recurs a partir de la petició
$request = explode("/", $_GET['PATH_INFO']);
$resource = array_shift($request);
$existing_resources = array('orders','points','process','robots','status_order','status_robot','task','teams','workers');

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
		if ($resource == "orders") {
			echo $view->prints(Order::get($request));
		}else if($resource == "orders"){
			echo $view->prints(Worker::get($request));
		}
	break;
	case 'post':
		if ($resource == "orders") {
			$view->prints(Order::post($request));
		}else if($resource == "orders"){
			$view->prints(Worker::post($request));
		}
		break;
	case 'put':
		break;
	case 'delete':
		break;
	default:
		// mètode erroni
}