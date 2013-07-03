<?php 
	/*
    *  Copyright (c) UPEI sbateman@upei.ca lrosa@upei.ca
    */
    
	require_once('../../common.php');
    require_once('../user/class.user.php');
	require_once('../project/class.project.php');
	require_once('class.assignment.php');
	require_once('../permission/class.permission.php');
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
		
		
	
		$form_action = "create_new_assignment";
		$form_title = "New Assignment";
		$form_button_title = "Create";
		$error = "";
		$success = "";
		
		$assignment_blank = FALSE;
		$editing_assignment = FALSE;
		$error_editing_assignment = FALSE;
		
		$Project = new Project();
		$Assignment = array();
		
		// Verify if the uploaded description file is bigger than the allowed by php (defined in php.ini)
		if ( $_SERVER['REQUEST_METHOD'] == 'POST' && empty($_POST) && empty($_FILES) && $_SERVER['CONTENT_LENGTH'] > 0 ) {
		  $displayMaxSize = ini_get('post_max_size');
		 
		  switch ( substr($displayMaxSize,-1) )
		  {
		    case 'G':
		      $displayMaxSize = $displayMaxSize * 1024;
		    case 'M':
		      $displayMaxSize = $displayMaxSize * 1024;
		    case 'K':
		       $displayMaxSize = $displayMaxSize * 1024;
		  }
		 
			@$error = 'Description file is too large. '.
		           $_SERVER[CONTENT_LENGTH]. ' bytes (' . number_format($_SERVER[CONTENT_LENGTH]/1024/1024, 1). ' MB)' .
		           ' exceeds the maximum size of '.
		           $displayMaxSize.' bytes '.  '(' . number_format($displayMaxSize/1024/1024, 1). ' MB)';
		}
		
		if (isset($_POST['action'])) {
			
			if ($_POST['action'] == 'delete_assignment') {
				if ($Project->DeleteAssignment($_POST['id'])) {
					$success = "Assignment deleted with success.";
					$assignment_blank = TRUE;
				} else {
					$error = "Assignment could not be removed properly.";
					$assignment_blank = TRUE;
				}
				
			}else if ($_POST['action'] == 'edit_assignment') {
				$form_action = "save_edited_assignment";
				$form_title = "Edit Assignment";
				$form_button_title = "Update";
				$editing_assignment = TRUE;
				$Assignment = $Project->GetAssignmentWithId($_POST['id']);
				$Assignment['due_date_date'] = date("m/d/Y", strtotime($Assignment['due_date']));
				$Assignment['due_date_time'] = date("H:i A", strtotime($Assignment['due_date']));
				$hour = substr($Assignment['due_date_time'], 0 , 2);
				$hour = intval($hour);
				if ($hour > 12) {
					$hour -= 12;
				}
				$Assignment['due_date_time'] = $hour . substr($Assignment['due_date_time'], 2 , 6);
					
			} else if (($_POST['action'] == 'create_new_assignment') || ($_POST['action'] == 'save_edited_assignment')) {
				
			
				//$Assignment["id"] = $_POST['id'];
				//if ($Assignment["id"] == "") 
				//	$error .= "The field ID/Folder cannot be blank. <br />";
				
				$Assignment["name"] = $_POST['project_name'];
				if ($Assignment["name"] == "") 
					$error .= "The field Assignment / Project Name cannot be blank. <br />";
					
				if ($_POST['action'] == 'create_new_assignment') {
					$Assignment["id"] = str_replace(" ","_",$_POST['project_name']);
        			$Assignment["id"] = preg_replace('/[^\w-]/', '', $Assignment["id"]);	
				} else {
					$Assignment["id"] = $_POST['id'];
					$Assignment = $Project->GetAssignmentWithId($_POST['id']);
					$Assignment["name"] = $_POST['project_name'];
				}
				
				$Assignment["visibility"] = "true";	
				
				if ($_POST['due_date'] == "") 
					$error .= "The field Date cannot be blank. <br />";
				
				if ($_POST['due_time'] == "") 
					$error .= "The field Time cannot be blank. <br />";
				
				$Assignment['due_date'] = date("Y-m-d H:i:s", strtotime($_POST['due_date'] . " " . $_POST['due_time']));
				
				
				$Assignment['allow_late_submission'] = $_POST['late_submission_days'];
				$Assignment['maximum_number_group_members'] = $_POST['maximum_number_of_group_members'];
					
				$keep_old_description_file = FALSE;	
				
				if (isset($_POST['keep_old_description_file'])) {
					if ($_POST['keep_old_description_file'] == "TRUE") {
						$keep_old_description_file = TRUE;
					}
				} 
				
				
				if (!$keep_old_description_file) {
					// Uploading the pdf file
					$allowedExts = array("pdf");
					$extension = end(explode(".", $_FILES["file"]["name"]));
					
					
					if (in_array($extension, $allowedExts) && $error == '') {
						if ($_FILES["file"]["error"] > 0) {
							$error .= " ";
							if ($_FILES["file"]["error"] == 1) {
								$error .=  "Description file is too large. <br />";
							} else if ($_FILES["file"]["error"] == 3) {
								$error .= "An error occurred when uploading the file. <br />";
							} else if ($_FILES["file"]["error"] == 4) {
								$error .= "No file was uploaded. <br />";
							} else if ($_FILES["file"]["error"] == 7) {
								$error .= "Failed to write file into disk. <br />";
							} else {
								$error .=  "Return Code: " . $_FILES["file"]["error"] . "<br>";
							}
						} else {
							
							if (file_exists("../../data/assignments/" . $_FILES["file"]["name"])) {
								$error = "The description file \"" . $_FILES["file"]["name"] . "\" already exists. ";
							} else {
								move_uploaded_file($_FILES["file"]["tmp_name"], "../../data/assignments/" . $_FILES["file"]["name"]);
								$Assignment["description_url"] = 'http';
								
								if (isset($_SERVER["HTTPS"])) {
									if ($_SERVER["HTTPS"] == "on") {
										$Assignment["description_url"] .= "s";
									}
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
						if ($error == '') {
							$error = "Invalid description file. <br />";	
						}
					}
				}
				
				if ($_POST['action'] == 'create_new_assignment') {
					$Assignment["id"] = str_replace(" ","_",$_POST['project_name']);
	        		$Assignment["id"] = preg_replace('/[^\w-]/', '', $Assignment["id"]);					
					//$Project->path = $_POST['id'];
					$Project->path = $Assignment["id"]; 
					$Project->name = $_POST['project_name'];
					$Project->privacy = "private";
					$Project->course = $_POST['course'];
					
					//$Assignment["id"] = $Project->path;
					
					$Assignment["owner"] = $_SESSION["user"];
					
					// If there is no errors until now, the operation continues
					if ($error == '') {
						$Project->assignment = $Assignment;
						$creation_result = $Project->CreateProjectsOnDatabaseWithAssignments();
						
						if ($creation_result == "success") {
							$success = "Assignment created with success!";
						} else {
							$error = $creation_result;
							$url = $Project->assignment['description_url'];
							// Deleting the description file
							$tokens = explode('/', $url);
							$description_file_name = $tokens[sizeof($tokens)-1];
							unlink("../../data/assignments/" . $description_file_name);
						}
					}
					
				} else if ($_POST['action'] == 'save_edited_assignment') {
					if ($error == '') {
						//$Assignment['course'] = $_POST['course'];
						if ($Project->SaveAssignment($Assignment)) {
							$success = "Assignment updated with success!";
						} else {
							$error = "Error updating the assignment.";
						}
					}
				} 
				
				if ($error != '') {
					$assignment_blank = FALSE;
					if ($_POST['action'] == 'save_edited_assignment') {
						$error_editing_assignment = TRUE;
						$editing_assignment = TRUE;
						$form_action = "save_edited_assignment";
						$form_title = "Edit Assignment";
						$form_button_title = "Update";	
					}
				} else if ($success != '') {
					$assignment_blank = TRUE;
				}
			}
		} else {
			$assignment_blank = TRUE;
		}
		
		if ($assignment_blank) {
			$Assignment["owner"] = '';
			$Assignment["id"] = '';
			$Assignment["name"] = '';
			$Assignment['course'] = '';
			$Assignment['due_date'] = '';
			$Assignment['allow_late_submission'] = '';
			$Assignment['maximum_number_group_members'] = '';
			$Assignment['due_date_date'] = '';
			$Assignment['due_date_time'] = '';
		} else  if (!$editing_assignment || $error_editing_assignment){
			if ($error_editing_assignment) {
				$Assignment['id'] = $_POST['id'];	
			} else {
				$Assignment["id"] = str_replace(" ","_",$_POST['project_name']);
	       		$Assignment["id"] = preg_replace('/[^\w-]/', '', $Assignment["id"]);	
			}
			
			$Assignment['name'] = $_POST['project_name'];
			$Assignment['course'] = $_POST['course'];
			$Assignment['due_date_date'] = $_POST['due_date'];
			$Assignment['due_date_time'] = $_POST['due_time'];
			$Assignment['allow_late_submission'] = $_POST['late_submission_days'];
			$Assignment['maximum_number_group_members'] = $_POST['maximum_number_of_group_members'];
		}
		
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
								<th>Late submission days</th>
								<th>Description</th>
								<th style="width: 50px;">Maximum number of group members</th>
								<th>Submitted Projects</th>
								<? if ($Permission->GetPermissionToCreateAndEditAssignments()) { ?>
									<th>Edit</th>
								<? }?>
								<th>Delete</th>
							</tr>
							<?
							//////////////////////////////////////////////////////////////////
						    // LF: List all assignments which this user is the owner
						    //////////////////////////////////////////////////////////////////
						    $Project = new Project();
						    //$assignments = $Project->GetAssignments();
						    $current_user = $_SESSION['user'];
						    $assignments = $Project->GetAssignmentsInTheSameCoursesOfUser($current_user); 
							$Course = new Course();
							
							for ($k = 0; $k < count($assignments); $k++) {
								$Course->id = $assignments[$k]['course'];
								$Course->Load();
								
							?>
							<tr>
								<td><?=$assignments[$k]['name']?></td>
								<td><?=$assignments[$k]['owner']?></td>
								<td><?=$Course->code ." - ". $Course->name?></td>
								<td>
									<?=date("m/d/Y", strtotime($assignments[$k]['due_date'])); ?>
									<br />
									&nbsp;<?=date("H:i A", strtotime($assignments[$k]['due_date'])); ?>
									</td>
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
								<td>
									<div align='center'>
										<a href="<?=$assignments[$k]['description_url']?>" target='_blank'>
											<button>View</button>
										</a>
									</div>
								</td>
								<td><?=$assignments[$k]['maximum_number_group_members']?></td>
								<td>
									<div align="center">
										<form method="post" name="view_submitted_assignments_form" action="submitted_assignments.php">
											<input type="hidden" name="id" value="<?=$assignments[$k]['id']?>" />
											<button>View</button>
										</form>
									</div>
								</td>
								<? if ($Permission->GetPermissionToCreateAndEditAssignments()) { ?>
								<td>
									<div align="center">
										<form method="post" name="edit_assignment_form">
											<input type="hidden" name="action" value="edit_assignment" />
											<input type="hidden" name="id" value="<?=$assignments[$k]['id']?>" />
											<button class="icon-pencil icon"></button>
										</form>
									</div>
								</td>
								<? } ?>
								<td>
									<div align="center">
										<form method="post" name="delete_assignment_form" onsubmit="return confirm('Are you sure you want to delete this assignment?')">
											<input type="hidden" name="action" value="delete_assignment" />
											<input type="hidden" name="id" value="<?=$assignments[$k]['id']?>" />
											<button class="icon-cancel icon"></button>
										</form>
									</div>
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
		
		<? if ($Permission->GetPermissionToCreateAndEditAssignments()) { ?>
		
			<div style="padding-bottom: 25px; padding-top: 25px;">
				<div align="center">
					<?
						$color = "green";
						$message = $success;
		
						if ($error != "") {
							$color = "red";
							$message = $error;
						}
					?>
					<span style="color: <?=$color?>;"><?=$message?></span>
				</div>		
			</div>
			<div id="modal" style="display: block; width: 700px; margin:0 auto;" >
				<div id="modal-content">
					<form method="post" name="assignment_form" enctype="multipart/form-data">
						<input type="hidden" name="action" value="<?=$form_action?>" />
						<label>
							<?=$form_title;?> 
						</label>
						<div id="project-list">
							<table width="100%">
								<tbody>
									<?
									/*
									if (!$editing_assignment) {
									?>
									<tr>
										<th>ID (Project's folder name)</th>
										<td><input type="text" name="id" value="<?=$Assignment['id']?>" /></td>
									</tr>
									<?
									} else {
									 * 
									 */
									?>
										<input type="hidden" name="id" value="<?=$Assignment['id']?>" />
									<?
									//}
									 
									?>
									<tr>
										<th>Assignment name (Project's name)</th>
										<td><input type="text" name="project_name" value="<?=$Assignment['name']?>" /></td>
									</tr>
									<?
									
									if (!$editing_assignment) {
									?>
									<tr
									<tr>
										<th>Course</th>
										<td>
											<select name="course">
												<?php
												
												$Course = new Course();
												$courses = $Course->GetAllCourses();
												
												foreach($courses as $course){
												?>
											 		<option value="<?=$course['_id']?>" <? if ($course['_id'] == $Assignment['course']) echo "selected='selected'"; ?>><?=$course['code'] . " - " . $course['name']?></option>
											  	<?
											  	}
											  	?>
											</select>
										</td>
									</tr>
									<?
									}
									?>
									<tr>
										<th>Due Date</th>
										<td>
											<table>
												<tr>
													<td style="border:0px;">Date</td>
													<td style="border:0px;">
														<input type="text" name="due_date" id="datepicker" value="<?=$Assignment['due_date_date']?>" readonly="readonly" />
													</td>
												</tr>
													<td style="border:0px;">Time</td>
													<td style="border:0px;">
														<input type="text" name="due_time" id="due_time" value="<?=$Assignment['due_date_time']?>" readonly="readonly"  />
													</td>
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
											 		<option value="<?=$i?>" <? if ($i == $Assignment['allow_late_submission']) echo "selected='selected'"; ?>><?=$i?></option>
											  	<?
											  	}
											  	?>
											</select>
										</td>
									</tr>
									<? if ($editing_assignment) {?>
									<tr>
										<th>Keep the old description file?</th>
										<td><input name="keep_old_description_file" type="checkbox" value="TRUE" /></td>
									</tr>
									<? } ?>
									<tr>
										<th>Description File (Maximum of 2 MB)</th>
										<td><input name="file" type="file" accept=".pdf"  /></td>
									</tr>
									<tr>
										<th>Maximum number of group members</th>
										<td>
											<select name="maximum_number_of_group_members">
												<?php
												for($i = 1; $i <= 100; $i++){
												?>
											 		<option value="<?=$i?>" <? if ($i == $Assignment['maximum_number_group_members']) echo "selected='selected'"; ?>><?=$i?></option>
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
						<button>
							<?=$form_button_title?>
						</button>
						<?	if ($editing_assignment) {
								$pageURL = 'http';
								if (@$_SERVER["HTTPS"] == "on") {
									$pageURL .= "s";
								}
								
								$pageURL .= "://";
								
								if ($_SERVER["SERVER_PORT"] != "80") {
									 $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
								} else {
									$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
								}
							?>
							<a href="<?=$pageURL?>">Cancel</a>
						<? } ?>
					</form>
				</div>
			</div>
		<? } ?>
	</body>
	<? 
	} 
	?>