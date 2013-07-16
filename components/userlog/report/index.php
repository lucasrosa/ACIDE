<?
	// TODO Close all the sections that are open and passed the timeout
	
	
	require_once('../../../common.php');
    require_once('../class.userlog.php');
	require_once('../../user/class.user.php');
	require_once('../../project/class.project.php');
	
	//////////////////////////////////////////////////////////////////
    // Verify Session or Key
    //////////////////////////////////////////////////////////////////
	
    checkSession();
	
	$MainUserlog = new Userlog();
	$MainUserlog->CloseAllOpenSectionsThatReachedTimeout();
	
	$User = new User();
	//$User->users = getJSON('users.php');
	// Connect
	$mongo_client = new MongoClient();
	// select the database
	$database = $mongo_client->codiad_database;
	// Select the collection 
	$collection = $database->users;
	
	$users = $User->GetUsersInTheSameCoursesOfUser($_SESSION['user']);
	
	$user_types = $User->GetUsersTypes();
	$student_user_type = $user_types[0];
	
	foreach($users as $user) {
		if ($user['type'] == $student_user_type) {
			$Userlog = new Userlog();
			echo "<br><br>";
			echo "<h1>User: " . $user['username'] . "</h1>";
			$Userlog->username = $user['username'];
			$sessions = $Userlog->GetAllSessionsForUser();
			$total_time_system = new DateTime('0000-00-00 00:00:00');
			$total_time_system_helper = clone $total_time_system;
			
			echo "<h2>Sessions:</h2>";
			
			foreach($sessions as $session) {
				//$total_time_system += (strtotime($session['last_update_timestamp']) - strtotime($session['start_timestamp']));
				$date1 = new DateTime($session['start_timestamp']);
				$date2 = new DateTime($session['last_update_timestamp']);
				
				$interval = $date1->diff($date2);
				
				$total_time_system->add($interval);
				
				echo "<br><h3><u>This session consists of: </u><h3>";
				// <!-- Session
				

				$Project = new Project();
				$projects = $Project->GetProjectsForUser($user['username']);
				
				echo "<h2>Projects:</h2>";
				
				foreach($projects as $project) {
					
					$Userlog->path = $project['path'];
					$project_sessions = $Userlog->GetAllLogsForProject($session['_id']);
					if ($project_sessions->count() != 0) {
						echo "<h4><u>'" . $project['name']. "'</u></h4>";
					}
					
					$total_time_project = new DateTime('0000-00-00 00:00:00');
					$total_time_project_helper = clone $total_time_project;
				
					foreach ($project_sessions as $project_session) {
						$date1 = new DateTime($project_session['start_timestamp']);
						$date2 = new DateTime($project_session['last_update_timestamp']);
						$interval = $date1->diff($date2);
						
						$total_time_project->add($interval);
						if (
						$interval->y > 0 ||
						$interval->m > 0 ||
						$interval->d > 0 ||
						$interval->h > 0 ||
						$interval->i > 0 ||
						$interval->s > 0 
						){
							echo "Total time user spend in a session of project " . $project['path'] . ": <br>";
							printf("&nbsp;&nbsp;&nbsp; %d years, %d months, %d days, %d hours, %d minutes, %d seconds <br>", $interval->y, $interval->m, $interval->d, $interval->h, $interval->i, $interval->s);
						}
					}
					
					$total_time_project_interval = $total_time_project_helper->diff($total_time_project);
					
					if (
						$total_time_project_interval->y > 0 ||
						$total_time_project_interval->m > 0 ||
						$total_time_project_interval->d > 0 ||
						$total_time_project_interval->h > 0 ||
						$total_time_project_interval->i > 0 ||
						$total_time_project_interval->s > 0 
						){
							// Get all the files for this project and make clear if the file doesn't have a log (like: 0 seconds, this file wasn't opened by the user)
							// <!-- File
							$project_directory = WORKSPACE . "/". $project['path'];
							
							echo "Directory = $project_directory";
							$files = listdir($project_directory);// $files = listdir('.');
							
							for ($j = 0; $j < count($files); $j++) {
								$exploded_path = explode("/", $files[$j]);
								$filename = "";
								$workspace_folder_index = 0;
								
								for ($k = 0; $k < count($exploded_path); $k++) {
									
									if ($exploded_path[$k] == "workspace") {
										$workspace_folder_index = $k;
									}
									// If the iteration passed the workspace, start setting the name of the file
									if ($k > $workspace_folder_index && $workspace_folder_index > 0) {
										if ($k >($workspace_folder_index + 1)) {
											$filename .= "/";
										}
										$filename .= $exploded_path[$k];
									}
								}	
								
								// Search the logs of file $filename
								
								echo "<br>$filename";
								
								// TODO show the files logs
								// <!-- Show file log
								
								$FileUserlog = new Userlog();
								$FileUserlog->username = $Userlog->username;
								$FileUserlog->path = $filename;
								$file_sessions = $FileUserlog->GetAllLogsForFile($session['_id']);
								
								$name_of_file = explode("/", $filename);
								$name_of_file = $name_of_file[count($name_of_file)-1];
								
								
								//if ($file_sessions->count() != 0) {
									echo "<h4><u>Logs for file: '" . $name_of_file . "'</u></h4>";
								//} else {
								//	echo "<h1>NOPE</h1>";
								//}
								
								$total_time_file = new DateTime('0000-00-00 00:00:00');
								$total_time_file_helper = clone $total_time_file;
							
								foreach ($file_sessions as $file_session) {
									$date1 = new DateTime($file_session['start_timestamp']);
									$date2 = new DateTime($file_session['last_update_timestamp']);
									$interval = $date1->diff($date2);
									
									$total_time_file->add($interval);
									if (
									$interval->y > 0 ||
									$interval->m > 0 ||
									$interval->d > 0 ||
									$interval->h > 0 ||
									$interval->i > 0 ||
									$interval->s > 0 
									){
										echo "Total time user spend in this session of file " . $name_of_file . ": <br>";
										printf("&nbsp;&nbsp;&nbsp; %d years, %d months, %d days, %d hours, %d minutes, %d seconds <br>", $interval->y, $interval->m, $interval->d, $interval->h, $interval->i, $interval->s);
									}
								}
								
								$total_time_file_interval = $total_time_file_helper->diff($total_time_file);
								
								if (
									$total_time_file_interval->y > 0 ||
									$total_time_file_interval->m > 0 ||
									$total_time_file_interval->d > 0 ||
									$total_time_file_interval->h > 0 ||
									$total_time_file_interval->i > 0 ||
									$total_time_file_interval->s > 0 
									){
										echo "<h4>Total time the user spend in this file:<br>";
										printf("&nbsp;&nbsp;&nbsp; %d years, %d months, %d days, %d hours, %d minutes, %d seconds <br>", 
												$total_time_file_interval->y,
												$total_time_file_interval->m,
												$total_time_file_interval->d,
												$total_time_file_interval->h,
												$total_time_file_interval->i,
												$total_time_file_interval->s);
										echo "</h4>";
								} else {
									echo "<h4> The user never opened this file</h4>";
								}
								// --> Show file log
							}
							
							// --> File
							// <!-- Terminal
							$terminal_sessions = $Userlog->GetAllLogsForTerminalInThisProject($session['_id']);
							if ($terminal_sessions->count() != 0) {
								echo "<h4><u>Terminal logs for: '" . $project['name']. "'</u></h4>";
							}
							
							$total_time_terminal = new DateTime('0000-00-00 00:00:00');
							$total_time_terminal_helper = clone $total_time_terminal;
						
							foreach ($terminal_sessions as $terminal_session) {
								$date1 = new DateTime($terminal_session['start_timestamp']);
								$date2 = new DateTime($terminal_session['last_update_timestamp']);
								$interval = $date1->diff($date2);
								
								$total_time_terminal->add($interval);
								if (
								$interval->y > 0 ||
								$interval->m > 0 ||
								$interval->d > 0 ||
								$interval->h > 0 ||
								$interval->i > 0 ||
								$interval->s > 0 
								){
									echo "Total time user spend in the terminal in this project " . $project['path'] . ": <br>";
									printf("&nbsp;&nbsp;&nbsp; %d years, %d months, %d days, %d hours, %d minutes, %d seconds <br>", $interval->y, $interval->m, $interval->d, $interval->h, $interval->i, $interval->s);
								}
							}
							
							$total_time_terminal_interval = $total_time_terminal_helper->diff($total_time_terminal);
							
							if (
								$total_time_terminal_interval->y > 0 ||
								$total_time_terminal_interval->m > 0 ||
								$total_time_terminal_interval->d > 0 ||
								$total_time_terminal_interval->h > 0 ||
								$total_time_terminal_interval->i > 0 ||
								$total_time_terminal_interval->s > 0 
								){
									echo "<h4>Total time the user spend in the terminal in this project is:<br>";
									printf("&nbsp;&nbsp;&nbsp; %d years, %d months, %d days, %d hours, %d minutes, %d seconds <br>", 
											$total_time_terminal_interval->y,
											$total_time_terminal_interval->m,
											$total_time_terminal_interval->d,
											$total_time_terminal_interval->h,
											$total_time_terminal_interval->i,
											$total_time_terminal_interval->s);
									echo "</h4>";
							}
								
							
							// --> Terminal
							
							
							echo "<h3>Total time the user spend in the project is:<br>";
							printf("&nbsp;&nbsp;&nbsp; %d years, %d months, %d days, %d hours, %d minutes, %d seconds <br>", 
									$total_time_project_interval->y, 
									$total_time_project_interval->m, 
									$total_time_project_interval->d, 
									$total_time_project_interval->h, 
									$total_time_project_interval->i, 
									$total_time_project_interval->s);
							echo "</h3>";
						}
				}
				
				// --> Session
				
				echo " <br>Total time user spend in session " . $session['_id'] . ":";
				printf("<br>&nbsp;&nbsp;&nbsp; %d years, %d months, %d days, %d hours, %d minutes, %d seconds <br>", $interval->y, $interval->m, $interval->d, $interval->h, $interval->i, $interval->s);
				
				echo "<hr><hr><hr>";
			}	
			$total_time_system_interval = $total_time_system_helper->diff($total_time_system);
			
			
			echo "<h3>Total time the user spend in the system is:<br>";
			printf("&nbsp;&nbsp;&nbsp; %d years, %d months, %d days, %d hours, %d minutes, %d seconds <br>", 
					$total_time_system_interval->y, 
					$total_time_system_interval->m, 
					$total_time_system_interval->d, 
					$total_time_system_interval->h, 
					$total_time_system_interval->i, 
					$total_time_system_interval->s);
			echo "</h3>";
			
			
			echo "<hr><hr><hr><hr><hr><hr><hr>";
			//$projects = $user['projects'];
			// Get all the user's projects
			$Project = new Project();
			$projects = $Project->GetProjectsForUser($user['username']);
			
			echo "<h2>Projects:</h2>";
			
			foreach($projects as $project) {
				
				$Userlog->path = $project['path'];
				$project_sessions = $Userlog->GetAllLogsForProject();
				if ($project_sessions->count() != 0) {
					echo "<h4><u>'" . $project['name']. "'</u></h4>";
				}
				
				$total_time_project = new DateTime('0000-00-00 00:00:00');
				$total_time_project_helper = clone $total_time_project;
			
				foreach ($project_sessions as $project_session) {
					$date1 = new DateTime($project_session['start_timestamp']);
					$date2 = new DateTime($project_session['last_update_timestamp']);
					$interval = $date1->diff($date2);
					
					$total_time_project->add($interval);
					if (
					$interval->y > 0 ||
					$interval->m > 0 ||
					$interval->d > 0 ||
					$interval->h > 0 ||
					$interval->i > 0 ||
					$interval->s > 0 
					){
						echo "Total time user spend in a session of project " . $project['path'] . ": <br>";
						printf("&nbsp;&nbsp;&nbsp; %d years, %d months, %d days, %d hours, %d minutes, %d seconds <br>", $interval->y, $interval->m, $interval->d, $interval->h, $interval->i, $interval->s);
					}
				}
				
				$total_time_project_interval = $total_time_project_helper->diff($total_time_project);
				
				if (
					$total_time_project_interval->y > 0 ||
					$total_time_project_interval->m > 0 ||
					$total_time_project_interval->d > 0 ||
					$total_time_project_interval->h > 0 ||
					$total_time_project_interval->i > 0 ||
					$total_time_project_interval->s > 0 
					){
						// Get all the files for this project and make clear if the file doesn't have a log (like: 0 seconds, this file wasn't opened by the user)
						// <!-- File
						$project_directory = WORKSPACE . "/". $project['path'];
						
						echo "Directory = $project_directory";
						$files = listdir($project_directory);// $files = listdir('.');
						
						for ($j = 0; $j < count($files); $j++) {
							$exploded_path = explode("/", $files[$j]);
							$filename = "";
							$workspace_folder_index = 0;
							
							for ($k = 0; $k < count($exploded_path); $k++) {
								
								if ($exploded_path[$k] == "workspace") {
									$workspace_folder_index = $k;
								}
								// If the iteration passed the workspace, start setting the name of the file
								if ($k > $workspace_folder_index && $workspace_folder_index > 0) {
									if ($k >($workspace_folder_index + 1)) {
										$filename .= "/";
									}
									$filename .= $exploded_path[$k];
								}
							}	
							
							// Search the logs of file $filename
							
							echo "<br>$filename";
							
							// TODO show the files logs
							// <!-- Show file log
							
							$FileUserlog = new Userlog();
							$FileUserlog->username = $Userlog->username;
							$FileUserlog->path = $filename;
							$file_sessions = $FileUserlog->GetAllLogsForFile();
							
							$name_of_file = explode("/", $filename);
							$name_of_file = $name_of_file[count($name_of_file)-1];
							
							
							//if ($file_sessions->count() != 0) {
								echo "<h4><u>Logs for file: '" . $name_of_file . "'</u></h4>";
							//} else {
							//	echo "<h1>NOPE</h1>";
							//}
							
							$total_time_file = new DateTime('0000-00-00 00:00:00');
							$total_time_file_helper = clone $total_time_file;
						
							foreach ($file_sessions as $file_session) {
								$date1 = new DateTime($file_session['start_timestamp']);
								$date2 = new DateTime($file_session['last_update_timestamp']);
								$interval = $date1->diff($date2);
								
								$total_time_file->add($interval);
								if (
								$interval->y > 0 ||
								$interval->m > 0 ||
								$interval->d > 0 ||
								$interval->h > 0 ||
								$interval->i > 0 ||
								$interval->s > 0 
								){
									echo "Total time user spend in this session of file " . $name_of_file . ": <br>";
									printf("&nbsp;&nbsp;&nbsp; %d years, %d months, %d days, %d hours, %d minutes, %d seconds <br>", $interval->y, $interval->m, $interval->d, $interval->h, $interval->i, $interval->s);
								}
							}
							
							$total_time_file_interval = $total_time_file_helper->diff($total_time_file);
							
							if (
								$total_time_file_interval->y > 0 ||
								$total_time_file_interval->m > 0 ||
								$total_time_file_interval->d > 0 ||
								$total_time_file_interval->h > 0 ||
								$total_time_file_interval->i > 0 ||
								$total_time_file_interval->s > 0 
								){
									echo "<h4>Total time the user spend in this file:<br>";
									printf("&nbsp;&nbsp;&nbsp; %d years, %d months, %d days, %d hours, %d minutes, %d seconds <br>", 
											$total_time_file_interval->y,
											$total_time_file_interval->m,
											$total_time_file_interval->d,
											$total_time_file_interval->h,
											$total_time_file_interval->i,
											$total_time_file_interval->s);
									echo "</h4>";
							} else {
								echo "<h4> The user never opened this file</h4>";
							}
							// --> Show file log
						}
						
						// --> File
						// <!-- Terminal
						$terminal_sessions = $Userlog->GetAllLogsForTerminalInThisProject();
						if ($terminal_sessions->count() != 0) {
							echo "<h4><u>Terminal logs for: '" . $project['name']. "'</u></h4>";
						}
						
						$total_time_terminal = new DateTime('0000-00-00 00:00:00');
						$total_time_terminal_helper = clone $total_time_terminal;
					
						foreach ($terminal_sessions as $terminal_session) {
							$date1 = new DateTime($terminal_session['start_timestamp']);
							$date2 = new DateTime($terminal_session['last_update_timestamp']);
							$interval = $date1->diff($date2);
							
							$total_time_terminal->add($interval);
							if (
							$interval->y > 0 ||
							$interval->m > 0 ||
							$interval->d > 0 ||
							$interval->h > 0 ||
							$interval->i > 0 ||
							$interval->s > 0 
							){
								echo "Total time user spend in the terminal in this project " . $project['path'] . ": <br>";
								printf("&nbsp;&nbsp;&nbsp; %d years, %d months, %d days, %d hours, %d minutes, %d seconds <br>", $interval->y, $interval->m, $interval->d, $interval->h, $interval->i, $interval->s);
							}
						}
						
						$total_time_terminal_interval = $total_time_terminal_helper->diff($total_time_terminal);
						
						if (
							$total_time_terminal_interval->y > 0 ||
							$total_time_terminal_interval->m > 0 ||
							$total_time_terminal_interval->d > 0 ||
							$total_time_terminal_interval->h > 0 ||
							$total_time_terminal_interval->i > 0 ||
							$total_time_terminal_interval->s > 0 
							){
								echo "<h4>Total time the user spend in the terminal in this project is:<br>";
								printf("&nbsp;&nbsp;&nbsp; %d years, %d months, %d days, %d hours, %d minutes, %d seconds <br>", 
										$total_time_terminal_interval->y,
										$total_time_terminal_interval->m,
										$total_time_terminal_interval->d,
										$total_time_terminal_interval->h,
										$total_time_terminal_interval->i,
										$total_time_terminal_interval->s);
								echo "</h4>";
						}
							
						
						// --> Terminal
						
						
						echo "<h3>Total time the user spend in the project is:<br>";
						printf("&nbsp;&nbsp;&nbsp; %d years, %d months, %d days, %d hours, %d minutes, %d seconds <br>", 
								$total_time_project_interval->y, 
								$total_time_project_interval->m, 
								$total_time_project_interval->d, 
								$total_time_project_interval->h, 
								$total_time_project_interval->i, 
								$total_time_project_interval->s);
						echo "</h3>";
					}
			}

			// <!-- Terminal
			$terminal_sessions = $Userlog->GetAllLogsForTerminal();
			if ($terminal_sessions->count() != 0) {
				echo "<h4><u>Total time this user spent in the terminal:</u></h4>";
			}
			
			$total_time_terminal = new DateTime('0000-00-00 00:00:00');
			$total_time_terminal_helper = clone $total_time_terminal;
		
			foreach ($terminal_sessions as $terminal_session) {
				$date1 = new DateTime($terminal_session['start_timestamp']);
				$date2 = new DateTime($terminal_session['last_update_timestamp']);
				$interval = $date1->diff($date2);
				
				$total_time_terminal->add($interval);
				/*
				if (
				$interval->y > 0 ||
				$interval->m > 0 ||
				$interval->d > 0 ||
				$interval->h > 0 ||
				$interval->i > 0 ||
				$interval->s > 0 
				){
					echo "Total time user spend in the terminal <br>";
					printf("&nbsp;&nbsp;&nbsp; %d years, %d months, %d days, %d hours, %d minutes, %d seconds <br>", $interval->y, $interval->m, $interval->d, $interval->h, $interval->i, $interval->s);
				}
				*/
			}
			
			$total_time_terminal_interval = $total_time_terminal_helper->diff($total_time_terminal);
			
			if (
				$total_time_terminal_interval->y > 0 ||
				$total_time_terminal_interval->m > 0 ||
				$total_time_terminal_interval->d > 0 ||
				$total_time_terminal_interval->h > 0 ||
				$total_time_terminal_interval->i > 0 ||
				$total_time_terminal_interval->s > 0 
				){
					echo "<h4>Total time the user spend in the terminal is:<br>";
					printf("&nbsp;&nbsp;&nbsp; %d years, %d months, %d days, %d hours, %d minutes, %d seconds <br>", 
							$total_time_terminal_interval->y,
							$total_time_terminal_interval->m,
							$total_time_terminal_interval->d,
							$total_time_terminal_interval->h,
							$total_time_terminal_interval->i,
							$total_time_terminal_interval->s);
					echo "</h4>";
			}
				
			
			// --> Terminal 
			
			$Compilation_userlog = new Userlog();
			$Compilation_userlog->username = $user['username'];
			$compilation_attempts = $Compilation_userlog->GetAllLogsForCompilationAttempt();
			
			$compilation_attempts_count = $compilation_attempts->count();
			$compilation_attempts_successful = 0;
			$compilation_attempts_failed	 = 0;
			
			echo "<h4><u>This user made $compilation_attempts_count compilation attempts,</u></h4>";
			foreach ($compilation_attempts as $compilation_attempt) {
				if ($compilation_attempt['succeeded'] == "TRUE") {
					$compilation_attempts_successful++;	
				} else {
					$compilation_attempts_failed++;
				}
			}
			
			echo "<h5>$compilation_attempts_successful successful</h5>";
			echo "<h5>$compilation_attempts_failed failed</h5>";
		}
	}

	
	function pp($arr){
	    $retStr = '<ul>';
	    if (is_array($arr)){
	        foreach ($arr as $key=>$val){
	            if (is_array($val)){
	                $retStr .= '<li>' . $key . ' => ' . pp($val) . '</li>';
	            }else{
	                $retStr .= '<li>' . $key . ' => ' . $val . '</li>';
	            }
	        }
	    }
	    $retStr .= '</ul>';
	    return $retStr;
	}
	
	function listdir($start_dir='.') {

	  $files = array();
	  if (is_dir($start_dir)) {
	    $fh = opendir($start_dir);
	    while (($file = readdir($fh)) !== FALSE) {
	      # loop through the files, skipping . and .., and recursing if necessary
	      if (strcmp($file, '.')==0 || strcmp($file, '..')==0 || strcmp($file[0], '.')==0) 
	      	continue;
	      $filepath = $start_dir . '/' . $file;
	      if ( is_dir($filepath) )
	        $files = array_merge($files, listdir($filepath));
	      else
	        array_push($files, $filepath);
	    }
	    closedir($fh);
	  } else {
	    # false if the function was called with an invalid non-directory argument
	    $files = false;
	  }
	
	  return $files;
	
	}
		
?>