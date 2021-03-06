<?php 

require_once("utilities/ExceptionApi.php");

class Task extends AbstractDAO {
	//Camps de la taula "tasks"
	const TABLE_NAME = "tasks";
	const ID = "id";
	const ID_TEAM = "id_team";
	const ID_ORDER = "id_order";
	const ID_WORKER = "id_worker";
	const DATE_ASSIGNATION = "date_assignation";
	const DATE_COMPLETION = "date_completion";
	const JUSTIFICATION = "justification";

	//HTTP REQUEST GET
	public static function get($request){
		if($request[0] == 'getAll'){
			return parent::getAll();
		}else if($request[0] == 'getById'){
			return parent::getById($request[1]);
		}else if ($request[0] == 'getAllTasksAdmin') {
			return self::getAllTasksAdmin();
		}else if($request[0] == "updateOrderTaskExecute"){
			$idWorker = $request[1];
			$idOrder = $request[2];
			if(self::updateOrderTaskExecute($idWorker, $idOrder) > 0){
				http_response_code(200);
				return [
					"state" => parent::STATE_SUCCESS,
					"message" => "Actualització de tasca existosa"
				];
			}else{
				throw new ExceptionApi(parent::STATE_URL_INCORRECT, "La tasca que intentes accedir no existeix",404);
			}			
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
			$idTask = $request[1];
			$body = file_get_contents('php://input');
			$task = json_decode($body);
			if($route == "updateAll"){
				if(self::update($idTask, $task) > 0){
					http_response_code(200);
					return [
						"state" => parent::STATE_SUCCESS,
						"message" => "Actualització de tasca existosa"
					];
				}else{
					throw new ExceptionApi(parent::STATE_URL_INCORRECT, "La tasca que intentes accedir no existeix",404);
				}
			}else{
				throw new ExceptionApi(parent::STATE_ERROR_PARAMETERS, "La ruta especificada no existeix",422);
			}
		}else{
			throw new ExceptionApi(parent::STATE_ERROR_PARAMETERS, "Falta la ruta de la tasca", 422);
		}
	}

	public static function getAllTasksAdmin(){
		try{ 
			$db = new Database();
			$sql = "select " . self::TABLE_NAME . ".". self::ID .",
			teams.name as team_name,
			orders.description as description_order,
			concat(workers.name, ' ', workers.surname) as worker,
			" . self::TABLE_NAME . "." . self::DATE_ASSIGNATION .", 
			" . self::TABLE_NAME . "." . self::DATE_COMPLETION .", 
			" . self::TABLE_NAME . "." . self::JUSTIFICATION ."
			from " . self::TABLE_NAME . "
			 inner join teams on teams.id = ". self::TABLE_NAME ."." . self::ID_TEAM .
			 " inner join orders on orders.id = ". self::TABLE_NAME ."." . self::ID_ORDER .
			 " left join workers on workers.id = ". self::TABLE_NAME ."." . self::ID_WORKER .";";
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

	//TODO
	public static function insert($task){
		$team = $task->team;
		$order = $task->order;
		$worker = $task->worker;
		$dateCompletion = $task->dateCompletion;
		$justification = $task->justification;
		try{
			$db = new Database();
			$sql = "INSERT INTO " . self::TABLE_NAME . " ( " .
				self::ID_TEAM . "," .
				self::ID_ORDER . "," .
				self::ID_WORKER . "," .
				self::DATE_ASSIGNATION . "," .
				self::DATE_COMPLETION . "," .
				self::JUSTIFICATION . ")" .
				" VALUES(:id_team,:id_order,:id_worker,NOW(),:date_completion,:justification)";

			$stmt = $db->prepare($sql);
			$stmt->bindParam(":id_team", $team);
			$stmt->bindParam(":id_order", $order);
			$stmt->bindParam(":id_worker", $worker);
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

	public static function update($id, $task){
		try{
			//creant la consulta UPDATE
			$db = new Database();
			$sql = "UPDATE " . self::TABLE_NAME . 
			" SET " . self::ID_TEAM . " = :id_team," .
			self::ID_ORDER . " = :id_order," .
			self::ID_WORKER . " = :id_worker," .
			self::DATE_ASSIGNATION . " = NOW()," .
			self::DATE_COMPLETION . " = :date_completion, " .
			self::JUSTIFICATION . " = :justification" .
			" WHERE " . self::ID . " = :id";

			//prerarem la sentencia
			$stmt = $db->prepare($sql);
			$stmt->bindParam(":id_team", $task->team);
			$stmt->bindParam(":id_order", $task->order);
			$stmt->bindParam(":id_worker", $task->worker);
			$stmt->bindParam(":date_completion", $task->dateCompletion);
			$stmt->bindParam(":justification", $task->justification);
			$stmt->bindParam(":id", $id);

			$stmt->execute();

			return $stmt->rowCount();
		}catch(PDOException $e){
			throw new ExceptionApi(parent::STATE_ERROR_DB, $e->getMessage());
		}
	}

	public static function updateOrderTaskExecute($idWorker, $idOrder){
		try{
			//creant la consulta UPDATE
			$db = new Database();
			$sql = "UPDATE " . self::TABLE_NAME . 
			" SET " . self::ID_WORKER . " = :id_worker " .
			"WHERE " . self::ID_ORDER . " = :id_order";

			//prerarem la sentencia
			$stmt = $db->prepare($sql);
			$stmt->bindParam(":id_worker", $idWorker);
			$stmt->bindParam(":id_order", $idOrder);

			$stmt->execute();

			return $stmt->rowCount();
		}catch(PDOException $e){
			throw new ExceptionApi(parent::STATE_ERROR_DB, $e->getMessage());
		}
	}
	public static function updateTaskTeam($idTeam,$idWorker) {
		try {
			$db = new Database();
			$sql = "UPDATE ". self::TABLE_NAME ." SET ". self::ID_TEAM ." = :id_team WHERE ". self::ID_WORKER ." = :id_worker";
			$stmt = $db->prepare($sql);
			$stmt->bindParam(":id_team", $idTeam);
			$stmt->bindParam(":id_worker", $idWorker);

			$stmt->execute();
			return $stmt->rowCount();
		} catch (PDOException $e) {
			throw new ExceptionApi(parent::STATE_ERROR_DB, $e->getMessage());
		}
	}

	public static function updateTaskCompleted($idOrder,$date){
		try{

			//creant la consulta UPDATE
			$db = new Database();
			$sql = "UPDATE " . self::TABLE_NAME . 
			" SET " . self::DATE_COMPLETION . " = :date_completion " .
			"WHERE " . self::ID_ORDER . " = :id_order";

			//prerarem la sentencia
			$stmt = $db->prepare($sql);
			$stmt->bindParam(":date_completion", $date);
			$stmt->bindParam(":id_order", $idOrder);
			
			$stmt->execute();
			return $stmt->rowCount();
		}catch(PDOException $e){
			throw new ExceptionApi(parent::STATE_ERROR_DB, $e->getMessage());
		}
	}

	public static function updateTaskCancelled($idOrder,$justification){
		try{
			//creant la consulta UPDATE
			$db = new Database();
			$sql = "UPDATE " . self::TABLE_NAME . 
			" SET " . self::JUSTIFICATION . " = :justification " .
			"WHERE " . self::ID_ORDER . " = :id_order";

			//prerarem la sentencia
			$stmt = $db->prepare($sql);
			$stmt->bindParam(":justification", $justification);
			$stmt->bindParam(":id_order", $idOrder);

			$stmt->execute();

			return $stmt->rowCount();
		}catch(PDOException $e){
			throw new ExceptionApi(parent::STATE_ERROR_DB, $e->getMessage());
		}
	}



	public static function getTaskIdByIdOrder($id){
		try{
			$db = new Database();
			$sql = "SELECT " . self::ID . " FROM " . self::TABLE_NAME . " WHERE ". self::ID_ORDER ." = :id";
			$stmt = $db->prepare($sql);
			$stmt->execute(array(':id' => $id));
			$taskid = $stmt->fetchAll(PDO::FETCH_ASSOC);
			if($taskid){
				return $taskid;
			}
		}catch(PDOException $e){
			throw new ExceptionApi(parent::STATE_ERROR_DB, $e->getMessage());
		}
	}






}

 ?>