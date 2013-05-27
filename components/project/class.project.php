<?php

/*
*  Copyright (c) Codiad & Kent Safranski (codiad.com), distributed
*  as-is and without warranty under the MIT License. See
*  [root]/license.txt for more. This information must remain intact.
*/

require_once('../../common.php');

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
	// LF: Assignment name is the name of the zip file when submitting the project as an assignment
	public $assignmentName	 	= '';
	// LF: Submitted identifies if a project was already submitted or not :: can be true or false
	public $submitted 			= '';
	// LF: Actual database
	public $database			= '';
	// LF: The members of the group with access to this project
	public $group_members		= '';
	// LF: The assignment related to this project
	public $assignment 			= '';
	
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
		
		$user = $collection->findOne(array("username" => $this->user));
		for ($i = 0; $i < count($user["projects"]); $i++) {
			if ($user["projects"][$i]["path"] == $this->path) {
				$pass = true;
	            $this->name = $user["projects"][$i]["name"];
				$this->privacy = $user["projects"][$i]["privacy"];
				$this->group_members = $user["projects"][$i]["group_members"];
				$this->assignment = $user["projects"][$i]["assignment"];
	            $_SESSION['project'] = $user["projects"][$i]["path"];
			}
		}
		
		return $pass;
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

    public function CreateProjectOnDatabase(){
    	$this->user = $_SESSION['user'];
        $project = array(
						"name" => $this->name,
						"path" => $this->path,
						"privacy" => $this->privacy,
						"group_members" => array(
													array(
															"username" => $this->user,
														 )														
												),
						"assignment" => ''
					 );
					 
		$collection = $this->database->users;
		// LF: Find the current user
		$user = $collection->findOne(array("username" => $this->user));
		// LF: Push the new project in the end of the project's array
		array_push($user["projects"], $project);
		// LF: Saves the new array in the database by overwriting the previous user
		$collection->update(array("username" => $this->user), $user);
		
    }
	
    //////////////////////////////////////////////////////////////////
    // LF: Create
    //////////////////////////////////////////////////////////////////

    public function Create(){
        if($this->name != '' && $this->path != '') {
            $this->path = $this->cleanPath();
            if(strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' || !$this->isAbsPath($this->path)) {
                $this->path = $this->SanitizePath();
            }
            $pass = $this->checkDuplicate();
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
                //$this->projects[] = array("name"=>$this->name,"path"=>$this->path,"privacy"=>$this->privacy,"user"=>$_SESSION['user']);
                //saveJSON('projects.php',$this->projects);
				$this->CreateProjectOnDatabase();
				
                // Pull from Git Repo?
                if($this->gitrepo){
                    if(!$this->isAbsPath($this->path)) {
                        $this->command_exec = "cd " . WORKSPACE . '/' . $this->path . " && git init && git remote add origin " . $this->gitrepo . " && git pull origin " . $this->gitbranch;
                    } else {
                        $this->command_exec = "cd " . $this->path . " && git init && git remote add origin " . $this->gitrepo . " && git pull origin " . $this->gitbranch;
                    }
                    $this->ExecuteCMD();
                }
                
                echo formatJSEND("success",array("name"=>$this->name,"path"=>$this->path));
            }else{
                echo formatJSEND("error","A Project With the Same Name or Path Exists");
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
		
		$projectName = $_SESSION['user'] . " - ".$this->assignmentName;
		$this->name = '[S] ' . $this->name;
		$this->assignment["submitted_date"] = new MongoDate(strtotime(date("Y-m-d H:i:s")));
		$this->save();
		
        // Save array back to JSON
        zipJSON($this->path, $projectName);
		
		// Response
		echo formatJSEND("success",null);	
    }

    //////////////////////////////////////////////////////////////////
    // LF:  Delete Project
    //////////////////////////////////////////////////////////////////
	
	public function Delete(){
        $collection = $this->database->users;
		
		// LF: Find the current user
		$user = $collection->findOne(array("username" => $this->user));
			for ($i = 0; $i < count($user["projects"]); $i++) {
				// LF: The project is selected based on the path :-> As it is the project's id
				if ($user["projects"][$i]["path"] == $this->path) {
					// LF: Remove the project from the projects array
					unset($user["projects"][$i]);
					$user["projects"] = array_values($user["projects"]);
				}
			}
		// LF: Updating in the database : Overwriting the user document 	
		if($collection->update(array("username" => $this->user), $user)) {
			// Response
        	echo formatJSEND("success",null);	
		}
    }

    //////////////////////////////////////////////////////////////////
    // Check Duplicate
    //////////////////////////////////////////////////////////////////

    public function CheckDuplicate(){
    	// must check the user's project name and all the projects paths
        $pass = true;
        $collection = $this->database->users;
		
		// LF: Find the current user and verifies the name of all of its projects
		$user = $collection->findOne(array("username" => $this->user));
		for ($i = 0; $i < count($user["projects"]); $i++) {
			if ($user["projects"][$i]["name"] == $this->name) {
				$pass = false;
			}	
		}
		
		// LF: Looking if the current path is equal of at least one of the saved paths
		$user = '';
		$users = $collection->find();
		foreach ($users as $user) {
			for ($i = 0; $i < count($user["projects"]); $i++) {
				if ($user["projects"][$i]["path"] == $this->path) {
					$pass = false;
				}
			}
		}	

        return $pass;
    }

    public function CheckDuplicate_old(){
    	// must check the user's project name and all the projects paths
        $pass = true;
        foreach($this->projects as $project=>$data){
            if($data['name']==$this->name || $data['path']==$this->path){
                $pass = false;
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
		$user = $collection->findOne(array("username" => $username), array("_id" => FALSE, "projects" => TRUE));
		return $user["projects"];
    }
	
	//////////////////////////////////////////////////////////////////
    // Save this project
    //////////////////////////////////////////////////////////////////
    		
	public function Save() {
		$collection = $this->database->users;
		
		// LF: Find the current user
		$user = $collection->findOne(array("username" => $this->user));
		for ($i = 0; $i < count($user["projects"]); $i++) {
			// LF: The project is selected based on the path :-> As it is the project's id
			if ($user["projects"][$i]["path"] == $this->path) {
				$user["projects"][$i]["name"] = $this->name;
				$user["projects"][$i]["privacy"] = $this->privacy;
				$user["projects"][$i]["group_members"] = $this->group_members;
				$user["projects"][$i]["assignment"] = $this->assignment;
			}
		}
		// LF: Updating in the database : Overwriting the user document 	
		return $collection->update(array("username" => $this->user), $user);
    }
	
}
