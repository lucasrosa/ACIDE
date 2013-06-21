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

    public function __construct($code = "", $name = ""){
    	$this->code = $code;
    	$this->name = $name;
    	/*
		 * Defining the collection
		 */
    	// Connect
		$mongo_client = new MongoClient();
		// select the database
		$database = $mongo_client->codiad_database;
		// Return the collection
		$this->collection = $database->courses;
		
		/*
		 * How to find
		 * $item = $collection->findOne(array(
    		'_id' => new MongoId('4e49fd8269fd873c0a000000')));
		 */
		 
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
		return $this->collection->insert($new_course);
	}
	public function GetAllCourses () {
		return $this->collection->find();
	}
}
