<?php

    /*
     * Copyright (c) UPEI lrosa@upei.ca sbateman@upei.ca
     */


    require_once('../../common.php');
    require_once('class.log.php');
    //////////////////////////////////////////////////////////////////
    // Verify Session or Key
    //////////////////////////////////////////////////////////////////

    checkSession();

    $Log = new Log();

    $no_return = false;

    //////////////////////////////////////////////////////////////////
    // Open Project
    //////////////////////////////////////////////////////////////////

    if($_GET['action']=='log_user_login'){
		// LF : Set the user of the project to be open
		$Log->username = $_SESSION['user'];
		$Log->type = "login";
        $Log->Save();
    }
?>