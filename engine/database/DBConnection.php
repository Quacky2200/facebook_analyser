<?php
class DBConnection{
	//Singlton instance
	private static $instance;
	//We only need to connect to one database.
	public static final function instance(){
		if(is_null(self::$instance)){
			throw new Exception("No database connection has been created.");
		}
		else{
			return self::$instance;
		}
	}
	public function __construct($DB_HOST, $DB_USERNAME, $DB_PASSWORD, $DB_NAME){
		mysqli_report(MYSQLI_REPORT_STRICT);
		$this->DB_HOST = $DB_HOST;
		$this->DB_NAME = $DB_NAME;
		//Try connecting to the database
		$this->conection = mysqli_connect('p:' . $DB_HOST, $DB_USERNAME, $DB_PASSWORD, $DB_NAME);
		//If we cannot connect
		if(mysqli_connect_errno($this->connection)){
			//Move no further
			throw new Exception("Cannot connect to database: " . mysqli_connect_error());
		}
		self::$instance = $this;
	}
	private $connection, $DB_HOST, $DB_NAME;
	public static final function connectToDB($DB_HOST, $DB_USERNAME, $DB_PASSWORD, $DB_NAME){
		return new DBConnection($DB_HOST, $DB_USERNAME, $DB_PASSWORD, $DB_NAME);
	}
	public final function getHost(){
		return $this->DB_HOST;
	}
	public final function getDatabaseName(){
		return $this->DB_NAME;
	}
	public final function isConnected(){
		return $this->connection != null;
	}
	//Always run this function before using Fetch() with form/url data. Otherwise, we're open to SQL Injections
	//Read about SQL Injections here: http://www.w3schools.com/sql/sql_injection.asp
	public final function clear($input){
		return mysqli_escape_string($this->connection, $input);
	}
	//Fetch data back.
	public final function fetch($query, $once = false, $type = MYSQLI_ASSOC){
		//Fetch a queries either associative or numeric results using MYSQLI_ASSOC or MYSQLI_NUM
		if($this->isConnected()){
			//If we are connected to the database
			$queryResult = mysqli_query($this->connection, $query);
			//Perform the query to the database
			if($queryResult){
				//If we have any results from the database
				if($once){
					return mysqli_fetch_array($queryResult, $type);
				}
				else{
					$results = [];
					$index = 0;
					while($row = mysqli_fetch_array($queryResult, $type)){
						$results[$index++] = $row;
					}
					//Return these results using the type we wanted 
					return $results;
				}
			}
		}
		else{
			//We're not connected, return nothing.
			throw new exception("Not connected to a database to be able to fetch results");
		}
	}
	public final function close(){
		return mysqli_close($this->connection);
	}
	public final function command($query){
		if($this->isConnected()){
			return (mysqli_query($this->connection, $query) or function(){throw new Exception("Cannot run command with Query: " . $query);} != null ? true : false);
		} else {
			throw new exception("Not connected to a database to be able to run command queries");
		}
	}
}
?>