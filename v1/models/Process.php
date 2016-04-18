<?php 

require_once("utilities/ExceptionApi.php");

class Process{
	//Camps de la taula "processes"
	const TABLE_NAME = "processes";
	const ID = "id";
	const CODE = "code";
	const DESCRIPTION = "description";

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
		$process = json_decode($body);
		//validar camps
		//crear proces
		$response = self::insert($process);
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

	public static function insert($process){
		$code = $process->code;
		$description = $process->description;

		try{
			$db = new Database();
			$sql = "INSERT INTO " . self::TABLE_NAME . " ( " .
				self::CODE . "," .
				self::DESCRIPTION . ")" .
				" VALUES(:code,:description)";

			$stmt = $db->prepare($sql);
			$stmt->bindParam(":code", $code);
			$stmt->bindParam(":description", $description);;

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
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			if($row){	
				http_response_code(200);
				return [
					"state" => self::STATE_SUCCESS,
					"data" => $row
				];
			}else{
				throw new ExceptionApi(self::STATE_ERROR_DB, "S'ha produït un error al obtindre el procés.");
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