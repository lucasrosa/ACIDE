<?php

    /*
    *  Copyright (c) Codiad & Kent Safranski (codiad.com), distributed
    *  as-is and without warranty under the MIT License. See
    *  [root]/license.txt for more. This information must remain intact.
    */


    require_once('../../common.php');
    require_once('class.project.php');
    //////////////////////////////////////////////////////////////////
    // Verify Session or Key
    //////////////////////////////////////////////////////////////////

    checkSession();

    $Project = new Project();

    //////////////////////////////////////////////////////////////////
    // Get Current Project
    //////////////////////////////////////////////////////////////////

    $no_return = false;
    if(isset($_GET['no_return'])){ $no_return = true; }

    if($_GET['action']=='get_current'){
        if(!isset($_SESSION['project'])){
            // Load default/first project
            if($no_return){ $this->no_return = true; }
            $Project->GetFirst();
        }else{
            // Load current
            $Project->path = $_SESSION['project'];
			$Project->user = $_SESSION['user'];
			$Project->load();
			if (isset($Project->assignment['description_url'])) {
				$description_url = $Project->assignment['description_url'];
			} else {
				$description_url = "null";
			}
			
            $project_name = $Project->GetName();
            if(!$no_return){ echo formatJSEND("success",array("name"=>$project_name,"path"=>$_SESSION['project'], "description_url"=>$description_url)); }
        }
    }

    //////////////////////////////////////////////////////////////////
    // Open Project
    //////////////////////////////////////////////////////////////////

    if($_GET['action']=='open'){
        $Project->path = $_GET['path'];
		// LF : Set the user of the project to be open
		$Project->user = $_SESSION['user'];
        $Project->Open();
    }

    //////////////////////////////////////////////////////////////////
    // Create Project
    //////////////////////////////////////////////////////////////////

    if($_GET['action']=='create'){
        if(checkAccess()) {
            $Project->name = $_GET['project_name'];
			$Project->privacy = $_GET['project_privacy'];
			
            if($_GET['project_path'] != '') {
                $Project->path = $_GET['project_path'];
            } else {
                $Project->path = $_GET['project_name'];
            }
            // Git Clone?
            if(!empty($_GET['git_repo'])){
                $Project->gitrepo = $_GET['git_repo'];
                $Project->gitbranch = $_GET['git_branch'];
            }
			
			// LF: Define the name of the path
			if($_GET['project_privacy'] == 'private') {
				$Project->path = $_SESSION['user'] . "-" . $Project->path;
			}
			
            $Project->Create();
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
    // LF: Submit Project
    //////////////////////////////////////////////////////////////////

    if($_GET['action']=='submit'){
        $Project->path = $_GET['project_path'];
		$Project->user = $_SESSION['user'];
		$Project->load();
        $Project->Submit();
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
    // Return Current
    //////////////////////////////////////////////////////////////////

    if($_GET['action']=='current'){
        if(isset($_SESSION['project'])){
            echo formatJSEND("success",$_SESSION['project']);
        }else{
            echo formatJSEND("error","No Project Returned");
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