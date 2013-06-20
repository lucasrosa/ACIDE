<?php 
	/*
    *  Copyright (c) UPEI sbateman@upei.ca lrosa@upei.ca
    */
    
	require_once('../../../common.php');
    require_once('../class.user.php');
	require_once('../../permission/class.permission.php');
	
	//////////////////////////////////////////////////////////////////
    // This page shows a list of projects submitted for an assignment
    //////////////////////////////////////////////////////////////////
	
	//////////////////////////////////////////////////////////////////
    // Verify Session or Key
    //////////////////////////////////////////////////////////////////
    checkSession();
	
	//////////////////////////////////////////////////////////////////
    // Defining the user permission
    //////////////////////////////////////////////////////////////////
	$Permission = new Permission($_SESSION['user']);
	
	if (!($Permission->GetPermissionToImportUsers())) {
		echo "Permission Denied";
	} else {
		
		if (@$_POST['action'] == 'import_users') {
			if ($_FILES['file_csv']['size'] > 0) { 
				//print_r($_FILES);
			    //get the csv file 
			    $file = $_FILES['file_csv']['tmp_name']; 
			    $handle = fopen($file,"r"); 
			     
			    //loop through the csv file and insert into database
			    $users = array(); 
				
				do { 
			        if (@$data[0] != "") {
			        	if (strtolower($data[0]) != "username") {
			        		if (strlen($data[0]) > 0 && strlen($data[1]) > 0 && strlen($data[2]) > 0) {
			        			$users[] = $data;
			        		}
			        	}
			        }
			    } while ($data = fgetcsv($handle,1000,",","'")); 
			
				$users_imported_count = 0;
				$errors = array();
				
				for ($i = 0; $i < count($users); $i++) {
					$User = new User();
					$User->username = $users[$i][0];
					$User->email 	= $users[$i][1];
					$User->password = $users[$i][2];
					
					// If no type was defined, the type is defined as "student"
					if (isset($users[$i][3])) {
						if (strlen($users[$i][3]) > 0) {
							$User->type = $users[$i][3];
						} else {
							$User->type = "student";
						}
					} else {
						$User->type = "student";
					}
					
					$return_a_string = TRUE;
					$result = $User->Create($return_a_string);
					
					if ($result == "success") {
						$users_imported_count++;
					} else {
						$errors[] = "The following error was encountered while inserting the user '" . $User->username . "': \"<i>" . $result. "</i>\"";
					}
				}
				
				$success = $users_imported_count . " users inserted with success! <br />";
				$error = "";
				if (count($errors) > 0) {
					for ($i = 0; $i < count($errors); $i++) {
						$error .= $errors[$i] . "<br />";
					}						
				}
			} 
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
		<link rel="stylesheet" href="../../../themes/default/assignment/screen.css">
		<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
		<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
	  	<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
	  	
	</head>
	<body>
		<form method="post" action="<?=$pageURL?>">
			<button>Open IDE</button>
		</form>
		<h1  align="center">Import Users</h1>
		
		
		<div id="modal" style="display: block; width: 800px; margin:0 auto;" >
			<div id="modal-content">
				<label style="padding-bottom: 25px;">Import users to the system</label>
				<div id="project-list">
					<form method="post" name="import_users_form" enctype="multipart/form-data">
						<input type="hidden" name="action" value="import_users" />
						<table width="100%">
							<tbody>
								<tr>
									<th>File (.csv)</th>
									<td><input name="file_csv" type="file" accept=".csv"  /></td>
									<td align="center"><button>Import</button></td>
								</tr>
								
							</tbody>
						</table>
					</form>
				</div>
				<div align='left'>
					<a href="">
						<button>Back</button>
					</a>
				</div>
			</div>
		</div>
		<div align="center">
			<br />
			<?
			if (@$_POST['action'] == 'import_users') {
				if ($error != "") {
			?>
				<span style="color: red;"><?=$error ?> </span>
				<br />
			<?
				}
			?>
			<span style="color: green;"><?=$success ?> </span>
			<?
			}
			?>
		</div>
	</body>
	<?
	}
	?>