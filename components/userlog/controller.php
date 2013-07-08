<?php

    /*
     * Copyright (c) UPEI lrosa@upei.ca sbateman@upei.ca
     */


    require_once('../../common.php');
    require_once('class.userlog.php');
    //////////////////////////////////////////////////////////////////
    // Verify Session or Key
    //////////////////////////////////////////////////////////////////

    checkSession();

    $Userlog = new Userlog();

    $no_return = false;

    //////////////////////////////////////////////////////////////////
    // Open Project
    //////////////////////////////////////////////////////////////////

    if ($_GET['action'] == 'log_user_opened_system'){
		// LF : Log the user action "user_opened_system" in the database
		$Userlog->username = $_SESSION['user'];
		$Userlog->type = "user_opened_system";
        $Userlog->Save();
    }
	
	if ($_GET['action'] == 'log_user_closed_system'){
		// LF : Log the user action "user_closed_system" in the database
		$Userlog->username = $_SESSION['user'];
		$Userlog->type = "user_closed_system";
        $Userlog->Save();
    }
	
	if ($_GET['action'] == 'log_user_has_focus_on_the_system'){
		$Userlog->username = $_SESSION['user'];
        $Userlog->UpdateCurrentSession();
    }
	
	if ($_GET['action'] == 'log_user_has_focus_on_file'){
		$Userlog->username 	= $_SESSION['user'];
		$Userlog->path		= $_GET['path'];
        $Userlog->UpdateCurrentFile();
    }
?>