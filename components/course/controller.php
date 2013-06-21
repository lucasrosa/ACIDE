<?php

    /*
	 *  Copyright (c) UPEI lrosa@upei.ca sbateman@upei.ca
	 */


    require_once('../../common.php');
    require_once('../course/class.course.php');
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

    if($_GET['action']=='delete'){
        if(checkAccess()) {
            $Project->path = $_GET['project_path'];
			$Project->user = $_SESSION['user'];
            $Project->Delete();
        }
    }

	
	//////////////////////////////////////////////////////////////////
    // Manage Users
    //////////////////////////////////////////////////////////////////

    if($_GET['action']=='manage_users'){
    	$Project->user = $_SESSION["user"];
		$Project->path = $_POST["project_path"];
		$Project->Load();
		
		if (isset($_POST['group_user'])) {
			$group_users = $_POST['group_user'];	
		}else {
			$group_users = array();
		}
		
		$formated_group_users = array();
		$number_of_users = count($group_users);
		
		$formated_group_users[0] = array("username" => $Project->user);
		
		for($i = 0; $i < $number_of_users; $i++) {
			$formated_group_users[$i+1] = array("username" => $group_users[$i]);
		}
		
		if ($number_of_users > 0) {
			$Project->privacy = "shared";
		} else {
			$Project->privacy = "private";
		}
		
		$Project->group_members = $formated_group_users;
		
		$maximum_number_group_members = $Project->GetMaximumNumberGroupMembers();
		
		header('Content-type: application/json');
		
		if ($maximum_number_group_members > 0) {
			if (($number_of_users + 1) <= $maximum_number_group_members) {
				if ($Project->Save()) {
					$response_array['status'] = 'success'; 	
				} else {
					$response_array['status'] = 'error_database';
				}
			} else {
				$response_array['status'] = 'error_user_maximum_reached'; 
			}
		} else {
			if ($Project->Save()) {
				$response_array['status'] = 'success'; 	
			} else {
				$response_array['status'] = 'error_database';
			}
		}
		echo json_encode($response_array);
    }

?>