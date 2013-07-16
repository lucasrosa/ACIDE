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
	
}
