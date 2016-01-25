<?php
class DBObject{
	public function getTableName(){
		return __CLASS__;
	}
	public function get($ignoreNullValues = true){
		$exportedObject = $this->export($ignoreNullValues);
		$retrievedObjects = DBConnection::instance()->fetch("SELECT * FROM " . $this->getTableName() . " WHERE " . urldecode(http_build_query($exportedObject, "", " AND ")));
		return $retrievedObjects;
	}
	public function put(){
		$exportedObject = $this->export(false);
		DBConnection::instance()->command("INSERT INTO " . $this->getTableName() . " (" . 
			join(",", array_keys($exportedObject)) . ") VALUES (" . 
			join(",", array_values($exportedObject)) . ")");
	}
	public function export($ignoreNullValues = true){
		return ($ignoreNullValues ? array_filter(get_object_vars($this), function($var){return !is_null($var);}) : get_object_vars($this));
	}
	public function exportToJSON($ignoreNullValues = true){
		return json_encode($this->export($ignoreNullValues));
	}
}
?>