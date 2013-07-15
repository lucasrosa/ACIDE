var hasFocus 					= true;
var projectHasFocus 			= true;
var checkFocusInterval 			= 0.5; // seconds
var checkFocusForFileInterval 	= 0.5; // seconds


$(document).ready(function() {
	$('.ace_line').attr('id', 'file_input');
	//$('.ace_text-input').attr('id', 'file_input_textarea');
});
	
(function(global, $){
	var codiad = global.codiad;
	
	codiad.userlog.logUserHasFocusOnTheSystem();
	
	setInterval ("CheckFocus()", (checkFocusInterval * 1000));
	setInterval ("CheckFocusForFile()", (checkFocusForFileInterval * 1000));
	
	window.onfocus = function() {
    	hasFocus = true;
    };

	window.onblur = function() {
		hasFocus = false;
	};
	
	// Project logging
	
	// File logging
	
	
})(this, jQuery);

function CheckFocus () {
	
	if (hasFocus) {
		//console.log("The document has focus.");
		
		if (codiad.project.getCurrent() != '') {
			console.log("Logging project: '" + codiad.project.getCurrent()+ "'");
			codiad.userlog.logUserHasFocusOnProject(codiad.project.getCurrent());	
		}
		codiad.userlog.logUserHasFocusOnTheSystem();
	}
	/*
	else {
		console.log("The document doesn't have the focus.");
    }
    */
}

function CheckFocusForFile () {
	
	if (hasFocus) {
		var file = document.getElementById('file_input');
		var active_file_path = codiad.active.getPath();
		
		if ((file = document.activeElement) && file != document.body && codiad.project.getCurrent() != '') {
			 if (document.activeElement.tagName == 'TEXTAREA') {
			 	//console.log("The id is: " + document.activeElement.id);
				codiad.userlog.logUserHasFocusOnFile(active_file_path); 	
			 } else if (document.activeElement.tagName == 'INPUT' && document.activeElement.id == 'prompt_text') {
			 	//console.log("user has focus on the terminal")
			 	codiad.userlog.logUserHasFocusOnTerminal(); 
			 }
		}	
	}
}
