<?php

/*
 *  Copyright (c) UPEI lrosa@upei.ca sbateman@upei.ca
 */

class Userlog {

    //////////////////////////////////////////////////////////////////
    // PROPERTIES
    //////////////////////////////////////////////////////////////////
	
	// The user which this log is related to
    public $username    = '';
	// Type of Log
	/*
	 * logged_in
	 * logged_out
	 * session
	 */
    public $type 					= '';
	// The timestamp that this log was registered
	public $start_timestamp			= '';
	// The timestamp of the last update
	public $last_update_timestamp 	= '';
	// The last timestamp
	public $end_timestamp			= '';
	// Identify if the session is open
	public $is_open					= '';
	// Path of the subject: can be a file path or a project name (path)
	public $path					= '';
	// Session timeout
	private $session_timeout		= 10; // minutes
	// File timeout
	private $file_timeout			=  5; // seconds
	// Project timeout
	private $project_timeout		=  5; // seconds
	// Terminal timeout
	private $terminal_timeout		=  5; // seconds
	// Session timeout
	private $last_action_timeout   	= 10; // minutes
	// The session ID to be saved in the file
	public  $session_id				= '';
	// The output of a compilation attempt
	public $output					= '';
	// The command of a compilation attempt
	public $command					= '';
	// The language of the compilation
	public $language				= '';
	// Identify if the code was compiled successfuly
	public $succeeded 				= '';
	// Identify if the project was submitted or not
	public $assignment_submitted = '';
	
	/*
	 * TODO Create an array  for timeout like project => 5, terminal -> 2.5, file => 1
	 * so it can be called as $timeout['file'] for example and then change the methos to a single method
	 */ 
	 public $timeouts				= array();
	 public $types					= array();
	
    //////////////////////////////////////////////////////////////////
    // METHODS
    //////////////////////////////////////////////////////////////////

    // -----------------------------||----------------------------- //
	
	//////////////////////////////////////////////////////////////////
    // Construct
    //////////////////////////////////////////////////////////////////

    public function __construct(){
    	$this->types[0] = 'session';
		$this->types[1] = 'project';
		$this->types[2] = 'file';
		$this->types[3] = 'terminal';
		//$this->types[4] = 'compilation_attempt';
		
		$this->timeouts[$this->types[0]] 	= $session_timeout;  // Minutes
		$this->timeouts[$this->types[1]] 	= $project_timeout;  // seconds 
		$this->timeouts[$this->types[2]] 	= $file_timeout;     // seconds
		$this->timeouts[$this->types[3]] 	= $terminal_timeout; // seconds
	}
	
    //////////////////////////////////////////////////////////////////
    // Save
    //////////////////////////////////////////////////////////////////

    public function Save(){
		$collection = $this->GetCollection();
			
		$new_log = array( 	
							"username" => $this->username,
							"type" => $this->type,
							"start_timestamp" => date("Y-m-d H:i:s")
						 );
		
		// Insert the log in the database:
		return $collection->insert($new_log);
    }
	
	//////////////////////////////////////////////////////////////////
    // Save user last action
    //////////////////////////////////////////////////////////////////

    public function SaveAsUserLastAction(){
		$collection = $this->GetCollection();
			
		$new_log = array( 	
							"username" => $this->username,
							"type" => "user_last_action",
							"last_update_timestamp" => date("Y-m-d H:i:s")
						 );
		
		// Insert the log in the database:
		return $collection->insert($new_log);
    }
	
	//////////////////////////////////////////////////////////////////
    // Save Session
    //////////////////////////////////////////////////////////////////

    public function SaveAsSession(){
		$collection = $this->GetCollection();
			
		$new_log = array( 	
							"username" => $this->username,
							"type" => "session",
							"start_timestamp" => date("Y-m-d H:i:s"),
							"last_update_timestamp" => date("Y-m-d H:i:s"),
							"is_open" => 'TRUE'
						 );
		
		// Insert the log in the database:
		return $collection->insert($new_log);
    }
	
	//////////////////////////////////////////////////////////////////
    // Save as File
    //////////////////////////////////////////////////////////////////

    public function SaveAsFile(){
		$collection = $this->GetCollection();
		$project_path = explode("/", $this->path);
		$project_path = $project_path[0];
		
		$this->assignment_submitted = $this->GetAssignmentSubmitted($project_path);
		
		$new_log = array( 	
							"username" => $this->username,
							"type" => "file",
							"start_timestamp" => date("Y-m-d H:i:s"),
							"last_update_timestamp" => date("Y-m-d H:i:s"),
							"is_open" => 'TRUE',
							"session_id" => $this->GetCurrentSessionId(),
							"path" => $this->path,
							"assignment_submitted" => $this->assignment_submitted
						 );
		
		// Insert the log in the database:
		return $collection->insert($new_log);
    }
	
	public function GetAssignmentSubmitted ($project_path = "") {
		// Connect
		$mongo_client = new MongoClient();
		// select the database
		$database = $mongo_client->codiad_database;
		// Select the collection 
		$collection = $database->users;	
		
		$users = $collection->find();
		if ($project_path == "") {
			$project_path = $this->path;
		}
		
		foreach ($users as $user) {
		//$user = $collection->findOne(array("username" => $this->user));
			if (isset($user["projects"][0])) {	
				for ($i = 0; $i < count($user["projects"]); $i++) {
					if ($user["projects"][$i]["path"] ==$project_path) {
						if (isset($user["projects"][$i]["assignment"]['submitted_date'])) {
							return "TRUE";	
						} else {
							return "FALSE";
						}
					}
				}
			}
		}
	}
	//////////////////////////////////////////////////////////////////
    // Save as Project
    //////////////////////////////////////////////////////////////////

    public function SaveAsProject(){
		$collection = $this->GetCollection();
		$this->assignment_submitted = $this->GetAssignmentSubmitted();
		
		$new_log = array( 	
							"username" => $this->username,
							"type" => "project",
							"start_timestamp" => date("Y-m-d H:i:s"),
							"last_update_timestamp" => date("Y-m-d H:i:s"),
							"is_open" => 'TRUE',
							"session_id" => $this->GetCurrentSessionId(),
							"path" => $this->path,
							"assignment_submitted" => $this->assignment_submitted
						 );
		
		// Insert the log in the database:
		return $collection->insert($new_log);
    }
    
	//////////////////////////////////////////////////////////////////
    // Save as Project
    //////////////////////////////////////////////////////////////////

    public function SaveAsTerminal(){
		$collection = $this->GetCollection();
		$this->assignment_submitted = $this->GetAssignmentSubmitted();	
		$new_log = array( 	
							"username" => $this->username,
							"type" => "terminal",
							"start_timestamp" => date("Y-m-d H:i:s"),
							"last_update_timestamp" => date("Y-m-d H:i:s"),
							"is_open" => 'TRUE',
							"session_id" => $this->GetCurrentSessionId(),
							"path" => $this->path,
							"assignment_submitted" => $this->assignment_submitted
						 );
		
		// Insert the log in the database:
		return $collection->insert($new_log);
    }
	
	
	//////////////////////////////////////////////////////////////////
    // Save as Compilation Attempt
    //////////////////////////////////////////////////////////////////

    public function SaveAsCompilationAttempt(){
		$collection = $this->GetCollection();
		
		$project_path = "";
		
		$path_exploded = explode("/", $this->path);
		$workspace_found = FALSE;
		for ($i = 0; $i < count($path_exploded); $i++) {
			
			if ($workspace_found) {
				$project_path = $path_exploded[$i];
				break;
			}
			
			if ($path_exploded[$i] == "workspace") {
				$workspace_found = TRUE;
			}	
		}
		
		$this->path = $project_path;
		$this->assignment_submitted = $this->GetAssignmentSubmitted();
		$new_log = array( 	
							"username" => $this->username,
							"type" => "compilation_attempt",
							"start_timestamp" => date("Y-m-d H:i:s"),
							"session_id" => $this->GetCurrentSessionId(),
							"path" => $project_path,
							"current_path" => $this->path,
							"output" => $this->output,
							"command" => $this->command,
							"language" => $this->language,
							"succeeded" => $this->succeeded,
							"assignment_submitted" => $this->assignment_submitted
						 );
		
		// Insert the log in the database:
		return $collection->insert($new_log);
    }

	//////////////////////////////////////////////////////////////////
    // Update user last action
    //////////////////////////////////////////////////////////////////
    
	public function UpdateUserLastAction(){
		$collection = $this->GetCollection();
			
		$log = $collection->findOne(array("username" => $this->username, "type" =>"user_last_action"));
		if (isset($log['username'])) {
			$log['last_update_timestamp'] = date("Y-m-d H:i:s");
			// Overwrite the log in the database:
			return $collection->update(array("username" => $this->username, "type" =>"user_last_action"), $log);	
		} else {
			$this->SaveAsUserLastAction();
		}
    }
	
	//////////////////////////////////////////////////////////////////
    // Update current session
    //////////////////////////////////////////////////////////////////
    
	public function UpdateCurrentSession(){
		$collection = $this->GetCollection();
			
		$log = $collection->findOne(array("username" => $this->username, "is_open" => 'TRUE', "type" =>"session"));
		if (isset($log['username'])) {
			$now = strtotime(date("Y-m-d H:i:s"));
			$last_update_timestamp = strtotime($log['last_update_timestamp']);
			$time_difference =  $this->DateMinuteDifference ($now, $last_update_timestamp);
			
			if ($time_difference >= $this->session_timeout) {
				$log['is_open']			= 'FALSE';
				
				// Overwrite the log in the database:
				$collection->update(array("username" => $this->username, "is_open" => 'TRUE', "type" =>"session"), $log);
				
				$this->SaveAsSession();
			} else {
				$log['last_update_timestamp'] = date("Y-m-d H:i:s");
				// Overwrite the log in the database:
				return $collection->update(array("username" => $this->username, "is_open" => 'TRUE', "type" =>"session"), $log);	
			}
		} else {
			$this->SaveAsSession();
		}
    }
	
	//////////////////////////////////////////////////////////////////
    // Update current file session
    //////////////////////////////////////////////////////////////////
	
	public function UpdateCurrentFile(){
		$collection = $this->GetCollection();
		$current_type = "file";
		$project_path = explode("/", $this->path);
		$project_path = $project_path[0];
		
		$this->assignment_submitted = $this->GetAssignmentSubmitted($project_path);
		
		$log = $collection->findOne(array("username" => $this->username, "is_open" => 'TRUE', "type" => $current_type, 'path' => $this->path));
		if (isset($log['username'])) {
			$now = strtotime(date("Y-m-d H:i:s"));
			$last_update_timestamp = strtotime($log['last_update_timestamp']);
			$time_difference =  $this->DateSecondDifference($now, $last_update_timestamp);
			
			if ($time_difference >= $this->project_timeout) {
				// Update all the other logs for files to closed
				$collection->update(
				    array("username" => $this->username, "is_open" => 'TRUE', "type" => $current_type),
				    array('$set' => array('is_open' => "FALSE")),
				    array("multiple" => true)
				);
				
				$this->SaveAsFile();
			} else {
				$log['last_update_timestamp'] = date("Y-m-d H:i:s");
				$log["assignment_submitted"] = $this->assignment_submitted;
				
				// Overwrite the log in the database:
				return $collection->update(array("username" => $this->username, "is_open" => 'TRUE', "type" => $current_type, 'path' => $this->path), $log);	
			}
		} else {
			$collection->update(
			    array("username" => $this->username, "is_open" => 'TRUE', "type" => $current_type),
			    array('$set' => array('is_open' => "FALSE")),
			    array("multiple" => true)
			);
				
			$this->SaveAsFile();
		}
    }

	//////////////////////////////////////////////////////////////////
    // Update current project session
    //////////////////////////////////////////////////////////////////
	
	public function UpdateCurrentProject(){
		$collection = $this->GetCollection();
		$current_type = "project";
		$this->assignment_submitted = $this->GetAssignmentSubmitted();
		$log = $collection->findOne(array("username" => $this->username, "is_open" => 'TRUE', "type" => $current_type, 'path' => $this->path));
		if (isset($log['username'])) {
			$now = strtotime(date("Y-m-d H:i:s"));
			$last_update_timestamp = strtotime($log['last_update_timestamp']);
			$time_difference =  $this->DateSecondDifference($now, $last_update_timestamp);
			
			if ($time_difference >= $this->project_timeout) {
				// Update all the other logs for files to closed
				$collection->update(
				    array("username" => $this->username, "is_open" => 'TRUE', "type" => $current_type),
				    array('$set' => array('is_open' => "FALSE")),
				    array("multiple" => true)
				);
				
				$this->SaveAsProject();
			} else {
				$log['last_update_timestamp'] = date("Y-m-d H:i:s");
				$log["assignment_submitted"] = $this->assignment_submitted;
				
				// Overwrite the log in the database:
				return $collection->update(array("username" => $this->username, "is_open" => 'TRUE', "type" => $current_type, 'path' => $this->path), $log);	
			}
		} else {
			$collection->update(
			    array("username" => $this->username, "is_open" => 'TRUE', "type" => $current_type),
			    array('$set' => array('is_open' => "FALSE")),
			    array("multiple" => true)
			);
				
			$this->SaveAsProject();
		}
    }
	
	//////////////////////////////////////////////////////////////////
    // Update current terminal session
    //////////////////////////////////////////////////////////////////
	
	public function UpdateCurrentTerminal(){
		$collection = $this->GetCollection();
		$current_type = "terminal";
		$this->assignment_submitted = $this->GetAssignmentSubmitted();
		$log = $collection->findOne(array("username" => $this->username, "is_open" => 'TRUE', "type" => $current_type, 'path' => $this->path));
		if (isset($log['username'])) {
			$now = strtotime(date("Y-m-d H:i:s"));
			$last_update_timestamp = strtotime($log['last_update_timestamp']);
			$time_difference =  $this->DateSecondDifference($now, $last_update_timestamp);
			
			if ($time_difference >= $this->terminal_timeout) {
				
				//$log['is_open']	= 'FALSE';
				
				// Overwrite the log in the database:
				//$collection->update(array("username" => $this->username, "is_open" => 'TRUE', "type" => $current_type, 'path' => $this->path), $log);
				
				// Update all the other logs for files to closed
				$collection->update(
				    array("username" => $this->username, "is_open" => 'TRUE', "type" => $current_type),
				    array('$set' => array('is_open' => "FALSE")),
				    array("multiple" => true)
				);
				
				$this->SaveAsTerminal();
			} else {
				$log['last_update_timestamp'] = date("Y-m-d H:i:s");
				$log["assignment_submitted"] = $this->assignment_submitted;
				// Overwrite the log in the database:
				return $collection->update(array("username" => $this->username, "is_open" => 'TRUE', "type" => $current_type, 'path' => $this->path), $log);	
			}
		} else {
			$collection->update(
			    array("username" => $this->username, "is_open" => 'TRUE', "type" => $current_type),
			    array('$set' => array('is_open' => "FALSE")),
			    array("multiple" => true)
			);
				
			$this->SaveAsTerminal();
		}
    }
	
	//////////////////////////////////////////////////////////////////
    // GetCollection
    //////////////////////////////////////////////////////////////////
    
	private function GetCollection() {
		// Connect
		$mongo_client = new MongoClient();
		// select the database
		$database = $mongo_client->codiad_database;
		// Select the collection 
		$collection = $database->logs;
		// Return the collection
		return $collection;
	}
	
	private function GetCurrentSessionId() {
		$collection = $this->GetCollection();
		$log = $collection->findOne(array("username" => $this->username, "is_open" => 'TRUE', "type" =>"session"));
		
		if (!isset($log['username'])) {
			$this->SaveAsSession();
			$log = $collection->findOne(array("username" => $this->username, "is_open" => 'TRUE', "type" =>"session"));
		} 
		
		return $log['_id'];
	}
	
	private function DateMinuteDifference ($date1timestamp, $date2timestamp) {
		
		$all = round(($date1timestamp - $date2timestamp) / 60);
		$day = floor ($all / 1440);
		$hour = floor (($all - $day * 1440) / 60);
		$minute = $all - ($day * 1440) - ($hour * 60);
		
		return $minute;
	}
	
	private function DateSecondDifference ($date1timestamp, $date2timestamp) {
		return ($date1timestamp - $date2timestamp);
	}
	
	
	public function GetAllSessionsForUser () {
		$collection = $this->GetCollection();
		return $collection->find(array("username" => $this->username, "is_open" => 'FALSE', "type" =>"session"));
	}
	
	public function GetAllLogsForProject ($session = NULL) {
		$collection = $this->GetCollection();
		if ($session == NULL) {
			return $collection->find(array("username" => $this->username, "is_open" => 'FALSE', "type" =>"project", "path" => $this->path, "assignment_submitted" => 'FALSE'));
		} else {
			return $collection->find(array("username" => $this->username, "is_open" => 'FALSE', "type" =>"project", "path" => $this->path, "session_id" => $session, "assignment_submitted" => 'FALSE'));
		}
	}
	
	public function GetAllLogsForFile ($session = NULL) {
		$collection = $this->GetCollection();
		if ($session == NULL) {
			return $collection->find(array("username" => $this->username, "is_open" => 'FALSE', "type" =>"file", "path" => $this->path, "assignment_submitted" => 'FALSE'));
		} else {
			return $collection->find(array("username" => $this->username, "is_open" => 'FALSE', "type" =>"file", "path" => $this->path, "session_id" => $session, "assignment_submitted" => 'FALSE'));
		}
	}
	
	public function GetAllLogsForTerminalInThisProject ($session = NULL) {
		$collection = $this->GetCollection();
		if ($session == NULL) {
			return $collection->find(array("username" => $this->username, "is_open" => 'FALSE', "type" =>"terminal", "path" => $this->path, "assignment_submitted" => 'FALSE'));
		} else {
			return $collection->find(array("username" => $this->username, "is_open" => 'FALSE', "type" =>"terminal", "path" => $this->path, "session_id" => $session, "assignment_submitted" => 'FALSE'));
		}
	}
	
	public function GetAllLogsForTerminal ($session = NULL) {
		$collection = $this->GetCollection();
		if ($session == NULL) {
			return $collection->find(array("username" => $this->username, "is_open" => 'FALSE', "type" => "terminal", "assignment_submitted" => 'FALSE'));
		} else {
			return $collection->find(array("username" => $this->username, "is_open" => 'FALSE', "type" => "terminal", "session_id" => $session, "assignment_submitted" => 'FALSE'));
		}
	}
	
	public function GetAllLogsForCompilationAttempt ($succeeded = NULL) {
		$collection = $this->GetCollection();
		
		if ($this->path == "" && $succeeded === NULL) {
			return $collection->find(array("username" => $this->username, "type" => "compilation_attempt", "assignment_submitted" => 'FALSE'));	
		} else if ($succeeded === NULL) {
			return $collection->find(array("username" => $this->username, "type" => "compilation_attempt", "path" => $this->path, "assignment_submitted" => 'FALSE'));
		} else {
			$succeeded_string = "";
			
			if ($succeeded) {
				$succeeded_string = "TRUE";
			} else {
				$succeeded_string = "FALSE";
			}
			
			if ($this->path == "") {
				return $collection->find(array("username" => $this->username, "type" => "compilation_attempt", "succeeded" => $succeeded_string, "assignment_submitted" => 'FALSE'));	
			} else {
				return $collection->find(array("username" => $this->username, "type" => "compilation_attempt", "succeeded" => $succeeded_string, "path" => $this->path, "assignment_submitted" => 'FALSE'));
			}
			
		}
	}
	
	public function CloseAllOpenSectionsThatReachedTimeout() {
		/*
		 * Session
		 * Project
		 * Terminal
		 * File
		 */
		$collection = $this->GetCollection();
		
		for ($i = 0; $i < count($this->types); $i++) {
			
			$type = $this->types[$i];
		
			$logs = $collection->find(array("is_open" => 'TRUE', "type" =>$type));
			
			
			foreach ($logs as $log) {
				$now = strtotime(date("Y-m-d H:i:s"));
				$last_update_timestamp = strtotime($log['last_update_timestamp']);
				
				if ($this->type == $this->types[0]) { // 0 = session
					$time_difference =  $this->DateMinuteDifference ($now, $last_update_timestamp);
				} else {
					$time_difference =  $this->DateSecondDifference($now, $last_update_timestamp);
				}
				
				if ($time_difference >= $this->timeouts[$type]) {
					$log['is_open'] = 'FALSE';
					$collection->update(array("_id" => $log['_id']), $log);
				}
			}
		}
	}
	
	public function CloseAllOpenSectionsThatReachedTimeoutOfUserLastAction() {
		
		$collection = $this->GetCollection();
		
	
		$log = $collection->findOne(array("username" => $this->username, "type" =>"user_last_action"));
		
		$now = strtotime(date("Y-m-d H:i:s"));
		$last_update_timestamp = strtotime($log['last_update_timestamp']);
		$time_difference =  $this->DateMinuteDifference ($now, $last_update_timestamp);
		
		
		if ($time_difference >= $this->last_action_timeout) {
			$collection->update(
			    array("username" => $this->username, "is_open" => 'TRUE'),
			    array('$set' => array('is_open' => "FALSE")),
			    array("multiple" => true)
			);
		}	
	}
	
}
