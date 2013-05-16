// LF: Modifying the interface to fit the terminal between the editor and the bottom bar
	$(document).ready(function() {
		// LF: Decreasing the size of the editor
		$("#root-editor-wrapper").attr('style', 'height:70% !important');
		$(".editor").attr('style', 'height:70% !important');
		$(".ace_editor").attr('style', 'height:70% !important');
		$(".ace_nobold").attr('style', 'height:70% !important');
		$(".ace-twilight").attr('style', 'height:70% !important');
		$(".ace_dark").attr('style', 'height:70% !important');
		$(".ace_nobold").attr('style', 'height:70% !important');
		$(".ace_text-input").attr('style', 'height:70% !important');
		$(".ace_gutter").attr('style', 'height:70% !important');
		$(".ace_folding-enabled").attr('style', 'height:70% !important');
		$(".ace_gutter-layer").attr('style', 'height:70% !important');
		$(".ace_layer").attr('style', 'height:70% !important');
		$(".ace_gutter").attr('style', 'height:70% !important');
		$(".ace_scroller").attr('style', 'height:70% !important');
		/*
		$(".ace_content").attr('style', 'height:70% !important');
		$(".ace_print-margin-layer").attr('style', 'height:70% !important');
		$(".ace_layer").attr('style', 'height:70% !important');
		$(".ace_content").attr('style', 'height:70% !important');
		$(".ace_marker-layer").attr('style', 'height:70% !important');
		$(".ace_text-layer").attr('style', 'height:70% !important');
		$(".ace_hidden-cursors").attr('style', 'height:70% !important');
		$(".ace_cursor-layer").attr('style', 'height:70% !important');
		$(".ace_layer").attr('style', 'height:70% !important');
		$(".ace_scrollbar").attr('style', 'height:70% !important');
		$(".ace_scrollbar-inner").attr('style', 'height:70% !important');
		$(".ace_gutter-tooltip").attr('style', 'height:70% !important');
		$(".ace_content").attr('style', 'height:70% !important');
		$(".ace_content").attr('style', 'height:70% !important');
		$(".ace_content").attr('style', 'height:70% !important');
		*/
		
		//      
		// LF: Increasing the size of the bottom bar
		$("#editor-bottom-bar").css("height", "24%");
		// LF: Inserting the div that will fit the terminal
		$("#editor-bottom-bar").prepend("<div id='terminal_container' style=\"height:95%\"></div>");
		// Removing the annoying left bar (this code doesn't work, to remove the z-index has to be set in the .js file of the Ace Editor')
		$(".ace_gutter").css('z-index', '0');
		// LF: Adding content to the terminal container
		$("#terminal_container").append("<h2 style=\"font-size: 15px;\">Terminal</h2><div style='width:100%; height:87%;'><iframe id='terminal' width='100%' height='100%' src='components/terminal/emulator/index.php?id=kd9kdi8nundj' style='height: 100%;'></iframe></div>");
	});
	
		// LF: Removing the split option that is currently bugged
		$("#editor-bottom-bar").find(".divider")[1].remove();
		$("#split").remove();