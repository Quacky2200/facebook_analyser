<?php
require_once(__DIR__ . '/AnalysisElement.php');
	class ActivityAnalysis extends AnalysisElement{

		public function __construct(){}

		private $activity = array();

		public function analyse($data){
			$field = 'created_time';
			$categories = array('posts', 'likes', 'movies', 'music', 'books', 'photos', 'videos');

			foreach ($categories as $category){
				$scannedData = $this->getIntervals($this->scanFacebookData($data, 'posts', $field)); 
				$this->activity[$category] = array(
					'mean' => $this->getArrayMean($scannedData),
					'prediction' => $this->getPrediction($scannedData)
				);
			}
			//Finally add all as an optional section
			$allData = array_column($this->activity, 'mean');

			return $this->activity;
		}
		private function scanFacebookData($data, $category, $field){
			//Return an array full of the values of the category wanted along with the value.
			//E.g. created_time in all posts, created_time in all liked pages
			$scannedData = array_column($data[$category], $field);
			//Convert all the data appropriatly
			array_walk($scannedData, function(&$item, $key){
				$item = $item->getTimestamp();
			});
			return $scannedData;
		}
		private function getIntervals($array){
			//Get the amount of time between two unix timestamps
			//This will remove one results from the original array
			$intervals = array();
			for($i = 1; $i < count($array); $i++){
				//The increase in number, the older the result.
				$intervals[count($intervals)] = $array[$i - 1] - $array[$i];
			}
			return $intervals;
		}
		private function getArrayMean($array){
			return array_sum($array) / count($array);
		}
		private function getPrediction($array){
			//Divide the data into chunks of four which will be the last index to read.
			$count = floor(count($array) / 4);
			//Predict what the next time will be based on the last few results
			//Always keep at least one result in-case we can't do anything else
			$changedPerspective = array($array[0]);
			for($i = 1; $i < $count; $i++){
				$changedPerspective[count($changedPerspective)] = $array[$i];
			}
			return $this->getArrayMean($changedPerspective);
		}
	}
?>