<?php

    /*
    *  Copyright (c) Codiad & Kent Safranski (codiad.com), distributed
    *  as-is and without warranty under the MIT License. See 
    *  [root]/license.txt for more. This information must remain intact.
    */


    require_once('../../common.php');
    require_once('../user/class.user.php');
	require_once('../course/class.course.php');
	require_once('class.project.php');
	
    //////////////////////////////////////////////////////////////////
    // Verify Session or Key
    //////////////////////////////////////////////////////////////////
    
    checkSession();

    switch($_GET['action']){
    
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
            	$User = new User();
				$User->username = $_SESSION['user'];
				$courses = $User->GetUserCourses();
				
	            // Get projects JSON data
				$projects = getProjectsForUser($_SESSION['user']);
	            sort($projects);
	            
				for ($l = 0; $l < count($courses); $l++) {
					$course_title_added = FALSE;
					$course_div_added = FALSE;
						
			        foreach($projects as $project=>$data){
		                $show = true;
		                if($projects_assigned && !in_array($data['path'],$projects_assigned)) {
		                	$show=false; 
						}
						$user_in_course = TRUE;
						//if (isset($data['course'])) {
						//	if (!in_array($data['course'], $courses)) {
						//		$user_in_course = FALSE;		
						//	}
						//}
						
						
		                if($show && $data['privacy'] == 'public' 
		                	&& $data['visibility'] == 'true' 
		                	&& $data['course'] == $courses[$l] 
						  ) {// && $user_in_course){
		                	if (!$course_title_added) {
		                		$This_course = new Course();
								$This_course->id = $courses[$l];
								$This_course->Load();
								$course_title_added = TRUE;
		               		?>
		                		<li id="public_li_<?=$This_course->id?>" style="font-size:13px; font-style:italic;  cursor: pointer;">
			                			<span id="public_span_right_<?=$This_course->id?>" class="icon-right-dir icon" alt="Collapse" title="Collapse"></span>
			                			<span id="public_span_down_<?=$This_course->id?>" class="icon-down-dir icon" alt="Collapse" title="Collapse"></span>
			                			<i><?=$This_course->code?></i>
			                		</li>
			                		
			                		<script>
			                			$('#public_div_<?=$This_course->id?>').hide();
			                			$('#public_span_down_<?=$This_course->id?>').hide();
			                			$("#public_li_<?=$This_course->id?>").on('click', function () {
			                				$('#public_div_<?=$This_course->id?>').slideToggle();
			                				$('#public_span_right_<?=$This_course->id?>').toggle();
			                				$('#public_span_down_<?=$This_course->id?>').toggle();
			                				
			                			});
			                		</script>
			                		
			                		<div id="public_div_<?=$This_course->id?>" class="acide-course" >
		                	<?		
		                	}
		                	?>
		                
		                <li style="padding-left:20px;">
							<div>
								<span onclick="codiad.project.open('<?php echo($data['path']); ?>');">
									<div class="icon-archive icon"></div>
									<?php echo($data['name']); ?>
								</span>
							</div>
						</li>
	                
		                <?php
		                }
		            }

                if ($course_title_added && !$course_div_added) {
                    $course_div_added = TRUE;
                ?>
                    </div>
            <?		
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
            	
            	$User = new User();
				$User->username = $_SESSION['user'];
				$courses = $User->GetUserCourses();
				$User->Load();
				
	            // Get projects JSON data
				$projects = getProjectsForUser($_SESSION['user']);
	            sort($projects);
	            
	            for ($l = 0; $l < count($courses); $l++) {
					$course_title_added = FALSE;
	            	$course_div_added = FALSE;
	            	
					########################
					########PROFESSOR { ####
					########################
					if ($User->type != "student") {					
						
						$This_course = new Course();
						$This_course->id = $courses[$l];
						$This_course->Load();
						
						$students = $This_course->GetUsersInCourse();
						
						// Add course title for this course
						?>
						<li id="li_<?=$This_course->id?>" style="font-size:13px; font-style:italic;  cursor: pointer;">
                			<span id="span_right_<?=$This_course->id?>" class="icon-right-dir icon" alt="Collapse" title="Collapse"></span>
                			<span id="span_down_<?=$This_course->id?>" class="icon-down-dir icon" alt="Collapse" title="Collapse"></span>
                			<i><?=$This_course->code?></i>
                		</li>
                		
                		<script>
                			$('#div_<?=$This_course->id?>').hide();
                			$('#span_down_<?=$This_course->id?>').hide();
                			$("#li_<?=$This_course->id?>").on('click', function () {
                				$('#div_<?=$This_course->id?>').slideToggle();
                				$('#span_right_<?=$This_course->id?>').toggle();
                				$('#span_down_<?=$This_course->id?>').toggle();
                				
                			});
                		</script>
                		
                		<div id="div_<?=$This_course->id?>" class="acide-course" >
						<?
						
						
						// go through all the students
						for ($istudents = 0; $istudents < count($students); $istudents++) {
						
							$Student = new User();
							$Student->username = $students[$istudents];
							//$courses = $User->GetUserCourses();
							$Student->Load(); 
							
							
							// This list only contains students, if not a student, jumps to the next one
							if ($Student->type != "student") {
								continue;
							}
							
							// Add li for each student
						?>
							<li id="li_<?=$This_course->id?>_<?=$students[$istudents]?>" style="font-size:13px; font-style:italic;  cursor: pointer;">
	                			<span id="span_right_<?=$This_course->id?>_<?=$students[$istudents]?>" 
	                				class="icon-right-dir icon" alt="Collapse" title="Collapse"></span>
	                			<span id="span_down_<?=$This_course->id?>_<?=$students[$istudents]?>" 
	                				class="icon-down-dir icon" alt="Collapse" title="Collapse"></span>
	                			<i><?=$students[$istudents]?></i>
	                		</li>
	                		
	                		<script>
	                			$('#div_<?=$This_course->id?>_<?=$students[$istudents]?>').hide();
	                			$('#span_down_<?=$This_course->id?>_<?=$students[$istudents]?>').hide();
	                			$("#li_<?=$This_course->id?>_<?=$students[$istudents]?>").on('click', function () {
	                				$('#div_<?=$This_course->id?>_<?=$students[$istudents]?>').slideToggle();
	                				$('#span_right_<?=$This_course->id?>_<?=$students[$istudents]?>').toggle();
	                				$('#span_down_<?=$This_course->id?>_<?=$students[$istudents]?>').toggle();
	                				
	                			});
	                		</script>
	                		
	                		<div id="div_<?=$This_course->id?>_<?=$students[$istudents]?>" class="acide-assignments" >
						<?
							// Get assignments for this user
							$Project = new Project();
							$this_user_projects = $Project->GetProjectsForUser($students[$istudents]);
							//for ($iprojects = 0; $iprojects < count($this_user_projects); $iprojects++) {
							foreach($this_user_projects as $this_project){
								// If this project is not in this course or is not an assignment, go to the next one
								if (($this_project["course"] != $This_course->id) || (!isset($this_project["assignment"]["name"]))) {
									continue;
								}
								
								// Show the project
								?>
								<li style="padding-left:20px;">
									<div>
										<span onclick="codiad.project.open('<?php echo($data['path']); ?>');">
											<div class="icon-archive icon"></div>
											<?=$this_project['name'];?>
										</span>
										<!-- Adding a button to Submit the project as an assignment, only if it has an assignment attached to it -->
										<?php
										if ($this_project['assignment'] != '' && !isset($this_project['assignment']['submitted_date'])) {
										?>
										<span  onclick="codiad.project.submit('<?php echo($this_project['path']); ?>');">
											<div title="Submit Assignment" 
												class="icon-graduation-cap icon" style="position:absolute; right:25px;">
												&nbsp;&nbsp;Submit
											</div>
										</span>
										<?php 
										} else if (isset($this_project['assignment']['submitted_date'])) {
										?>
										<span style="cursor: auto;">
											<div title="The changes made in this assignment will not be submitted to evaluation anymore." 
												class="icon-lock icon" style="position:absolute; right:25px;">
											</div>
										</span>
										<?
										}
										?>
									</div>
								</li>
								<?
							}
						?>
							</div>
						<?	
						}
						// Close acide-course DIV
						?>
                		</div>
						<?
						
					########################
					#### } PROFESSOR########
					########################
					} else {
					
						########################
						#########STUDENT { #####
						########################
						# Assignments
						$Project = new Project();
						$assignments  = $Project->GetAssignmentsInTheSameCoursesOfUser($_SESSION['user'], $courses[$l]);
						array_unshift($assignments, "User");
						//error_log(print_r($assignments, TRUE));
						
						for ($a = 0; $a < count($assignments); $a++) {
							$assignment_title_added = FALSE;
							$assignment_div_added = FALSE;				
				            foreach($projects as $project=>$data){
				                $show = true;
				                if($projects_assigned && !in_array($data['path'],$projects_assigned)){ $show=false; }
				                if($show 
				                	&& $data['privacy'] == 'private' 
				                	&& $data['visibility'] == 'true' 
				                	&& $data['course'] == $courses[$l]
									&& (
										($data["assignment"] == '' && $a == 0)
										||
										(@$data["assignment"]['id'] == $assignments[$a]['id'])
								       )
									  ){ //: needed when not using getUserProjects && $data['user'] == $_SESSION['user']){
				                	if (!$course_title_added) {
				                		$This_course = new Course();
										$This_course->id = $courses[$l];
										$This_course->Load();
										$course_title_added = TRUE;
				               		?>
				                		<li id="li_<?=$This_course->id?>" style="font-size:13px; font-style:italic;  cursor: pointer;">
				                			<span id="span_right_<?=$This_course->id?>" class="icon-right-dir icon" alt="Collapse" title="Collapse"></span>
				                			<span id="span_down_<?=$This_course->id?>" class="icon-down-dir icon" alt="Collapse" title="Collapse"></span>
				                			<i><?=$This_course->code?></i>
				                		</li>
				                		
				                		<script>
				                			$('#div_<?=$This_course->id?>').hide();
				                			$('#span_down_<?=$This_course->id?>').hide();
				                			$("#li_<?=$This_course->id?>").on('click', function () {
				                				$('#div_<?=$This_course->id?>').slideToggle();
				                				$('#span_right_<?=$This_course->id?>').toggle();
				                				$('#span_down_<?=$This_course->id?>').toggle();
				                				
				                			});
				                		</script>
				                		
				                		<div id="div_<?=$This_course->id?>" class="acide-course" >
				                	<?		
				                	}
				                	
	                                //the name for an assignment
				                	if (!$assignment_title_added && $a > 0 && $User->type != "student") {
											$assignment_title_added = TRUE;
				               		?>
				                		<li id="li_<?=$assignments[$a]['id']?>" style="font-size:12px; text-decoration:underline; cursor: pointer;">
				                			<span id="span_right_<?=$assignments[$a]['id']?>" class="icon-right-dir icon" alt="Collapse" title="Collapse"></span>
				                			<span id="span_down_<?=$assignments[$a]['id']?>" class="icon-down-dir icon" alt="Collapse" title="Collapse"></span>
				                			<i><?=$assignments[$a]['name']?></i>
				                		</li>
				                		
				                		<script>
				                			$('#div_<?=$assignments[$a]['id']?>').hide();
				                			$('#span_down_<?=$assignments[$a]['id']?>').hide();
				                			$("#li_<?=$assignments[$a]['id']?>").on('click', function () {
				                				$('#div_<?=$assignments[$a]['id']?>').slideToggle();
				                				$('#span_right_<?=$assignments[$a]['id']?>').toggle();
				                				$('#span_down_<?=$assignments[$a]['id']?>').toggle();
				                				
				                			});
				                		</script>
				                		
				                		<div id="div_<?=$assignments[$a]['id']?>" >
				                	<?		
				                	}
				                	?>
				                <li style="padding-left:20px;">
									<div>
										<span onclick="codiad.project.open('<?php echo($data['path']); ?>');">
											<div class="icon-archive icon"></div>
											<?
	                                        //if it's the first assignment or the 
	                                        //user is a student use just the name 
	                                        //of the assignment
											if ($a == 0 || $User->type == "student") {
												echo $data['name'];	
	                                        //otherwise show the name of the user 
	                                        //owns the assignment
											} else {
												echo $data['group_members'][0]['username'];
											}
											?>
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
							if ($assignment_title_added && !$assignment_div_added) {
						    	$assignment_div_added = TRUE;
						    ?>
						        </div>
						    <?		
						    }
						}
						if ($course_title_added && !$course_div_added) {
							$course_div_added = TRUE;
						?>
							</div>
						<?		
						}
						########################
						####### } STUDENT#######
						########################
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
            	
            	$User = new User();
				$User->username = $_SESSION['user'];
				$courses = $User->GetUserCourses();
				$User->Load();
				
	            // Get projects JSON data
				$projects = getProjectsForUser($_SESSION['user']);
	            sort($projects);
	            
	            for ($l = 0; $l < count($courses); $l++) {
					$course_title_added = FALSE;
					$course_div_added = FALSE;
					
					# Assignments
					$Project = new Project();
					$assignments  = $Project->GetAssignmentsInTheSameCoursesOfUser($_SESSION['user'], $courses[$l]);
					array_unshift($assignments, "User");
					
					for ($a = 0; $a < count($assignments); $a++) {
						$assignment_title_added = FALSE;
						$assignment_div_added = FALSE;
						
			            foreach($projects as $project=>$data){
			                $show = true;
			                if($projects_assigned && !in_array($data['path'],$projects_assigned)){ $show=false; }
			                if($show 
			                	&& $data['privacy'] == 'shared'  
			                	&& $data['visibility'] == 'true' 
			                	&& $data['course'] == $courses[$l]
								&& (
									($data["assignment"] == '' && $a == 0)
									||
									(@$data["assignment"]['id'] == $assignments[$a]['id'])
							       )
			                	
							  ){ //: needed when not using getUserProjects && $data['user'] == $_SESSION['user']){
			                	if (!$course_title_added) {
			                		$This_course = new Course();
									$This_course->id = $courses[$l];
									$This_course->Load();
									$course_title_added = TRUE;
			               		?>
			                		<li id="shared_li_<?=$This_course->id?>" style="font-size:13px; font-style:italic;  cursor: pointer;">
			                			<span id="shared_span_right_<?=$This_course->id?>" class="icon-right-dir icon" alt="Collapse" title="Collapse"></span>
			                			<span id="shared_span_down_<?=$This_course->id?>" class="icon-down-dir icon" alt="Collapse" title="Collapse"></span>
			                			<i><?=$This_course->code?></i>
			                		</li>
			                		
			                		<script>
			                			$('#shared_div_<?=$This_course->id?>').hide();
			                			$('#shared_span_down_<?=$This_course->id?>').hide();
			                			$("#shared_li_<?=$This_course->id?>").on('click', function () {
			                				$('#shared_div_<?=$This_course->id?>').slideToggle();
			                				$('#shared_span_right_<?=$This_course->id?>').toggle();
			                				$('#shared_span_down_<?=$This_course->id?>').toggle();
			                				
			                			});
			                		</script>
			                		
			                		<div id="shared_div_<?=$This_course->id?>" class="acide-course" >
			                	<?		
			                	}
								
			                	if (!$assignment_title_added && $a > 0 && $User->type != "student") {
										$assignment_title_added = TRUE;
			               		?>
			                		<li id="shared_li_<?=$assignments[$a]['id']?>" style="font-size:12px; text-decoration:underline; cursor: pointer;">
			                			<span id="shared_span_right_<?=$assignments[$a]['id']?>" class="icon-right-dir icon" alt="Collapse" title="Collapse"></span>
			                			<span id="shared_span_down_<?=$assignments[$a]['id']?>" class="icon-down-dir icon" alt="Collapse" title="Collapse"></span>
			                			<i><?=$assignments[$a]['name']?></i>
			                		</li>
			                		
			                		<script>
			                			$('#shared_div_<?=$assignments[$a]['id']?>').hide();
			                			$('#shared_span_down_<?=$assignments[$a]['id']?>').hide();
			                			$("#shared_li_<?=$assignments[$a]['id']?>").on('click', function () {
			                				$('#shared_div_<?=$assignments[$a]['id']?>').slideToggle();
			                				$('#shared_span_right_<?=$assignments[$a]['id']?>').toggle();
			                				$('#shared_span_down_<?=$assignments[$a]['id']?>').toggle();
			                				
			                			});
			                		</script>
			                		
			                		<div id="shared_div_<?=$assignments[$a]['id']?>" >
			                			
			                	<?		
			                	}
			                	?>
			                <li style="padding-left:20px;">
								<div>
									<span onclick="codiad.project.open('<?php echo($data['path']); ?>');">
										<div class="icon-archive icon"></div>
										<?
										if ($a == 0 || $User->type == "student") {
											echo $data['name'];	
										} else {
											echo $data['group_members'][0]['username'];
										}
										?>
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

									if ($assignment_title_added && !$assignment_div_added) {
					                	$assignment_div_added = TRUE;
					                ?>
					                </div>
					                <?		
					                }
			    	}

								if ($course_title_added && !$course_div_added) {
					               	$course_div_added = TRUE;
					            ?>
					            </div>
					            <?		
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
        
        case 'list':
        
            // Get access control data
            $projects_assigned = false;
            if(file_exists(BASE_PATH . "/data/" . $_SESSION['user'] . '_acl.php')){
                $projects_assigned = getJSON($_SESSION['user'] . '_acl.php');
            }
            
            ?>
            <label>Project List</label>
            <div id="project-list">
            <table width="100%">
                <tr>
                    <th width="5">Open</th>
                    <th>Project Name</th>
                    <th>Path</th>
                    <th>Group Members</th>
                    <?php if(checkAccess()){ ?><th width="5">Delete</th><?php } ?>
                </tr>
            <?php
            
            // Get projects JSON data
            $projects = getProjectsForUser($_SESSION['user']);
            sort($projects);
            foreach($projects as $project=>$data){
                $show = true;
                if($projects_assigned && !in_array($data['path'],$projects_assigned)){ $show=false; }
                if($show && $data['visibility'] == 'true'){
                ?>
                <tr>
                    <td><a onclick="codiad.project.open('<?php echo($data['path']); ?>');" class="icon-folder bigger-icon"></a></td>
                    <td><?php echo($data['name']); ?></td>
                    <td><?php echo($data['path']); ?></td>
                    <?php
                    if($data['privacy'] != 'public') {
                    ?> 
                    	<td><a onclick="codiad.project.manage_users('<?php echo($data['path']); ?>');" class="icon-users bigger-icon"></a></td>
                    <?php
					} else {
                    ?>
                    	<td><span class="icon-user bigger-icon"></span></td>
                    <?php
					}
                    ?>
                    
                    <?php
                        if(checkAccess()){
                        	/*
                            if($_SESSION['project'] == $data['path']){
                            ?>
                            <td><a onclick="codiad.message.error('Active Project Cannot Be Removed');" class="icon-block bigger-icon"></a></td>
                            <?php
                            }else{
                            */
                            ?>
                            <td><a onclick="codiad.project.delete('<?php echo($data['name']); ?>','<?php echo($data['path']); ?>');" class="icon-cancel-circled bigger-icon"></a></td>
                            <?php
                            //}
                        }
                    ?>
                </tr>
                <?php
                }
            }
            ?>
            </table>
            </div>
            <div style="text-align: right;">
            	<span>The icon <span class="icon-users bigger-icon"></span> indicates that the group members can be edited.</span>
            </div>
            <?php if(checkAccess()){ ?><button class="btn-left" onclick="codiad.project.create();">New Project</button><?php } ?><button class="<?php if(checkAccess()){ echo('btn-right'); } ?>" onclick="codiad.modal.unload();return false;">Close</button>
            <?php
            
            break;
            
        //////////////////////////////////////////////////////////////////////
        // Create New Project
        //////////////////////////////////////////////////////////////////////
        
        case 'create':
        
			$Course = new Course();
			$User = new User();
			$User->username = $_SESSION['user'];
			
            ?>
            <form>
            <label>Project Name</label>
            <input name="project_name" autofocus="autofocus" autocomplete="off">
			<label>Project Privacy</label>
			
			
			<select id="privacy_select" name="project_privacy">
				<option id="option_public" value="public" selected >Public</option>
				<option id="option_private" value="private">Private or Shared</option>
			  <!-- There is not need to shared projects here because a project turns to shared when new users are added -->
			</select>
			
			<label>Course</label>
			
			
			<select name="project_course">
				<?
					$courses = $User->GetUserCourses();
					for ($i = 0; $i < count($courses); $i++) {
						$Course->id = $courses[$i];
						$Course->Load();
						if ($Course->name != '') {
						?>
					 		<option value="<?=$Course->id?>"><?=$Course->code . " - " .$Course->name?></option>
					  	<?
						}
					}
			  	?>
			  <!-- There is not need to shared projects here because a project turns to shared when new users are added -->
			</select>
			
            <?php
            /*
            if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') { ?>
            <label>Folder Name (<span class="sb-dialog-warning">This is the main directory of your project</span>)</label>
			<label id="path_prefix">Folder prefix: "<?=$_SESSION['user']?>-"</label>
			<input name="project_path" autofocus="off" autocomplete="off">
			
            <?php } else { ?>
            <input type="hidden" name="project_path">
            <?php }
			 */ 
			/*
			?>
            
            <!-- Clone From GitHub -->
            <div style="width: 500px;">
            <table class="hide" id="git-clone">
                <tr>
                    <td>
                        <label>Git Repository</label>
                        <input name="git_repo">
                    </td>
                    <td width="5%">&nbsp;</td>
                    <td width="25%">
                        <label>Branch</label>
                        <input name="git_branch" value="master">
                    </td>
                </tr>
                <tr>
                    <td colspan="3" class="note">Note: This will only work if your Git repo DOES NOT require interactive authentication and your server has git installed.</td>
                </tr>
            </table>
            </div>
            <!-- /Clone From GitHub -->
            <?php
            */
                $action = 'codiad.project.list();';
                if($_GET['close'] == 'true') {
                    $action = 'codiad.modal.unload();';
                } 
            ?>           
            <button class="btn-left">Create Project</button><button class="btn-right" onclick="<?php echo $action;?>return false;">Cancel</button>
            <?
            /*
            <button onclick="$('#git-clone').slideDown(300); $(this).hide(); return false;" class="btn-mid">...From Git Repo</button>
            */
            ?>
            
            <form>
            <?php
            break;
            
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
        
        case 'delete':
        
        ?>
            <form>
            <input type="hidden" name="project_path" value="<?php echo($_GET['path']); ?>">
            <label>Confirm Project Deletion</label>
            <pre>Name: <?php echo($_GET['name']); ?>, Path: <?php echo($_GET['path']); ?></pre>
            <? /*<table>
            <tr><td width="5"><input type="checkbox" name="delete" id="delete" value="true"></td><td>Delete Project Files</td></tr>
            <tr><td width="5"><input type="checkbox" name="follow" id="follow" value="true"></td><td>Follow Symbolic Links </td></tr>
            </table>
			 */ 
			 ?>
            <button class="btn-left">Confirm</button><button class="btn-right" onclick="codiad.project.list();return false;">Cancel</button>
            <?php
            break;
     	
		//////////////////////////////////////////////////////////////////
        // LF: Manage Users
        //////////////////////////////////////////////////////////////////
        case 'manage_users':
		
		$Project = new Project();
		$Project->path = $_GET['path'];
		$Project->user = $_SESSION['user'];
		$Project->Load();
		$users_in_project = $Project->GetUsersInProject();
						
						
        ?>
        <form id="group_users_form">
        <input type="hidden" name="project_path" value="<?php echo($_GET['path']); ?>">
        <label><span class="icon-users"></span>Manage Users:</label>
        <?
        $maximum_number_group_members = $Project->GetMaximumNumberGroupMembers();
        if ($maximum_number_group_members > 0) {
        ?>
        	<label class="sb-dialog-warning">This assignment has a maximum of <?=$maximum_number_group_members?> users. </label>
	    <?
		}
	    ?>
	        <table width="100%">
	                <tr>
	                    <th>Username</th>
	                    <th>Permitted</th>
	                </tr>    
			      <?php 
			        	
			        	$User = new User();
						//$User->users = getJSON('users.php');
						// Connect
						$mongo_client = new MongoClient();
						// select the database
						$database = $mongo_client->selectDB(DATABASE_NAME);
						// Select the collection 
						$collection = $database->users;
						// TODO get all the users in the same class of the project //$User->users = $collection->find(); // Get all the users in the database
						$users = $User->GetUsersInCourse($Project->course);
						
						foreach($users as $user) {
							if($user['username'] != $_SESSION['user'] && $user['type'] == "student") {
								$username = $user['username'];
								?>
								<tr>
									<td><?=$username; ?></td>
									<td><input type="checkbox" name="group_user[]" value="<?=$username; ?>"
										<? if(in_array($username, $users_in_project)) { echo "checked=\"checked\""; } ?>/>
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
			      url: 'components/project/controller.php?action=manage_users',
			      data: form.serialize(),
			      dataType: 'json', 
			      success: function( response ) {
			        if(response.status == 'success') {
			        	codiad.modal.unload();
			        	codiad.message.success(i18n('Project updated'));
			        } else if(response.status == 'error_user_maximum_reached') {
			        	codiad.message.error(i18n('Maximum limit of users reached.'));
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
<script>
	$("#path_prefix").toggle();
	$(document).ready(function() {
		$("#privacy_select").change(function () {			
			$("#path_prefix").toggle();
		});
	});	
</script>
