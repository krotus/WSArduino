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
			throw new ExceptionApi(parent::STATE_URL_INCORRECT, "Url mal formada", 400);
		}
	}

	//HTTP REQUEST DELETE
	public static function delete($request){
		if($request[0] == 'deleteById'){
			return parent::deleteById($request[1]);
		}else{
			throw new ExceptionApi(parent::STATE_URL_INCORRECT, "Url mal formada", 400);
		}
	}

	//HTTP REQUEST PUT
	public static function put($request){
		if(!empty($request[0]) && !empty($request[1])){
			$route = $request[0];
			$idRobot = $request[1];
			$body = file_get_contents('php://input');
			$robot = json_decode($body);
			if($route == "updateAll"){
				if(self::update($idRobot, $robot) > 0){
					http_response_code(200);
					return [
						"state" => parent::STATE_SUCCESS,
						"message" => "Actualització robot existosa"
					];
				}else{
					throw new ExceptionApi(parent::STATE_URL_INCORRECT, "El robot que intentes accedir no existeix",404);
				}
			}else{
				throw new ExceptionApi(parent::STATE_ERROR_PARAMETERS, "La ruta especificada no existeix",422);
			}
		}else{
			throw new ExceptionApi(parent::STATE_ERROR_PARAMETERS, "Falta la ruta del robot", 422);
		}
	}


	public static function update($id, $worker){
		try{
			//creant la consulta UPDATE
			$db = new Database();
			$sql = "UPDATE " . self::TABLE_NAME . 
			" SET " . self::ID . " = :id," .
			self::USERNAME . " = :username," .
			self::PASSWORD . " = :password," .
			self::NIF . " = :nif," .
			self::NAME . " = :name," .
			self::SURNAME . " = :surname " .
			self::MOBILE . " = :mobile " .
			self::TELEPHONE . " = :telephone " .
			self::CATEGORY . " = :category " .
			self::ID_TEAM . " = :id_team " .
			self::IS_ADMIN . " = :is_admin " .
			"WHERE " . self::ID . " = :id";

			//prerarem la sentencia
			$stmt = $db->prepare($sql);
			$stmt->bindParam(":id", $worker->id);
			$stmt->bindParam(":username", $worker->username);
			$stmt->bindParam(":password", $worker->password);
			$stmt->bindParam(":nif", $worker->nif);
			$stmt->bindParam(":name", $worker->name);
			$stmt->bindParam(":mobile", $worker->mobile);
			$stmt->bindParam(":telephone", $worker->telephone);
			$stmt->bindParam(":category", $worker->category);
			$stmt->bindParam(":id_team", $worker->team);
			$stmt->bindParam(":is_admin", $worker->isAdmin);
			$stmt->bindParam(":id", $id);

			$stmt->execute();

			return $stmt->rowCount();
		}catch(PDOException $e){
			throw new ExceptionApi(parent::STATE_ERROR_DB, $e->getMessage());
		}
	}



	public static function insert($worker){
		$username = $worker->username;
		//tracte sha1 aqui o la plataforma app
		$password = $worker->password;
		$NIF = $worker->nif;
		$name = $worker->name;
		$surname = $worker->surname;
		$mobile = $worker->mobile;
		$telephone = $worker->telephone;
		$category = $worker->category;
		$idTeam = $worker->team;
		$isAdmin = $worker->isAdmin;
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
				return parent::STATE_CREATE_SUCCESS;
			}else{
				return parent::STATE_CREATE_FAIL;
			}
		}catch(PDOException $e){
			throw new ExceptionApi(parent::STATE_ERROR_DB, $e->getMessage());
		}
	}







}

 ?>