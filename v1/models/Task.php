<?php 

require_once("utilities/ExceptionApi.php");

class Task {
	//Camps de la taula "tasks"
	const TABLE_NAME = "tasks";
	const ID = "id";
	const ID_TEAM = "id_team";
	const ID_ORDER = "id_order";
	const ID_WORKER = "id_worker";
	const DATA_ASSIGNATION = "data_assignation";
	const DATA_COMPLETION = "data_completion";
	const JUSTIFICATION = "justification";

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
		$task = json_decode($body);
		//validar camps
		//crear usuari
		$response = self::insert($task);
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

	//TODO
	public static function insert($task){
		$code = $robot->code;
		$name = $robot->name;
		$ipAddress = $robot->ip_address;
		$latitude = $robot->latitude;
		$longitude = $robot->longitude;
		$idCurrentStatus = $robot->id_current_status;
		try{
			$db = new Database();
			$sql = "INSERT INTO " . self::TABLE_NAME . " ( " .
				self::CODE . "," .
				self::NAME . "," .
				self::IP_ADDRESS . "," .
				self::LATITUDE . "," .
				self::LONGITUDE . "," .
				self::ID_CURRENT_STATUS . ")" .
				" VALUES(:code,:name,:ip_address,:latitude,:longitude,:id_current_status)";

			$stmt = $db->prepare($sql);
			$stmt->bindParam(":code", $code);
			$stmt->bindParam(":name", $name);
			$stmt->bindParam(":ip_address", $ipAddress);
			$stmt->bindParam(":latitude", $latitude);
			$stmt->bindParam(":longitude", $longitude);
			$stmt->bindParam(":id_current_status", $idCurrentStatus);

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
			$sql = "SELECT * FROM " . self::TABLE_NAME . " WHERE ". self::ID ." = :id";
			$stmt = $db->prepare($sql);
			$stmt->execute(array(':id' => $id));
			$task = $stmt->fetchAll(PDO::FETCH_ASSOC);
			if($task){
				http_response_code(200);
				return $task;
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