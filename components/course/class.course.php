<?php

/*
 *  Copyright (c) UPEI lrosa@upei.ca sbateman@upei.ca
 */

//require_once("../user/user.class.php");
 
class Course {

    //////////////////////////////////////////////////////////////////
    // PUBLIC  PROPERTIES
    //////////////////////////////////////////////////////////////////
    // The id of the course (MongoId: '51c497a850ebc1b804fba437')
	public $id	= '';
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
	
	public function Load () {
		$course = $this->collection->findOne(array('_id' => new MongoId($this->id)));
		
		$this->code = $course['code'];
		$this->name = $course['name']; 
	}
	
	public function GetUsersInCourse() {
		// Connect
		$mongo_client = new MongoClient();
		// select the database
		$database = $mongo_client->codiad_database;
		
		$collection = $database->users;
		$users_in_course = array();
		
		$users = $collection->find();
		foreach ($users as $user) {
			if (isset($user["courses"][0])) {		
				for ($i = 0; $i < count($user["courses"]); $i++) {
					if ($user["courses"][$i] == $this->id) {
						$users_in_course[] =  $user["username"];
					}
				}
			}
		}
		
		return $users_in_course;
	}
	
	public function Delete () {
		/* Don't remove, the users must have a course in the project
		// Remove from the users
		$User = new User();
		foreach($User->users as $user) {
			$user->RemoveCourse($this->id);	
		}
		*/
		// Remove User from database
		return $this->collection->remove(array('_id' => new MongoId($this->id)));
	}
}
