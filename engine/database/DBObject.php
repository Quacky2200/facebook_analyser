<?php
class DBObject{
	//We assume that the table is already created for us right now
	public function getTableName(){
		return get_class($this);
	}
	//Retrieves objects based of the derived object values (e.g. a user where user->name == "Matt")
	public function select($ignoreNullValues = true){
		$table = $this->getTableName();
		$values = $this->buildMYSQLQueryString($this->export($ignoreNullValues), " AND ");
		//$values = urldecode(http_build_query($exportedObject, "", " AND "));
		$retrievedObjects = DBConnection::instance()->fetch("SELECT * FROM $table WHERE $values");
		return $retrievedObjects;
	}
	private function buildMYSQLQueryString($items, $seperator){
		$queryStr = "";
		foreach($items as $key=>$value){
			$queryStr .= "$key='$value'$seperator";
		}
		return $queryStr;
	}
	//Inserts the derived object model into the database
	public function insert(){
		$exportedObject = $this->export(false);
		$table = $this->getTableName();
		$keys = "\"" . join("\",\"", array_keys($exportedObject)) . "\"";
		$values = "\"" . join("\",\"", array_values($exportedObject)) . "\"";
		DBConnection::instance()->command("INSERT INTO $table ($keys) VALUES ($values)");
	}
	//Updates the derived object into the database with the following key and value
	// (e.g. update username where User->name == "Matt" with values User->email == "")
	public function update($key, $value){
		$exportedObject = $this->export(false);
		$table = $this->getTableName();
		$keys = "\"" . join("\",\"", array_keys($exportedObject)) . "\"";
		$values = "\"" . join("\",\"", array_values($exportedObject)) . "\"";
		DBConnection::instance()->command("UPDATE $table SET $values WHERE $key='$value'");
	}

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