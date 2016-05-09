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
				return self::STATE_CREATE_SUCCESS;
			}else{
				return self::STATE_CREATE_FAIL;
			}
		}catch(PDOException $e){
			throw new ExceptionApi(self::STATE_ERROR_DB, $e->getMessage());
		}
	}


	public static function update(){}

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
			throw new ExceptionApi(self::STATE_ERROR_DB, $e->getMessage());
		}
	}

}

?>