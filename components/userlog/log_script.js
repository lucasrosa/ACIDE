var hasFocus = true;
	
(function(global, $){
	var codiad = global.codiad;
	
	codiad.userlog.logUserHasFocusOnTheSystem();
	
	setInterval ("CheckFocus()", 100);
	
	window.onfocus = function() {
    	hasFocus = true;
    };

	window.onblur = function() {
		hasFocus = false;
	};
	
})(this, jQuery);

function CheckFocus () {
	
	if (hasFocus) {
		console.log("The document has the focus.");
		codiad.userlog.logUserHasFocusOnTheSystem();
	} else {
		console.log("The document doesn't have the focus.");
    }
    
}