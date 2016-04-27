<?php
require_once(__DIR__ . '/AnalysisElement.php');
require(__DIR__ . '/../libs/simplehtmldom-1.5/simple_html_dom.php');
	class HoroscopeAnalysis extends AnalysisElement{

		//All zodiac signs, their dates, strengths, weaknesses and descriptions. 
		private $zodiacSigns, $zodiacSign;

		public function __construct(){
			//Load all astrology data. Stripped data from http://zodiac-signs-astrology.com/
			//Stripped to remove extra network load times & improve reliability and readability
			$this->zodiacSigns = json_decode(file_get_contents(__DIR__ . "/astrology-zodiac-signs.json"), true);
		}

		private $activity = array();

		public function analyse($data){
			foreach ($this->zodiacSigns as $zodiacSign){
				$startDate = explode(" ", $zodiacSign['start-date']);
				$endDate = explode(" ", $zodiacSign['end-date']);
				$birthdate = $data['birthday']->getTimestamp();
				if (date('F', $birthdate) == $startDate[0] && date('d', $birthdate) >= (int)$startDate[1] || 
					date('F', $birthdate) == $endDate[0] && date('d', $birthdate) <= (int)$endDate[1]){
					$this->zodiacSign = $zodiacSign;
					break;
				}
			}
			return array(
				"zodiac" => $this->zodiacSign,
				"insight" => $this->getToday()
			);
			
		}
		public function getToday(){
			//Returns the daily horoscope for the zodiac sign (We HAVE to change the browser to the latest sadly)
			$options = array(
			'http'=>array(
				'method'=>"GET",
				'header'=>"Accept-language: en\r\n" .
						"Cookie: \r\n" .  // check function.stream-context-create on php.net
						"User-Agent: " . $_SERVER['HTTP_USER_AGENT'] . "\r\n"
			  )
			);
			$context = stream_context_create($options);
			$dom = file_get_contents("http://new.theastrologer.com/" . strtolower($this->zodiacSign['name']) . "/", false, $context);
			$dom = str_get_html($dom);
			$text = $dom->find('#today p', 0)->innerText();
			return $text;
		}
	}
?>