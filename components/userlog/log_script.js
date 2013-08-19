var hasFocus 						= true;
var projectHasFocus 				= true;
var checkFocusInterval 				=  0.5; // seconds
var checkFocusForFileInterval 		=  0.5; // seconds
var checkExpirationForLastAction	=   10; // seconds

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
	
	$("body").keydown(function() {
  		//console.log("Handler for .keypress() called.");
  		codiad.userlog.logUserLastAction();
	});
	
	$( "body" ).click(function() {
  		//console.log("Handler for .click() called." );
  		codiad.userlog.logUserLastAction();
	});
	
	//$(".ace_text-input").scroll(function() {
  	//		console.log('Handler for .scroll() called.');
	//});
	
	//$(".ace_content").scroll(function() {
  	//	console.log('Handler for .scroll() called.');
	//});
	
	//$('.editor').attr('id', 'editor');
	//var aceEditor = ace.edit("editor");
	//var Editor = require('ace/editor').Editor;
	//Editor.getSession.on('change', function(e) {
	//	console.log("change called");	
	//});
	
	//var tEditor = $("#root-editor-wrapper").children()[0]
	//tEditor.attr('id', 'editor');
	//$("#root-editor-wrapper").children("div").attr("id","editor");
    	
	//var aceEditor = ace.edit("editor");
	//aceEditor.resize();
			
	//	var codiad = global.codiad;
	//	codiad.editor.getSession().removeAllListeners('change');
		
	//window.onscroll = function (e) {  
		// called when the window is scrolled.
		//console.log('Handler for .scroll() called.');  
	//} 
	/*
	var mouseChangedPixelNumber = 0;
	
	$("body").mousemove(function(event) {
	  var msg = "Handler for .mousemove() called at ";
	  msg += event.pageX + ", " + event.pageY;
	  if (mouseChangedPixelNumber > 50) {
	  	console.log(msg);	
	  	mouseChangedPixelNumber = 0;
	  }
	  
	  
	  mouseChangedPixelNumber++;
	});
	*/
});
	
(function(global, $){
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
	console.log("CheckExpirationForLastAction");
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
