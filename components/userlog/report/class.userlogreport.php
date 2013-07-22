<?php

/*
 *  Copyright (c) UPEI lrosa@upei.ca sbateman@upei.ca
 */
require_once ('../../../common.php');
require_once ('../class.userlog.php');
require_once ('../../user/class.user.php');
require_once ('../../project/class.project.php');

class Userlogreport {

	//////////////////////////////////////////////////////////////////
	// PROPERTIES
	//////////////////////////////////////////////////////////////////

	// The user which this log is related to
	public $username = '';
	public $userlog = '';
	public $user = '';
	public $student_user_type = '';

	//////////////////////////////////////////////////////////////////
	// METHODS
	//////////////////////////////////////////////////////////////////

	// -----------------------------||----------------------------- //

	//////////////////////////////////////////////////////////////////
	// Construct
	//////////////////////////////////////////////////////////////////

	public function __construct() {
		$this->userlog = new Userlog();
		$this->user = new User();
		$user_types = $this -> user -> GetUsersTypes();
		$this->student_user_type = $user_types[0];
	}

	//////////////////////////////////////////////////////////////////
	// Get Time Spent In The System
	//////////////////////////////////////////////////////////////////

	public function GetTimeSpentInTheSystem() {
		$this -> userlog = new Userlog();

		$this -> userlog -> username = $this -> username;
		$sessions = $this -> userlog -> GetAllSessionsForUser();
		$total_time_system = new DateTime('0000-00-00 00:00:00');
		$total_time_system_helper = clone $total_time_system;

		foreach ($sessions as $session) {
			$date1 = new DateTime($session['start_timestamp']);
			$date2 = new DateTime($session['last_update_timestamp']);

			$interval = $date1 -> diff($date2);
			$total_time_system -> add($interval);
		}

		$total_time_system_interval = $total_time_system_helper -> diff($total_time_system);

		return $total_time_system_interval;
	}

	public function GetTimeUserSpentInEachSection() {
		$this -> userlog = new Userlog();

		$this -> userlog -> username = $this -> username;
		$sessions = $this -> userlog -> GetAllSessionsForUser();
		$total_time_system = new DateTime('0000-00-00 00:00:00');
		$total_time_system_helper = clone $total_time_system;
		$sections_time = array();

		foreach ($sessions as $session) {
			$date1 = new DateTime($session['start_timestamp']);
			$date2 = new DateTime($session['last_update_timestamp']);

			$interval = $date1 -> diff($date2);
			//$total_time_system->add($interval);
			$this_section = array();
			$this_section['_id'] = $session['_id'];
			$this_section['interval'] = $interval;
			$sections_time[] = $this_section;
		}

		return $sections_time;
	}

	public function GetTimeSpentInProjectsInSession($session_id, $return_projects_without_logs = TRUE) {
		$this -> userlog = new Userlog();
		$this -> userlog -> username = $this -> username;
		$projects_time = array();

		$Project = new Project();
		$projects = $Project -> GetProjectsForUser($this -> username);

		foreach ($projects as $project) {
			$this -> userlog -> path = $project['path'];
			$project_sessions = $this -> userlog -> GetAllLogsForProject($session_id);

			if ($project_sessions -> count() > 0 || $return_projects_without_logs) {
				$total_time_project = new DateTime('0000-00-00 00:00:00');
				$total_time_project_helper = clone $total_time_project;

				foreach ($project_sessions as $project_session) {
					$date1 = new DateTime($project_session['start_timestamp']);
					$date2 = new DateTime($project_session['last_update_timestamp']);
					$interval = $date1 -> diff($date2);

					$total_time_project -> add($interval);
				}

				$total_time_project_interval = $total_time_project_helper -> diff($total_time_project);

				$current_project_time = array();
				$current_project_time['path'] = $project['path'];
				$current_project_time['interval'] = $total_time_project_interval;
				$projects_time[] = $current_project_time;
			}
		}

		return $projects_time;
	}

	public function GetTimeSpentInFilesOfProjectInSession($session_id, $return_files_without_logs = TRUE) {
		$this -> userlog = new Userlog();
		$this -> userlog -> username = $this -> username;
		$projects_time = array();

		$Project = new Project();
		$projects = $Project -> GetProjectsForUser($this -> username);

		foreach ($projects as $project) {
			$this -> userlog -> path = $project['path'];
			$project_sessions = $this -> userlog -> GetAllLogsForProject($session_id);

			if ($project_sessions -> count() > 0 || $return_projects_without_logs) {
				$total_time_project = new DateTime('0000-00-00 00:00:00');
				$total_time_project_helper = clone $total_time_project;

				foreach ($project_sessions as $project_session) {
					$date1 = new DateTime($project_session['start_timestamp']);
					$date2 = new DateTime($project_session['last_update_timestamp']);
					$interval = $date1 -> diff($date2);

					$total_time_project -> add($interval);
				}

				$total_time_project_interval = $total_time_project_helper -> diff($total_time_project);

				$current_project_time = array();
				$current_project_time['path'] = $project['path'];
				$current_project_time['interval'] = $total_time_project_interval;
				$projects_time[] = $current_project_time;
			}
		}

		return $projects_time;
	}

	public function GetTimeSpentInProject($project_path) {

		$this -> userlog = new Userlog();
		$this -> userlog -> username = $this -> username;
		$projects_time = array();

		$Project = new Project();
		$Project -> path = $project_path;
		//$projects = $Project->GetProjectsForUser($this->username);

		$this -> userlog -> path = $project_path;
		$project_sessions = $this -> userlog -> GetAllLogsForProject();

		$total_time_project = new DateTime('0000-00-00 00:00:00');
		$total_time_project_helper = clone $total_time_project;

		foreach ($project_sessions as $project_session) {
			$date1 = new DateTime($project_session['start_timestamp']);
			$date2 = new DateTime($project_session['last_update_timestamp']);
			$interval = $date1 -> diff($date2);

			$total_time_project -> add($interval);
		}

		$total_time_project_interval = $total_time_project_helper -> diff($total_time_project);

		return $total_time_project_interval;
	}

	public function GetTimeSpentInFileOfProject($file_name, $project_path) {

		$project_directory = WORKSPACE . "/" . $project_path;

		$files = $this -> listdir($project_directory);
		// $files = listdir('.');

		for ($j = 0; $j < count($files); $j++) {
			$exploded_path = explode("/", $files[$j]);
			$filename = "";
			$workspace_folder_index = 0;

			// get filename
			for ($k = 0; $k < count($exploded_path); $k++) {

				if ($exploded_path[$k] == "workspace") {
					$workspace_folder_index = $k;
				}
				// If the iteration passed the workspace, start setting the name of the file
				if ($k > $workspace_folder_index && $workspace_folder_index > 0) {
					if ($k > ($workspace_folder_index + 1)) {
						$filename .= "/";
					}
					$filename .= $exploded_path[$k];
				}
			}
			if ($filename == $file_name) {
				$FileUserlog = new Userlog();
				$FileUserlog -> username = $Userlog -> username;
				$FileUserlog -> path = $filename;
				$file_sessions = $FileUserlog -> GetAllLogsForFile($session['_id']);

				$name_of_file = explode("/", $filename);
				$name_of_file = $name_of_file[count($name_of_file) - 1];

				$total_time_file = new DateTime('0000-00-00 00:00:00');
				$total_time_file_helper = clone $total_time_file;

				foreach ($file_sessions as $file_session) {
					$date1 = new DateTime($file_session['start_timestamp']);
					$date2 = new DateTime($file_session['last_update_timestamp']);
					$interval = $date1 -> diff($date2);

					$total_time_file -> add($interval);
				}

				$total_time_file_interval = $total_time_file_helper -> diff($total_time_file);

				return $total_time_file_interval;
			}
		}
	}
	
	// If $project_path != null, the method will return the terminal logs related to that project
	public function GetTimeUserSpentInTerminal ($session_id = NULL, $project_path = NULL) {
		
		$this -> userlog -> username = $this -> username;
		$terminal_sessions = NULL;
		if ($project_path == NULL) {
			$terminal_sessions = $this->userlog->GetAllLogsForTerminal($session_id);	
		} else {
			$this->userlog->path = $project_path;
			$terminal_sessions = $this->userlog->GetAllLogsForTerminalInThisProject($session_id);
		}
		
		
		$total_time_terminal = new DateTime('0000-00-00 00:00:00');
		$total_time_terminal_helper = clone $total_time_terminal;
	
		foreach ($terminal_sessions as $terminal_session) {
			$date1 = new DateTime($terminal_session['start_timestamp']);
			$date2 = new DateTime($terminal_session['last_update_timestamp']);
			$interval = $date1->diff($date2);
			
			$total_time_terminal->add($interval);
		}
		
		$total_time_terminal_interval = $total_time_terminal_helper->diff($total_time_terminal);
		
		return $total_time_terminal_interval;
	}
	
	// Success means to return: if TRUE only succeeded compilations, if FALSE only failed, if NULL both 
	public function GetAllCompilationAttempts ($success = NULL) {
		$Compilation_userlog = new Userlog();
		$Compilation_userlog->username = $this->username;
		
		if ($success === NULL) {
			return $Compilation_userlog->GetAllLogsForCompilationAttempt();	
		} else {
			$Compilation_userlog->GetAllLogsForCompilationAttempt($success);
		}
	}
	
	public function GetNumberOfCompilations ($path = "", $succeeded = NULL) {
		$Compilation_userlog = new Userlog();
		$Compilation_userlog->username = $this->username;
		
		if ($path != "") {
			$Compilation_userlog->path = $path;	
		}
		
		$compilation_attempts = $Compilation_userlog->GetAllLogsForCompilationAttempt($succeeded);
		$compilation_attempts_count = $compilation_attempts->count();
		
		return $compilation_attempts_count;
			
	}

	public function listdir($start_dir = '.') {

		$files = array();
		if (is_dir($start_dir)) {
			$fh = opendir($start_dir);
			while (($file = readdir($fh)) !== FALSE) {
				# loop through the files, skipping . and .., and recursing if necessary
				if (strcmp($file, '.') == 0 || strcmp($file, '..') == 0 || strcmp($file[0], '.') == 0)
					continue;
				$filepath = $start_dir . '/' . $file;
				if (is_dir($filepath))
					$files = array_merge($files, $this -> listdir($filepath));
				else
					array_push($files, $filepath);
			}
			closedir($fh);
		} else {
			# false if the function was called with an invalid non-directory argument
			$files = false;
		}

		return $files;

	}

}
