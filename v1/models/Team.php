<?php 
class Team  extends AbstractDAO {
	//Camps de la taula "Teams"
	const TABLE_NAME = "teams";
	const ID = "id";
	const CODE = "code";
	const NAME = "name";

	//HTTP REQUEST GET
	public static function get($request){
		if($request[0] == 'getAll'){
			return parent::getAll();
		}else if($request[0] == 'getById'){
			return parent::getById($request[1]);
		} else if ($request[0] == 'getAllTeamsAdmin') {
			return self::getAllTeamsAdmin();
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
			$idTeam = $request[1];
			echo $route;
			$body = file_get_contents('php://input');
			$team = json_decode($body);
			if($route == "updateAll"){
				if(self::update($idTeam, $team) > 0){
					http_response_code(200);
					return [
					"state" => parent::STATE_SUCCESS,
					"message" => "Actualització equip existosa"
					];
				}else{
					throw new ExceptionApi(parent::STATE_URL_INCORRECT, "El equip que intentes accedir no existeix",404);
				}
			}else{
				throw new ExceptionApi(parent::STATE_ERROR_PARAMETERS, "La ruta especificada no existeix",422);
			}
		}else{
			throw new ExceptionApi(parent::STATE_ERROR_PARAMETERS, "Falta la ruta del equip", 422);
		}
	}

	public static function getAllTeamsAdmin(){
		try{ 
			$db = new Database();
			$sql = "select " . self::TABLE_NAME . ".". self::ID .",
			" . self::TABLE_NAME . ".". self::CODE .",
			" . self::TABLE_NAME . "." . self::NAME .", 
			(select group_concat(workers.name, ' ', workers.surname) as worker 
			from workers where workers.id_team = " . self::TABLE_NAME . ".id) as workers
			from " . self::TABLE_NAME . "
			inner join workers on workers.id_team = ". self::TABLE_NAME ."." . self::ID . ";";
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

	public static function insert($team){
		$code = $team->code;
		$name = $team->name;
		try{
			$db = new Database();
			$sql = "INSERT INTO " . self::TABLE_NAME . " ( " .
			self::CODE . "," .
			self::NAME . ")" .
			" VALUES(:code,:name)";

			$stmt = $db->prepare($sql);
			$stmt->bindParam(":code", $code);
			$stmt->bindParam(":name", $name);


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

	public static function update($id,$team){
		try{
			//creant la consulta UPDATE
			$db = new Database();
			$sql = "UPDATE " . self::TABLE_NAME . 
			" SET " . self::CODE . " = :code," .
			self::NAME . " = :name " .
			"WHERE " . self::ID . " = :id";
			//prerarem la sentencia
			$stmt = $db->prepare($sql);
			$stmt->bindParam(":code", $team->code);
			$stmt->bindParam(":name", $team->name);
			$stmt->bindParam(":id", $id);

			$stmt->execute();

			return $stmt->rowCount();
		}catch(PDOException $e){
			throw new ExceptionApi(parent::STATE_ERROR_DB, $e->getMessage());
		}

	}

}

?>