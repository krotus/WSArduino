<?php

require("utilities/Database.php");
require("views/ViewJson.php");
require("models/Order.php");
require("models/Point.php");
require("models/Worker.php");
require("models/Process.php");
require("models/Robot.php");

$db = new Database();

//print $db->errorCode();


// Obtenim el recurs a partir de la petició
$request = explode("/", $_GET['PATH_INFO']);
$resource = array_shift($request);
$existing_resources = array('orders','points','processes','robots','status_order','status_robot','task','teams','workers');

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
		}else if($resource == "points"){
			echo $view->prints(Point::get($request));
		}else if($resource == "workers"){
			echo $view->prints(Worker::get($request));
		}else if($resource == "processes"){
			echo $view->prints(Process::get($request));
		}else if($resource == "robots"){
			echo $view->prints(Robot::get($request));
		}
	break;
	case 'post':
		if ($resource == "orders") {
			$view->prints(Order::post($request));
		}else if($resource == "points"){
			$view->prints(Point::post($request));
		}else if($resource == "workers"){
			$view->prints(Worker::post($request));
		}else if($resource == "processes"){
			$view->prints(Process::post($request));
		}else if($resource == "robots"){
			$view->prints(Robot::post($request));
		}
		break;
	case 'put':
		break;
	case 'delete':
		break;
	default:
		// mètode erroni
}