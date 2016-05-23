<?php 

require_once("utilities/ExceptionApi.php");
require_once("AbstractDAO.php");

class Language extends AbstractDAO {
	//Camps de la taula "languages"
	const TABLE_NAME = "languages";
	const ID = "id";
	const CODE = "code";
	const DESCRIPTION = "description";

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
		//
	}


	public static function update($id, $worker){
		//
	}



	public static function insert($worker){
		//
	}







}

 ?>