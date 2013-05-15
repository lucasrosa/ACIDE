// LF: Modifying the interface to fit the terminal between the editor and the bottom bar
	$(document).ready(function() {
		// LF: Decreasing the size of the editor
		$("#root-editor-wrapper").css("height", "50%");
		// LF: Increasing the size of the bottom bar
		$("#editor-bottom-bar").css("height", "45%");
		// LF: Inserting the div that will fit the terminal
		$("#editor-bottom-bar").prepend("<div id='terminal_container' style=\"height:95%\"></div>");
		// Removing the annoying left bar (this code doesn't work, to remove the z-index has to be set in the .js file of the Ace Editor')
		$(".ace_gutter").css('z-index', '0');
		// LF: Adding content to the terminal container
		$("#terminal_container").append("<h2 style=\"font-size: 15px;\">Terminal</h2><div style='width:100%; height:95%;'><iframe id='terminal' width='100%' height='100%' src='components/terminal/emulator/index.php?id=kd9kdi8nundj' style='height: 100%;'></iframe></div>");
	});
	
		// LF: Removing the split option that is currently bugged
		$("#editor-bottom-bar").find(".divider")[1].remove();
		$("#split").remove();