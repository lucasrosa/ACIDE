/*
 *  Copyright (c) Codiad & Kent Safranski (codiad.com), 
 *  lrosa@upei.ca & sbateman@upei.ca (upei.ca),
 *  distributed as-is and without warranty under the MIT License. See
 *  [root]/license.txt for more. This information must remain intact.
 */

(function(global, $){

    var codiad = global.codiad;

    $(function() {
        codiad.userlog.init();
    });

    codiad.userlog = {

        controller: 'components/userlog/controller.php',
        interval: 10000,

        init: function() {
            var _this = this;
            
            //_this.logUserOpenedSystem();
            /*
            setInterval(function() {

                _this.checkAuth();
                _this.saveDrafts();

            }, _this.interval); 
            */
           /*
           	$(window).bind('beforeunload', function(){
				//_this.logUserClosedSystem();
			});
			
			$('.ace_content').focus(function() {
			  //console.log('ace_content focused');
			});
			/* to know when a file become active. 
            amplify.subscribe('active.onFocus', function (path) {
                //console.log("Hello! How are you?");
            });
            
            /* to know when a file is being closed. 
            amplify.subscribe('active.blur', function (path) {
                //console.log("Good bye! Have a nice day!");
            });
			*/
        },
        logUserOpenedSystem: function() {
            // Run controller to register user's login
            $.get(this.controller + '?action=log_user_opened_system');
        },
        logUserClosedSystem: function() {
            // Run controller to register user's login
            $.get(this.controller + '?action=log_user_closed_system');
        },
        logUserHasFocusOnTheSystem: function() {
            // Run controller to register user's login
            $.get(this.controller + '?action=log_user_has_focus_on_the_system');
        },
        logUserHasFocusOnFile: function(active_file_path) {
        	// Run controller to register the focus in a file
        	$.get(this.controller + '?action=log_user_has_focus_on_file&path=' + active_file_path);
        },
        logUserHasFocusOnProject: function(active_project_path) {
        	// Run controller to register the focus in a project
        	$.get(this.controller + '?action=log_user_has_focus_on_project&path=' + active_project_path);
        },
        logUserHasFocusOnTerminal: function() {
        	// Get the active project
        	var active_project_path = codiad.project.getCurrent();
        	// Run controller to register the focus in a project
        	$.get(this.controller + '?action=log_user_has_focus_on_terminal&path=' + active_project_path);
        },
        logUserLastAction: function() {
        	// Run controller to register the user's last action
        	$.get(this.controller + '?action=log_user_last_action');
        },
        closeAllOpenSectionsThatReachedTimeoutOfUserLastAction: function() {
        	// Run controller to close expired open sections
        	$.get(this.controller + '?action=close_all_open_sections_that_reached_timeout_of_user_last_action');
        }
        
        
        /*
        ,

        //////////////////////////////////////////////////////////////////
        // Poll authentication
        //////////////////////////////////////////////////////////////////

        checkAuth: function() {

            // Run controller to check session (also acts as keep-alive)
            $.get(this.controller + '?action=check_auth', function(data) {

                if (data) {
                    parsed = codiad.jsend.parse(data);
                    if (parsed == 'error') {
                        // Session not set, reload
                        codiad.user.logout();
                    }
                }

            });

            // Check user
            $.get(codiad.user.controller + '?action=verify', function(data) {
                if (data == 'false') {
                    codiad.user.logout();
                }
            });

        },

        //////////////////////////////////////////////////////////////////
        // Poll For Auto-Save of drafts (persist)
        //////////////////////////////////////////////////////////////////

        saveDrafts: function() {
            $('#active-files a.changed')
                .each(function() {

                // Get changed content and path
                var path = $(this)
                    .attr('data-path');
                var content = codiad.active.sessions[path].getValue();

                // TODO: Add some visual indication about draft getting saved.

                // Set localstorage
                localStorage.setItem(path, content);

            });
        }
		*/
    };

})(this, jQuery);