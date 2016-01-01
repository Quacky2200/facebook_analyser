<?php
	
	/* This file contains all the common functions shared between other classes
	   in order to avoid repetition and confusion i've put them here so the functions
	   are only declared once */

	function databaseQuery($query) {

		if (!$con = mysqli_connect("127.0.0.1", "root", "", "sma")) {
			echo "Error connection to database ".mysqli_connect_error();
		}

		if (!$result = mysqli_query($con, $query)) {
			echo "Query error ".mysqli_error($con);
		}
		return $result;
	}


?>