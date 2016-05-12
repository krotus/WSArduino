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

	public static function insert($order){
		$code = $order->code;
		$description = $order->description;
		$priority = $order->priority;
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
				self::QUANTITY . "," .
				self::ID_STATUS_ORDER . "," .
				self::ID_ROBOT . "," .
				self::ID_PROCESS . ")" .
				" VALUES(:code,:description,:priority,:quantity,:id_status_order,:id_robot,:id_process)";

			$stmt = $db->prepare($sql);
			$stmt->bindParam(":code", $code);
			$stmt->bindParam(":description", $description);
			$stmt->bindParam(":priority", $priority);
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


	public static function update(){}

	
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
					$points = $point_class::getAllByIdProcess($ordre['id_process']);
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

	

}

?>