<?
	if (isset($_SESSION['user'])) {
		// Connect
		$mongo_client = new MongoClient();
		// select the database
		$database = $mongo_client->selectDB(DATABASE_NAME);
		// Select the collection 
		$collection = $database->users;
		
		$type = "";
		$users = $collection->find();
		foreach ($users as $user) {
			if($user['username'] == $_SESSION['user']) {
				$type = $user['type'];	
			} 
		}
		

		if ($type == 'admin') {
			echo('<script src="components/permission/interface_insertion.js"></script>"');
		}
	} 
	
	 /*
	  
	 	{
        "title": "plugins",
        "icon": null,
        "onclick": null
    	},
	 	{
        "title": "Plugins",
        "icon": "icon-tag",
        "onclick": "codiad.plugin_manager.list();"
    	},
	 * 
	 * 
	 	{
        "title": "Users",
        "icon": "icon-users",
        "onclick": "codiad.user.list();"
    	},
	 * 
	     {
        "title": "Update Check",
        "icon": "icon-share",
        "onclick": "codiad.update.check();"
    	},
	 * 
	 {
        "title": "break",
        "icon": null,
        "onclick": null
    },
	 */

?>