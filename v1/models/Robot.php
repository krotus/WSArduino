<?php 

require_once("utilities/ExceptionApi.php");

class Robot {
	//Camps de la taula "robots"
	const TABLE_NAME = "robots";
	const ID = "id";
	const CODE = "code";
	const NAME = "name";
	const IP_ADDRESS = "ip_address";
	const LATITUDE = "latitude";
	const LONGITUDE = "longitude";
	const ID_CURRENT_STATUS = "id_current_status";

	//CODES
	const STATE_SUCCESS = 200;
	const STATE_CREATE_SUCCESS = 201;
	const STATE_URL_INCORRECT = 404;
	const STATE_CREATE_FAIL = 400;
	const STATE_ERROR_PARAMETERS = 422;
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
		}else if($request[0] == 'ip'){
			$idRobot = $request[1];
			$body = file_get_contents('php://input');
			$robot = json_decode($body);
			if(self::updateIP($idRobot, $robot) > 0){
				http_response_code(200);
				return [
					"state" => self::STATE_SUCCESS,
					"message" => "Actualització IP del robot existosa"
				];
			}else{
				throw new ExceptionApi(self::STATE_URL_INCORRECT, "El robot que intentes accedir no existeix",404);
			}
		}else{
			throw new ExceptionApi(self::STATE_URL_INCORRECT, "Url mal formada", 400);
		}
	}

	//HTTP REQUEST PUT
	public static function put($request){
		if(!empty($request[0]) && !empty($request[1])){
			$route = $request[0];
			$idRobot = $request[1];
			$body = file_get_contents('php://input');
			$robot = json_decode($body);
			if($route == "all"){
				if(self::update($idRobot, $robot) > 0){
					http_response_code(200);
					return [
						"state" => self::STATE_SUCCESS,
						"message" => "Actualització robot existosa"
					];
				}else{
					throw new ExceptionApi(self::STATE_URL_INCORRECT, "El robot que intentes accedir no existeix",404);
				}
			}else if($route == "ip"){
				if(self::updateIP($idRobot, $robot) > 0){
					http_response_code(200);
					return [
						"state" => self::STATE_SUCCESS,
						"message" => "Actualització IP del robot existosa"
					];
				}else{
					throw new ExceptionApi(self::STATE_URL_INCORRECT, "El robot que intentes accedir no existeix",404);
				}
			}else{
				throw new ExceptionApi(self::STATE_ERROR_PARAMETERS, "La ruta especificada no existeix",422);
			}
		}else{
			throw new ExceptionApi(self::STATE_ERROR_PARAMETERS, "Falta la ruta del robot", 422);
		}
	}

	//METHOD CREATE CALLS INSERT FUNCTION
	public static function create(){
		$body = file_get_contents('php://input');
		$robot = json_decode($body);
		//validar camps
		//crear usuari
		$response = self::insert($robot);
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

	public static function insert($robot){
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

	public static function update($id, $robot){
		try{
			//creant la consulta UPDATE
			$db = new Database();
			$sql = "UPDATE " . self::TABLE_NAME . 
			" SET " . self::CODE . " = :code," .
			self::NAME . " = :name," .
			self::IP_ADDRESS . " = :ip_address," .
			self::LATITUDE . " = :latitude," .
			self::LONGITUDE . " = :longitude," .
			self::ID_CURRENT_STATUS . " = :id_current_status " .
			"WHERE " . self::ID . " = :id";

			//prerarem la sentencia
			$stmt = $db->prepare($sql);
			$stmt->bindParam(":code", $robot->code);
			$stmt->bindParam(":name", $robot->name);
			$stmt->bindParam(":ip_address", $robot->ip_address);
			$stmt->bindParam(":latitude", $robot->latitude);
			$stmt->bindParam(":longitude", $robot->longitude);
			$stmt->bindParam(":id_current_status", $robot->id_current_status);
			$stmt->bindParam(":id", $id);

			$stmt->execute();

			return $stmt->rowCount();
		}catch(PDOException $e){
			throw new ExceptionApi(self::STATE_ERROR_DB, $e->getMessage());
		}
	}

	public static function updateIP($id, $robot){
		try{
			//creant la consulta UPDATE
			$db = new Database();
			$sql = "UPDATE " . self::TABLE_NAME . 
			" SET " . self::IP_ADDRESS . " = :ip_address " .
			"WHERE " . self::ID . " = :id";

			//prerarem la sentencia
			$stmt = $db->prepare($sql);
			$stmt->bindParam(":ip_address", $robot->ip_address);
			$stmt->bindParam(":id", $id);

			$stmt->execute();

			return $stmt->rowCount();
		}catch(PDOException $e){
			throw new ExceptionApi(self::STATE_ERROR_DB, $e->getMessage());
		}
	}

	public static function getById($id){
		try{
			$db = new Database();
			$sql = "SELECT * FROM " . self::TABLE_NAME . " WHERE ". self::ID ." = :id";
			$stmt = $db->prepare($sql);
			$stmt->execute(array(':id' => $id));
			$robots = $stmt->fetchAll(PDO::FETCH_ASSOC);
			if($robots){
				http_response_code(200);
				return $robots;
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