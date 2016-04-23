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
		$sql = "SELECT * FROM Results WHERE Result_ID IN (SELECT Result_ID FROM Result_History WHERE Result_ID= :result) LIMIT 1";
		$stmt = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
		$stmt->execute(array(':result'=> $resultID));
		$result = $stmt->fetchObject('Result');
		if($result == false) exit(); //There's no result to give an image for
		
		$result->Data = json_decode($result->Data, true);

		$data = $result->Data;
		$blueBackground = imagecreatefromstring(file_get_contents(__DIR__ . '/../public/images/share-background.png', "r"));
		$friends = array_keys($data['interaction']);
		$friend1 = imagecreatefromstring(file_get_contents(User::getAvatar($friends[0]))); //200,200 (width x height)
		$friend2 = imagecreatefromstring(file_get_contents(User::getAvatar($friends[1])));
		$friend3 = imagecreatefromstring(file_get_contents(User::getAvatar($friends[2])));

		$gaussian = array(array(1.0, 2.0, 1.0), array(2.0, 4.0, 2.0), array(1.0, 2.0, 1.0));
		for($i = 0; $i < 60; $i++){
			imageconvolution($friend1, $gaussian, 16, 0);
			imageconvolution($friend2, $gaussian, 16, 0);
			imageconvolution($friend3, $gaussian, 16, 0);
		}

		$graph = imagecreatefromstring(file_get_contents(__DIR__ . '/../public/images/white-logo-transparent-medium.png', "r"));
		$foreground = imagecreatefromstring(file_get_contents(__DIR__ . '/../public/images/share-foreground.png', "r"));				
		imagecopy($blueBackground, $friend1, -50, 25, 0, 0, imagesx($friend1), imagesy($friend1));
		imagecopy($blueBackground, $friend2, 150, 25, 0, 0, imagesx($friend2), imagesy($friend2));
		imagecopy($blueBackground, $friend3, 350, 25, 0, 0, imagesx($friend3), imagesy($friend3));
		$graph = imagescale($graph, imagesx($friend1) * 2);
		imagecopy($blueBackground, $graph, 80, -20, 0, 0, imagesx($graph), imagesy($graph));
		imagecopy($blueBackground, $foreground, 0, 0, 0, 0, imagesx($foreground), imagesy($foreground));
		ob_clean();
		ob_start();
		header('Content-Type: image/png');
		imagepng($blueBackground);
	}
	public function show($template){

	}
}
?>