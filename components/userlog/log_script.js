var hasFocus 					= true;
var projectHasFocus 			= true;
var checkFocusInterval 			= 5; // seconds
var checkFocusForFileInterval 	= 0.5; // seconds

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
	
	$(document).ready(function() {
		$('.ace_line').attr('id', 'file_input');
	});
	
	
})(this, jQuery);

function CheckFocus () {
	
	if (hasFocus && window.blurred) {
		//console.log("The document has focus.");
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
		
		if ((file = document.activeElement) && file != document.body) {
			 if (document.activeElement.tagName == 'TEXTAREA') {
				codiad.userlog.logUserHasFocusOnFile(active_file_path); 	
			 } else if (document.activeElement.tagName == 'INPUT') {
			 	console.log("user has focus on the terminal") 
			 }
		}	
	}
}
