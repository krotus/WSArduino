<?php 

require_once("utilities/ExceptionApi.php");
require_once("AbstractDAO.php");

class Worker extends AbstractDAO {
	//Camps de la taula "workers"
	const TABLE_NAME = "workers";
	const ID = "id";
	const USERNAME = "username";
	const PASSWORD = "password";
	const NIF = "nif";
	const NAME = "name";
	const SURNAME = "surname";
	const MOBILE = "mobile";
	const TELEPHONE = "telephone";
	const CATEGORY = "category";
	const ID_TEAM = "id_team";
	const IS_ADMIN = "is_admin";

	//HTTP REQUEST GET
	public static function get($request){
		if($request[0] == 'getAll'){
			return parent::getAll();
		}else if($request[0] == 'getById'){
			return parent::getById($request[1]);
		}else{
			throw new ExceptionApi(parent::STATE_URL_INCORRECT, "Url mal formada", 400);
		}
	}


	//HTTP REQUEST POST
	public static function post($request){
		if($request[0] == 'create'){
			return parent::create();
		}else{
			throw new ExceptionApi(self::STATE_URL_INCORRECT, "Url mal formada", 400);
		}
	}

	//HTTP REQUEST DELETE
	public static function delete($request){
		if($request[0] == 'deleteById'){
			return parent::deleteById($request[1]);
		}else{
			throw new ExceptionApi(self::STATE_URL_INCORRECT, "Url mal formada", 400);
		}
	}

	public static function insert($worker){
		$username = $worker->username;
		//tracte sha1 aqui o la plataforma app
		$password = $worker->password;
		$NIF = $worker->NIF;
		$name = $worker->name;
		$surname = $worker->surname;
		$mobile = $worker->mobile;
		$telephone = $worker->telephone;
		$category = $worker->category;
		$idTeam = $worker->id_team;
		$isAdmin = $worker->is_admin;
		try{
			$db = new Database();
			$sql = "INSERT INTO " . self::TABLE_NAME . " ( " .
				self::USERNAME . "," .
				self::PASSWORD . "," .
				self::NIF . "," .
				self::NAME . "," .
				self::SURNAME . "," .
				self::MOBILE . "," .
				self::TELEPHONE . "," .
				self::CATEGORY . "," .
				self::ID_TEAM . "," .
				self::IS_ADMIN . ")" .
				" VALUES(:username,:password,:NIF,:name,:surname,:mobile,:telephone,:category,:id_team,:is_admin)";

			$stmt = $db->prepare($sql);
			$stmt->bindParam(":username", $username);
			$stmt->bindParam(":password", $password);
			$stmt->bindParam(":NIF", $NIF);
			$stmt->bindParam(":name", $name);
			$stmt->bindParam(":surname", $surname);
			$stmt->bindParam(":mobile", $mobile);
			$stmt->bindParam(":telephone", $telephone);
			$stmt->bindParam(":category", $category);
			$stmt->bindParam(":id_team", $idTeam);
			$stmt->bindParam(":is_admin", $isAdmin);

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

	public static function update(){}






}

 ?>