<!--
/*
*  PHP+JQuery Temrinal Emulator by Fluidbyte <http://www.fluidbyte.net>
*
*  This software is released as-is with no warranty and is complete free
*  for use, modification and redistribution
*/
-->
<link rel="stylesheet" href="components/terminal/emulator/css/screen.css">
<div id="terminal">

    <div id="output"></div>

    <div id="command">
        <div id="prompt">&gt;</div>
		<!-- LF: Setting the color of the terminal line to white -> needs the !important tag -->
        <input id="prompt_text" style="padding-bottom:18px; color:white !important;" type="text">
    </div>

</div>
<!-- <script src="js/system.js"></script> -->

<script>
	$(function(){ terminal.init(); });
	
	var terminal = {
	    
	    // Controller
	    controller : 'components/terminal/emulator/term.php',
	    
	    // DOM Objects
	    command : $('#command input'),
	    screen : $('#terminal'),
	    output : $('#terminal>#output'),
	    target_name : '',
	    
	    // Command History
	    command_history : [],
	    command_counter : -1,
	    history_counter : -1,
	    
	    init : function(){
	        terminal.listener();
	        // Start with authentication
	        terminal.process_command();
	    },
	    
	    listener : function(){
	        terminal.command.focus().keydown(function(e){
	            var code = (e.keyCode ? e.keyCode : e.which);
	            var command = terminal.get_command();
	            switch(code){
	                // Enter key, process command
	                case 13:
	                    if(command=='clear'){
	                        terminal.clear();
	                    }else{
	                        terminal.command_history[++terminal.command_counter] = command;
	                        terminal.history_counter = terminal.command_counter;
	                        terminal.process_command();
	                        terminal.command.val('Processing...').focus();
	                    }
	                    break;
	                // Up arrow, reverse history
	                case 38:
	                    if(terminal.history_counter>=0){
	                        $(this).val(terminal.command_history[terminal.history_counter--]);
	                    }
	                    break;
	                // Down arrow, forward history
	                case 40:
	                    if (terminal.history_counter <= terminal.command_counter) {
	                        $(this).val(terminal.command_history[++terminal.history_counter]);
	                    }
	                    break;
	            }
	        });
	    },
	    
	    process_command : function(){
	        var command = terminal.get_command();
	        $.post(terminal.controller,{command:command},function(data){
	            terminal.command.val('').focus();
	            switch(data){
	                case '[CLEAR]':
	                    terminal.clear();
	                    break;
	                case '[CLOSED]':
	                    terminal.clear();
	                    terminal.process_command();
	                    window.parent.codiad.modal.unload();
	                    break;
	                case '[AUTHENTICATED]':
	                    terminal.command_history = [];
	                    terminal.command_counter = -1;
	                    terminal.history_counter = -1;
	                    terminal.clear();
	                    break;
	                case 'Enter Password:':
	                    terminal.clear();
	                    terminal.display_output('Authentication Required',data);
	                    terminal.command.css({'color':'#333'});
	                    break;
	                default:
	                    terminal.display_output(command,data);
	                    if (data.substr(0, 10) != "opnths::: ") {
	                    	$('#prompt_text').focus();
	                    }
	                    //$("#prompt").text(">");
	                    var this_name = "";
	                    $.post(terminal.controller,{command:'get_current_directory', target_name: this_name},function(data) {
	                    	data = data.split("/");
	                    	var directory =  data[data.length -1];
				       		$("#prompt").text("" + directory + "/ >");
				       		$("#prompt_text").css("padding-left" , ($("#prompt").width() + 10));	
				       	});
	            }
	        });
	    },
	    
	    change_directory : function(name){
	    	var this_name = name;
	    	if (this_name != '') {
		        $.post(terminal.controller,{command:'change_directory', target_name: this_name},function(data) {
		            if (this_name != '') {
		            	terminal.display_output("Directory changed to '" + this_name + "' project root directory.", null);
		            	//$("#prompt").text("/" + this_name + "/ >");	
		            }
		       });
			}
	    },
	    
	    get_command : function(){
	        return terminal.command.val();
	    },
	    
	    display_output : function(command,data){
	    	if (data != null) {
	    		if (data.substr(0, 10) == "opnths::: ") {
	    			var this_url = data.substr(10);
	    			this_url = this_url.replace("&amp;","&");
	    			terminal.output.append('<pre class="command">'+command+'</pre><pre class="data">File ready to execution. Open the ".jnlp" file after it\'s download is done.</pre>');
	    			terminal.output.append('<pre class="command">'+command+'</pre><pre class="data">Click <a id="link_to_jnlp" href="'+this_url+'">here.</a> Or press enter to execute the file.</pre>');
	    			
	    			
	    			// create a new keyup event
					//var press =jQuery.Event("keydown", true);
					//press.ctrlKey = false;
					//press.which = 13;
	    			
	    			$('a#link_to_jnlp').focus();
	    			$("a#link_to_jnlp").click(function() {
						$('#prompt_text').focus();
					});
					
	    			//console.log("after that");
	    		} else {
	    			terminal.output.append('<pre class="command">'+command+'</pre><pre class="data">'+data+'</pre>');
	    		}
	    			
	    	} else {
	    		terminal.output.append('<pre class="command">'+command+'</pre>');
	    	}
	        
	        terminal.screen.scrollTop(terminal.output.height());
	        // LF
	        document.getElementById('prompt_text').scrollIntoView(true);
	        
	    },
	    
	    clear : function(){
	        terminal.output.html('');
	        terminal.command.css({ 'color':'#fff' }).val('');
	    }
	    
	};
</script>