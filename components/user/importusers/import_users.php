<?php 
	/*
    *  Copyright (c) UPEI sbateman@upei.ca lrosa@upei.ca
    */
    
	require_once('../../../common.php');
    require_once('../class.user.php');
	
	//////////////////////////////////////////////////////////////////
    // This page shows a list of projects submitted for an assignment
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
	<link rel="stylesheet" href="../../../themes/default/assignment/screen.css">
	<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
	<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
  	<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
  	
</head>
<body>
	<h1  align="center">Import Users</h1>
	
	
	<div id="modal" style="display: block; width: 800px; margin:0 auto;" >
		<div id="modal-content">
			<label style="padding-bottom: 25px;">Import users to the system</label>
			<div id="project-list">
				<form method="post" name="import_users_form" enctype="multipart/form-data">
					<table width="100%">
						<tbody>
							<tr>
								<th>File (.csv)</th>
								<td><input name="file" type="file" accept=".csv"  /></td>
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
</body>