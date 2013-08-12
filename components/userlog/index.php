<?php 
	/*
    *  Copyright (c) UPEI sbateman@upei.ca lrosa@upei.ca
    */
    
	require_once('../../common.php');
	require_once('../permission/class.permission.php');
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
		<h1 align="center">Assignments</h1>	
		
		<div id="modal" style="display: block; width: 1000px; margin:0 auto;" >
			<div id="modal-content">
				<label>Assignment List</label>
				<div id="project-list">
					<table width="100%">
						<tbody>
							<tr>
								<th>Name</th>
								<th>Created by</th>
								<th>Course</th>
								<th>Due Date</th>
							</tr>
							<tr>
								<td>1</td>
								<td>2</td>
								<td>3</td>
								<td>4</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</body>
	<? 
	} 
	?>