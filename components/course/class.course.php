<?php

/*
 *  Copyright (c) UPEI lrosa@upei.ca sbateman@upei.ca
 */

class Course {

    //////////////////////////////////////////////////////////////////
    // PUBLIC  PROPERTIES
    //////////////////////////////////////////////////////////////////
	// The code of the course (CS 101, MATH 101)
	public $code	= '';
	// The name of the course
    public $name    = '';
	
	//////////////////////////////////////////////////////////////////
    // PRIVATE PROPERTIES
    //////////////////////////////////////////////////////////////////
    
	// The user collection
	private $collection; 
	
    //////////////////////////////////////////////////////////////////
    // METHODS
    //////////////////////////////////////////////////////////////////

    // -----------------------------||----------------------------- //
	
	//////////////////////////////////////////////////////////////////
    // Construct
    //////////////////////////////////////////////////////////////////

    public function __construct($code, $username){
    	$this->code 	= $code;
    	$this->username = $username;
    	/*
		 * Defining the collection
		 */
    	// Connect
		$mongo_client = new MongoClient();
		// select the database
		$database = $mongo_client->codiad_database;
		// Return the collection
		$this->collection = $database->courses;
    }
	
	//////////////////////////////////////////////////////////////////
    // Return permissions
    //////////////////////////////////////////////////////////////////
    
    public function Save () {
    	$new_course = array( 	
								"code" => $this->code,
								"name" => $this->name
							 );
		// Insert the user in the database:
		return $collection->insert($new_course);
    }	
}
