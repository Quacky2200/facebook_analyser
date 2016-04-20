<?php

include('UserDummy.php');


/* Will add comments explaining each function
* The last test currently fails but i will write why in the report
* Will add more dummy data to test with tomorrow
*/

class UserTest extends PHPUnit_Framework_TestCase {
	
    public function test_getLikeUsers() {
    	$udummy = new UserDummy(1634766393446243);
    	$dummyFBValues = ([0  => (Object) ['id' => "1634766393446243", "name" => "Shaun George"]]);
    	$expectedAnswer = ["1", ["1634766393446243" => ["1", "Shaun George", "1634766393446243"]]];
    	$this->assertEquals($expectedAnswer, $udummy->getLikeUsers($dummyFBValues));

    }

    public function test_getMostLikeUsers() {
    	$udummy = new UserDummy(1634766393446243);
    	$dummyFBValues = ["1634766393446243" => ["1", "Shaun George", "1634766393446243"]];
    	$dummyTotalLikeCount = 1;
    	$expectedAnswer = ["1634766393446243" => ["1", "Shaun George", "1634766393446243"]];
        $this->assertEquals($expectedAnswer, $udummy->getMostLikeUsers($dummyFBValues, $dummyTotalLikeCount));
    }

    public function test_getCommentUsers() {
        $udummy = new UserDummy(1634766393446243);
        $dummyFBValues = [0 => (Object) ["from" => (Object) ["name" => "Shaun George", "id" => "1634766393446243"], "id" => "1636236923299190_1666520600270822"]];
        $expectedAnswer = ["1", ["1634766393446243" => ["1", "Shaun George", "1634766393446243"]]];
        $this->assertEquals($expectedAnswer, $udummy->getCommentUsers($dummyFBValues));

    }

    public function test_getMostCommentingUsers() {
        $udummy = new UserDummy(1634766393446243);
        $dummyFBValues = ["1634766393446243" => ["1", "Shaun George", "1634766393446243"]];
        $dummyTotalLikeCount = 1;
        $expectedAnswer = ["1634766393446243" => ["1", "Shaun George", "1634766393446243"]];
        $this->assertEquals($expectedAnswer, $udummy->getMostCommentingUsers($dummyFBValues, $dummyTotalLikeCount));
    }

    public function test_getLikeAndCommentUsers() {
        $udummy = new UserDummy(1634766393446243);
        $dummyFBValuesLike = ["1634766393446243" => ["1", "Shaun George", "1634766393446243"]];
        $dummyFBValuesComment = ["1634766393446243" => ["1", "Shaun George", "1634766393446243"]];
        $expectedAnswer = ["1634766393446243" => ["2", "Shaun George", "1634766393446243"]];
        $this->assertEquals($expectedAnswer, $udummy->getLikeAndCommentUsers($dummyFBValuesLike, $dummyFBValuesComment));
    }

    public function test_getMostLikeAndCommentUsers() {
        $udummy = new UserDummy(1634766393446243);
        $dummyFBValues = ["1634766393446243" => ["2", "Shaun George", "1634766393446243"]];
        $dummytotalLikeAndCommentCount = 2;
        $expectedAnswer = ["0" => ["2", "Shaun George", "1634766393446243"]];

        print_r($expectedAnswer);
        print_r($udummy->getMostLikeAndCommentUsers($dummyFBValues, $dummytotalLikeAndCommentCount));
        $this->assertEquals($expectedAnswer, $udummy->getMostLikeAndCommentUsers($dummyFBValues, $dummytotalLikeAndCommentCount));
    }

    public function test_readPosts() {
        $udummy = new UserDummy(1634766393446243);
        $dummyFBValues = ["1634766393446243" => ["2", "Shaun George", "1634766393446243"]];

        echo $dummyFBValues;


    }


}

?>