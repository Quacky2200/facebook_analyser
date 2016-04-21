<?php
include('DummyPostInteraction.php');

class InteractionTest extends \PHPUnit_Framework_TestCase 
{	

	/*
	* Testing whether user interaction array initializes properly
	*/
	public function test_attachUser() {
		$name = "Jim Jones";
		$id = 124052858;
		$expectedArray = array('124052858' => array('name'=>$name, 'likes'=>0,'comments'=>0,'tags'=>0));
		$this->assertEquals($expectedArray, DummyPostInteraction::attachUser($name, $id));
	}
}

?>