	// LF: Modifying the interface to fit the terminal between the editor and the bottom bar
	$(document).ready(function() {
		
		// LF: Decreasing the size of the editor
		$("#root-editor-wrapper").attr('style', 'height:70% !important');
		// LF: Increasing the size of the bottom bar
		$("#editor-bottom-bar").css("height", "24%");
		// Removing the annoying left bar (this code doesn't work, to remove the z-index has to be set in the .js file of the Ace Editor')
		$(".ace_gutter").css('z-index', '0');
		
		// Adding the function to the Terminal close button
		$("#terminal-close-button").on('click', function () {			
			$("#terminal_container").toggle();
			
			$("#editor-bottom-bar").css("height", "4%");
			$("#root-editor-wrapper").attr('style', 'height:91% !important');
			$(".editor").attr('style', 'height:100% !important');
			$(".ace_layer").attr('style', 'height:110% !important');
			
			$("#terminal-open-button-divider").slideToggle("slow");
			$("#terminal-open-button").slideToggle("slow");
			
			$('.editor').attr('id', 'editor');
			var aceEditor = ace.edit("editor");
			aceEditor.resize();
		});
		
		// LF: Adding the function to the Terminal open button
		$("#terminal-open-button").on('click', function () {			
			$("#terminal_container").toggle();
			
			
			$("#editor-bottom-bar").css("height", "24%");
			$("#root-editor-wrapper").attr('style', 'height:70% !important');
			$(".ace_gutter").css('z-index', '0');
			
			$("#terminal-open-button-divider").slideToggle("slow");
			$("#terminal-open-button").slideToggle("slow");
			
			$('.editor').attr('id', 'editor');
			var aceEditor = ace.edit("editor");
			aceEditor.resize();
		});
		
		//$(".editor").attr('style', 'height:70% !important');
		//$(".ace_editor").attr('style', 'height:70% !important');
		//$(".ace_nobold").attr('style', 'height:70% !important');
		//$(".ace-twilight").attr('style', 'height:70% !important');
		//$(".ace_dark").attr('style', 'height:70% !important');
		//$(".ace_nobold").attr('style', 'height:70% !important');
		//$(".ace_text-input").attr('style', 'height:70% !important');
		//$(".ace_gutter").attr('style', 'height:70% !important');
		//$(".ace_folding-enabled").attr('style', 'height:70% !important');
		//$(".ace_gutter-layer").attr('style', 'height:70% !important');
		//$(".ace_layer").attr('style', 'height:70% !important');
		//$(".ace_gutter").attr('style', 'height:70% !important');
		//$(".ace_scroller").attr('style', 'height:70% !important');
		
		
		//$("#editor-bottom-bar").prepend("<div id='terminal_container' style='height:95%'><iframe src='components/terminal/emulator/container.php'> </iframe></div>");
		// LF: Adding content to the terminal container
		//$("#terminal_container").append("<div style='font-size: 15px;'><span class='icon-cancel-circled bigger-icon' style='padding-top:2px;'></span><span style='padding-left:10px;'>Terminal</span></div><div style='width:100%; height:87%;'><iframe id='terminal' width='100%' height='100%' src='components/terminal/emulator/index.php?id=kd9kdi8nundj' style='height: 100%;'></iframe></div>");
		//$("#terminal_container").append("");
		// LF: Removing the split option that is currently bugged
		$("#editor-bottom-bar").find(".divider")[2].remove();
		$("#split").remove();
		
		// LF: Hiding the Terminal option until someone closes it
		$("#terminal-open-button-divider").toggle();
		$("#terminal-open-button").toggle();
		
		$('.editor').attr('id', 'editor');
		
	});