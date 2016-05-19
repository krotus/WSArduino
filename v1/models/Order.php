<?php 

require_once("utilities/ExceptionApi.php");

class Order extends AbstractDAO {
	//Camps de la taula "orders"
	const TABLE_NAME = "orders";
	const ID = "id";
	const CODE = "code";
	const DESCRIPTION = "description";
	const PRIORITY = "priority";
	const DATE = "date";
	const QUANTITY = "quantity";
	const ID_STATUS_ORDER = "id_status_order";
	const ID_ROBOT = "id_robot";
	const ID_PROCESS = "id_process";


	//HTTP REQUEST GET
	public static function get($request){
		if($request[0] == 'getAll'){
			return parent::getAll();
		}else if($request[0] == 'getById'){
			return parent::getById($request[1]);
		}else if($request[0] == 'getByIdArduino'){
			return self::getByIdArduino($request[1]);
		}else if($request[0] == 'completedByTask'){
			$idOrder = $request[1];
			$idStatusOrder = $request[2];
			if(self::completedByTask($idOrder, $idStatusOrder) > 0){
				http_response_code(200);
				return [
					"state" => parent::STATE_SUCCESS,
					"message" => "Actualització order i tasca executada"
				];
			}
		}else if($request[0] == 'changeOrderStatus'){
			$idOrder = $request[1];
			$idStatusOrder = $request[2];
			if(self::changeOrderStatus($idOrder, $idStatusOrder) > 0){
				http_response_code(200);
				return [
					"state" => parent::STATE_SUCCESS,
					"message" => "Actualització order i tasca executada"
				];
			}
		}else if($request[0] == 'getOrdersByStatus'){
			$parm1 = $request[1];
			$parm2 = $request[2];
			return self::getOrdersByStatus($parm1, $parm2);
		}else if($request[0] == "updateExecute"){
			$idOrder = $request[1];
			$idStatusOrder = $request[2];
			$idWorker = $request[3];
			if(self::executeByWorker($idOrder, $idStatusOrder, $idWorker) > 0){
				http_response_code(200);
				return [
					"state" => parent::STATE_SUCCESS,
					"message" => "Actualització order i tasca executada"
				];
			}else{
				throw new ExceptionApi(parent::STATE_URL_INCORRECT, "El order que intentes accedir no existeix",404);
			}
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

	//HTTP REQUEST PUT
	public static function put($request){
		if(!empty($request[0]) && !empty($request[1])){
			$route = $request[0];
			$idOrder = $request[1];
			$body = file_get_contents('php://input');
			$order = json_decode($body);
			if($route == "updateAll"){
				if(self::update($idOrder, $order) > 0){
					http_response_code(200);
					return [
						"state" => parent::STATE_SUCCESS,
						"message" => "Actualització order existosa"
					];
				}else{
					throw new ExceptionApi(parent::STATE_URL_INCORRECT, "El order que intentes accedir no existeix",404);
				}
			}else if($route == "updateExecute"){
				$idStatusOrder = $request[2];
				$idWorker = $request[3];
				if(self::executeByWorker($idOrder, $idStatusOrder, $idWorker) > 0){
					http_response_code(200);
					return [
						"state" => parent::STATE_SUCCESS,
						"message" => "Actualització order i tasca executada"
					];
				}else{
					throw new ExceptionApi(parent::STATE_URL_INCORRECT, "El order que intentes accedir no existeix",404);
				}
			}else{
				throw new ExceptionApi(parent::STATE_ERROR_PARAMETERS, "La ruta especificada no existeix",422);
			}
		}else{
			throw new ExceptionApi(parent::STATE_ERROR_PARAMETERS, "Falta la ruta del order", 422);
		}
	}

public static function getAllWorkersAdmin(){
		try{ 
			$db = new Database();
			$sql = "select " . self::TABLE_NAME . ".". self::ID ." as order_id,
			" . self::TABLE_NAME . "." . self::CODE ." as order_code, 
			" . self::TABLE_NAME . "." . self::DESCRIPTION ." as order_description, 
			" . self::TABLE_NAME . "." . self::PRIORITY ." as order_priority, 
			" . self::TABLE_NAME . "." . self::DATE ."  as order_date, 
			processes.description as process_description, 
			" . self::TABLE_NAME . "." . self::QUANTITY . " as order_quantity, 
			robots.name as robot_name, 
			robots.code as robot_code,
			status_order.description as status_order_description
			from " . self::TABLE_NAME . "
			 inner join processes on processes.id = ". self::TABLE_NAME .".id_process"
			 "inner join robots on robots.id = ". self::TABLE_NAME .".id_robot"
			 "inner join status_order on status_order.id = ". self::TABLE_NAME .".id_status_order;";
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

	public static function insert($order){
		$code = $order->code;
		$description = $order->description;
		$priority = $order->priority;
		$date = $order->date;
		$quantity = $order->quantity;
		$idStatusOrder = $order->statusOrder;
		$idRobot = $order->robot;
		$idProcess = $order->process;
		try{
			$db = new Database();
			$sql = "INSERT INTO " . self::TABLE_NAME . " ( " .
				self::CODE . "," .
				self::DESCRIPTION . "," .
				self::PRIORITY . "," .
				self::DATE . "," .
				self::QUANTITY . "," .
				self::ID_STATUS_ORDER . "," .
				self::ID_ROBOT . "," .
				self::ID_PROCESS . ")" .
				" VALUES(:code,:description,:priority,:date,:quantity,:id_status_order,:id_robot,:id_process)";

			$stmt = $db->prepare($sql);
			$stmt->bindParam(":code", $code);
			$stmt->bindParam(":description", $description);
			$stmt->bindParam(":priority", $priority);
			$stmt->bindParam(":date", $date);
			$stmt->bindParam(":quantity", $quantity);
			$stmt->bindParam(":id_status_order", $idStatusOrder);
			$stmt->bindParam(":id_robot", $idRobot);
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


	public static function update($id, $order){
		try{
			//creant la consulta UPDATE
			$db = new Database();
			$sql = "UPDATE " . self::TABLE_NAME . 
			" SET " . self::CODE . " = :code," .
			self::DESCRIPTION . " = :description," .
			self::PRIORITY . " = :priority," .
			self::DATE . " = :date," .
			self::QUANTITY . " = :quantity," .
			self::ID_STATUS_ORDER . " = :id_status_order," .
			self::ID_ROBOT . " = :id_robot," .
			self::ID_PROCESS . " = :id_process " .
			"WHERE " . self::ID . " = :id";

			//prerarem la sentencia
			$stmt = $db->prepare($sql);
			$stmt->bindParam(":code", $order->code);
			$stmt->bindParam(":description", $order->description);
			$stmt->bindParam(":priority", $order->priority);
			$stmt->bindParam(":date", $order->date);
			$stmt->bindParam(":quantity", $order->quantity);
			$stmt->bindParam(":id_status_order", $order->statusOrder);
			$stmt->bindParam(":id_robot", $order->robot);
			$stmt->bindParam(":id_process", $order->process);
			$stmt->bindParam(":id", $id);

			$stmt->execute();

			return $stmt->rowCount();
		}catch(PDOException $e){
			throw new ExceptionApi(parent::STATE_ERROR_DB, $e->getMessage());
		}
	}

	public static function executeByWorker($idOrder, $idStatus, $idWorker){
		try{
			//creant la consulta GET pero hauria de ser UPDATE
			$db = new Database();
			$sql = "UPDATE " . self::TABLE_NAME . 
			" SET " . self::ID_STATUS_ORDER . " = :id_status_order " .
			"WHERE " . self::ID . " = :id";

			//prerarem la sentencia
			$stmt = $db->prepare($sql);
			$stmt->bindParam(":id_status_order", $idStatus);
			$stmt->bindParam(":id", $idOrder);

			$stmt->execute();
			Task::updateOrderTaskExecute($idWorker, $idOrder);

			return $stmt->rowCount();
		}catch(PDOException $e){
			throw new ExceptionApi(parent::STATE_ERROR_DB, $e->getMessage());
		}
	}

	public static function completedByTask($idOrder, $idStatus){
		try{
			//creant la consulta GET pero hauria de ser UPDATE
			$db = new Database();
			$sql = "UPDATE " . self::TABLE_NAME . 
			" SET " . self::ID_STATUS_ORDER . " = :id_status_order " .
			"WHERE " . self::ID . " = :id";
			//prerarem la sentencia
			$stmt = $db->prepare($sql);
			$stmt->bindParam(":id_status_order", $idStatus);
			$stmt->bindParam(":id", $idOrder);
			$stmt->execute();
			if (isset($_GET['dataExtra'])) {
				$extraData = $_GET['dataExtra'];
				if ($idStatus == 3) {
					Task::updateTaskCompleted($idOrder,$extraData);
				} else if ($idStatus == 5) {
					Task::updateTaskCancelled($idOrder,$extraData);
				}
			}
			return $stmt->rowCount();
		}catch(PDOException $e){
			throw new ExceptionApi(parent::STATE_ERROR_DB, $e->getMessage());
		}
	}


	public static function changeOrderStatus($idOrder, $idStatus){
		try{
			//creant la consulta GET pero hauria de ser UPDATE
			$db = new Database();
			$sql = "UPDATE " . self::TABLE_NAME . 
			" SET " . self::ID_STATUS_ORDER . " = :id_status_order " .
			"WHERE " . self::ID . " = :id";
			//prerarem la sentencia
			$stmt = $db->prepare($sql);
			$stmt->bindParam(":id_status_order", $idStatus);
			$stmt->bindParam(":id", $idOrder);
			$stmt->execute();
			return $stmt->rowCount();
		}catch(PDOException $e){
			throw new ExceptionApi(parent::STATE_ERROR_DB, $e->getMessage());
		}
	}




	
	public static function getByIdArduino($id){
		try{
			$db = new Database();
			$sql = "SELECT * FROM " . self::TABLE_NAME . " WHERE id = :id";
			$stmt = $db->prepare($sql);
			$stmt->execute(array(':id' => $id));
			$ordre = $stmt->fetch(PDO::FETCH_ASSOC);
			if($ordre){
				//agafa els punts
				$id_process = $ordre['id_process'];
				include_once("Process.php");
				$process_class = new Process();
				$process = $process_class::getById($id_process);
				$ordre['id_process'] = $process['data'][0]['id'];
				if($process){
					include_once("Point.php");
					$point_class = new Point();
					$points = $point_class::getAllByIdProcessArduino($ordre['id_process']);
					if($points){
						http_response_code(200);
						return [
							"state" => parent::STATE_SUCCESS,
							"order" => $ordre,
							"points"=> $points
						];
					}else{
						throw new ExceptionApi(parent::STATE_ERROR, "S'ha produït un error al obtindre els punts");
					}
				}else{
					throw new ExceptionApi(parent::STATE_ERROR_DB, "S'ha produït un error al obtindre el process");
				}
			}else{
				throw new ExceptionApi(parent::STATE_ERROR_DB, "S'ha produït un error al obtindre la ordre");
			}
		}catch(PDOException $e){
			throw new ExceptionApi(parent::STATE_ERROR_DB, $e->getMessage());
		}

	}

	public static function getOrdersByStatus($worker, $status)
	{
		try{ 
			$db = new Database();
			$sql = "SELECT DISTINCT o.id, o.code as code_ord, o.description as desc_ord, o.priority, o.date, o.quantity, r.code as code_robot, r.name, sr.description as desc_sr
FROM ". self::TABLE_NAME ." as o INNER JOIN status_order as so on o.id_status_order = so.id 
INNER JOIN robots as r on id_robot = r.id
INNER JOIN status_robot as sr on r.id_current_status = sr.id
INNER JOIN tasks as t on t.id_order = o.id
INNER JOIN teams as te on t.id_team = te.id
INNER JOIN workers as w on w.id_team = te.id
INNER JOIN processes as pr on o.id_process = pr.id
WHERE w.id = :worker AND so.description = :stat ";
if ($status == "uncompleted") {
	$sql = $sql .";";
}else{
	$sql = $sql ."and o.date BETWEEN now() AND cast(concat(DATE_SUB(curdate(), INTERVAL -1 DAY), ' 00:00:00') as datetime);";

}
			$stmt = $db->prepare($sql);
			$stmt->bindParam(':worker', $worker);
			$stmt->bindParam(':stat', $status);
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