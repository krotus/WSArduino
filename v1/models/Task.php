<?php 

require_once("utilities/ExceptionApi.php");

class Task extends AbstractDAO {
	//Camps de la taula "tasks"
	const TABLE_NAME = "tasks";
	const ID = "id";
	const ID_TEAM = "id_team";
	const ID_ORDER = "id_order";
	const ID_WORKER = "id_worker";
	const DATA_ASSIGNATION = "data_assignation";
	const DATA_COMPLETION = "data_completion";
	const JUSTIFICATION = "justification";

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

	//METHOD CREATE CALLS INSERT FUNCTION
	public static function create(){
		$body = file_get_contents('php://input');
		$task = json_decode($body);
		//validar camps
		//crear usuari
		$response = self::insert($task);
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

	//TODO
	public static function insert($task){
		$team = $task->team;
		$order = $task->order;
		$worker = $task->worker;
		$dateAssignation = $task->dateAssignation;
		$dateCompletion = $task->dateCompletion;
		$justification = $task->justification;
		try{
			$db = new Database();
			$sql = "INSERT INTO " . self::TABLE_NAME . " ( " .
				self::ID_TEAM . "," .
				self::ID_ORDER . "," .
				self::ID_WORKER . "," .
				self::DATA_ASSIGNATION . "," .
				self::DATA_COMPLETION . "," .
				self::JUSTIFICATION . ")" .
				" VALUES(:id_team,:id_order,:id_worker,:date_assignation,:date_completion,:justification)";

			$stmt = $db->prepare($sql);
			$stmt->bindParam(":id_team", $team);
			$stmt->bindParam(":id_order", $order);
			$stmt->bindParam(":id_worker", $worker);
			$stmt->bindParam(":date_assignation", $dateAssignation);
			$stmt->bindParam(":date_completion", $dateCompletion);
			$stmt->bindParam(":justification", $justification);

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

	public static function getById($id){
		try{
			$db = new Database();
			$sql = "SELECT * FROM " . self::TABLE_NAME . " WHERE ". self::ID ." = :id";
			$stmt = $db->prepare($sql);
			$stmt->execute(array(':id' => $id));
			$task = $stmt->fetchAll(PDO::FETCH_ASSOC);
			if($task){
				http_response_code(200);
				return $task;
			}
		}catch(PDOException $e){
			throw new ExceptionApi(parent::STATE_ERROR_DB, $e->getMessage());
		}
	}

	public static function getAll(){
		try{
			$db = new Database();
			$sql = "SELECT * FROM ".self::TABLE_NAME;
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






}

 ?>