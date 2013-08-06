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

	
	if (isset($data_array[0])) {
		$students = $data_array[0];	
	}
	
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
		$students_with_counters[$k]['username'] = $students[$k];
		$students_with_counters[$k]['count'] = 0;
	}
	
	for ($idx = 0; $idx < count($students); $idx++) {
		
		$Userlogreport = new Userlogreport();
		$Userlogreport -> username =  $students[$idx];
		
		$time_spent  = $Userlogreport->GetTimeSpentInTheSystem();
		$hours_spent =	($time_spent->d*24) +
						($time_spent->h) +
						($time_spent->i / 60) +
						($time_spent->s / 60 / 60);
							
		$students_with_counters[$idx]['count'] = round($hours_spent, 2);
		
	}
	
	header('Content-type: application/json');
	$response_array['status'] = 'success';
	$response_array['students_with_counters'] = $students_with_counters;
	
	
	echo json_encode($response_array);
}
?>