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
	public $id			= '';
	// The code of the course (CS 101, MATH 101)
	public $code		= '';
	// The name of the course
    public $name    	= '';
	// Identify if the course can be edited by students or not
    public $readonly	= '';
	
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

    public function __construct($code = "", $name = "", $readonly = ""){
    	$this->code = $code;
    	$this->name = $name;
		$this->readonly = $readonly;
		
    	/*
		 * Defining the collection
		 */
    	// Connect
		$mongo_client = new MongoClient();
		// select the database
		$database = $mongo_client->selectDB(DATABASE_NAME);
		// Return the collection
		$this->collection = $database->courses;
		
		/*
		 * How to find
		 * $item = $collection->findOne(array(
    		'_id' => new MongoId('4e49fd8269fd873c0a000000')));
		 */
		 
    }
	
	//////////////////////////////////////////////////////////////////
    // Save
    //////////////////////////////////////////////////////////////////
    
    public function Save () {
    	$new_course = array( 	
								"code" => $this->code,
								"name" => $this->name,
								"readonly" => $this->readonly
							 );
		// Insert the user in the database:
		return $this->collection->insert($new_course);
	}
	
	//////////////////////////////////////////////////////////////////
    // Update
    //////////////////////////////////////////////////////////////////
    
    public function Update () {
    	$new_course = $this->collection->findOne(array('_id' => new MongoId($this->id)));
		$new_course['code'] 	= $this->code;
		$new_course['name'] 	= $this->name;
		$new_course['readonly'] = $this->readonly;
		
		// LF: Updating in the database : Overwriting the user document  
		return $this->collection->update(array("_id" => $new_course["_id"]), $new_course);
	}
	
	public function GetAllCourses () {
		return $this->collection->find();
	}
	
	public function Load () {
		$course = $this->collection->findOne(array('_id' => new MongoId($this->id)));
		
		$this->code 	= $course['code'];
		$this->name 	= $course['name'];
		$this->readonly = $course['readonly']; 
	}
	
	public function GetUsersInCourse() {
		// Connect
		$mongo_client = new MongoClient();
		// select the database
		$database = $mongo_client->selectDB(DATABASE_NAME);
		
		$collection = $database->users;
		$users_in_course = array();
		
		$users = $collection->find();
		foreach ($users as $user) {
			//error_log("user = " . $user['username']);
			if (isset($user["courses"][0])) {		
				for ($i = 0; $i < count($user["courses"]); $i++) {
					//error_log("course = " . $user["courses"][$i]);
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
	
	public static function getPermissionToCreateCourse($user_type) {
		return in_array($user_type, array("admin"));
	}
	
	public static function getPermissionToEditProfessorsInCourse($user_type) {
		return in_array($user_type, array("admin"));
	}
}
