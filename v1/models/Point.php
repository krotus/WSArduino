<?php 

require_once("utilities/ExceptionApi.php");


class Point extends AbstractDAO {
	//Camps de la taula "points"
	const TABLE_NAME = "points";
	const ID = "id";
	const POS_X = "pos_x";
	const POS_Y = "pos_y";
	const POS_Z = "pox_z";
	const TWEEZER = "tweezer";
	const ID_PROCESS = "id_process";

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
			$idPoint = $request[1];
			$body = file_get_contents('php://input');
			$point = json_decode($body);
			if($route == "updateAll"){
				if(self::update($idPoint, $point) > 0){
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


	public static function insert($point){
		$posX = $point->posX;
		$posY = $point->posY;
		$posZ = $point->posZ;
		$tweezer = $point->tweezer;
		$idProcess = $point->process;

		try{
			$db = new Database();
			$sql = "INSERT INTO " . self::TABLE_NAME . " ( " .
				self::POS_X . "," .
				self::POS_Y . "," .
				self::POS_Z . "," .
				self::TWEEZER . "," .
				self::ID_PROCESS . ")" .
				" VALUES(:pos_x,:pos_y,:pox_z,:tweezer,:id_process)";

			$stmt = $db->prepare($sql);
			$stmt->bindParam(":pos_x", $posX);
			$stmt->bindParam(":pos_y", $posY);
			$stmt->bindParam(":pox_z", $posZ);
			$stmt->bindParam(":tweezer", $tweezer);
			$stmt->bindParam(":id_process", $idProcess);

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


	public static function update($id, $point){
		try{
			//creant la consulta UPDATE
			$db = new Database();
			$sql = "UPDATE " . self::TABLE_NAME . 
			" SET " . self::POS_X . " = :pos_x," .
			self::POS_Y . " = :pos_y," .
			self::POS_Z . " = :pox_z," .
			self::TWEEZER . " = :tweezer," .
			self::ID_PROCESS . " = :id_process " .
			"WHERE " . self::ID . " = :id";

			//prerarem la sentencia
			$stmt = $db->prepare($sql);
			$stmt->bindParam(":pos_x", $point->posX);
			$stmt->bindParam(":pos_y", $point->posY);
			$stmt->bindParam(":id_worker", $point->posZ);
			$stmt->bindParam(":tweezer", $point->tweezer);
			$stmt->bindParam(":id_process", $point->process);
			$stmt->bindParam(":id", $id);

			$stmt->execute();

			return $stmt->rowCount();
		}catch(PDOException $e){
			throw new ExceptionApi(parent::STATE_ERROR_DB, $e->getMessage());
		}
	}
	public static function getAllByIdProcess($idProcess){
		try{
			$db = new Database();
			$sql = "SELECT * FROM " . self::TABLE_NAME . " WHERE ". self::ID_PROCESS ." = :idProcess";
			$stmt = $db->prepare($sql);
			$stmt->execute(array(':idProcess' => $idProcess));
			$points = $stmt->fetchAll(PDO::FETCH_ASSOC);
			if($points){
				http_response_code(200);
				return $points;
			}
		}catch(PDOException $e){
			throw new ExceptionApi(parent::STATE_ERROR_DB, $e->getMessage());
		}
	}

	public static function getAllByIdProcessArduino($idProcess){
		try{
			$db = new Database();
			$sql = "SELECT pos_x,pos_y, pos_z, tweezer FROM " . self::TABLE_NAME . " WHERE ". self::ID_PROCESS ." = :idProcess";
			$stmt = $db->prepare($sql);
			$stmt->execute(array(':idProcess' => $idProcess));
			$points = $stmt->fetchAll(PDO::FETCH_ASSOC);
			if($points){
				http_response_code(200);
				return $points;
			}
		}catch(PDOException $e){
			throw new ExceptionApi(parent::STATE_ERROR_DB, $e->getMessage());
		}
	}

}

?>