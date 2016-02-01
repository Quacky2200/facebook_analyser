<?php
class Kudos{
	public $FirstName, $LastName, $Role;
	public function __construct($FirstName, $LastName, $Role){
		$this->FirstName = $FirstName;
		$this->LastName = $LastName;
		$this->Role = $Role;
	}
	public function toString(){
		return $Role . ": " . $this->FirstName . " " . $this->LastName;
	}
}
?>