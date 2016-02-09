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
}
?>