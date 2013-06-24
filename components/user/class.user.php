<?php

/*
*  Copyright (c) Codiad & Kent Safranski (codiad.com), distributed
*  as-is and without warranty under the MIT License. See
*  [root]/license.txt for more. This information must remain intact.
*/

class User {

    //////////////////////////////////////////////////////////////////
    // PROPERTIES
    //////////////////////////////////////////////////////////////////

    public $username    = '';
    public $password    = '';
    public $project     = '';
    public $projects    = '';
    public $users       = '';
    public $actives     = '';
    public $lang        = '';

	//////////////////////////////////////////////////////////////////
    // LF: PROPERTIES
    //////////////////////////////////////////////////////////////////
    
    /* Types of users (from bottom to top) :
	 * 		student 
	 * 		marker
	 * 		professor
	 * 		admin
	 */
    public $type		= '';
    public $email		= '';
	// LF: The classes that the user is in
	public $courses		= '';
	// LF: The actual collection
	
    //////////////////////////////////////////////////////////////////
    // METHODS
    //////////////////////////////////////////////////////////////////

    // -----------------------------||----------------------------- //

    //////////////////////////////////////////////////////////////////
    // Construct
    //////////////////////////////////////////////////////////////////

    public function __construct(){
        // Load the users from the database
		$this->collection = $this->GetCollection();
		// Get all the users in the database and set it as the users of this instance
		$this->users = $this->collection->find(); //$this->users = getJSON('users.php');
		
        $this->actives = getJSON('active.php');
    }

    //////////////////////////////////////////////////////////////////
    // Authenticate
    //////////////////////////////////////////////////////////////////

    public function Authenticate(){

        $pass = false;
        $this->EncryptPassword();
       
		// Load the users from the database and verifies if one of them is this one
		$collection = $this->GetCollection();
		// Get all the users in the database
		$users = $collection->find();
		foreach ($users as $user) {
			if($user['username']==$this->username && $user['password']==$this->password){
                $pass = true;
                $_SESSION['user'] = $this->username;
                $_SESSION['lang'] = $this->lang;
                if($user['project']!=''){ $_SESSION['project'] = $user['project']; }
            }			
		}
		

        if($pass){ echo formatJSEND("success",array("username"=>$this->username)); }
        else{ echo formatJSEND("error","Incorrect Username or Password"); }
    }

    //////////////////////////////////////////////////////////////////
    // Create Account
    //////////////////////////////////////////////////////////////////

    public function Create($return_a_string = FALSE){
        $this->EncryptPassword();
        $pass = $this->checkDuplicate();
        if($pass){
        	
            $collection = $this->GetCollection();
			
			$new_user = array( 	
								"type" => $this->type,
								"username" => $this->username,
								"password" => $this->password, 
								"email" => $this->email, 
								"projects" => '',
								"project" => '',
								"classes" => $this->classes
							 );
			
			// Insert the user in the database:
			if ($collection->insert($new_user)) {
				if ($return_a_string) {
					return "success";
				} else {
					echo formatJSEND("success",array("username"=>$this->username));	
				}
			} else {
				if ($return_a_string) {
					return "The user could not be inserted on the database";
				} else {
					echo formatJSEND("error","The user could not be inserted on the database");
				}				
			}
            
        }else{
        	if ($return_a_string) {
				return "The Username is Already Taken";
			} else {
            	echo formatJSEND("error","The Username is Already Taken");
			}
        }
    }

    //////////////////////////////////////////////////////////////////
    // Delete Account
    //////////////////////////////////////////////////////////////////

    public function Delete(){
		// Remove User from database
		$collection = $this->GetCollection();
		$collection->remove(array('username' => $this->username));
		
        // Remove any active files
        foreach($this->actives as $active=>$data){
            if($this->username==$data['username']){
                unset($this->actives[$active]);
            }
        }
        saveJSON('active.php',$this->actives);

        // Remove access control list (if exists)
        if(file_exists(BASE_PATH . "/data/" . $this->username . '_acl.php')){
            unlink(BASE_PATH . "/data/" . $this->username . '_acl.php');
        }

        // Response
        echo formatJSEND("success",null);
    }

    //////////////////////////////////////////////////////////////////
    // Change Password
    //////////////////////////////////////////////////////////////////

    public function Password(){
        $this->EncryptPassword();
		$collection = $this->GetCollection();
		$users = $collection->find();
		foreach ($users as $user) {
			if($user['username']==$this->username) {
				$user["password"] = $this->password;
				$collection->update(array("username" => $user["username"]), $user);	
			} 
		}
		
        // Response
        echo formatJSEND("success",null);
    }

    //////////////////////////////////////////////////////////////////
    // Set Project Access
    //////////////////////////////////////////////////////////////////

    public function Project_Access(){
        // Access set to all projects
        if($this->projects==0){
            if(file_exists(BASE_PATH . "/data/" . $this->username . '_acl.php')){
                unlink(BASE_PATH . "/data/" . $this->username . '_acl.php');
            }
        // Access set to restricted list
        }else{
            // Save array back to JSON
            saveJSON($this->username . '_acl.php',$this->projects);
        }
        // Response
        echo formatJSEND("success",null);
    }

    //////////////////////////////////////////////////////////////////
    // Set Current Project
    //////////////////////////////////////////////////////////////////

    public function Project(){
		$collection = $this->GetCollection();
		$users = $collection->find();
		foreach ($users as $user) {
			if($user['username']==$this->username) {
				$user["project"] = $this->project;
				$collection->update(array("username" => $user["username"]), $user);	
			} 
		}
        // Response
        echo formatJSEND("success",null);
    }

    //////////////////////////////////////////////////////////////////
    // Check Duplicate
    //////////////////////////////////////////////////////////////////

    public function CheckDuplicate(){
        $pass = true;
        
		$collection = $this->GetCollection();
		// Get all the users in the database
		$users = $collection->find();
		foreach ($users as $user) {
			if($user['username']==$this->username){
                $pass = false;
            }			
		}
		
		
        return $pass;
    }

    //////////////////////////////////////////////////////////////////
    // Verify Account Exists
    //////////////////////////////////////////////////////////////////

    public function Verify(){
        $pass = 'false';
		
		$collection = $this->GetCollection();
		// Get all the users in the database
		$users = $collection->find();
		foreach ($users as $user) {
			if($user['username']==$this->username){
                $pass = 'true';
            }			
		}
		
        echo($pass);
    }

    //////////////////////////////////////////////////////////////////
    // Encrypt Password
    //////////////////////////////////////////////////////////////////

    private function EncryptPassword(){
        $this->password = sha1(md5($this->password));
    }

    //////////////////////////////////////////////////////////////////
    // Clean username
    //////////////////////////////////////////////////////////////////

    public static function CleanUsername( $username ){
        return preg_replace('#[^A-Za-z0-9'.preg_quote('-_@. ').']#','', $username);
    }
	
	private function GetCollection() {
		// Connect
		$mongo_client = new MongoClient();
		// select the database
		$database = $mongo_client->codiad_database;
		// Select the collection 
		$collection = $database->users;
		// Return the collection
		return $collection;
	}
	
	public function GetUserType() {
		$collection = $this->GetCollection();
		$users = $collection->find();
		foreach ($users as $user) {
			if($user['username']==$this->username) {
				return $user['type'];	
			} 
		}
		
		return "";
	}
	
	public function AddCourse ($course_id) {
		$users = $this->users;
		foreach ($users as $user) {
			if($user['username'] == $this->username) {
				$course_already_inserted = FALSE;
				if (isset($user['courses'])) {
					for ($k = 0; $k < count($user['courses']); $k++) {
						if ($user['courses'][$k] == $course_id) {
							$course_already_inserted = TRUE;
							break;
						}
					}
					
					if (!$course_already_inserted) {
						$user['courses'][] = $course_id;
					}
				} else {
					$user['courses'] = array();
					$user['courses'][] = $course_id;
				}
				
				return ($this->collection->update(array("username" => $user["username"]), $user));
			} 
		}
		
	}
}
