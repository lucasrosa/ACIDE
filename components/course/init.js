	/*
	 *  Copyright (c) UPEI lrosa@upei.ca sbateman@upei.ca
	 */

(function(global, $){

    var codiad = global.codiad;

    $(function() {
        codiad.course.init();
    });

    codiad.course = {

        controller: 'components/course/controller.php',
        dialog: 'components/course/dialog.php',

        init: function() {
            //this.loadCurrent();
            //this.loadSide();
            
            var _this = this;
            /*
            $('#projects-create').click(function(){
                codiad.project.create('true');
            });
            
            $('#projects-collapse').click(function(){
                if (!_this._sideExpanded) {
                    _this.projectsExpand();
                } else {
                    _this.projectsCollapse();
                }
            });
            */
        },

        //////////////////////////////////////////////////////////////////
        // Get Current Project
        //////////////////////////////////////////////////////////////////
		/*
        loadCurrent: function() {
            $.get(this.controller + '?action=get_current', function(data) {
                var projectInfo = codiad.jsend.parse(data);
                if (projectInfo != 'error') {
                    $('#file-manager')
                        .html('')
                        .append('<ul id="project-root-ul"><li><a id="project-root" data-type="root" class="directory" data-path="' + projectInfo.path + '">' + projectInfo.name + '</a></li></ul>');
                        if (projectInfo.description_url != 'null') {
                        	$('#project-root-ul').append('<ul><li><a href="'+ projectInfo.description_url +'" target="_blank" class="description_file" >Assignment Description</a></li></ul>');	
                        }
                    codiad.filemanager.index(projectInfo.path);
                    codiad.user.project(projectInfo.path);
                    codiad.message.success(i18n('Project ' + projectInfo.name + ' Loaded'));
                }
            });
        },

        //////////////////////////////////////////////////////////////////
        // Open Project
        //////////////////////////////////////////////////////////////////

        open: function(path) {
            var _this = this;
            codiad.finder.contractFinder();
            $.get(this.controller + '?action=open&path=' + path, function(data) {
                var projectInfo = codiad.jsend.parse(data);
                if (projectInfo != 'error') {
                    _this.loadCurrent();
                    codiad.modal.unload();
                    codiad.user.project(path);
                }
            });
        },
		*/
        //////////////////////////////////////////////////////////////////
        // Open the course manager dialog
        //////////////////////////////////////////////////////////////////

        list: function() {
            $('#modal-content form')
                .die('submit'); // Prevent form bubbling
            codiad.modal.load(500, this.dialog + '?action=list');
        },
        /*
        //////////////////////////////////////////////////////////////////
        // Load and list projects in the sidebar.
        //////////////////////////////////////////////////////////////////
        loadSide: function() {
            $('.sb-projects-content').load(this.dialog + '?action=sidelist');
            this._sideExpanded = true;
        },
        
        projectsExpand: function() {
            this._sideExpanded = true;
            $('#side-projects').css('height', 276+'px');
            $('.project-list-title').css('right', 0);
            $('.sb-left-content').css('bottom', 276+'px');
            $('#projects-collapse')
                .removeClass('icon-up-dir')
                .addClass('icon-down-dir');
        },
        
        projectsCollapse: function() {
            this._sideExpanded = false;
            $('#side-projects').css('height', 33+'px');
            $('.project-list-title').css('right', 0);
            $('.sb-left-content').css('bottom', 33+'px');
            $('#projects-collapse')
                .removeClass('icon-down-dir')
                .addClass('icon-up-dir');
        },
        
        //////////////////////////////////////////////////////////////////
        // Open the project manager dialog
        //////////////////////////////////////////////////////////////////

        list: function() {
            $('#modal-content form')
                .die('submit'); // Prevent form bubbling
            codiad.modal.load(500, this.dialog + '?action=list');
        },
		*/
        //////////////////////////////////////////////////////////////////
        // Create Course
        //////////////////////////////////////////////////////////////////

        create: function(close) {
            var _this = this;
            create = true;
            codiad.modal.load(500, this.dialog + '?action=create&close=' + close);
            $('#modal-content form')
                .live('submit', function(e) {
                e.preventDefault();
                var courseCode = $('#modal-content form input[name="course_code"]')
                    .val(),
                    courseName = $('#modal-content form input[name="course_name"]')
                    .val();
                    
                if(create) {    
                    $.get(_this.controller + '?action=create&course_code=' + courseCode + '&course_name=' + courseName, function(data) {
                        createResponse = codiad.jsend.parse(data);
                        if (createResponse != 'error') {
                            codiad.modal.unload();
                            codiad.message.success(i18n('Course created with success!'));
                        }
                    });
                }
            });
        },
        /*
        
        //////////////////////////////////////////////////////////////////
        // Rename Project
        //////////////////////////////////////////////////////////////////

        rename: function(path) {
            var _this = this;
            codiad.modal.load(500, this.dialog + '?action=rename&path=' + escape(path));
            $('#modal-content form')
                .live('submit', function(e) {
                e.preventDefault();
                var projectPath = $('#modal-content form input[name="project_path"]')
                    .val();
                var projectName = $('#modal-content form input[name="project_name"]')
                    .val();    
                $.get(_this.controller + '?action=rename&project_path=' + projectPath + '&project_name=' + projectName, function(data) {
                   renameResponse = codiad.jsend.parse(data);
                    if (renameResponse != 'error') {
                        codiad.message.success(i18n('Project renamed'));
                        _this.loadSide();
                        $('#file-manager a[data-type="root"]').html(projectName);
                        codiad.modal.unload();
                    }
                });
            });
        },
        */
        //////////////////////////////////////////////////////////////////
        // Delete Project
        //////////////////////////////////////////////////////////////////

        delete: function(id) {
            var _this = this;
            codiad.modal.load(500, this.dialog + '?action=delete&id=' + escape(id));
            $('#modal-content form')
                .live('submit', function(e) {
                e.preventDefault();
                var id = $('#modal-content form input[name="id"]')
                    .val();
                $.get(_this.controller + '?action=delete&id=' + id, function(data) {
                    deleteResponse = codiad.jsend.parse(data);
                    if (deleteResponse != 'error') {
                    	codiad.modal.unload();
                        codiad.message.success('Course Deleted');
                    }
                });
            });
        },
        /*
        //////////////////////////////////////////////////////////////////
        // Check Absolute Path
        //////////////////////////////////////////////////////////////////
        
        isAbsPath: function(path) {
            if ( path.indexOf("/") == 0 ) {
                return true;
            } else {
                return false;
            }
        },

        //////////////////////////////////////////////////////////////////
        // Get Current (Path)
        //////////////////////////////////////////////////////////////////

        getCurrent: function() {
            var _this = this;
            var currentResponse = null;
            $.ajax({
                url: _this.controller + '?action=current',
                async: false,
                success: function(data) {
                    currentResponse = codiad.jsend.parse(data);
                } 
             });
            return currentResponse;
        },
		
        //////////////////////////////////////////////////////////////////
        // LF: Submit Project (Submit as an Assignment)
        //////////////////////////////////////////////////////////////////
		
        submit: function(path) {
	            var _this = this;
	            codiad.modal.load(500, this.dialog + '?action=submit&path=' + escape(path));
	            $('#modal-content form')
	                .live('submit', function(e) {
	                e.preventDefault();
	                var projectPath = $('#modal-content form input[name="project_path"]')
	                    .val();
						
	                $.get(_this.controller + '?action=submit&project_path=' + projectPath, function(data) {
	                   renameResponse = codiad.jsend.parse(data);
	                    if (renameResponse != 'error') {
	                        codiad.message.success(i18n('Project submited'));
	                        _this.loadSide();
	                        codiad.modal.unload();
	                    }
	                });
	            });
        },
        */
        //////////////////////////////////////////////////////////////////
        // LF: Manage users of the Course
        //////////////////////////////////////////////////////////////////
		
        manage_users: function(id) {
	            var _this = this;
	            codiad.modal.load(500, this.dialog + '?action=manage_users&id=' + escape(id));
	            /*
	            $('#modal-content form')
	                .live('submit', function(e) {
	                e.preventDefault();
	                var projectPath = $('#modal-content form input[name="project_path"]')
	                    .val();
	                var group_user = $('#modal-content form input[name="group_user"]')
	                    .val();    
					/*	
	                $.get(_this.controller + '?action=anage_users&project_path=' + projectPath + '&group_user=' + group_user, function(data) {
	                   renameResponse = codiad.jsend.parse(data);
	                    if (renameResponse != 'error') {
	                        codiad.message.success(i18n('Project submited'));
	                        _this.loadSide();
							//$('#file-manager a[data-type="root"]').html("[S] " + $('#file-manager a[data-type="root"]').html());  // This changes the name (of the active project) in the file inspector
	                        codiad.modal.unload();
	                    }
	                });
	                
	            }); 
	            */
        }
		
		
		
    };
})(this, jQuery);
