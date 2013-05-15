<!--
/*
*  PHP+JQuery Temrinal Emulator by Fluidbyte <http://www.fluidbyte.net>
*
*  This software is released as-is with no warranty and is complete free
*  for use, modification and redistribution
*/
-->
<?php

require_once('../../../common.php');
    
//////////////////////////////////////////////////////////////////
// Verify Session or Key
//////////////////////////////////////////////////////////////////

checkSession();

?>
<!doctype html>

<head>
    <meta charset="utf-8">
    <title>Terminal</title>
    <link rel="stylesheet" href="css/screen.css">
</head>

<body>

    
    <div id="terminal">
    
        <div id="output"></div>
    
        <div id="command">
            <div id="prompt">&gt;</div>
            <input id="prompt_text" type="text">
        </div>
    
    </div>

    <script src="js/jquery-1.8.2.js"></script>
	<script>
		// LF: Setting the color of the prompt line before login in --> doens't work
		//$("#prompt_text").css('background-color', 'white');
		//$("#command").append("<input class=\"prompt_text_class\" type=\"text\">");
	</script>
    <script src="js/system.js"></script>
	
</body>
</html>