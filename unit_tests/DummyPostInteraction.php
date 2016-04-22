<?php
	/*
	* Created standalone class DummyPostInteraction
	* to avoid any dependancy on other classes while testing
	*/
	class DummyPostInteraction{

		public function attachUser($name, $id){
			$interaction = array();

			if (!array_key_exists($id, $interaction)){
				$interaction[$id] = array(
					'name' => $name,
					'likes' => 0,
					'comments' => 0,
					'tags' => 0
				);
			}

			return $interaction;
		}




	}
?>