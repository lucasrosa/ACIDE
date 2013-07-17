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
    public $username    		= '';
	public $userlog				= '';
	public $user				= '';
	public $student_user_type	= '';
	
    //////////////////////////////////////////////////////////////////
    // METHODS
    //////////////////////////////////////////////////////////////////

    // -----------------------------||----------------------------- //
	
	//////////////////////////////////////////////////////////////////
    // Construct
    //////////////////////////////////////////////////////////////////

    public function __construct () {
    	$this->userlog = new Userlog();
		$this->user = new User();
		$user_types = $this->user->GetUsersTypes();
		$this->student_user_type = $user_types[0];
	}
	
    //////////////////////////////////////////////////////////////////
    // Get Time Spent In The System
    //////////////////////////////////////////////////////////////////

    public function GetTimeSpentInTheSystem (){
    	$this->userlog = new Userlog();
		
		$this->userlog->username = $this->username;
		$sessions = $this->userlog->GetAllSessionsForUser();
		$total_time_system = new DateTime('0000-00-00 00:00:00');
		$total_time_system_helper = clone $total_time_system;
		
		foreach($sessions as $session) {
			$date1 = new DateTime($session['start_timestamp']);
			$date2 = new DateTime($session['last_update_timestamp']);
			
			$interval = $date1->diff($date2);
			$total_time_system->add($interval);
		}
		
		$total_time_system_interval = $total_time_system_helper->diff($total_time_system);
		
		return $total_time_system_interval;
    }
	
	public function GetTimeUserSpentInEachSection () {
		$this->userlog = new Userlog();
		
		$this->userlog->username = $this->username;
		$sessions = $this->userlog->GetAllSessionsForUser();
		$total_time_system = new DateTime('0000-00-00 00:00:00');
		$total_time_system_helper = clone $total_time_system;
		$sections_time = array();
		
		foreach($sessions as $session) {
			$date1 = new DateTime($session['start_timestamp']);
			$date2 = new DateTime($session['last_update_timestamp']);
			
			$interval = $date1->diff($date2);
			//$total_time_system->add($interval);
			$this_section = array();
			$this_section['_id'] = $session['_id'];
			$this_section['interval'] = $interval;
			$sections_time[] = $this_section;
		}
		
		return $sections_time;
	}

	public function GetTimeSpentInProjectsInSession ($session_id, $return_projects_without_logs = TRUE) {
		$this->userlog = new Userlog();
		$this->userlog->username = $this->username;
		$projects_time = array();
		
		$Project = new Project();
		$projects = $Project->GetProjectsForUser($this->username);
				
		foreach($projects as $project) {
			$this->userlog->path = $project['path'];
			$project_sessions = $this->userlog->GetAllLogsForProject($session_id);
			
			if ($project_sessions->count() > 0 || $return_projects_without_logs) {
				$total_time_project = new DateTime('0000-00-00 00:00:00');
				$total_time_project_helper = clone $total_time_project;
			
				foreach ($project_sessions as $project_session) {
					$date1 = new DateTime($project_session['start_timestamp']);
					$date2 = new DateTime($project_session['last_update_timestamp']);
					$interval = $date1->diff($date2);
					
					$total_time_project->add($interval);
				}
			
				$total_time_project_interval = $total_time_project_helper->diff($total_time_project);
				
				$current_project_time = array();
				$current_project_time['path'] = $project['path'];
				$current_project_time['interval'] = $total_time_project_interval;
				$projects_time[] = $current_project_time;
			}
		}
		
		return $projects_time;
	}
	
	
	public function GetTimeSpentInFilesOfProjectInSession ($session_id, $return_files_without_logs = TRUE) {
		$this->userlog = new Userlog();
		$this->userlog->username = $this->username;
		$projects_time = array();
		
		$Project = new Project();
		$projects = $Project->GetProjectsForUser($this->username);
				
		foreach($projects as $project) {
			$this->userlog->path = $project['path'];
			$project_sessions = $this->userlog->GetAllLogsForProject($session_id);
			
			if ($project_sessions->count() > 0 || $return_projects_without_logs) {
				$total_time_project = new DateTime('0000-00-00 00:00:00');
				$total_time_project_helper = clone $total_time_project;
			
				foreach ($project_sessions as $project_session) {
					$date1 = new DateTime($project_session['start_timestamp']);
					$date2 = new DateTime($project_session['last_update_timestamp']);
					$interval = $date1->diff($date2);
					
					$total_time_project->add($interval);
				}
			
				$total_time_project_interval = $total_time_project_helper->diff($total_time_project);
				
				$current_project_time = array();
				$current_project_time['path'] = $project['path'];
				$current_project_time['interval'] = $total_time_project_interval;
				$projects_time[] = $current_project_time;
			}
		}
		
		return $projects_time;
	}

	public function GetTimeSpentInProject ($project_path) {
		$this->userlog = new Userlog();
		$this->userlog->username = $this->username;
		$projects_time = array();
		
		$Project = new Project();
		$Project->path = $project_path;
		//$projects = $Project->GetProjectsForUser($this->username);
				
		
		$this->userlog->path = $project_path;
		$project_sessions = $this->userlog->GetAllLogsForProject();
		
		
		$total_time_project = new DateTime('0000-00-00 00:00:00');
		$total_time_project_helper = clone $total_time_project;
	
		foreach ($project_sessions as $project_session) {
			$date1 = new DateTime($project_session['start_timestamp']);
			$date2 = new DateTime($project_session['last_update_timestamp']);
			$interval = $date1->diff($date2);
			
			$total_time_project->add($interval);
		}
	
		$total_time_project_interval = $total_time_project_helper->diff($total_time_project);
		
		$current_project_time = array();
		$current_project_time['path'] = $project['path'];
		$current_project_time['interval'] = $total_time_project_interval;
		
		
		return $current_project_time;
	}	
}
