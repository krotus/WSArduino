<?php 

require_once("utilities/ExceptionApi.php");


class Process  extends AbstractDAO {
	//Camps de la taula "processes"
	const TABLE_NAME = "processes";
	const ID = "id";
	const CODE = "code";
	const DESCRIPTION = "description";


	//HTTP REQUEST GET
	public static function get($request){
		if($request[0] == 'getAll'){
			return parent::getAll();
		}else if($request[0] == 'getById'){
			return self::getById($request[1]);
		}else if ($request[0] == 'getAllProcessesAdmin') {
			return self::getAllProcessesAdmin();
		}else{
			throw new ExceptionApi(parent::STATE_URL_INCORRECT, "Url mal formada", 400);
		}
	}

	//HTTP REQUEST GET
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
			return self::deleteByIdCascade($request[1]);
		}else{
			throw new ExceptionApi(parent::STATE_URL_INCORRECT, "Url mal formada", 400);
		}
	}

	//HTTP REQUEST PUT
	public static function put($request){
		if(!empty($request[0]) && !empty($request[1])){
			$route = $request[0];
			$idProcess = $request[1];
			$body = file_get_contents('php://input');
			$process = json_decode($body);
			if($route == "updateAll"){
				if(self::update($idProcess, $process) > 0){
					http_response_code(200);
					return [
						"state" => parent::STATE_SUCCESS,
						"message" => "Actualització Process existosa"
					];
				}else{
					throw new ExceptionApi(parent::STATE_URL_INCORRECT, "El Process que intentes accedir no existeix",404);
				}
			}else{
				throw new ExceptionApi(parent::STATE_ERROR_PARAMETERS, "La ruta especificada no existeix",422);
			}
		}else{
			throw new ExceptionApi(parent::STATE_ERROR_PARAMETERS, "Falta la ruta del Process", 422);
		}
	}

	public static function getAllProcessesAdmin(){
		try{ 
			$db = new Database();
			$sql = "select " . self::TABLE_NAME . ".". self::ID .",
			" . self::TABLE_NAME . ".". self::CODE .",
			" . self::TABLE_NAME . "." . self::DESCRIPTION .", 
			points.pos_x, 
			points.pos_y, 
			points.pos_z, 
			case 
				when points.tweezer = 0 then 'tancada' 
    			when points.tweezer = 1 then 'oberta' 
    		end as pinca 
			from " . self::TABLE_NAME . "
			 inner join points on points.id_process = ". self::TABLE_NAME . "." . self::ID . ";";
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
				return parent::STATE_CREATE_SUCCESS;
			}else{
				return parent::STATE_CREATE_FAIL;
			}
		}catch(PDOException $e){
			throw new ExceptionApi(parent::STATE_ERROR_DB, $e->getMessage());
		}
	}


	public static function update($id, $process){
			try{
				//creant la consulta UPDATE
				$db = new Database();
				$sql = "UPDATE " . self::TABLE_NAME . 
				" SET " . self::CODE . " = :code," . 
				self::DESCRIPTION . " = :description" .
				" WHERE " . self::ID . " = :id";

				//prerarem la sentencia
				$stmt = $db->prepare($sql);
				$stmt->bindParam(":code", $process->code);
				$stmt->bindParam(":description", $process->description);
				$stmt->bindParam(":id", $id);

				$stmt->execute();

				return $stmt->rowCount();
			}catch(PDOException $e){
				throw new ExceptionApi(parent::STATE_ERROR_DB, $e->getMessage());
			}
	}

	public static function deleteByIdCascade($id){
		try{
			$db = new Database();
			$idPoints = Point::getAllByIdProcess($id);
			for ($i=0; $i < count($idPoints); $i++) { 
				Point::deleteById($idPoints[$i]["id"]);
			}
			$sql = "DELETE FROM " . self::TABLE_NAME . " WHERE id = :id";
			$stmt = $db->prepare($sql);
			$result = $stmt->execute(array(':id' => $id));
			if($result){
				return self::STATE_CREATE_SUCCESS;
			}else{
				return self::STATE_CREATE_FAIL;
			}
		}catch(PDOException $e){
			throw new ExceptionApi(self::STATE_ERROR_DB, $e->getMessage());
		}
	}


}

?>