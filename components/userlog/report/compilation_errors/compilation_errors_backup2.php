<?php 
	/*
    *  Copyright (c) UPEI sbateman@upei.ca lrosa@upei.ca
    */
    
	require_once('../../../../common.php');
    require_once('../../../user/class.user.php');
	require_once('../../../project/class.project.php');
//	require_once('class.assignment.php');
	require_once('../../../permission/class.permission.php');
	require_once('../../../course/class.course.php');
	require_once('../../class.userlog.php');
	
	//////////////////////////////////////////////////////////////////
    // This page offers an interface to manage assignments
    //////////////////////////////////////////////////////////////////
	
	//////////////////////////////////////////////////////////////////
    // Verify Session or Key
    //////////////////////////////////////////////////////////////////
	
    checkSession();
	
	//////////////////////////////////////////////////////////////////
    // Defining the user permission
    //////////////////////////////////////////////////////////////////
	$Permission = new Permission($_SESSION['user']);
	
	if (FALSE) {//!($Permission->GetPermissionToSeeAssignments())) {
		echo "Permission Denied";
	} else {
		
		$MainUserlog = new Userlog();
		$MainUserlog -> CloseAllOpenSectionsThatReachedTimeout();
		
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
		
		$compilation_errors = array();
		
		
		$error_to_log = "";
		
		$outputted_errors = array();
		$single_error = array();
		
		
		// Project
		$Project = new Project();
		$Assignment = array();
		
?>
<!doctype html>

<html lang="en">
	<head>
		<meta charset="utf-8" />
		<title>Codiad - Compilation Errors</title>
		  <?php
			// Load System CSS Files
			$stylesheets = array("fonts.css","screen.css");
			   
			    foreach($stylesheets as $sheet){
			        if(file_exists(THEMES . "/". THEME . "/".$sheet)){
			        echo('<link rel="stylesheet" href="../../../../themes/'.THEME.'/'.$sheet.'">');
			    } else {
			        echo('<link rel="stylesheet" href="../../../../themes/default/'.$sheet.'">');
			    }
			}
			?>
		<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
		<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
		<link href="../../../../themes/default/jquery-ui/jquery-ui-1.10.3.custom.vader/css/vader/jquery-ui-1.10.3.custom.css" rel="stylesheet">
		<script src="../../../../themes/default/jquery-ui/jquery-ui-1.10.3.custom.vader/js/jquery-ui-1.10.3.custom.js"></script>

		<style>
			#feedback, #assignments_feedback {
				font-size: 1.4em;
			}
			#assignments_selectable  .ui-selecting, #students_selectable .ui-selecting {
				background: #888888;
			}
			#assignments_selectable .ui-selected, #students_selectable .ui-selected {
				background: #888888;
				color: white;
			}
			#assignments_selectable, #students_selectable {
				list-style-type: none;
				margin: 0;
				padding: 0;
				width: 60%;
			}
			#assignments_selectable li, #students_selectable li {
				margin: 3px;
				padding: 0.4em;
				font-size: 1.4em;
				height: 18px;
			}
		</style>
		<script>
			$(function() {
				
				$("#students_selectable").selectable({
					stop : function() {
						var result = $("#select-result").empty();
						$(".ui-selected", this).each(function() {
							var index = $(this).attr('id');
							result.append("<br> " + index);
						});
					}
				});
				
				$("#assignments_selectable").selectable({
					stop : function() {
						var result = $("#assignments_select-result").empty();
						$(".ui-selected", this).each(function() {
							var index = $(this).attr('id');
							result.append("<br> " + index);
						});
					}
				});
			});
		</script>
	</head>
	<body>
	<div style="width:800px; margin:0 auto;">
		<div style="float: left; width: 50%;">
			<p id="feedback">
				<span>You've selected:</span><span id="select-result">none</span>.
			</p>
			<ol id="students_selectable">
			<?
			foreach ($users as $user) {
				if ($user['type'] == $student_user_type) {
			?>
					
						<li id="<?=$user['username']?>" class="ui-widget-content">
							<?=$user['username']?>
						</li>
					
				<?
				}
			}
			?>
			</ol>
		</div>
		<div style="float: left; width: 50%;">
			<p id="assignments_feedback">
				<span>You've selected:</span><span id="assignments_select-result">none</span>.
			</p>
			<ol id="assignments_selectable">
				<?
				//////////////////////////////////////////////////////////////////
				// LF: List all assignments which this user is the owner
				//////////////////////////////////////////////////////////////////
				$Project = new Project();
				//$assignments = $Project->GetAssignments();
				$current_user = $_SESSION['user'];
				$assignments = $Project->GetAssignmentsInTheSameCoursesOfUser($current_user); 
				
				for ($k = 0; $k < count($assignments); $k++) {
					//$Course->id = $assignments[$k]['course'];
					
				?>
				<li id="<?=$assignments[$k]['name']?>" class="ui-widget-content">
					<?=$assignments[$k]['name']?>
				</li>
				<? } ?>
			</ol>			
		</div>
		<div style="float:none;">
			<ul>
				<input type="radio" id="rd" /> <label>One</label>
				<input type="radio" id="rd" /> <label>Two</label>
				<input type="radio" id="rd" /> <label>Three</label>	
			</ul>
			 
		</div>
	</div>
</html>
<?
}
?>