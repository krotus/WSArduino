<?php 

require_once("utilities/ExceptionApi.php");

class Worker {
	//Camps de la taula "workers"
	const TABLE_NAME = "workers";
	const ID = "id";
	const NIF = "nif";
	const NAME = "name";
	const SURNAME = "surname";
	const MOBILE = "mobile";
	const TELEPHONE = "telephone";
	const CATEGORY = "category";
	const ID_TEAM = "id_team";

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
		$worker = json_decode($body);
		//validar camps
		//crear usuari
		$response = self::insert($worker);
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

	public static function insert($worker){
		$NIF = $worker->NIF;
		$name = $worker->name;
		$surname = $worker->surname;
		$mobile = $worker->mobile;
		$telephone = $worker->telephone;
		$category = $worker->category;
		$idTeam = $worker->id_team;
		try{
			$db = new Database();
			$sql = "INSERT INTO " . self::TABLE_NAME . " ( " .
				self::NIF . "," .
				self::NAME . "," .
				self::SURNAME . "," .
				self::MOBILE . "," .
				self::TELEPHONE . "," .
				self::CATEGORY . "," .
				self::ID_TEAM . ")" .
				" VALUES(:NIF,:name,:surname,:mobile,:telephone,:category,:id_team)";

			$stmt = $db->prepare($sql);
			$stmt->bindParam(":NIF", $NIF);
			$stmt->bindParam(":name", $name);
			$stmt->bindParam(":surname", $surname);
			$stmt->bindParam(":mobile", $mobile);
			$stmt->bindParam(":telephone", $telephone);
			$stmt->bindParam(":category", $category);
			$stmt->bindParam(":id_team", $idTeam);

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
			$worker = $stmt->fetchAll(PDO::FETCH_ASSOC);
			if($worker){
				http_response_code(200);
				return $worker;
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