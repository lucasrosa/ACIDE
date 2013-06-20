<?php
	if (isset($_SESSION['user'])) {
		// Instantiate the permission object and set the username
		$Permission = new Permission($_SESSION['user']);
		
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
		$pageURL .= "/" . $dirs[1] . "/components/assignment";
		
		if ($Permission->GetPermissionToSeeAssignments()) {
			echo("<script>
				$(document).ready(function() {
					$('.sb-right-content').prepend('<a href=\'" .$pageURL . "\' target=\'_blank\' style=\'text-decoration: none\'><span class=\'icon-docs l bigger-icon\'></span>Assignments</a>');
				});	
			</script>");
		}		
	}
?>

