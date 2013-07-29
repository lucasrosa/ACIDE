<?php

    /*
    *  Copyright (c) Codiad & Kent Safranski (codiad.com), distributed
    *  as-is and without warranty under the MIT License. See
    *  [root]/license.txt for more. This information must remain intact.
    */


    require_once('../../../../common.php');
    require_once('../class.userlogreport.php');
    //////////////////////////////////////////////////////////////////
    // Verify Session or Key
    //////////////////////////////////////////////////////////////////

    checkSession();

    $Userlogreport = new Userlogreport();
	
	//////////////////////////////////////////////////////////////////
    // Get Data for Chart
    //////////////////////////////////////////////////////////////////

    if($_GET['action'] == 'get_data_for_chart'){
    	/*
		
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
		*/
		
		header('Content-type: application/json');		
		$response_array['status'] = 'success';
		$response_array['robert'] = 'downey'; 	
			
		echo json_encode($response_array);
    }

?>