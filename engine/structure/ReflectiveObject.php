<?php
class ReflectiveObject{
	public function export($ignoreNullValues = true){
		return ($ignoreNullValues ? array_filter(get_object_vars($this), function($var){return !is_null($var);}) : get_object_vars($this));
	}
	public function exportToJSON($ignoreNullValues = true){
		return json_encode($this->export($ignoreNullValues));
	}
	public function importFromJSON($JSONString){
		$this->import(json_decode($JSONString));
		return $this;
	}
	public function import($items){
		foreach($items as $key=>$value){
			$this->{$key} = $value;
		}
	}
	public function copy($array, $obj){
		//Copy associative array into object
		foreach ($array as $key=>$value){
			$obj->{$key} = $value;
		}
	}
	public function copyObj($obj1, $obj2){
		//Copy object 1 object properties to object 2's properties
		$this->copy($this->export($obj1), $obj2);
	}
}
?>