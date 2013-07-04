/*
	$(document).ready(function() {
		var tern = $('.editor')[1];
		$(tern).text('a');
	});
*/

	// LF: Modifying the interface to fit the terminal between the editor and the bottom bar
	$(document).ready(function() {
		
		// LF: Decreasing the size of the editor
		$("#root-editor-wrapper").attr('style', 'height:71% !important');
		$("#root-editor-wrapper").attr('style', 'top:45px');
		// LF: Increasing the size of the bottom bar
		$("#editor-bottom-bar").css("height", "24%");
		
		// Adding the function to the Terminal close button
		$("#terminal-close-button").on('click', function () {			
			// LF: Changing the status of the terminal container to hidden
			$("#terminal_container").toggle();
			// LF: Decreasing the size of the bottom bar
			$("#editor-bottom-bar").css("height", "4%");
			// LF: Increasing the size of the editor
			$("#root-editor-wrapper").attr('style', 'height:91% !important');
			// LF: Showing the Terminal open button and its divider
			$("#terminal-open-button-divider").slideToggle("slow");
			$("#terminal-open-button").slideToggle("slow");
			// LF: Calling the resize function of the Editor
			$('.editor').attr('id', 'editor');
			var aceEditor = ace.edit("editor");
			aceEditor.resize();
		});
		
		// LF: Adding the function to the Terminal open button
		$("#terminal-open-button").on('click', function () {			
			// LF: Changing the status of the terminal container to visible
			$("#terminal_container").toggle();
			// LF: Increasing the size of the bottom bar
			$("#editor-bottom-bar").css("height", "24%");
			// LF: Decreasing the size of the editor
			$("#root-editor-wrapper").attr('style', 'height:71% !important');
			// LF: Hiding the Terminal open button and its divider
			$("#terminal-open-button-divider").slideToggle("slow");
			$("#terminal-open-button").slideToggle("slow");
			// LF: Calling the resize function of the Editor
			$('.editor').attr('id', 'editor');
			var aceEditor = ace.edit("editor");
			aceEditor.resize();
		});
		
		
		// LF: Removing the split option that is currently bugged
		//$("#editor-bottom-bar").find(".divider")[2].remove();
		$("#split").remove();
		
		// LF: Hiding the Terminal option until someone closes it
		$("#terminal-open-button-divider").toggle();
		$("#terminal-open-button").toggle();
		
		$('.editor').attr('id', 'editor');
		
		 //$("#user-workstation").splitter({type: 'h'});	
	});
