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
		
		$url =  $pageURL;
		$directory = explode("/", $_SERVER["REQUEST_URI"]);
		    
		for ($i = 1; $i < count($directory); $i++) {
		    if ($directory[$i] == "components") {
		        break;
		    } else {
		        $url .= "/" . $directory[$i];   
		    }
		}
		
		$pageURL = $url . "/components/user/importusers";
		
		if ($Permission->GetPermissionToImportUsers()) {
			echo("<script>
				$(document).ready(function() {
					$('.sb-right-content').prepend('<a href=\'" .$pageURL . "\' target=\'_blank\' style=\'text-decoration: none\'><span class=\'icon-user-add l bigger-icon\'></span>Import Users</a>');
				});	
			</script>");
		}		
	}
?>
