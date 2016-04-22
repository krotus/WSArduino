<?php 

require_once("utilities/ExceptionApi.php");

class Order{
	//Camps de la taula "orders"
	const TABLE_NAME = "orders";
	const ID = "id";
	const CODE = "code";
	const DESCRIPTION = "description";
	const PRIORITY = "priority";
	const DATE = "date";
	const QUANTITY = "quantity";
	const ID_STATUS_ORDER = "id_status_order";
	const ID_ROBOT = "id_robot";
	const ID_PROCESS = "id_process";

	//CODES
	const STATE_SUCCESS = 200;
	const STATE_CREATE_SUCCESS = 201;
	const STATE_URL_INCORRECT = 404;
	const STATE_CREATE_FAIL = 400;
	const STATE_FAIL_UNKNOWN = 500;
	const STATE_ERROR_DB = 500;

	//HTTP REQUEST GET
	public static function get($request){
		if($request[0] == 'getAll'){
			return self::getAll();
		}else if($request[0] == 'getById'){
			return self::getById($request[1]);
		}else{
			throw new ExceptionApi(self::STATE_URL_INCORRECT, "Url mal formada", 400);
		}
	}

	//HTTP REQUEST GET
	public static function post($request){
		if($request[0] == 'create'){
			return self::create();
		}else{
			throw new ExceptionApi(self::STATE_URL_INCORRECT, "Url mal formada", 400);
		}
	}

	//METHOD CREATE CALLS INSERT FUNCTION
	public static function create(){
		$body = file_get_contents('php://input');
		$order = json_decode($body);
		//validar camps
		//crear usuari
		$response = self::insert($order);
		switch($response){
			case self::STATE_CREATE_SUCCESS:
				http_response_code(200);
				return
					[
						"state" => 200,
						"message" => utf8_encode("Register success.")
					];
				break;
			case 400:
				throw new ExceptionApi(self::STATE_CREATE_FAIL, "Ha sorgit un error");
				break;
			default:
				throw new ExceptionApi(self::STATE_FAIL_UNKNOWN, "Ha sorgit un algo malament", 400);

		}
	}

	public static function insert($order){
		$code = $order->code;
		$description = $order->description;
		$priority = $order->priority;
		$quantity = $order->quantity;
		$idStatusOrder = $order->id_status_order;
		$idRobot = $order->id_robot;
		$idProcess = $order->id_process;
		try{
			$db = new Database();
			$sql = "INSERT INTO " . self::TABLE_NAME . " ( " .
				self::CODE . "," .
				self::DESCRIPTION . "," .
				self::PRIORITY . "," .
				self::QUANTITY . "," .
				self::ID_STATUS_ORDER . "," .
				self::ID_ROBOT . "," .
				self::ID_PROCESS . ")" .
				" VALUES(:code,:description,:priority,:quantity,:id_status_order,:id_robot,:id_process)";

			$stmt = $db->prepare($sql);
			$stmt->bindParam(":code", $code);
			$stmt->bindParam(":description", $description);
			$stmt->bindParam(":priority", $priority);
			$stmt->bindParam(":quantity", $quantity);
			$stmt->bindParam(":id_status_order", $idStatusOrder);
			$stmt->bindParam(":id_robot", $idRobot);
			$stmt->bindParam(":id_process", $idProcess);

			$result = $stmt->execute();

			if($result){
				return self::STATE_CREATE_SUCCESS;
			}else{
				return self::STATE_CREATE_FAIL;
			}
		}catch(PDOException $e){
			throw new ExceptionApi(self::STATE_ERROR_DB, $e->getMessage());
		}
	}

	public static function delete(){}

	public static function update(){}

	public static function getById($id){
		try{
			$db = new Database();
			$sql = "SELECT * FROM " . self::TABLE_NAME . " WHERE id = :id";
			$stmt = $db->prepare($sql);
			$stmt->execute(array(':id' => $id));
			$ordre = $stmt->fetch(PDO::FETCH_ASSOC);
			if($ordre){
				//canvia id de status order
				include_once("StatusOrder.php");
				$statusOrder = StatusOrder::getById($ordre['id_status_order']);
				$ordre['id_status_order'] = $statusOrder[0]['description'];
				//canvia id de robot
				include_once("Robot.php");
				$robot = Robot::getById($ordre['id_robot']);
				$ordre['id_robot'] = $robot[0]['name'];

				//agafa els punts
				$id_process = $ordre['id_process'];
				include_once("Process.php");
				$process_class = new Process();
				$process = $process_class::getById($id_process);
				$ordre['id_process'] = $process['data']['description'];
				if($process){
					include_once("Point.php");
					$point_class = new Point();
					$points = $point_class::getAllByIdProcess(1);
					if($points){
						http_response_code(200);
						return [
							"state" => self::STATE_SUCCESS,
							"order" => $ordre,
							"points"=> $points
						];
					}else{
						throw new ExceptionApi(self::STATE_ERROR, "S'ha produït un error al obtindre els punts");
					}
				}else{
					throw new ExceptionApi(self::STATE_ERROR_DB, "S'ha produït un error al obtindre el process");
				}
			}else{
				throw new ExceptionApi(self::STATE_ERROR_DB, "S'ha produït un error al obtindre la ordre");
			}
		}catch(PDOException $e){
			throw new ExceptionApi(self::STATE_ERROR_DB, $e->getMessage());
		}

	}

	public static function getAll(){
		try{
			$db = new Database();
			$sql = "SELECT * FROM ".self::TABLE_NAME;
			$stmt = $db->prepare($sql);
			$result = $stmt->execute();

			if($result){
				http_response_code(200);
				return [
					"state" => self::STATE_SUCCESS,
					"data"	=> $stmt->fetchAll(PDO::FETCH_ASSOC)
				];
			}else{
				throw new ExceptionApi(self::STATE_ERROR, "S'ha produït un error");
			}
		}catch(PDOException $e){
			throw new ExceptionApi(self::STATE_ERROR_DB, $e->getMessage());
		}
	}

	

}

?>