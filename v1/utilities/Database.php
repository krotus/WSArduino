<?php 

require_once("configs/db.inc.php");

class Database extends PDO{

    public function __construct(){
		$dsn = 'mysql:dbname=' . DATABASE_NAME . ';host=' . HOST_NAME;
		$user = USER;
		$pw = PASSWORD;
		$arrayopt = array(
			PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
			PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION,
			PDO::ATTR_EMULATE_PREPARES, false
		);
		try{ 
			parent::__construct($dsn, $user, $pw, $arrayopt);
		}catch (PDOException $e){
			die($e->getMessage());
		}
    }
}

?>