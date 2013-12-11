<?php
	if (isset($_SESSION['user'])) {
		// Instantiate the permission object and set the username
		$Permission = new Permission($_SESSION['user']);
		
		
		$pageURL = WEB_BASE_PATH . "/components/userlog";
		
		if ($Permission->GetPermissionToSeeReports()) {
			echo("<script>
				$(document).ready(function() {
					$('.sb-right-content').prepend('<a href=\'" .$pageURL . "\' target=\'_blank\' style=\'text-decoration: none\'><span class=\'icon-chart-bar l bigger-icon\'></span>Reports</a>');
				});	
			</script>");
		}		
	}
?>

