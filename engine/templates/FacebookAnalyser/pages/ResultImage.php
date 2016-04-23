<?php
class ResultImage extends Page{
	public function getName(){
		return "Facebook Share Image";
	}
	public function getURL(){
		return null;
	}
	private $URLMatch;
	public function isMatch($URL){
		/*
			This regex is to match result images for example:
				/result/a8fjS8wK/image
		*/
		return preg_match("/^\/(?:result\/(\w{8})\/(?:image))[\/]?$/", $URL, $this->URLMatch);
	}
	public function run($template){
		//Remove the whole string as the first result
		array_shift($this->URLMatch);
		//Get the database
		$dbh = Engine::getDatabase();	
		//Get result ID	
		$resultID = $this->URLMatch[0];
		//Check if the result is in the array and return results
		$sql = "SELECT * FROM Users WHERE User_ID IN (SELECT User_ID FROM Result_History WHERE Result_ID= :result) LIMIT 1";
			$stmt = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
			$stmt->execute(array(':result'=> $resultID));
			//Apply an action if there is one
			$result = $stmt->fetchObject('User');
			if($result){
				//Let's create an image for the analysis
				$dest = imagecreatefromstring(file_get_contents(User::getAvatar($result->id))); //200,200 (width x height)
				$src = imagecreatefromstring(file_get_contents(__DIR__ . '/../public/images/white-logo-transparent-medium.png', "r"));				
				$src = imagescale($src, imagesx($dest)); //width
				//					dst_x  dst_y           src_x src_y   src_w     src_h
				imagecopy($dest, $src, 0, imagesy($dest) / 5, 0, 0, imagesx($dest), imagesy($src));
				//ob_start();
				//Paste the contents out
				header('Content-Type: image/png');
				imagepng($dest);
				//$imageData = ob_get_contents();
				//ob_end_clean();	
			} else {
				throw new Exception("Invalid Request");
			}
			$result->Data = json_decode($result->Data, true);
		
	}
	public function show($template){

	}
}
?>