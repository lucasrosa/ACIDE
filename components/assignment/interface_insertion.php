<?php
	if (isset($_SESSION['user'])) {
		// Instantiate the permission object and set the username
		$Permission = new Permission($_SESSION['user']);
		
		$pageURL = WEB_BASE_PATH;
		$pageURL .= "/components/assignment";
		
		if ($Permission->GetPermissionToSeeAssignments()) {
			echo("<script>
				$(document).ready(function() {
					$('.sb-right-content').prepend('<a href=\'" .$pageURL . "\' target=\'_blank\' style=\'text-decoration: none\'><span class=\'icon-docs l bigger-icon\'></span>Assignments</a>');
				});	
			</script>");
		}		
	}
?>

