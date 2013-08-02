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
	
	// Check if there are no assignments, then load all of them
	if (!isset($assignments[0]) || count($assignments[0]) == 0) {
		$Project = new Project();
		$current_user = $_SESSION['user'];
		$raw_assignments = $Project->GetAssignmentsInTheSameCoursesOfUser($current_user);
		
		foreach($raw_assignments as $raw_assignment) {
			$assignments[] =$raw_assignment['id'];
		}
	}
	$assignments_with_counters = array();
	$students_with_counters = array();
	if ($group_by == 0) {
		for ($k = 0; $k < count($assignments); $k++) {
				$this_assignment_counters = array();
				$this_assignment_counters['assignment'] = $assignments[$k];
				$this_assignment_counters['count'] = 0;
				$assignments_with_counters[] = $this_assignment_counters;		
		}	
	} else {
		for ($k = 0; $k < count($students); $k++) {
			$this_students_counters = array();
			$this_students_counters['student'] = $students[$k];
			$this_students_counters['counters'] = array();
			$students_with_counters[] = $this_students_counters;
		}
	}
	
	
	for ($idx = 0; $idx < count($students); $idx++) {
		
		$Userlogreport = new Userlogreport();
		$Userlogreport -> username =  $students[$idx];
		
		for ($k = 0; $k < count($assignments); $k++) {
			$project_path = "AS_" . $students[$idx] . "_" . $assignments[$k];
			$time_spent = $Userlogreport->GetTimeSpentInProject($project_path);
			$minutes_spent =	($time_spent->d*24*60) +
								($time_spent->h*60) +
								($time_spent->i) +
								($time_spent->s / 60);
								
			if ($group_by == 0 || $group_by == 2) {
				$assignments_with_counters[$k]['count'] += $minutes_spent;	
			} else if ($group_by == 1) {
				$students_with_counters[$idx]['counters'][$k] = round($minutes_spent, 1);
			}
			
		}
		//error_log(print_r($assignments_with_counters, TRUE));
	}
	
	if ($group_by == 2) {
		$students_count = count($students);
		for ($k = 0; $k < count($assignments); $k++) {
			$assignments_with_counters[$k]['count'] = round($assignments_with_counters[$k]['count'] / $students_count);
		}
	}
	
	
	header('Content-type: application/json');
	$response_array['status'] = 'success';
	//$response_array['outputted_errors'] = $outputted_errors;
	if ($group_by == 0 || $group_by == 2) {
		$response_array['assignments_with_counters'] = $assignments_with_counters;	
	} else {
		$response_array['students_with_counters'] = $students_with_counters;
		$response_array['assignments'] = $assignments;
	}
	
	//error_log(print_r($response_array['outputted_errors'], true));
	//$response_array['group_by'] = $group_by;
	
	echo json_encode($response_array);
}
?>