<?php 
/**
* 
*/
abstract class AbstractDAO
{

	//CODES
	const STATE_SUCCESS = 200;
	const STATE_CREATE_SUCCESS = 201;
	const STATE_URL_INCORRECT = 404;
	const STATE_CREATE_FAIL = 400;
	const STATE_FAIL_UNKNOWN = 500;
	const STATE_ERROR_DB = 500;

	public static function deleteById($id){
		try{
			$db = new Database();
			$sql = "DELETE FROM " . static::TABLE_NAME . " WHERE id = :id";
			$stmt = $db->prepare($sql);
			$result = $stmt->execute(array(':id' => $id));
			if($result){
				return parent::STATE_CREATE_SUCCESS;
			}else{
				return parent::STATE_CREATE_FAIL;
			}
		}catch(PDOException $e){
			throw new ExceptionApi(parent::STATE_ERROR_DB, $e->getMessage());
		}
	}

		public static function getById($id){
		try{
			$db = new Database();
			$sql = "SELECT * FROM " . static::TABLE_NAME . " WHERE ". static::ID ." = :id";
			$stmt = $db->prepare($sql);
			$stmt->execute(array(':id' => $id));
			$resource = $stmt->fetch(PDO::FETCH_ASSOC);
			if($resource){
				http_response_code(200);
				return [
					"state" => parent::STATE_SUCCESS,
					"data"	=> $resource
				];
			}
		}catch(PDOException $e){
			throw new ExceptionApi(parent::STATE_ERROR_DB, $e->getMessage());
		}
	}

	public static function getAll(){
		try{ 
			$db = new Database();
			$sql = "SELECT * FROM " . static::TABLE_NAME;
			$stmt = $db->prepare($sql);
			$result = $stmt->execute();

			if($result){
				http_response_code(200);
				return [
					"state" => parent::STATE_SUCCESS,
					"data"	=> $stmt->fetchAll(PDO::FETCH_ASSOC)
				];
			}else{
				throw new ExceptionApi(parent::STATE_ERROR, "S'ha produït un error");
			}
		}catch(PDOException $e){
			throw new ExceptionApi(parent::STATE_ERROR_DB, $e->getMessage());
		}
	}

//METHOD CREATE CALLS INSERT FUNCTION
	public static function create(){
		$body = file_get_contents('php://input');
		$resource = json_decode($body);
		//validar camps
		//crear usuari
		$response = static::insert($resource);
		switch($response){
			case parent::STATE_CREATE_SUCCESS:
				http_response_code(200);
				return
					[
						"state" => 200,
						"message" => utf8_encode("Register success.")
					];
				break;
			case 400:
				throw new ExceptionApi(parent::STATE_CREATE_FAIL, "Ha sorgit un error");
				break;
			default:
				throw new ExceptionApi(parent::STATE_FAIL_UNKNOWN, "Ha sorgit un algo malament", 400);

		}
	}




}

 ?>