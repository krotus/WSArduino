<?php 

class Technician{
	//Dades de la taula "technicians"
	const TABLE_NAME = "technicians";
	const ID_TECHNICIAN = "id";
	const NAME = "name";
	const SURNAME = "surname";
	const STATE_SUCCESS = 200;
	const STATE_CREATE_SUCCESS = 201;
	const STATE_URL_INCORRECT = 404;
	const STATE_CREATE_FAIL = 400;
	const STATE_FAIL_UNKNOWN = 500;
	const STATE_ERROR_DB = 500;

	public static function get($request){
		if($request[0] == 'getAll'){
			return self::getAll();
		}else if($request[0] == 'login'){
			return self::login();
		}else{
			throw new ExceptionApi(self::STATE_URL_INCORRECT, "Url mal formada", 400);
		}
	}

	public static function post($request){
		if($request[0] == 'register'){
			return self::register();
		}else if($request[0] == 'login'){
			return self::login();
		}else{
			throw new ExceptionApi(self::STATE_URL_INCORRECT, "Url mal formada", 400);
		}
	}

	public static function register(){
		$body = file_get_contents('php://input');
		$user = json_decode($body);
		//validar camps
		//crear usuari
		$response = self::create($user);
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

	public static function create($user){
		$name = $user->name;
		$surname = $user->surname;
		try{
			$db = new Database();
			$sql = "INSERT INTO " . self::TABLE_NAME . " ( " .
				self::NAME . "," .
				self::SURNAME . ")" .
				" VALUES(?,?)";

			$stmt = $db->prepare($sql);
			$stmt->bindParam(1, $name);
			$stmt->bindParam(2, $surname);

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