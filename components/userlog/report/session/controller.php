<?php

/*
 *  Copyright (c) sbateman@upei.ca, lrosa@upei.ca
 */

require_once ('../class.userlogreport.php');
//////////////////////////////////////////////////////////////////
// Verify Session or Key
//////////////////////////////////////////////////////////////////

checkSession();

$Userlogreport = new Userlogreport();

//////////////////////////////////////////////////////////////////
// Get Data for Chart
//////////////////////////////////////////////////////////////////

if ($_GET['action'] == 'get_data_for_chart') {
	// [0] => students
	// [1] => assignments
	// [2] => group_by
	$data_array = array();
	$data_array = $_POST['data_array'];
	
	$students = array();
	$assignments= array();
	$group_by = NULL;

	
	if (isset($data_array[0])) {
		$students = $data_array[0];	
	}
	if (isset($data_array[1])) {
		$assignments = $data_array[1];
	}
	
	if (isset($data_array[2])) {
		$group_by = $data_array[2];
	}
	// Get data <--
	$compilation_errors = array();

	$error_to_log = "";
	/*
	 * $outputted_errors groups the errors and count them, each position is another array $error
	 */
	$outputted_errors = array();
	$single_error = array();
	
	// Check if there are no students, then load all of them
	if (!isset($students[0]) || count($students[0]) == 0) {
		$User = new User();
		//$User->users = getJSON('users.php');
		// Connect
		$mongo_client = new MongoClient();
		// select the database
		$database = $mongo_client -> codiad_database;
		// Select the collection
		$collection = $database -> users;
		
		$users = $User -> GetUsersInTheSameCoursesOfUser($_SESSION['user']);
		
		$user_types = $User -> GetUsersTypes();
		$student_user_type = $user_types[0];
		
		$students = array();
		
		foreach ($users as $user) {
			if ($user['type'] == $student_user_type) {
				$students[] = $user['username'];
			}
		}
	}
	
	$students_with_counters = array();

	for ($k = 0; $k < count($students); $k++) {
		$this_students_counters = array();
		$this_students_counters['student'] = $students[$k];
		$this_students_counters['count'] = 0;
		$students_with_counters[] = $this_students_counters;
	}
	
	for ($idx = 0; $idx < count($students); $idx++) {
		
		$Userlogreport = new Userlogreport();
		$Userlogreport -> username =  $students[$idx];
		
		if ($group_by == 0) {
			$sessions_times = $Userlogreport->GetTimeUserSpentInEachSection();
			for ($k = 0; $k < count($sessions_times); $k++) {
				$time_spent = $sessions_times[$k]['interval'];
				$minutes_spent =	($time_spent->d*24*60) +
									($time_spent->h*60) +
									($time_spent->i) +
									($time_spent->s / 60);
									
				
					$students_with_counters[$idx]['count'] += $minutes_spent;	
			}
			
			$students_with_counters[$idx]['count'] = round(($students_with_counters[$idx]['count']/count($sessions_times)), 2); 
		} else if ($group_by == 1) {
			$number_of_sessions = $sessions_times = $Userlogreport->GetNumberOfSessions();
			$students_with_counters[$idx]['count'] = $number_of_sessions;
		}
	}
	
	header('Content-type: application/json');
	$response_array['status'] = 'success';
	//$response_array['outputted_errors'] = $outputted_errors;
	$response_array['students_with_counters'] = $students_with_counters;

	//error_log(print_r($response_array['outputted_errors'], true));
	//$response_array['group_by'] = $group_by;
	
	echo json_encode($response_array);
}
?>