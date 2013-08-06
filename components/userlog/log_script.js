var hasFocus 					= true;
var projectHasFocus 			= true;
var checkFocusInterval 			= 0.5; // seconds
var checkFocusForFileInterval 	= 0.5; // seconds


$(document).ready(function() {
	$('.ace_line').attr('id', 'file_input');
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
	
})(this, jQuery);

function CheckFocus () {
	
	if (hasFocus) {
		if (codiad.project.getCurrent() != '') {
			codiad.userlog.logUserHasFocusOnProject(codiad.project.getCurrent());	
		}
		codiad.userlog.logUserHasFocusOnTheSystem();
	}
}

function CheckFocusForFile () {
	
	if (hasFocus) {
		var file = document.getElementById('file_input');
		var active_file_path = codiad.active.getPath();
		
		if ((file = document.activeElement) && file != document.body && codiad.project.getCurrent() != '') {
			 if (document.activeElement.tagName == 'TEXTAREA') {
				codiad.userlog.logUserHasFocusOnFile(active_file_path); 	
			 } else if (document.activeElement.tagName == 'INPUT' && document.activeElement.id == 'prompt_text') {
			 	codiad.userlog.logUserHasFocusOnTerminal(); 
			 }
		}	
	}
}
