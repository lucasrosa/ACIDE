<?php 
	/*
    *  Copyright (c) UPEI sbateman@upei.ca lrosa@upei.ca
    */
    
	require_once('../../common.php');
    require_once('../user/class.user.php');
	require_once('../project/class.project.php');
	
	//////////////////////////////////////////////////////////////////
    // This page offers an interface to manage assignments
    //////////////////////////////////////////////////////////////////
	
	//////////////////////////////////////////////////////////////////
    // Verify Session or Key
    //////////////////////////////////////////////////////////////////

    checkSession();
	
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
			<label>Assignment List</label>
			<div id="project-list">
				<table width="100%">
					<tbody>
						<tr>
							<th>ID</th>
							<th>Due Date</th>
							<th>Late submission days</th>
							<th>Description</th>
							<th>Maximum number of group members</th>
						</tr>
						<tr>
							<td>Assignment 1</td>
							<td>2010-01-15 00:00:00</td>
							<td>Yes, 2 days.</td>
							<td>Download</td>
							<td>2</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<div style="margin-top: 50px;"></div>
	<div id="modal" style="display: block; width: 700px; margin:0 auto;" >
		<div id="modal-content">
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
							<td><input type="text" name="due_date" id="datepicker" readonly="readonly" /></td>
						</tr>
						<tr>
							<th>Late submission days</th>
							<td>
								<select name="late_submission_days">
									<?php
									for($i = 0; $i <= 10; $i++){
									?>
								 		<option value="<?=$i?>"><?=$i?></option>
								  	<?
								  	}
								  	?>
								</select>
							</td>
						</tr>
						<tr>
							<th>Description File</th>
							<td><input name="description_file" type="file" accept=".pdf"  /></td>
						</tr>
						<tr>
							<th>Maximum number of group members</th>
							<td>
								<select name="maximum_number_of_group_members">
									<?php
									for($i = 0; $i <= 100; $i++){
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
			<button class="btn-left" onclick="alert('hi!');">
				Create
			</button>
		</div>
	</div>
</body>