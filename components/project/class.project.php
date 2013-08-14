<?php

/*
*  Copyright (c) Codiad & Kent Safranski (codiad.com), distributed
*  as-is and without warranty under the MIT License. See
*  [root]/license.txt for more. This information must remain intact.
*/

//require_once('../../common.php');

class Project extends Common {

    //////////////////////////////////////////////////////////////////
    // PROPERTIES
    //////////////////////////////////////////////////////////////////

    public $name         		= '';
    public $path         		= '';
    public $gitrepo      		= false;
    public $gitbranch    		= '';
    public $projects     		= '';
	// LF: ProjectUser :: The user who created the project
	public $user 				= '';
	// LF: Project Privacy: Can be 'public', 'private' or 'shared'
	public $privacy				= '';
    public $no_return    		= false;
    public $assigned     		= false;
    public $command_exec 		= '';
	// LF: Submitted identifies if a project was already submitted or not :: can be true or false
	public $submitted 			= '';
	// LF: Actual database
	public $database			= '';
	// LF: The members of the group with access to this project
	public $group_members		= '';
	// LF: The assignment related to this project
	public $assignment 			= '';
	// LF: The course that the project is related to (discipline/course)
	public $course 				= '';
	
    //////////////////////////////////////////////////////////////////
    // METHODS
    //////////////////////////////////////////////////////////////////

    // -----------------------------||----------------------------- //

    //////////////////////////////////////////////////////////////////
    // Construct
    //////////////////////////////////////////////////////////////////

    public function __construct(){
    	$this->database = getDatabase();
		//error_log("LOG: " . serialize($this->GetProjectsForUser("lucasfurlani"))); // Testing the method
		$this->projects = $this->GetProjectsForUser($_SESSION['user']);
		
        if(file_exists(BASE_PATH . "/data/" . $_SESSION['user'] . '_acl.php')){
            $this->assigned = getJSON($_SESSION['user'] . '_acl.php');
        }
    }
    
    //////////////////////////////////////////////////////////////////
    // Get First (Default, none selected)
    //////////////////////////////////////////////////////////////////

    public function GetFirst(){

        $projects_assigned = false;
        if($this->assigned){
            foreach($this->projects as $project=>$data){
                if(in_array($data['path'],$this->assigned)){
                    $this->name = $data['name'];
					$this->course = $data['course'];
                    $this->path = $data['path'];
					$this->privacy = $data['privacy'];
					$this->user = $data['user'];
                    break;
                }
            }
        }else{
            $this->name = $this->projects[0]['name'];
            $this->path = $this->projects[0]['path'];
			$this->privacy = $this->projects[0]['privacy'];
			$this->user = $this->projects[0]['user'];
        }
        // Set Sessions
        $_SESSION['project'] = $this->path;

        if(!$this->no_return){
            echo formatJSEND("success",array("name"=>$this->name,"path"=>$this->path, "privacy"=>$this->privacy, "user"=>$this->user));
        }
    }

    //////////////////////////////////////////////////////////////////
    // Get Name From Path
    //////////////////////////////////////////////////////////////////

    public function GetName(){
        return $this->name;
    }
	
	//////////////////////////////////////////////////////////////////
    // LF: Load Project
    //////////////////////////////////////////////////////////////////
	public function Load() {
		$pass = false;
        
		$collection = $this->database->users;
		
		$users = $collection->find();
		
		foreach ($users as $user) {
		//$user = $collection->findOne(array("username" => $this->user));
			if (isset($user["projects"][0])) {	
				for ($i = 0; $i < count($user["projects"]); $i++) {
					if ($user["projects"][$i]["path"] == $this->path) {
						$pass = true;
			            $this->name = $user["projects"][$i]["name"];
						$this->course = $user["projects"][$i]["course"];
						$this->privacy = $user["projects"][$i]["privacy"];
						$this->group_members = $user["projects"][$i]["group_members"];
						$this->assignment = $user["projects"][$i]["assignment"];
			            $_SESSION['project'] = $user["projects"][$i]["path"];
						return $pass;
					}
				}
			}
		}
		
		return false;
	}
    //////////////////////////////////////////////////////////////////
    // Open Project
    //////////////////////////////////////////////////////////////////

    public function Open(){
    	if($this->load()){
			echo formatJSEND("success",array("name"=>$this->name,"path"=>$this->path, "privacy"=>$this->privacy, "user"=>$this->user));
        }else{
            echo formatJSEND("error","Error Opening Project");
        }
    }
	
	//////////////////////////////////////////////////////////////////
    // LF: Init project on database
    //////////////////////////////////////////////////////////////////

    public function CreateProjectOnDatabase($user = ''){
    	if ($user == '') {
    		$this->user = $_SESSION['user'];
			// LF: The assignment is defined as nothing here because when the user is not defined it means it's not an assignment
			$this->assignment = ''; 
		} else {
			$this->user = $user;
		}
		
        $project = array(
						"name" => $this->name,
						"path" => $this->path,
						"privacy" => $this->privacy,
						"visibility" => "true",
						"course" => $this->course,
						"group_members" => array(
													array(
															"username" => $this->user,
														 )														
												),
						"assignment" => $this->assignment
					 );
					 
		$collection = $this->database->users;
		if ($this->privacy == 'private') {
			// LF: Find the current user
			$user = $collection->findOne(array("username" => $this->user));
			// LF: Push the new project in the end of the project's array
			if (!isset($user["projects"][0])) {
				$user["projects"] = array();
			}
			
			array_push($user["projects"], $project);
			// LF: Saves the new array in the database by overwriting the previous user
			return $collection->update(array("username" => $this->user), $user);
		} else { // public project
			$users = $collection->find();
			foreach ($users as $user) {
				// LF: Identify if the array of projects exists, if not, it creates it
				if (!isset($user["projects"][0])) {
					$user["projects"] = array();
				}
				// LF: Push the new project in the end of the project's array
				array_push($user["projects"], $project);
				// LF: Saves the new array in the database by overwriting the previous user
				return $collection->update(array("username" => $user["username"]), $user);
			}
		}
		
		
    }
	
	//////////////////////////////////////////////////////////////////
    // LF: Init project on database
    //////////////////////////////////////////////////////////////////

    public function CreateProjectsOnDatabaseWithAssignments() {
    	$collection = $this->database->users;
		$Course = new Course();
		$Course->id = $this->course;
		
		$users = $collection->find();
		
    	$these_users = $Course->GetUsersInCourse(); //
		
		// Must check if there is no assignment with the same ID
		foreach ($users as $user) {
			if (isset($user["projects"][0]) && in_array($user['username'], $these_users)) {
				for ($i = 0; $i < count($user["projects"]); $i++) {
					if (isset($user["projects"][$i]["assignment"]['id'])) {
						if ($user["projects"][$i]["assignment"]['id'] == $this->assignment['id']) {
							return "An assignment with the same id already exists.";
						}
					}
				}
			}
		}	
		
		// Should rollback if some error occur
		$return = "success";
		$users = $collection->find();
		$user = '';
		foreach ($users as $user) {
			if (in_array($user['username'], $these_users)) {
				$this->path = "AS_" . $user['username'] ."_" . $this->assignment["id"];
				$result = $this->Create($user['username']);
				if ($result != 'success') {
					$return = $result;
				}
			}
		}
		
		if ($return != 'success') {
			$users = $collection->find();
			$user = '';
			foreach ($users as $user) {
				if (in_array($user['username'], $these_users)) {
					$this->path = "AS_" . $user['username'] ."_" . $this->assignment["id"];
					$delete_as_an_assignment = TRUE;
					$result = $this->Delete($delete_as_an_assignment);
				}
			}	
		}
			
		return $return;
	}
	
	
    //////////////////////////////////////////////////////////////////
    // LF: Create
    //////////////////////////////////////////////////////////////////

    public function Create($user = ''){
    	if (isset($this->assignment["id"])) {
    		$has_assignment = TRUE;
    	} else {
    		$has_assignment = FALSE;	
    	}
		
        if($this->name != '' && $this->path != '') {
            $this->path = $this->cleanPath();
            if(strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' || !$this->isAbsPath($this->path)) {
                $this->path = $this->SanitizePath();
            }
			if ($has_assignment) {
				$pass = $this->checkDuplicate(FALSE); // False stands for check name
			} else {
				$pass = $this->checkDuplicate();	
			}
            
			
            if($pass){
                if(!$this->isAbsPath($this->path)) {
                    mkdir(WORKSPACE . '/' . $this->path);
                } else {
                    if(defined('WHITEPATHS')) {
                        $allowed = false;
                        foreach (explode(",",WHITEPATHS) as $whitepath) {
                            if(strpos($this->path, $whitepath) === 0) {
                                $allowed = true;
                            }
                        }
                        if(!$allowed) {
                            die(formatJSEND("error","Absolute Path Only Allowed for ".WHITEPATHS));
                        }
                    }
                    if(!file_exists($this->path)) {
                        if(!mkdir($this->path.'/', 0755, true)) {
                            die(formatJSEND("error","Unable to create Absolute Path"));
                        }
                    } else {
                        if(!is_writable($this->path) || !is_readable($this->path)) {
                            die(formatJSEND("error","No Read/Write Permission"));
                        }
                    }
                }
				$assignment_successfully_created = FALSE;
				if ($has_assignment) {
					if ($this->CreateProjectOnDatabase($user)) {
						return "success";
					} else {
						return "Could not create the project in the database.";
					}
				} else {
					if ($this->CreateProjectOnDatabase()) {
						$assignment_successfully_created = TRUE;
					} else {
						echo formatJSEND("error","Could not create the project in the database.");
					}
				}
				
                // Pull from Git Repo?
                if($this->gitrepo){
                    if(!$this->isAbsPath($this->path)) {
                        $this->command_exec = "cd " . WORKSPACE . '/' . $this->path . " && git init && git remote add origin " . $this->gitrepo . " && git pull origin " . $this->gitbranch;
                    } else {
                        $this->command_exec = "cd " . $this->path . " && git init && git remote add origin " . $this->gitrepo . " && git pull origin " . $this->gitbranch;
                    }
                    $this->ExecuteCMD();
                }
				
				if ($assignment_successfully_created) {
					echo formatJSEND("success",array("name"=>$this->name,"path"=>$this->path));
				}
				
            }else{
            	if ($has_assignment) {
					return "A Project With the Same Name or Path Exists.";
				} else {
                	echo formatJSEND("error","A Project With the Same Name or Path Exists.");
				}
            }
        } else {
             echo formatJSEND("error","Project Name/Folder is empty");
        }
    }
    
    //////////////////////////////////////////////////////////////////
    // LF: Rename
    //////////////////////////////////////////////////////////////////

	public function Rename(){
		// Updating on database
		if ($this->Save()) {
			// Response
        	echo formatJSEND("success",null);	
		}
    }
	
    //////////////////////////////////////////////////////////////////
    // LF: Submit Project
    //////////////////////////////////////////////////////////////////
	
	public function Submit(){
		
		$projectName = $_SESSION['user'] . " - ".$this->name;
		$date_submitted = date("Y-m-d H:i:s");
		$due_date = $this->assignment["due_date"];	
		$allow_late_submission = intval($this->assignment["allow_late_submission"]);
		$late_date = date('Y-m-d H:i:s', strtotime($due_date) + (24*3600*$allow_late_submission));
		
		// Verifies if the user isn't sending the assignment in a late date
		if ($date_submitted <= $late_date) {
			// Set the date (current time) as a string
			$this->assignment["submitted_date"] = $date_submitted;
			// Verifies if the user is sending the project in a late (but allowed) date
			if ($date_submitted > $due_date) {
				$this->assignment["submitted_late"] = "TRUE";
			} else {
				$this->assignment["submitted_late"] = "FALSE";
			}
			
			$this->assignment["project_file_name"] = $projectName . ".zip"; 
			// Save the project in the database
			$this->save();
		
	        // Saves the project in a zip file
	        zipJSON($this->path, $projectName);
			
			// Response
			echo formatJSEND("success",null);
		} else {
			echo formatJSEND("error", "The project can't be submitted after the deadline.");	
		}	
    }

    //////////////////////////////////////////////////////////////////
    // LF:  Delete Project
    //////////////////////////////////////////////////////////////////
	
	public function Delete($delete_as_an_assignment = FALSE){
        $collection = $this->database->users;
		
		$users = $collection->find();
		$update_successful = TRUE;
		$deleted_project_path = $this->path;
		
		foreach ($users as $user) {
			if (isset($user["projects"][0])) {
				for ($i = 0; $i < count($user["projects"]); $i++) {
					// LF: The project is selected based on the path :-> As it is the project's id
					if ($user["projects"][$i]["path"] == $this->path) {
							// LF: clear the "project" attribute if it's the actual project
							if ($user["projects"][$i]["path"] == $user["project"]) {
								$user["project"] = '';
							}
							// LF: Remove the project from the projects array
							if ($delete_as_an_assignment) {
								if (isset($user["projects"][$i]["assignment"]["id"])) {
									unset($user["projects"][$i]);
								}
							} else {
								unset($user["projects"][$i]);	
							}
						$user["projects"] = array_values($user["projects"]);
					}
				}
			}
			// LF: Updating in the database : Overwriting the user document  
			if (!$collection->update(array("username" => $user["username"]), $user)){
				$update_successful = FALSE;	
			}
		}
		
		if ($update_successful) {
			// Remove this project from all users active project attribute -> $user['project]
			$users = $collection->find();
			foreach ($users as $user) {
				if (isset($user["project"])) {
					// LF: The project is selected based on the path :-> As it is the project's id
					if ($user["project"] == $deleted_project_path) {
						$user["project"] = '';
					}
				}
				// LF: Updating in the database : Overwriting the user document  
				if (!$collection->update(array("username" => $user["username"]), $user)){
					$update_successful = FALSE;	
				}
			}				
		}
		
		// LF: If everything is okay returns success 	
		if(!$delete_as_an_assignment) {
			// Response
			echo formatJSEND("success",null);	
		}
	}

    //////////////////////////////////////////////////////////////////
    // Check Duplicate
    //////////////////////////////////////////////////////////////////

    public function CheckDuplicate($check_name = TRUE){
    	// must check the user's project name and all the projects paths
        $pass = true;
        $collection = $this->database->users;
		
		if ($check_name) {
			// LF: Find the current user and verifies the name of all of its projects
			$user = $collection->findOne(array("username" => $this->user));
			if (isset($user["projects"][0])) {	
				for ($i = 0; $i < count($user["projects"]); $i++) {
					if ($user["projects"][$i]["name"] == $this->name) {
						$pass = false;
					}	
				}
			}
		}
		
		// LF: Looking if the current path is equal of at least one of the saved paths
		$user = '';
		$users = $collection->find();
		foreach ($users as $user) {
			if (isset($user["projects"][0])) {
				for ($i = 0; $i < count($user["projects"]); $i++) {
					if ($user["projects"][$i]["path"] == $this->path) {
						$pass = false;
					}
				}
			}
		}	

        return $pass;
    }

    //////////////////////////////////////////////////////////////////
    // Sanitize Path
    //////////////////////////////////////////////////////////////////

    public function SanitizePath(){
        $sanitized = str_replace(" ","_",$this->path);
        return preg_replace('/[^\w-]/', '', $sanitized);
    }
    
    //////////////////////////////////////////////////////////////////
    // Clean Path
    //////////////////////////////////////////////////////////////////
    
    function cleanPath(){

        // prevent Poison Null Byte injections
        $path = str_replace(chr(0), '', $this->path );

        // prevent go out of the workspace
        while (strpos($path , '../') !== false)
            $path = str_replace( '../', '', $path );

        return $path;
    }
    
    //////////////////////////////////////////////////////////////////
    // Execute Command
    //////////////////////////////////////////////////////////////////
    
    public function ExecuteCMD(){
        if(function_exists('system')){
            ob_start();
            system($this->command_exec);
            ob_end_clean();
        }
        //passthru
        else if(function_exists('passthru')){
            ob_start();
            passthru($this->command_exec);
            ob_end_clean();
        }
        //exec
        else if(function_exists('exec')){
            exec($this->command_exec , $this->output);
        }
        //shell_exec
        else if(function_exists('shell_exec')){
            shell_exec($this->command_exec);
        }
	}
	
    //////////////////////////////////////////////////////////////////
    // Get projects for a certain user
    //////////////////////////////////////////////////////////////////
    		
	public function GetProjectsForUser($username) {
		$collection = $this->database->users;
		
		$projects = array();
		// Get the projects from other users that are being shared with this user
		$users = $collection->find();
		$user = '';
		foreach ($users as $user) {
			if (isset($user["projects"][0])) {
				for ($i = 0; $i < count($user["projects"]); $i++) {
					for ($j = 0; $j < count($user["projects"][$i]["group_members"]); $j++) {
						if (($user["projects"][$i]["group_members"][$j]['username'] == $username) || $user["projects"][$i]['privacy'] == 'public') {
							array_push($projects, $user["projects"][$i]);
						}
					}
				}
			}
		}
		
		return $projects;
    }
	
	//////////////////////////////////////////////////////////////////
    // Save this project
    //////////////////////////////////////////////////////////////////
    		
	public function Save() {
		$collection = $this->database->users;
		
		$users = $collection->find();
		$update_successful = TRUE;
		foreach ($users as $user) {
			if (isset($user["projects"][0])) {	
				for ($i = 0; $i < count($user["projects"]); $i++) {
					// LF: The project is selected based on the path :-> As it is the project's id
					if ($user["projects"][$i]["path"] == $this->path) {
						$user["projects"][$i]["course"] = $this->course;
						$user["projects"][$i]["name"] = $this->name;
						$user["projects"][$i]["privacy"] = $this->privacy;
						$user["projects"][$i]["group_members"] = $this->group_members;
						$user["projects"][$i]["assignment"] = $this->assignment;
					}
				}
			}
			// LF: Updating in the database : Overwriting the user document  
			if (!$collection->update(array("username" => $user["username"]), $user)){
				$update_successful = FALSE;	
			}
		}
		return $update_successful;
    }
	
	//////////////////////////////////////////////////////////////////
    // LF: Returns an array with the usernames of all the users in a project
    //////////////////////////////////////////////////////////////////
	
	public function GetUsersInProject() {
		$collection = $this->database->users;
		$users_in_project = array();
		$users_in_project_formated = array();
		
		$users = $collection->find();
		foreach ($users as $user) {
			if (isset($user["projects"][0])) {		
				for ($i = 0; $i < count($user["projects"]); $i++) {
					if ($user["projects"][$i]["path"] == $this->path) {
						$users_in_project =  $user["projects"][$i]["group_members"];
					}
				}
			}
		}
		
		// Formating the returning array
		for ($i = 0; $i < count($users_in_project); $i++) {
			$users_in_project_formated[$i] = $users_in_project[$i]['username'];
		}
		
		if (count($users_in_project) > 0) {
			return $users_in_project_formated;
		} else {
			return null;	
		}
    }
	
	//////////////////////////////////////////////////////////////////
    // LF: Returns the maximum number of group members allowed to this assignment
    //////////////////////////////////////////////////////////////////
	
	public function GetMaximumNumberGroupMembers() {
		if ($this->assignment != '') {
			return $this->assignment['maximum_number_group_members'];
		} else {
			return 0;
		}	
	}
	
	//////////////////////////////////////////////////////////////////
    // LF: Returns all the assignments for a certain user
    //////////////////////////////////////////////////////////////////
	
	public function GetAssignmentsForOwner ($owner) {
		$assignments = array();
		
		$collection = $this->database->users;
		
		$users = $collection->find();
		foreach ($users as $user) {
			if (isset($user["projects"][0])) {
				for ($i = 0; $i < count($user["projects"]); $i++) {
					if (isset($user["projects"][$i]) && isset($user["projects"][$i]["assignment"]["owner"])) {	
						if ($user["projects"][$i]["assignment"]["owner"] == $owner) {
							$assignment_added = FALSE;
							
							for ($k = 0; $k < count($assignments); $k++) {
								if ($user["projects"][$i]["assignment"]["id"] == $assignments[$k]["id"]) {
									$assignment_added = TRUE;
								}
							}
							// If the assignment isn't added yet, add it
							if (!$assignment_added) {
								array_push($assignments, $user["projects"][$i]["assignment"]);												
							}
						}
					}
				}
			}
		}
		
		return $assignments;	
	}
	
	//////////////////////////////////////////////////////////////////
    // LF: Returns all the assignments for a certain user
    //////////////////////////////////////////////////////////////////
	
	public function GetAssignments () {
		$assignments = array();
		
		$collection = $this->database->users;
		
		$users = $collection->find();
		foreach ($users as $user) {
			if (isset($user["projects"][0])) {
				for ($i = 0; $i < count($user["projects"]); $i++) {
					if (isset($user["projects"][$i]) && isset($user["projects"][$i]["assignment"]["owner"])) {	
						$assignment_added = FALSE;
						
						for ($k = 0; $k < count($assignments); $k++) {
							if ($user["projects"][$i]["assignment"]["id"] == $assignments[$k]["id"]) {
								$assignment_added = TRUE;
							}
						}
						// If the assignment isn't added yet, add it
						if (!$assignment_added) {
							$user["projects"][$i]["assignment"]["course"] = $user["projects"][$i]["course"];
							array_push($assignments, $user["projects"][$i]["assignment"]);												
						}
					}
				}
			}
		}
		
		return $assignments;	
	}
	
	//////////////////////////////////////////////////////////////////
    // LF: Returns all the assignments for a certain user
    //////////////////////////////////////////////////////////////////
	
	public function GetAssignmentsInTheSameCoursesOfUser($current_user, $course_id = '') {
		
		// Load this user
		$CurrentUser = new User();
		$CurrentUser->username = $current_user;
		$CurrentUser->Load();
		
		$assignments = array();
		
		$collection = $this->database->users;
		
		$users = $collection->find();
		foreach ($users as $user) {
			if (isset($user["projects"][0])) {
				for ($i = 0; $i < count($user["projects"]); $i++) {
					if (isset($user["projects"][$i]) && isset($user["projects"][$i]["assignment"]["owner"])) {	
						$assignment_added = FALSE;
						if ($CurrentUser->type == "admin" || in_array($user["projects"][$i]['course'], $CurrentUser->courses)) {
								
							// Verifies if it's in the same course, if not, go to next iteration
							if ($course_id != '') {
								if ($user["projects"][$i]['course'] != $course_id) {
									continue;
								}
							}
							
							for ($k = 0; $k < count($assignments); $k++) {
								if ($user["projects"][$i]["assignment"]["id"] == $assignments[$k]["id"]) {
									$assignment_added = TRUE;
								}
							}
							// If the assignment isn't added yet, add it
							if (!$assignment_added) {
								$user["projects"][$i]["assignment"]["course"] = $user["projects"][$i]["course"];
								array_push($assignments, $user["projects"][$i]["assignment"]);												
							}
						}
					}
				}
			}
		}
		
		return $assignments;	
	}	
	
	//////////////////////////////////////////////////////////////////
    // LF: Returns all the projects for a certain assignment
    //////////////////////////////////////////////////////////////////
	
	public function GetProjectsForAssignment ($id) {
		$projects = array();
		
		$collection = $this->database->users;
		
		$users = $collection->find();
		foreach ($users as $user) {
			if (isset($user["projects"][0])) {		
				for ($i = 0; $i < count($user["projects"]); $i++) {
					if (isset($user["projects"][$i]) && isset($user["projects"][$i]["assignment"]["id"])) {
						if ($user["projects"][$i]["assignment"]["id"] == $id) {
							if (isset($user["projects"][$i]["assignment"]["submitted_date"])) {
								if ($user["projects"][$i]["assignment"]["submitted_date"] != "") {
									if ($user["projects"][$i]['privacy'] == 'shared' && count($user['projects'][$i]["group_members"]) > 1) {
										$user['projects'][$i]['name'] .= " (". $user['username'] . ")";
									}
									array_push($projects, $user["projects"][$i]);
								}
							}
							
						}
					}
				}
			}
		}
		
		return $projects;
	}
	
	//////////////////////////////////////////////////////////////////
    // LF: Returns the assignment with that id
    //////////////////////////////////////////////////////////////////
	
	public function GetAssignmentWithId ($id) {
		$assignments = array();
		
		$collection = $this->database->users;
		
		$users = $collection->find();
		foreach ($users as $user) {
			if (isset($user["projects"][0])) {		
				for ($i = 0; $i < count($user["projects"]); $i++) {
					if (isset($user["projects"][$i]) && isset($user["projects"][$i]["assignment"]["id"])) {	
						if ($user["projects"][$i]["assignment"]["id"] == $id) {
							// Return the course inside the assignment
							$user["projects"][$i]["assignment"]["course"] = $user["projects"][$i]["course"];
							return $user["projects"][$i]["assignment"];
						}
					}
				}
			}
		}
		return NULL;
	}
	
	//////////////////////////////////////////////////////////////////
    // LF: Saves the assignment
    //////////////////////////////////////////////////////////////////
	
	public function SaveAssignment ($assignment) {
		
		$collection = $this->database->users;
		
		$users = $collection->find();
		$update_successful = TRUE;
		foreach ($users as $user) {
			for ($i = 0; $i < count($user["projects"]); $i++) {
				if (isset($user["projects"][$i]) && isset($user["projects"][$i]["assignment"]["id"])) {	
					if ($user["projects"][$i]["assignment"]["id"] == $assignment['id']) {
						// Save the course in the project, not in the assignment
						$user["projects"][$i]["course"] = $assignment['course'];
						$user["projects"][$i]["assignment"]["name"] = $assignment['name'];
						$user["projects"][$i]["assignment"]["due_date"] = $assignment['due_date'];
						$user["projects"][$i]["assignment"]["description_url"] = $assignment['description_url'];
						$user["projects"][$i]["assignment"]["allow_late_submission"] = $assignment['allow_late_submission'];
						$user["projects"][$i]["assignment"]["maximum_number_group_members"] = $assignment['maximum_number_group_members'];
					}
				}
			}
			// LF: Updating in the database : Overwriting the user document  
			if (!$collection->update(array("username" => $user["username"]), $user)){
				$update_successful = FALSE;	
			}
		}
		return $update_successful;
	}

	//////////////////////////////////////////////////////////////////
    // LF: Get assignment's name
    //////////////////////////////////////////////////////////////////
    
    public function GetAssignmentName($assignment_id) {
    	$collection = $this->database->users;
    	$users = $collection->find();
		
		// Must check if there is no assignment with the same ID
		foreach ($users as $user) {
			if (isset($user["projects"][0])) {
				for ($i = 0; $i < count($user["projects"]); $i++) {
					if (isset($user["projects"][$i]["assignment"]['id'])) {
						if ($user["projects"][$i]["assignment"]['id'] == $assignment_id) {
							return $user["projects"][$i]["assignment"]['name'];
						}
					}
				}
			}
		}
		
		return "undefined";
	}
	
	//////////////////////////////////////////////////////////////////
    // LF: Delete an assignment
    //////////////////////////////////////////////////////////////////
    
    public function DeleteAssignment($id) {
    	// Just overwrites the assignment to ''
    	$collection = $this->database->users;
		
		$users = $collection->find();
		$update_successful = TRUE;
		
		foreach ($users as $user) {
			for ($i = 0; $i < count($user["projects"]); $i++) {
				if (isset($user["projects"][$i]["assignment"]['id'])) {
					if ($user["projects"][$i]["assignment"]['id'] == $id) {
						//  Get the url of the description url 
						$url = $user["projects"][$i]["assignment"]['description_url'];
						// LF: Remove the assignment from the project array
						$user["projects"][$i]["assignment"] = '';
						$user["projects"][$i]["name"] = "[Deleted] " . $user["projects"][$i]['name'];
						$user["projects"] = array_values($user["projects"]);
					}
				}
			}
			// LF: Updating in the database : Overwriting the user document  
			if (!$collection->update(array("username" => $user["username"]), $user)){
				$update_successful = FALSE;	
			}
		}
		
		$tokens = explode('/', $url);
		$description_file_name = $tokens[sizeof($tokens)-1];
		if ($update_successful) {
			$update_successful = unlink("../../data/assignments/" . $description_file_name);
		}
		
		return $update_successful;
	}
}















