<?php
define("CONFIG", dirname(__FILE__) . "/config/config-data.json");
try{
	Engine::requireAllInDir(__DIR__ . "/database/");
	Engine::requireAllInDir(__DIR__ . "/config/");
	Engine::requireAllInDir(__DIR__ . "/structure/");
} catch (Exception $e){
	ErrorHandler::primitiveError(500, "Cannot load Engine libraries", $e->getMessage());
}
class Engine{
	private $currentTemplate;
	public function __construct(){
		try{
			$this->startSession();
			$this->getRequiredVersion();
			if(!$this->getConfig()->configExists()){
				$this->currentTemplate = Engine::requireFile(__DIR__ . "/config/setup/main.php");
				$this->run();
				exit(0);
			}
			$this->getConfig()->open();
			DBConnection::connectToDB($this->getConfig()->DB_HOST, $this->getConfig()->DB_USERNAME, $this->getConfig()->DB_PASSWORD, $this->getConfig()->DB_NAME);
			$this->currentTemplate = null;
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
			array_push($returnedFiles, self::requireFile(realpath($filename)));
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
	public static function getRemoteAbsolutePath($path){
		return (self::isSecure() ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . Engine::getRemoteDir($path);
	}
	public static function isSecure(){
		//Thank you to this answer: http://stackoverflow.com/questions/1175096/how-to-find-out-if-youre-using-https-without-serverhttps#answer-2886224
		return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443;
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
		if($template instanceof Template){
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
		$find = __DIR__ . "/templates/" . $Name . "/main.php";
		return (file_exists($find) ? $find : null);
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