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

	public static function update(){


	}

}

 ?>