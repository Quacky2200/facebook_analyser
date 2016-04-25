<?php
define("CONFIG", dirname(__FILE__) . "/config/config-data.json");
class Engine{
	private $currentTemplate;
	private static $protocol, $host;
	public function __construct(){
		self::$protocol = (self::isSecure() ? "https://" : "http://");
		self::$host = $_SERVER['SERVER_NAME'];
		self::startSession();
		$this->getRequiredVersion();
		$this->getRequiredLibraries();
		$this->getRequiredConfig();
		$this->currentTemplate = self::getTemplate($this->getConfig()->TEMPLATE);
		$this->useTemplate($this->currentTemplate);
		$this->run();
	}
	private function getRequiredLibraries(){
		try{
			require("config/Config.php");
			require("structure/Template.php");
			require("structure/Page.php");
		} catch(Exception $e){
			ErrorHandler::primitiveError(500, "Cannot load Engine libraries", $e->getMessage());
		}
	}
	private function getRequiredVersion(){
		$versionRequired = "5.3.0";
		if (!(version_compare(PHP_VERSION, $versionRequired) >= 0)){
			ErrorHandler::primitiveError(500, "Cannot initiate Engine", "Requires PHP version $versionRequired and higher (Currently " . PHP_VERSION . ")");
		}
	}
	private function getRequiredConfig(){
		try{
			if(!$this->getConfig()->configExists()){
				$this->currentTemplate = Engine::requireFile(__DIR__ . "/config/setup/main.php");
				$this->run();
				exit(0);
			}
			$this->getConfig()->open();
		} catch (Exception $e) {
			ErrorHandler::primitiveError(500, "Cannot initiate configuration", $e->getMessage());
		}
	}
	public static function getConfig(){
		static $instance;
		if(null === $instance){
			$instance = new Config(CONFIG, false);
		}
		return $instance;
	}
	public static function getDatabase(){
		static $instance;
		if(null === $instance){
			$config = self::getConfig();
			$instance = new PDO("mysql:host=$config->DB_HOST;dbname=$config->DB_NAME", $config->DB_USERNAME, $config->DB_PASSWORD);
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
		return __DIR__;
	}
	public static function getRemoteDir($path){
		//Returns nothing if the path is already an absolute address
		if(Engine::startsWith($path, "http://") || Engine::startsWith($path, "https://")) return $path;
		//Check that the path exists as a potential folder and/or file on the computer
		if($path != "/" && realpath($path)){
			//This must mean the directory is real, let's remove the place that it's from to create the online equivelent
			$path = str_replace(realpath(__DIR__ . '/../'), "", realpath($path));
		}
		//Returns the new fixed relative address (e.g. /Hello/World & /Foo/Bar become /Hello/World/Foo/Bar).
		return self::fixPath(dirname($_SERVER['SCRIPT_NAME']) . $path);
	}
	public static function getRemoteAbsolutePath($path){
		//Returns nothing if the path is already an absolute address
		if(Engine::startsWith($path, "http://") || Engine::startsWith($path, "https://")) return $path;
		//Returns the absolute address of the provided path.
		//(e.g. /Hello/World & /Foo/Bar become http://localhost/Hello/World/Foo/Bar)
		return self::$protocol . self::$host . self::getRemoteDir($path);
	}
	public static function isSecure(){
		//Thank you to this answer: http://stackoverflow.com/questions/1175096/how-to-find-out-if-youre-using-https-without-serverhttps#answer-2886224
		return (!empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off") || $_SERVER["SERVER_PORT"] == 443;
	}
	public static function fixPath($path){
		//Return a path that has missing starting and ending slashes
		$path = (self::startsWith($path, "/") ? $path : "/" . $path);
		//$path = (self::endsWith($path, "/") ? $path : $path . "/");
		return preg_replace("#/+#", "/", preg_replace("#\\\\+#","/", $path));
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
		//Can be used to create seeds/ID"s etc etc
		$characters = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
		$randomString = "";
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
		//Get an array of templates
		$paths = glob(self::getLocalDir() . "/templates/*/main.php");
		foreach($paths as &$path){
			//Fix their path when returning the the template
			$path = realpath($path);
		}
		return $paths;
	}
	public static function getTemplate($Name){
		$find = __DIR__ . "/templates/" . $Name . "/main.php";
		return (file_exists($find) ? realpath($find) : null);
	}
	public function run(){
		try{
			//Traverse the Template if possible, otherwise the template is null;
			if(is_null($this->currentTemplate)){
				throw new Exception("No template found.");
			} else {
				$url = $this->fixPath(isset($_GET["current_engine_page"]) ? $_GET["current_engine_page"] : "");	
				$this->currentTemplate->traverse($url);
			}
		} catch (Exception $e) {
			ErrorHandler::primitiveError(500, "Cannot run Engine", $e->getMessage());
		}
	}
	public static function startSession(){
		session_start();
	}
	public static function clearSession(){
		session_unset();
        session_destroy();
        $_SESSION[] = array();
        //Delete the session cookie
        if(ini_get("session.use_cookies")){
            $params = session_get_cookie_params();
            setcookie(session_name(), "", time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
	}
}
?>