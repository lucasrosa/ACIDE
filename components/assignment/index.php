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
	if (isset($_POST['action'])) {
		
		$error = "";
		
		if ($_POST['action'] == 'create_new_assignment') {
			
			$Project = new Project();
			$Assignment = array();
			
			$Project->path = $_POST['id'];
			$Project->name = $_POST['project_name'];
			$Project->privacy = "private"; 
			
			$Assignment["owner"] = $_SESSION["user"];
			$Assignment["id"] = $Project->path;
			$Assignment["name"] = $_POST['project_name'];
			$Assignment['due_date'] = date("Y-m-d H:i:s", strtotime($_POST['due_date'] . " " . $_POST['due_time']));
			$Assignment['allow_late_submission'] = $_POST['late_submission_days'];
			$Assignment['maximum_number_group_members'] = $_POST['maximum_number_of_group_members'];
			
			// Uploading the pdf file
			$allowedExts = array("pdf");
			$extension = end(explode(".", $_FILES["file"]["name"]));
			
			if (in_array($extension, $allowedExts)) {
				if ($_FILES["file"]["error"] > 0) {
					$error =  "Return Code: " . $_FILES["file"]["error"] . "<br>";
				} else {
					
					if (file_exists("../../data/assignments/" . $_FILES["file"]["name"])) {
						$error = "The description file \"" . $_FILES["file"]["name"] . "\" already exists. ";
					} else {
						move_uploaded_file($_FILES["file"]["tmp_name"], "../../data/assignments/" . $_FILES["file"]["name"]);
						$Assignment["description_url"] = 'http';
						if ($_SERVER["HTTPS"] == "on") {
							$Assignment["description_url"] .= "s";
						}
						$Assignment["description_url"] .= "://";
						if ($_SERVER["SERVER_PORT"] != "80") {
							$Assignment["description_url"] .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
						} else {
							$Assignment["description_url"] .= $_SERVER["SERVER_NAME"]. "/Codiad/data/assignments/" . $_FILES["file"]["name"];
						}
					}
				}
			} else {
				$error = "Invalid file";
			}
			
			
			
			// If there is no errors until now, the operation continues, if there is, just print the error,
			if ($error == '') {
				$Project->assignment = $Assignment;
				$creation_result = $Project->CreateProjectsOnDatabaseWithAssignments();
				if ($creation_result == "success") {
					echo "Assignment created with success!";	
				} else {
					echo $creation_result;
				}
				
			} else {
				echo $error;
			}
			
		}
	}
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
  	<!-- Timepicker -->
  	<link rel="stylesheet" type="text/css" href="timepicker/jquery.ptTimeSelect.css" />
    <script type="text/javascript" src="timepicker/jquery.ptTimeSelect.js" /></script>   
  	<script>
  	$(document).ready(function() {
  		
  		$(function() {
		    $( "#datepicker" ).datepicker({minDate: new Date()});
		});
		$('input[name="due_time"]').ptTimeSelect();		 
	});
	
  	</script>
</head>
<body>
	<h1  align="center">Assignments</h1>
	
	
	
	
	<div id="modal" style="display: block; width: 800px; margin:0 auto;" >
		<div id="modal-content">
			<label>Assignment List</label>
			<div id="project-list">
				<table width="100%">
					<tbody>
						<tr>
							<th>Name</th>
							<th>Due Date</th>
							<th>Late submission days</th>
							<th>Description</th>
							<th>Maximum number of group members</th>
							<th>Submitted Projects</th>
						</tr>
						<?
						//////////////////////////////////////////////////////////////////
					    // LF: List all assignments which this user is the owner
					    //////////////////////////////////////////////////////////////////
					    $Project = new Project();
					    $assignments = $Project->GetAssignmentsForOwner($_SESSION['user']);
						
						for ($k = 0; $k < count($assignments); $k++) {
						?>
						<tr>
							<td><?=$assignments[$k]['name']?></td>
							<td><?=$assignments[$k]['due_date']?></td>
							<td>
								<?
									if ($assignments[$k]['allow_late_submission'] == 0) {
										echo 'No';
									} else {
										echo 'Yes, ' . $assignments[$k]['allow_late_submission'] . ' day';
										if ($assignments[$k]['allow_late_submission'] > 1) {
											echo 's';
										}
									}
									
									echo '.';
								?>
							</td>
							<td><a href="<?=$assignments[$k]['description_url']?>" target='_blank'>View</a></td>
							<td><?=$assignments[$k]['maximum_number_group_members']?></td>
							<td><a href="submitted_assignments.php?id=<?=$assignments[$k]['id']?>">View</a></form>
							</td>
						</tr>
						<?
						}
						?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	
	
	
	<div style="margin-top: 50px;"></div>
	<div id="modal" style="display: block; width: 700px; margin:0 auto;" >
		<div id="modal-content">
			<form method="post" name="assignment_form" enctype="multipart/form-data">
				<input type="hidden" name="action" value="create_new_assignment" />
				<label>New Assignment</label>
				<div id="project-list">
					<table width="100%">
						<tbody>
							<tr>
								<th>ID / Folder</th>
								<td><input type="text" name="id" /></td>
							</tr>
							<tr>
								<th>Assignment / Project Name</th>
								<td><input type="text" name="project_name"/></td>
							</tr>
							<tr>
								<th>Due Date</th>
								<td>
									<table>
										<tr>
											<td style="border:0px;">Date</td>
											<td style="border:0px;"><input type="text" name="due_date" id="datepicker" readonly="readonly" /></td>
										</tr>
											<td style="border:0px;">Time</td>
											<td style="border:0px;"><input type="text" name="due_time" id="due_time"   readonly="readonly"  /></td>
									</table>
								</td>
							</tr>
							<tr>
								<th>Late submission days</th>
								<td>
									<select name="late_submission_days">
										<?php
										for($i = 0; $i <= 31; $i++){
										?>
									 		<option value="<?=$i?>"><?=$i?></option>
									  	<?
									  	}
									  	?>
									</select>
								</td>
							</tr>
								<th>Description File</th>
								<td><input name="file" type="file" accept=".pdf"  /></td>
							</tr>
							<tr>
								<th>Maximum number of group members</th>
								<td>
									<select name="maximum_number_of_group_members">
										<?php
										for($i = 1; $i <= 100; $i++){
										?>
									 		<option value="<?=$i?>"><?=$i?></option>
									  	<?
									  	}
									  	?>
									</select>
								</td>
							</tr>
							<tr>
						</tbody>
					</table>
				</div>
				<button class="btn-left" onclick="$('form#assignment_form').submit()">
					Create
				</button>
			</form>
		</div>
	</div>
</body>