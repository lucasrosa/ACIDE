<?php

	if (isset($_SESSION['user'])) {
		// Connect
		$mongo_client = new MongoClient();
		// select the database
		$database = $mongo_client->codiad_database;
		// Select the collection 
		$collection = $database->users;
		
		$type = "";
		$users = $collection->find();
		foreach ($users as $user) {
			if($user['username'] == $_SESSION['user']) {
				$type = $user['type'];	
			} 
		}
		

		if ($type == 'student') {
			echo('<script src="components/assignment/interface_insertion.js"></script>"');
		}		
	}
?>