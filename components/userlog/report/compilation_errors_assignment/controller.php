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
	
	$course_id = $data_array[3];
	
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
		// Connect
		$mongo_client = new MongoClient();
		// select the database
		$database = $mongo_client -> selectDB(DATABASE_NAME);
		// Select the collection
		$collection = $database -> users;
		
		$users = $User -> GetUsersInCourse($course_id);
		
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
		$raw_assignments = $Project->GetAssignmentsInTheSameCoursesOfUser($current_user, $course_id);
		
		foreach($raw_assignments as $raw_assignment) {
			$assignments[] =$raw_assignment['id'];
		}
	}
	
	$assignments_with_counters = array();
	$students_with_counters = array();
	

	for ($idx = 0; $idx < count($students); $idx++) {
		
		$Userlogreport = new Userlogreport();
		$Userlogreport -> username =  $students[$idx];
		
		$students_with_counters[$idx]['username'] = $students[$idx];
		$students_with_counters[$idx]['total'] = array();
		$students_with_counters[$idx]['failed'] = array();
		
		for ($k = 0; $k < count($assignments); $k++) {
				$project_path = "AS_" . $students[$idx] . "_" . $assignments[$k];
			
				$project_path = Userlogreport::SanitizePath($project_path);
			
				$total_compilation_attempts = $Userlogreport-> GetNumberOfCompilations($project_path);
				error_log("total_compilation_attempts = $total_compilation_attempts");		
				$failed_compilation_attempts = $Userlogreport-> GetNumberOfCompilations($project_path, FALSE);

				$students_with_counters[$idx]['total'][$k] = $total_compilation_attempts;
				$students_with_counters[$idx]['failed'][$k] = $failed_compilation_attempts;
		}
	}
	
	
	header('Content-type: application/json');
	$response_array['status'] = 'success';
	$response_array['students_with_counters'] = $students_with_counters;
	error_log(print_r($response_array['students_with_counters'], TRUE));	
	$response_array['assignments'] = $assignments;
		
	echo json_encode($response_array);
}
?>