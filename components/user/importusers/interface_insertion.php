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
		
		
		$pageURL = 'http';
		
		if (@$_SERVER["HTTPS"] == "on") {
			$pageURL .= "s";
		}
		
		$pageURL .= "://";
		if ($_SERVER["SERVER_PORT"] != "80") {
			 $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"];
		} else {
			$pageURL .= $_SERVER["SERVER_NAME"];
		}
		
		$dir =  dirname($_SERVER['PHP_SELF']);
		$dirs = explode('/', $dir);
		$pageURL .= "/" . $dirs[1] . "/components/user/importusers";
		
		if ($type == 'student') {
			echo("<script>
				$(document).ready(function() {
					$('.sb-right-content').prepend('<a href=\'" .$pageURL . "\' target=\'_blank\' style=\'text-decoration: none\'><span class=\'icon-user-add l bigger-icon\'></span>Import Users</a>');
				});	
			</script>");
		}		
	}
?>

