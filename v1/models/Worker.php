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
	const LANGUAGE = "id_language";

	//HTTP REQUEST GET
	public static function get($request){
		if($request[0] == 'getAll'){
			return parent::getAll();
		}else if($request[0] == 'getById'){
			return parent::getById($request[1]);
		} else if ($request[0] == 'getAllWorkersAdmin') {
			return self::getAllWorkersAdmin();
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
			$idWorker = $request[1];
			$body = file_get_contents('php://input');
			$worker = json_decode($body);
			if($route == "updateAll"){
				if(self::update($idWorker, $worker) > 0){
					http_response_code(200);
					return [
						"state" => parent::STATE_SUCCESS,
						"message" => "Actualització treballador existosa"
					];
				}else{
					throw new ExceptionApi(parent::STATE_URL_INCORRECT, "El treballador que intentes accedir no existeix",404);
				}
			}else{
				throw new ExceptionApi(parent::STATE_ERROR_PARAMETERS, "La ruta especificada no existeix",422);
			}
		}else{
			throw new ExceptionApi(parent::STATE_ERROR_PARAMETERS, "Falta la ruta del treballador", 422);
		}
	}

	public static function getAllWorkersAdmin(){
		try{ 
			$db = new Database();
			$sql = "select " . self::TABLE_NAME . ".". self::ID .",
			" . self::TABLE_NAME . ".". self::USERNAME .",
			" . self::TABLE_NAME . "." . self::NIF .", 
			" . self::TABLE_NAME . "." . self::NAME .", 
			" . self::TABLE_NAME . "." . self::SURNAME .", 
			" . self::TABLE_NAME . "." . self::MOBILE .", 
			" . self::TABLE_NAME . "." . self::TELEPHONE .", 
			" . self::TABLE_NAME . "." . self::CATEGORY . ", 
			" . self::TABLE_NAME . "." . self::IS_ADMIN . ", 
			teams.name as team_name 
			from " . self::TABLE_NAME . "
			 inner join teams on teams.id = ". self::TABLE_NAME . "." . self::ID_TEAM . ";";
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


	public static function update($id, $worker){
		try{


			//creant la consulta UPDATE
			$db = new Database();
			$sql = "UPDATE " . self::TABLE_NAME . 
			" SET " . self::USERNAME . " = :username," .
			self::PASSWORD . " = :password," .
			self::NIF . " = :nif," .
			self::NAME . " = :name," .
			self::SURNAME . " = :surname, " .
			self::MOBILE . " = :mobile," .
			self::TELEPHONE . " = :telephone, " .
			self::CATEGORY . " = :category, " .
			self::ID_TEAM . " = :id_team, " .
			self::IS_ADMIN . " = :is_admin, " .
			self::LANGUAGE . " = :language " .
			"WHERE " . self::ID . " = :id";
			Task::updateTaskTeam($worker->team,$id);
			//prerarem la sentencia
			$stmt = $db->prepare($sql);
			var_dump($sql);
			$stmt->bindParam(":username", $worker->username);
			$stmt->bindParam(":password", $worker->password);
			$stmt->bindParam(":nif", $worker->nif);
			$stmt->bindParam(":name", $worker->name);
			$stmt->bindParam(":surname", $worker->surname);
			$stmt->bindParam(":mobile", $worker->mobile);
			$stmt->bindParam(":telephone", $worker->telephone);
			$stmt->bindParam(":category", $worker->category);
			$stmt->bindParam(":id_team", $worker->team);
			$stmt->bindParam(":is_admin", $worker->isAdmin);
			$stmt->bindParam(":language", $worker->language);
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
				self::IS_ADMIN . "," .
				self::LANGUAGE . ")" .
				" VALUES(:username,:password,:NIF,:name,:surname,:mobile,:telephone,:category,:id_team,:is_admin,:language)";

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
			$language = 1; //es
			$stmt->bindParam(":is_admin", $language);

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