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
	  	<!-- Timepicker -->
	  	<link rel="stylesheet" type="text/css" href="timepicker/jquery.ptTimeSelect.css" />
	    <script type="text/javascript" src="timepicker/jquery.ptTimeSelect.js" /></script>   
	  	
	</head>
	<body>
		<form method="post" action="<?=$pageURL?>">
			<button>Open IDE</button>
		</form>
		<h1 align="center">Reports</h1>	
		
		<div id="modal" style="display: block; width: 1000px; margin:0 auto;" >
			<div id="modal-content">
				<!-- <label>Assignment List</label> -->
				<div id="project-list">
					<table width="100%">
						<tbody>
							<tr>
								<th>Course</th>
								<th>Report</th>
							</tr>
							<tr>
								<td>
									<select>
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
									<select>
									  <option value="volvo">Volvo</option>
									  <option value="saab">Saab</option>
									  <option value="mercedes">Mercedes</option>
									  <option value="audi">Audi</option>
									</select>
								</td>
							</tr>
						</tbody>
					</table>
					<div align="center">
						<button>Generate Chart</button>
					</div>
				</div>
			</div>
		</div>
	</body>
	<? 
	} 
	?>