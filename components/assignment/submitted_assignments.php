<?php 
	/*
    *  Copyright (c) UPEI sbateman@upei.ca lrosa@upei.ca
    */
    
	require_once('../../common.php');
    require_once('../user/class.user.php');
	require_once('../project/class.project.php');
	require_once('class.assignment.php');
	
	//////////////////////////////////////////////////////////////////
    // This page shows a list of projects submitted for an assignment
    //////////////////////////////////////////////////////////////////
	
	//////////////////////////////////////////////////////////////////
    // Verify Session or Key
    //////////////////////////////////////////////////////////////////
    checkSession();
	if (!isset($_POST['id'])) {
		echo "Error: No Assignment defined.";	
	} else {
		$Project = new Project();
		$assignmentName = $Project->GetAssignmentName($_POST['id']);
		$projects = $Project->GetProjectsForAssignment($_POST['id']);
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
	<h1  align="center">Submitted Projects</h1>
	
	
	<div id="modal" style="display: block; width: 800px; margin:0 auto;" >
		<div id="modal-content">
			<label style="padding-bottom: 25px;">Projects submitted to assignment <?=$assignmentName?>.</label>
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
						
						for ($k = 0; $k < count($projects); $k++) {
						?>
						<tr>
							<td>
								<?
									for ($j = 0;  $j < count($projects[$k]['group_members']); $j++) {
										?>
										<span style="padding-bottom: 10px;">- <?=$projects[$k]['group_members'][$j]["username"]?><br /></span>
										<?
									}
								?>
							</td>
							<td><?=$projects[$k]["assignment"]['submitted_date']?></td>
							<td>
								<?
									if ($projects[$k]["assignment"]['submitted_late'] == "FALSE") {
										echo 'No';
									} else {
										echo 'Yes';
									}
								?>
							</td>
							<?
								$directory =  dirname($_SERVER['SCRIPT_NAME']);
								$directories = explode('/', $directory);
								if ($directories[1] != "") {
									$root_directory = "/" . $directories[1];
								}
								
								$url = $root_directory . "/components/project/" . $projects[$k]["assignment"]["project_file_name"];
							?>
							<td>
								<a href="<?=$url?>"><button>Download</button></a>
							</td>
						</tr>
						<?
						}
						?>
					</tbody>
				</table>
			</div>
			<div align='left'>
				<a href="index.php">
					<button>Back</button>
				</a>
			</div>
		</div>
	</div>
</body>
	<?
	}
	?>