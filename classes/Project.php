<?php
Class Project{
	private $ID;
	private $name;
	private $location;

	public function __construct($name, $location){
		$this->name     = $name;
		$this->location = $location;
	}
}
?>