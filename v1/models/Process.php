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
			return parent::deleteById($request[1]);
		}else{
			throw new ExceptionApi(parent::STATE_URL_INCORRECT, "Url mal formada", 400);
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


	public static function update(){}



}

?>