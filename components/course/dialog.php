<?php

    /*
	 *  Copyright (c) UPEI lrosa@upei.ca sbateman@upei.ca
	 */


    require_once('../../common.php');
	require_once('class.course.php');
	require_once('../user/class.user.php');
	
    //////////////////////////////////////////////////////////////////
    // Verify Session or Key
    //////////////////////////////////////////////////////////////////
    
    checkSession();

    switch($_GET['action']){
    	/*
        //////////////////////////////////////////////////////////////
        // List Projects Mini Sidebar
        //////////////////////////////////////////////////////////////
        case 'sidelist':
            
            // Get access control data
            $projects_assigned = false;
            if(file_exists(BASE_PATH . "/data/" . $_SESSION['user'] . '_acl.php')){
                $projects_assigned = getJSON($_SESSION['user'] . '_acl.php');
            }
			
            ?>  
            <!-- LF: Public Projects List -->
            <div id='public-projects-containter'>        
	            <ul>
					<lh>Public Projects</lh>
	            <?php
            
	            // Get projects JSON data
				$projects = getProjectsForUser($_SESSION['user']);
	            sort($projects);
	            foreach($projects as $project=>$data){
	                $show = true;
	                if($projects_assigned && !in_array($data['path'],$projects_assigned)){ $show=false; }
	                if($show && $data['privacy'] == 'public' && $data['visibility'] == 'true'){
	                ?>
	                <li>
						<div>
							<span onclick="codiad.project.open('<?php echo($data['path']); ?>');">
								<div class="icon-archive icon"></div>
								<?php echo($data['name']); ?>
							</span>
							
							<!-- Adding a button to Submit the project as an assignment, only if it has an assignment attached to it -->
							<?php
							if ($data['assignment'] != '') {
							?>
							<span  onclick="codiad.project.submit('<?php echo($data['path']); ?>');">
								<div title="Submit Assignment" class="icon-graduation-cap icon" style="position:absolute; right:25px;">&nbsp;&nbsp;Submit</div>
							</span>
							<?php 
							}
							?>
						</div>
					</li>
                
	                <?php
	                }
	            } 
	            ?>
            
	            </ul>
			</div>
			<!-- LF: Private Projects List -->
			<div id='private-projects-containter'>
	            <ul>
					<lh>Private Projects</lh>
	            <?php
            
	            // Get projects JSON data
				$projects = getProjectsForUser($_SESSION['user']);
	            sort($projects);
	            foreach($projects as $project=>$data){
	                $show = true;
	                if($projects_assigned && !in_array($data['path'],$projects_assigned)){ $show=false; }
	                if($show && $data['privacy'] == 'private' && $data['visibility'] == 'true'){ //: needed when not using getUserProjects && $data['user'] == $_SESSION['user']){
	                ?>
	                <li>
						<div>
							<span onclick="codiad.project.open('<?php echo($data['path']); ?>');">
								<div class="icon-archive icon"></div>
								<?php echo($data['name']); ?>
							</span>
							<!-- Adding a button to Submit the project as an assignment, only if it has an assignment attached to it -->
							<?php
							if ($data['assignment'] != '' && !isset($data['assignment']['submitted_date'])) {
							?>
							<span  onclick="codiad.project.submit('<?php echo($data['path']); ?>');">
								<div title="Submit Assignment" class="icon-graduation-cap icon" style="position:absolute; right:25px;">&nbsp;&nbsp;Submit</div>
							</span>
							<?php 
							} else if (isset($data['assignment']['submitted_date'])) {
							?>
							<span style="cursor: auto;">
								<div title="The changes made in this assignment will not be submitted to evaluation anymore." class="icon-lock icon" style="position:absolute; right:25px;"></div>
							</span>
							<?
							}
							?>
						</div>
					</li>
                
	                <?php
	                }
	            } 
	            ?>
            
	            </ul>
			</div>
			<!-- LF: Shared Projects List -->
			<div id='private-projects-containter'>
	            <ul>
					<lh>Shared Projects</lh>
	            <?php
            
	            // Get projects JSON data
				$projects = getProjectsForUser($_SESSION['user']);
	            sort($projects);
	            foreach($projects as $project=>$data){
	                $show = true;
	                if($projects_assigned && !in_array($data['path'],$projects_assigned)){ $show=false; }
	                if($show && $data['privacy'] == 'shared'  && $data['visibility'] == 'true'){ //: needed when not using getUserProjects && $data['user'] == $_SESSION['user']){
	                ?>
	                <li>
						<div>
							<span onclick="codiad.project.open('<?php echo($data['path']); ?>');">
								<div class="icon-archive icon"></div>
								<?php echo($data['name']); ?>
							</span>
							
							<!-- Adding a button to Submit the project as an assignment -->
							<!-- Only if it has an assignment attached to it and if this user is the owner (the first one in the group_members array) -->
							<?php
							if ($data['assignment'] != '' && $data['group_members'][0]["username"] == $_SESSION['user'] && !isset($data['assignment']['submitted_date'])) {
							?>
							<span  onclick="codiad.project.submit('<?=($data['path']); ?>');">
								<div title="Submit Assignment" class="icon-graduation-cap icon" style="position:absolute; right:25px;">&nbsp;&nbsp;Submit</div>
							</span>
							<?php 
							} else if (isset($data['assignment']['submitted_date'])) {
							?>
							<span style="cursor: auto;">
								<div title="The changes made in this assignment will not be submitted to evaluation anymore." class="icon-lock icon" style="position:absolute; right:25px;"></div>
							</span>
							<?
							}
							?>
							
						</div>
					</li>
                
	                <?php
	                }
	            } 
	            ?>
            
	            </ul>
			</div>       
            <?php
            
            break;
        
        //////////////////////////////////////////////////////////////
        // List Projects
        //////////////////////////////////////////////////////////////
        */
        case 'list':
        
            // Get access control data
            
            ?>
            <label>Course List</label>
            <div id="course-list">
            <table width="100%">
                <tr>
                    <th>Course Code</th>
                    <th>Course Name</th>
                    <th>Students</th>
                    <?php if(TRUE){ ?><th width="5">Delete</th><?php } ?>
                </tr>
            <?php
            
            $Course = new Course();
			$courses = $Course->GetAllCourses();
            
            //sort($courses);
            foreach($courses as $course){
                ?>
                <tr>
                    <td><?php echo($course['code']); ?></td>
                    <td><?php echo($course['name']); ?></td>
                    <td><a onclick="codiad.course.manage_users('<?=$course['_id']?>');" class="icon-users bigger-icon"></a></td>
                    <td><a onclick="codiad.course.delete('<?=($course['_id']); ?>');" class="icon-cancel-circled bigger-icon"></a></td>
                </tr>
                <?php
            }
            ?>
            </table>
            </div>
            <button class="btn-left" onclick="codiad.course.create();">New Course</button><button class="btn-right" onclick="codiad.modal.unload();return false;">Close</button>
            <?php
            
            break;
         
        //////////////////////////////////////////////////////////////////////
        // Create New Project
        //////////////////////////////////////////////////////////////////////
        
        case 'create':
        
            ?>
            <form>
            <label>Course Code</label>
            <input name="course_code" autofocus="autofocus" autocomplete="off">
			<label>Course Name</label>
            <input name="course_name" autocomplete="off">
			
          <?php
                $action = 'codiad.course.list();';
                if($_GET['close'] == 'true') {
                    $action = 'codiad.modal.unload();';
                } 
            ?>           
            <button class="btn-left">Create Course</button><button class="btn-right" onclick="<?php echo $action;?>return false;">Cancel</button>
            <form>
            <?php
            break;
        /*   
        //////////////////////////////////////////////////////////////////
        // Rename
        //////////////////////////////////////////////////////////////////
        case 'rename':
        ?>
        <form>
        <input type="hidden" name="project_path" value="<?php echo($_GET['path']); ?>">
        <label><span class="icon-pencil"></span>Rename Project</label>    
        <input type="text" name="project_name" autofocus="autofocus" autocomplete="off" value="<?php echo($_GET['project_name']); ?>">  
        <button class="btn-left">Rename</button>&nbsp;<button class="btn-right" onclick="codiad.modal.unload(); return false;">Cancel</button>
        <form>
        <?php
        break;
		
        //////////////////////////////////////////////////////////////////
        // LF: Submit Project
        //////////////////////////////////////////////////////////////////
        case 'submit':
		
        ?>
        <form>
        <input type="hidden" name="project_path" value="<?php echo($_GET['path']); ?>">
        <label><span class="icon-graduation-cap"></span>Are you sure you want to submit?</label>
        <label class="sb-dialog-warning">You cannot make changes in the assignment after submitting. </label>    
        <button class="btn-left">Yes</button>&nbsp;<button class="btn-right" onclick="codiad.modal.unload(); return false;">No</button>
        <form>
        <?php
        break;       
            
        //////////////////////////////////////////////////////////////////////
        // Delete Project
        //////////////////////////////////////////////////////////////////////
        */
        case 'delete':
		$Course = new Course();
		$Course->id = $_GET['id'];
		$Course->Load();
		
        ?>
            <form>
            <input type="hidden" name="id" value="<?=($_GET['id']); ?>">
            <label>Confirm Course Deletion</label>
            <pre>Course: <?=$Course->code ?> - <?=$Course->name ?></pre>
            <button class="btn-left">Confirm</button><button class="btn-right" onclick="codiad.course.list();return false;">Cancel</button>
            <?php
            break;
     	
		//////////////////////////////////////////////////////////////////
        // LF: Manage Users
        //////////////////////////////////////////////////////////////////
        case 'manage_users':
		
		
		$Course = new Course();
		$Course->id = $_GET['id'];
		$users_in_course = $Course->GetUsersInCourse();
						
        ?>
        <form id="group_users_form">
        <input type="hidden" name="course_id" value="<?=($_GET['id']); ?>">
        <label><span class="icon-users"></span>Manage Students:</label>
	        <table width="100%">
	                <tr>
	                    <th>Username</th>
	                    <th>Permitted</th>
	                </tr>    
			      <?php 
			        	
			        	$User = new User();
						$users = $User->users;
						
						foreach($users as $user) {
							$username = $user['username'];
							if ($user['type'] == 'student') {
								?>
								<tr>
									<td><?=$username; ?></td>
									<td><input type="checkbox" name="group_user[]" value="<?=$username; ?>"
										<?  if(in_array($username, $users_in_course)) { echo "checked=\"checked\""; } ?>/>
									</td>
								</tr>
								<?php
							}
						}
			        ?>
         	</table> 
        	<button class="btn-right" onclick="send_group_users_form(); return false;">Save</button>&nbsp;<button class="btn-right" onclick="codiad.modal.unload(); return false;">Cancel</button>
        
        <script>
		    function send_group_users_form() {
		    	var form = $('#group_users_form');
		    	$.ajax( {
			      type: "POST",
			      url: 'components/course/controller.php?action=manage_users',
			      data: form.serialize(),
			      dataType: 'json', 
			      success: function( response ) {
			        if(response.status == 'success') {
			        	codiad.modal.unload();
			        	codiad.message.success(i18n('Course updated'));
			        } else if(response.status == 'error_database') {
			        	codiad.message.error(i18n('Changes couldn\'t be saved on database.'));
			        }
			      },
			      error: function( response ) {
			        codiad.modal.unload();
			        codiad.message.error(i18n('An unexpected error ocurred. Please try again.'));
			      }
			    } );
			}
		  </script>
        	
        <?php
        break;     
    }
    
?>
