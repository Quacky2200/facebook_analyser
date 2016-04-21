<?php

$a = array ('posts' => array(
			array(

				'message' => 't',
				'likes' => array(
								 array('id' => '1023494821039111', 'name' => 'Testing account'),
					            ),
				'comments' => array(
									array(
											'from' => array(
													'name' => 'Tom',
													'id' => '1056894821039111'
												),
											'message' => 'You are my friend',
											'id' => '1636236923299190_1666520600270822'
										),
					               ),
		         ),
		    ),
	);


//print_r($a['posts'][0]['likes'][0]['name']);

?>