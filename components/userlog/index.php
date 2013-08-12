<?php 
	/*
    *  Copyright (c) UPEI sbateman@upei.ca lrosa@upei.ca
    */
    
	require_once('../../common.php');
	require_once('../permission/class.permission.php');
	require_once('../user/class.user.php');
	require_once('../course/class.course.php');
	
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
	
	if (!($Permission->GetPermissionToSeeAssignments())) {
		echo "Permission Denied";
	} else {
		
		$pageURL = 'http';
		
		if (@$_SERVER["HTTPS"] == "on") {
			$pageURL .= "s";
		}
			$pageURL .= "://";
		if ($_SERVER["SERVER_PORT"] != "80") {
			 $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"];
		} else {
			$pageURL .= $_SERVER["SERVER_NAME"];
		}
		
		$dir =  dirname($_SERVER['PHP_SELF']);
		$dirs = explode('/', $dir);
		$pageURL .= "/" . $dirs[1];
		
		
		
		/*
		 * Set the user
		 */
		$User = new User();
		$User->username = $_SESSION['user'];
		$user_courses = $User->GetUserCourses();
			
			
		if (isset($_POST['action']) && $_POST['action'] ==  'generate_chart') {
			$report = $_POST['report'];
			$direct_page = $pageURL . "/components/userlog/report";
			switch ($report) {
				case 'compilation-errors':
						header("Location: ". $direct_page . "/compilation_errors/compilation_errors.php");
					break;
				case 'compilation-errors-assignment':
						header("Location: ". $direct_page . "/compilation_errors_assignment/compilation_errors_assignment.php");
					break;
				case 'compilation-errors-ratio':
						header("Location: ". $direct_page . "/compilation_errors_ratio/compilation_errors_ratio.php");
					break;
				case 'sessions': 
						header("Location: ". $direct_page . "/session/log_session_report.php");
					break;
				case 'time-project':
						header("Location: ". $direct_page . "/time_project/time_project_report.php");
					break;
				case 'time-system':
						header("Location: ". $direct_page . "/time_system/time_system_report.php");
					break;
				  
				default:
						echo "nah!";
					break;
			}
		}
		
	?>
	<!doctype html>
	
	<head>
		<meta charset="utf-8">
		<title>CODIAD</title>
		<link rel="stylesheet" href="../../themes/default/assignment/screen.css">
		<link rel="stylesheet" href="../../themes/default/fonts.css">
		<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
		<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
	  	<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
	  	<script src="/Codiad/components/assignment/init.js"></script>
	  	
	</head>
	<body>
		<form method="post" action="<?=$pageURL?>">
			<button>Open IDE</button>
		</form>
		<h1 align="center">Reports</h1>	
		
		<div id="modal" style="display: block; width: 1000px; margin:0 auto;" >
			<div id="modal-content">
				<!-- <label>Assignment List</label> -->
				<form method="post">
					<input type="hidden" name="action" value="generate_chart" />
					<div id="project-list">
						<table width="100%">
							<tbody>
								<tr>
									<th>Course</th>
									<th>Report</th>
								</tr>
								<tr>
									<td>
										<select name="course">
											<?
												$Course = new Course();
												for ($i = 0; $i < count($user_courses); $i++) {
													$Course->id = $user_courses[$i];
													$Course->Load();
											?>
										  		<option value="<?=$Course->id?>"><?=$Course->name?></option>
										  	<?
												}
										  	?>
										</select>
									</td>
									<td>
										<select name="report">
										  <option value="compilation-errors">Compilation Errors</option>
										  <option value="compilation-errors-assignment">Compilation Errors with Assignments</option>
										  <option value="compilation-errors-ratio">Compilation Errors Ratio</option>
										  <option value="sessions">Session</option>
										  <option value="time-project">Time spent in Project</option>
										  <option value="time-system">Time spent in System</option>
										</select>
									</td>
								</tr>
							</tbody>
						</table>
						<div align="center">
							<button>Generate Chart</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</body>
	<? 
	} 
	?>