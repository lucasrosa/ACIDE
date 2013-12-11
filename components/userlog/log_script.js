var hasFocus 						= true;
var projectHasFocus 				= true;
var checkFocusInterval 				=  0.5; // seconds
var checkFocusForFileInterval 		=  0.5; // seconds
var checkExpirationForLastAction	=   10; // seconds

//var EditSession = require('ace/edit_session').EditSession;
//EditSession.on("changeScrollTop", console.log("changeScrollTop"));


	window.onload = function() {
	    //adding the event listerner for Mozilla
	    if(window.addEventListener)
	        document.addEventListener('DOMMouseScroll', scrollIdentified, false);
	 
	    //for IE/OPERA etc
	    document.onmousewheel = scrollIdentified;
	}
	
	function scrollIdentified(event) {
		codiad.userlog.logUserLastAction();
	}	


$(document).ready(function() {
	$('.ace_line').attr('id', 'file_input');
	
	// Log keydown
	$("body").keydown(function() {
  		codiad.userlog.logUserLastAction();
	});
	
	// Log clicks
	$( "body" ).click(function() {
  		codiad.userlog.logUserLastAction();
	});
	
	// Log scrolling -- must add events periodically, as elements appear
    setInterval(function(){
        //editor window
        $('.ace_scrollbar').off('scroll');
        $('.ace_scrollbar').scroll(function() {
            codiad.userlog.logUserLastAction();
            $('.ace_scrollbar').off('scroll');
        });

        //file manager
        $('#file-manager').off('scroll');
        $('#file-manager').scroll(function() {
            codiad.userlog.logUserLastAction();
            $('#file-manager').off('scroll');
        });

        //project manager
        $('.sb-projects-content').off('scroll');
        $('.sb-projects-content').scroll(function() {
            codiad.userlog.logUserLastAction();
            $('.sb-projects-content').off('scroll');
        });
    }, 2000);
});
	
(function(global, $){
	//var aceEditor = ace.edit("editor");
	//aceEditor.ScrollBar.onScroll(console.log("scroll"));
	
	var codiad = global.codiad;
	
	codiad.userlog.logUserHasFocusOnTheSystem();
	
	setInterval ("CheckFocus()", (checkFocusInterval * 1000));
	setInterval ("CheckFocusForFile()", (checkFocusForFileInterval * 1000));
	setInterval ("CheckExpirationForLastAction()", (checkExpirationForLastAction * 1000));
	
	window.onfocus = function() {
    	hasFocus = true;
    };

	window.onblur = function() {
		hasFocus = false;
	};
	
})(this, jQuery);

function CheckExpirationForLastAction () {
	codiad.userlog.closeAllOpenSectionsThatReachedTimeoutOfUserLastAction();
}

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
