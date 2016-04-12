<?php 

require_once("ViewApi.php");

class ViewJson extends ViewApi{
	public function __construct($state = 200){
		$this->state = $state;
	}

	public function prints($body){
		if($this->state){
			http_response_code($this->state);
		}
		header("Content-Type: application/json; charset=utf8");
		echo json_encode($body, JSON_PRETTY_PRINT);
		
	}
}

?>