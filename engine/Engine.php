<?php
try{
	$self = dirname(__FILE__);
	Engine::requireAllInDir($self . "/database/");
	Engine::requireAllInDir($self . "/config/");
	Engine::requireAllInDir($self . "/structure/");
} catch (Exception $e){
	ErrorHandler::primitiveError(500, "Cannot load Engine libraries", $e->getMessage());
}
define("CONFIG", dirname(__FILE__) . "/config/config-data.json");
class Engine{
	private $currentTemplate;
	public function __construct(){
		try{
			$this->startSession();
			$this->getRequiredVersion();
			if(!$this->getConfig()->configExists()){
				$this->currentTemplate = self::getTemplate('setup');
				$this->useTemplate($this->currentTemplate);
				$this->run();
				exit(0);
			}
			$_GET = $this->returnProtectedGETVariables();
			$_POST = $this->returnProtectedPOSTVariables();	
			$currentTemplate = self::getTemplate($this->getConfig()->TEMPLATE);
			$this->useTemplate($currentTemplate);
			$this->run();	
		} catch(Exception $e){
			ErrorHandler::primitiveError(500, "Cannot initiate Engine", $e->getMessage());
		}
	}
	private function getRequiredVersion(){
		if (!(version_compare(PHP_VERSION, '5.3.0') >= 0)){
			throw new Exception("Requires PHP version 5.3.0 and higher (Currently " . PHP_VERSION . ")");
		}
	}
	public static function getConfig(){
		static $instance;
		if(null === $instance){
			$instance = new Config(CONFIG, false);
		}
		return $instance;
	}
	public static function requireAllInDir($dir){
		$returnedFiles = array();
		foreach(glob($dir . "*.php") as $filename){
			array_push($returnedFiles, self::requireFile($filename));
		}
		return $returnedFiles;
	}
	public static function requireFile($file){
		$required = require($file);
		return $required;
	}
	public static function getLocalDir(){
		return dirname(__FILE__);
	}
	public static function getRemoteDir($path){
		$replacePath = str_replace(realpath($_SERVER['DOCUMENT_ROOT']), '', $path);
		$replaceSlashes = str_replace('\\', '/', $replacePath);
		return $replaceSlashes;
	}
	public static function fixPath($path){
		//Return a path that has missing starting and ending slashes
		$path = (self::startsWith($path, '/') ? $path : '/' . $path);
		$path = (self::endsWith($path, '/') ? $path : $path . '/');
		return preg_replace('#/+#','/', $path);
	}
	public static function startsWith($haystack, $needle) {
		// search backwards starting from haystack length characters from the end
		return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== FALSE;
	}
	public static function endsWith($haystack, $needle) {
		// search forward starting from end minus needle length characters
		return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== FALSE);
	}
	public static function generateRandomString($length = 10) {
		//Generate a random string of uppercase and lowercase letters including numbers 0-9.
		//Can be used to create seeds/ID's etc etc
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, strlen($characters) - 1)];
		}
		return $randomString;
	}
	public function useTemplate($filename){
		$template = self::requireFile($filename);
		if($template instanceof Template && in_array(realpath($filename), self::getTemplates())){
			$this->currentTemplate = $template;
		} else if (!($template instanceof Template)){
			ErrorHandler::primitiveError(500, "Cannot initiate Template", $filename . "<br/>File above must use the Template class.");
		}
	}
	public static function getTemplates(){
		$paths = glob(self::getLocalDir() . "/templates/*/main.php");
		foreach($paths as &$path){
			$path = realpath($path);
		}
		return $paths;
	}
	public static function getTemplate($Name){
		$findFileResults = glob(self::getLocalDir() . "/templates/" . $Name . "/main.php");
		$filename = reset($findFileResults);
		return ($filename !== false && file_exists($filename) ? $filename : null);
	}
	public function run(){
		try{
			//Traverse the Template if possible, otherwise the template is null;
			if(is_null($this->currentTemplate)){
				throw new Exception("No template found.");
			} else {
				$url = $this->fixPath(isset($_GET['current_engine_page']) ? $_GET['current_engine_page'] : '');	
				$this->currentTemplate->traverse($url);
			}
		} catch (Exception $e) {
			ErrorHandler::primitiveError(500, "Cannot run Engine", $e->getMessage());
		}
	}
	public static function returnProtectedGETVariables(){
		//Clear all GET variables
		$GET = $_GET;
		foreach($GET as $key=>$value){
			$GET[$key] = DBConnection::instance()->clear($value);
		}
		return $GET;
	}
	public static function returnProtectedPOSTVariables(){
		//Clear all POST variables
		$POST = $_POST;
		foreach($POST as $key=>$value){
			$POST[$key] = DBConnection::instance()->clear($value);
		}
		return $POST;
	}
	public function startSession(){
		session_start();
	}
	public function clearSession(){
		session_unset();
        session_destroy();
        $_SESSION[] = array();
        //Delete the session cookie
        if(init_get("session.use_cookies")){
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
	}
}
?>