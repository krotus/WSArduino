<?php 

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
			return self::getById();
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

	public static function getById(){}

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