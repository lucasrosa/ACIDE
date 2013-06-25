<?php

    /*
	 *  Copyright (c) UPEI lrosa@upei.ca sbateman@upei.ca
	 */


    require_once('../../common.php');
    require_once('../course/class.course.php');
	require_once('../user/class.user.php');
    //////////////////////////////////////////////////////////////////
    // Verify Session or Key
    //////////////////////////////////////////////////////////////////

    checkSession();

    $Course= new Course();
    

    //////////////////////////////////////////////////////////////////
    // Create Project
    //////////////////////////////////////////////////////////////////

    if($_GET['action']=='create'){
        if(checkAccess()) {
        	$Course->code = $_GET['course_code'];
            $Course->name = $_GET['course_name'];
			// Saving the Course in the database
            if ($Course->Save()) {
            	echo formatJSEND("success");
            }
        }
    }
    
    //////////////////////////////////////////////////////////////////
    // Rename Project
    //////////////////////////////////////////////////////////////////

    if($_GET['action']=='rename'){
        $Project->path = $_GET['project_path'];
		$Project->user = $_SESSION['user'];
		$Project->load();
		$Project->name = $_GET['project_name'];
        $Project->Rename();
    }

    //////////////////////////////////////////////////////////////////
    // Delete Project
    //////////////////////////////////////////////////////////////////

    if($_GET['action'] == 'delete'){
        $Course->id = $_GET['id'];
        if ($Course->Delete()) {
        	echo formatJSEND("success");
        } else {
        	echo formatJSEND("error");
        }
    }

	
	//////////////////////////////////////////////////////////////////
    // Manage Users
    //////////////////////////////////////////////////////////////////

    if($_GET['action'] == 'manage_users'){
    	$Course->id = $_POST["course_id"];
		
		if (isset($_POST['group_user'])) {
			$group_users = $_POST['group_user'];	
		}else {
			$group_users = array();
		}
		
		$success = TRUE;
		
		$User = new User();
		
		$users = $User->users;
		foreach ($users as $user) {
			$User->username = $user['username'];
			if(in_array($user['username'], $group_users)) {
				if (!$User->AddCourse($Course->id)) {
					$success = FALSE;
				}
			} else {
				if (!$User->RemoveCourse($Course->id)) {
					$success = FALSE;
				}
			}
			
		}
		header('Content-type: application/json');
		
		if ($success) {
			$response_array['status'] = 'success'; 	
		} else {
			$response_array['status'] = 'error_database';
		}
		echo json_encode($response_array);
    }

?>