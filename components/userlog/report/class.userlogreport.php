<?php

/*
 *  Copyright (c) UPEI lrosa@upei.ca sbateman@upei.ca
 */
require_once('../../../common.php');
require_once('../class.userlog.php');
require_once('../../user/class.user.php');
require_once('../../project/class.project.php');
	

class Userlogreport {

    //////////////////////////////////////////////////////////////////
    // PROPERTIES
    //////////////////////////////////////////////////////////////////
	
	// The user which this log is related to
    public $username    = '';
	
    //////////////////////////////////////////////////////////////////
    // METHODS
    //////////////////////////////////////////////////////////////////

    // -----------------------------||----------------------------- //
	
	//////////////////////////////////////////////////////////////////
    // Construct
    //////////////////////////////////////////////////////////////////

    public function __construct () {
    	$MainUserlog = new Userlog();
		$MainUserlog->CloseAllOpenSectionsThatReachedTimeout();
		
		$User = new User();
		//$User->users = getJSON('users.php');
		// Connect
		$mongo_client = new MongoClient();
		// select the database
		$database = $mongo_client->codiad_database;
		// Select the collection 
		$collection = $database->users;
		
		$users = $User->GetUsersInTheSameCoursesOfUser($_SESSION['user']);
		
		$user_types = $User->GetUsersTypes();
		$student_user_type = $user_types[0];
		
	}
	
    //////////////////////////////////////////////////////////////////
    // Save
    //////////////////////////////////////////////////////////////////

    public function a(){
    }
	
	
}
