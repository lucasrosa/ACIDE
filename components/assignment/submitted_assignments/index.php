<?php 
	/*
    *  Copyright (c) UPEI sbateman@upei.ca lrosa@upei.ca
    */
    
	require_once('../../common.php');
    require_once('../user/class.user.php');
	require_once('../project/class.project.php');
	require_once('class.assignment.php');
	
	//////////////////////////////////////////////////////////////////
    // This page offers an interface to manage assignments
    //////////////////////////////////////////////////////////////////
	
	//////////////////////////////////////////////////////////////////
    // Verify Session or Key
    //////////////////////////////////////////////////////////////////

    checkSession();
	if (!isset($_POST['id'])) {
		echo "Error: No Assignment defined.";	
	} else {
		
?>
<!doctype html>

<head>
	<meta charset="utf-8">
	<title>CODIAD</title>
	<link rel="stylesheet" href="../../themes/default/assignment/screen.css">
	<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
	<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
  	<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
  	<script src="/Codiad/components/assignment/init.js"></script>
</head>
<body>
	<h1  align="center">Assignments</h1>
	
	
	
	
	<div id="modal" style="display: block; width: 800px; margin:0 auto;" >
		<div id="modal-content">
			<label>Projects submitted to assignment .....</label>
			<div id="project-list">
				<table width="100%">
					<tbody>
						<tr>
							<th>Student(s)</th>
							<th>Submitted date</th>
							<th>Submitted after due date</th>
							<th>Project</th>
						</tr>
						<?
						//////////////////////////////////////////////////////////////////
					    // LF: List all the projects submitted for this assignment
					    //////////////////////////////////////////////////////////////////
					    $Project = new Project();
					    $projects = $Project->GetProjectsForAssignment($_POST['id']);
						
						for ($k = 0; $k < count($projects); $k++) {
						?>
						<tr>
							<td>
								<?
									for ($j = 0;  $j < count($projects[$k]['group_members']); $j++) {
										echo $projects[$k]['group_members'][$j]["username"] . "<br />";
									}
								?>
							</td>
							<td><?=$projects[$k]["assignment"]['submitted_date']?></td>
							<td>
								<?
									if ($assignments[$k]["assignment"]['submitted_late'] == "FALSE") {
										echo 'No';
									} else {
										echo 'Yes';
									}
								?>
							</td>
							<td><a href="<?=$projects[$k]["assignment"]["project_file_url"]?>" target="_blank">View</a></td>
						</tr>
						<?
						}
						?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</body>
	<?
	}
	?>