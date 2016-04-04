<?php 

abstract class ViewApi{
	//Codi d'error
	public $state;

	public abstract function prints($body);
}

?>