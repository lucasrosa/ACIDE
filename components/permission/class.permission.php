<?php

/*
 *  Copyright (c) UPEI lrosa@upei.ca sbateman@upei.ca
 */

class Permission {

    //////////////////////////////////////////////////////////////////
    // PUBLIC  PROPERTIES
    //////////////////////////////////////////////////////////////////
	
	// The user which this log is related to
    public $username    = '';
	
	//////////////////////////////////////////////////////////////////
    // PRIVATE PROPERTIES
    //////////////////////////////////////////////////////////////////
    
	// The user collection
	private $collection;
	// The user's type
	private $user_type;
	// The types of user see class 'user' for all the up to date user types
	public $user_types = array(); 
	
    //////////////////////////////////////////////////////////////////
    // METHODS
    //////////////////////////////////////////////////////////////////

    // -----------------------------||----------------------------- //
	
	//////////////////////////////////////////////////////////////////
    // Construct
    //////////////////////////////////////////////////////////////////

    public function __construct($username){
    	$this->username = $username;
    	/*
		 * Defining the collection
		 */
    	// Connect
		$mongo_client = new MongoClient();
		// select the database
		$database = $mongo_client->codiad_database;
		// Return the collection
		$this->collection = $database->users;
		
		/* 
		 * Defining the array of user types
		 * (from top to bottom)
		 */	
		 $this->user_types[0] = "admin";
		 $this->user_types[1] = "professor";
		 $this->user_types[2] = "marker";
		 $this->user_types[3] = "student";
		 
		 /*
		  * Set the user's type
		  */
		$collection = $this->collection;
		
		$users = $collection->find();
		foreach ($users as $user) {
			if($user['username'] == $this->username) {
				$this->user_type = $user['type'];
				break;
			} 
		}
		
    }

    //////////////////////////////////////////////////////////////////
    // Return the user's type
    //////////////////////////////////////////////////////////////////

    public function GetUserType(){
		return $this->user_type;
    }
	
	//////////////////////////////////////////////////////////////////
    // Return permissions
    //////////////////////////////////////////////////////////////////
    
    public function GetPermissionToSeeAssignments () {
    	return 
    	(
    		$this->user_type == $this->user_types[0]
    		|| $this->user_type == $this->user_types[1]
			|| $this->user_type == $this->user_types[2]
		);
    }
	
	public function GetPermissionToCreateAndEditAssignments () {
    	return 
    	(
    		$this->user_type == $this->user_types[0]
    		|| $this->user_type == $this->user_types[1]
		);
    }
	
	public function GetPermissionToImportUsers () {
    	return 
    	(
    		$this->user_type == $this->user_types[0]
    		|| $this->user_type == $this->user_types[1]
		);
    }
    
	public function GetAdminPermission () {
    	return 
    	(
    		$this->user_type == $this->user_types[0]
		);
    }
	
	public function GetPermissionToEditCourses () {
    	return 
    	(
    		$this->user_type == $this->user_types[0]
    		|| $this->user_type == $this->user_types[1]
		);
    }
	
	public function GetPermissionToEditUsers () {
    	return 
    	(
    		$this->user_type == $this->user_types[0]
    		|| $this->user_type == $this->user_types[1]
		);
    }
}
