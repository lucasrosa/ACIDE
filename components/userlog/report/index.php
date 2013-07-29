<?
require_once ('../../../common.php');
require_once ('../class.userlog.php');
require_once ('../../user/class.user.php');
require_once ('../../project/class.project.php');
require_once ('class.userlogreport.php');

//////////////////////////////////////////////////////////////////
// Verify Session or Key
//////////////////////////////////////////////////////////////////

checkSession();

$MainUserlog = new Userlog();
$MainUserlog -> CloseAllOpenSectionsThatReachedTimeout();

$User = new User();
//$User->users = getJSON('users.php');
// Connect
$mongo_client = new MongoClient();
// select the database
$database = $mongo_client -> codiad_database;
// Select the collection
$collection = $database -> users;

$users = $User -> GetUsersInTheSameCoursesOfUser($_SESSION['user']);

$user_types = $User -> GetUsersTypes();
$student_user_type = $user_types[0];

$compilation_errors = array();


$error_to_log = "";
// String test
/*
 * $string = 'reply-234-private';
 preg_match('/reply-(.*?)-private/',$string, $display);
 echo $display[1];
 $string = 'NumberFactorial.java:21: error: illegal start of type
 for (int i =(number - 1); i > 1; i--)
 ^';
 preg_match('/error:(.*?)\n/',$string, $display);
 echo $display[1];
 echo "<br>";
 */
 /*
 * $outputted_errors groups the errors and count them, each position is another array $error
 */
$outputted_errors = array();
$single_error = array();
		
foreach ($users as $user) {
	if ($user['type'] == $student_user_type) {

		$Userlogreport = new Userlogreport();
		$Userlogreport -> username = $user['username'];
		/*
		 $time_spent_in_system = $Userlogreport->GetTimeSpentInTheSystem();

		 echo "<h3>Total time the user spend in the system is:<br>";
		 printf("&nbsp;&nbsp;&nbsp; %d years, %d months, %d days, %d hours, %d minutes, %d seconds <br>",
		 $time_spent_in_system->y,
		 $time_spent_in_system->m,
		 $time_spent_in_system->d,
		 $time_spent_in_system->h,
		 $time_spent_in_system->i,
		 $time_spent_in_system->s);
		 echo "</h3>";

		 */
		/*
		 $sections_time = $Userlogreport->GetTimeUserSpentInEachSection();

		 foreach ($sections_time as $section_time) {
		 echo " <br>Total time user spend in session " . $section_time['_id'] . ":";
		 $interval = $section_time['interval'];
		 printf("<br>&nbsp;&nbsp;&nbsp; %d years, %d months, %d days, %d hours, %d minutes, %d seconds <br>", $interval->y, $interval->m, $interval->d, $interval->h, $interval->i, $interval->s);
		 }
		 *
		 */
		/*
		 // projects in session
		 $Userlog = new Userlog();
		 $Userlog->username = $user['username'];

		 $sessions = $Userlog->GetAllSessionsForUser();
		 $total_time_system = new DateTime('0000-00-00 00:00:00');
		 $total_time_system_helper = clone $total_time_system;

		 echo "<h2>User:" . $user['username'] . "  </h2>";

		 foreach($sessions as $session) {
		 echo "<h3>Session:" . $session['_id'] . "  </h3>";
		 $session_id = $session['_id'];
		 $return_projects_without_logs = FALSE;
		 $projects_time = $Userlogreport->GetTimeSpentInProjectsInSession($session_id, $return_projects_without_logs);

		 foreach ($projects_time as $project_time) {
		 echo " <br>Total time user spend in session of project:  " . $project_time['path'] . ":";
		 $interval = $project_time['interval'];
		 printf("<br>&nbsp;&nbsp;&nbsp; %d years, %d months, %d days, %d hours, %d minutes, %d seconds <br>", $interval->y, $interval->m, $interval->d, $interval->h, $interval->i, $interval->s);
		 }
		 }
		 *
		 */
		/*
		 // Time in projects

		 $Project = new Project();
		 $projects = $Project->GetProjectsForUser($user['username']);

		 foreach($projects as $project) {
		 echo " <br>Total time user spend in session of project:  " . $project['path'] . ":";
		 $interval = $Userlogreport->GetTimeSpentInProject($project['path']);
		 printf("<br>&nbsp;&nbsp;&nbsp; %d years, %d months, %d days, %d hours, %d minutes, %d seconds <br>", $interval->y, $interval->m, $interval->d, $interval->h, $interval->i, $interval->s);
		 }
		 *
		 *
		 */
		/*
		 // Time in terminal

		 $interval = $Userlogreport->GetTimeUserSpentInTerminal();
		 printf("<br>&nbsp;&nbsp;&nbsp; %d years, %d months, %d days, %d hours, %d minutes, %d seconds <br>", $interval->y, $interval->m, $interval->d, $interval->h, $interval->i, $interval->s);
		 *
		 *
		 */

		/*
		 // Time in terminal in a session

		 $Userlog = new Userlog();
		 $Userlog->username = $user['username'];

		 $sessions = $Userlog->GetAllSessionsForUser();
		 $total_time_system = new DateTime('0000-00-00 00:00:00');
		 $total_time_system_helper = clone $total_time_system;

		 echo "<h2>User:" . $user['username'] . "  </h2>";

		 foreach($sessions as $session) {
		 $session_id = $session['_id'];
		 $interval = $Userlogreport->GetTimeUserSpentInTerminal($session_id);

		 echo "<br><br>Total time user spend in terminal in session:  $session_id :";
		 printf("<br>&nbsp;&nbsp;&nbsp; %d years, %d months, %d days, %d hours, %d minutes, %d seconds <br>", $interval->y, $interval->m, $interval->d, $interval->h, $interval->i, $interval->s);

		 }
		 *
		 */

		/*
		 // Time in terminal in a session with project

		 $Userlog = new Userlog();
		 $Userlog->username = $user['username'];

		 $sessions = $Userlog->GetAllSessionsForUser();

		 echo "<h2>User:" . $user['username'] . "  </h2>";
		 $Project = new Project();
		 $projects = $Project->GetProjectsForUser($user['username']);

		 foreach($sessions as $session) {
		 echo "<br><h3> Session:  $session_id </h3>";

		 foreach($projects as $project) {
		 $session_id = $session['_id'];
		 $interval = $Userlogreport->GetTimeUserSpentInTerminal($session_id, $project['path']);
		 echo "<br>Total time user spend in terminal in project " . $project['path'];
		 printf("<br>&nbsp;&nbsp;&nbsp; %d years, %d months, %d days, %d hours, %d minutes, %d seconds <br>", $interval->y, $interval->m, $interval->d, $interval->h, $interval->i, $interval->s);

		 }
		 }

		 */
		/*
		 // Time spent in terminal in a project

		 $Userlog = new Userlog();
		 $Userlog->username = $user['username'];

		 echo "<h2>User:" . $user['username'] . "  </h2>";
		 $Project = new Project();
		 $projects = $Project->GetProjectsForUser($user['username']);

		 $session_id = NULL;

		 foreach($projects as $project) {
		 $session_id = $session['_id'];
		 $interval = $Userlogreport->GetTimeUserSpentInTerminal($session_id, $project['path']);
		 echo "<br>Total time user spend in terminal in project " . $project['path'];
		 printf("<br>&nbsp;&nbsp;&nbsp; %d years, %d months, %d days, %d hours, %d minutes, %d seconds <br>", $interval->y, $interval->m, $interval->d, $interval->h, $interval->i, $interval->s);

		 }
		 */
		/*
		 // Compilation attempt
		 $Compilation_userlogreport = new Userlogreport();
		 $Compilation_userlogreport->username = $user['username'];
		 //$compilation_attempts = $Compilation_userlog->GetAllLogsForCompilationAttempt();

		 //$compilation_attempts_count = $compilation_attempts->count();
		 $compilation_attempts = $Compilation_userlogreport->GetNumberOfCompilations();
		 $compilation_attempts_successful = $Compilation_userlogreport->GetNumberOfCompilations("", TRUE);
		 $compilation_attempts_failed	 = $Compilation_userlogreport->GetNumberOfCompilations("", FALSE);

		 echo "<h5>$compilation_attempts attempts</h5>";
		 echo "<h5>$compilation_attempts_successful successful</h5>";
		 echo "<h5>$compilation_attempts_failed failed</h5>";
		 *
		 */

		// Compilation attempt in project
		/*
		 echo "<h2>User:" . $user['username'] . "  </h2>";
		 $Project = new Project();
		 $projects = $Project->GetProjectsForUser($user['username']);

		 $session_id = NULL;

		 foreach($projects as $project) {
		 echo "<h3> Project: " . $project['path'];
		 $Compilation_userlogreport = new Userlogreport();
		 $Compilation_userlogreport->username = $user['username'];
		 //$compilation_attempts = $Compilation_userlog->GetAllLogsForCompilationAttempt();

		 //$compilation_attempts_count = $compilation_attempts->count();
		 $compilation_attempts = $Compilation_userlogreport->GetNumberOfCompilations($project['path']);
		 $compilation_attempts_successful = $Compilation_userlogreport->GetNumberOfCompilations($project['path'], TRUE);
		 $compilation_attempts_failed	 = $Compilation_userlogreport->GetNumberOfCompilations($project['path'], FALSE);

		 echo "<h5>$compilation_attempts attempts</h5>";
		 echo "<h5>$compilation_attempts_successful successful</h5>";
		 echo "<h5>$compilation_attempts_failed failed</h5>";
		 }
		 */

		// Compilation attempt test
		$Compilation_userlog = new Userlog();
		$Compilation_userlog -> username = $user['username'];
		$compilation_attempts = $Compilation_userlog -> GetAllLogsForCompilationAttempt();

		$current_error = "";
		
		/*
		 * $array_of_errors keeps the common errors to be compared with the javac output 
		 */
		$array_of_errors = array();
		$array_of_errors[] = "javac: invalid flag:";
		$array_of_errors[] = "javac: file not found:";
		
		foreach ($compilation_attempts as $compilation_attempt) {
			$current_error = "";
			$display = array();
			$error = $compilation_attempt['output'];
			if ($compilation_attempt['succeeded'] == 'FALSE') {
				preg_match('/error:(.*?)\n/', $error, $display);
				
				$array_of_errors_iterator = 0;
				// Iterate through the errors to find which one it corresponds to
				while ((!isset($display[1])) || @$display[1] == "") {
					$this_error = $array_of_errors[$array_of_errors_iterator];
					if (substr($error, 0, (strlen($this_error))) == $this_error) {
						$display[1] = $this_error . " 'FILENAME'";
					}
					$array_of_errors_iterator++;
				}
				/*
				 * Treat errors like: 'Class names, 'testhallo.txt', are only accepted if annotation processing is explicitly requested'
				 */
				// Remove class/file name 
				$current_error = $display[1];
				
				if (preg_match('/\'(.*?)\'/', $error, $display)) {
					$array_of_symbols = array(";", "\"", "(", ")", "{", "}", "[", "]", ":", ".", "!", "=");
					if (!in_array($display[1], $array_of_symbols)) {
						$current_error = str_replace($display[1], 'FILENAME', $current_error);
					}
				}
				 
				 /*
				  * Verify if the error is already inserted in the array
				  */ 
				 $error_already_inserted = FALSE;
				 for ($k = 0; $k < count($outputted_errors); $k++) {
				 	if ($outputted_errors[$k]['error'] == $current_error) {
				 		$error_already_inserted = TRUE;
						$outputted_errors[$k]['count']++;
				 	}
				 }
				 
				 /*
				  * If it's not inserted yet, insert it
				  */
				 if (!$error_already_inserted) {
				 	$single_error['error'] = $current_error;
					$single_error['count'] = 1; 
					$outputted_errors[] = $single_error;
					$error_to_log = $error;
				 }
				 
				 
				//echo "<br> $current_error";
				//echo "<br> {" . $compilation_attempt['output'] . "}";
				//echo "<br>" . $display[1];
				//preg_match('/\'(.*?)\'/', $error, $display);
				//echo "<br> this: "; 
				//echo "<br> 0 = " . $display[0];
				//echo "<br> 1 = " . $display[1];
				//echo "<br> 2 = " . $display[2];
				//echo "<hr>";
			}
		}
	}
}

/*
 * Show the errors and their counts
 */
 for ($k = 0; $k < count($outputted_errors); $k++) {
 	//echo "<br>Error: " . $outputted_errors[$k]['error'];
	//echo "<br>Count: " . $outputted_errors[$k]['count'];
	//echo "<hr>";
 }
 
?>
<!DOCTYPE HTML>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>Highcharts Example</title>

		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
		<script type="text/javascript">
	$(function () {
        $('#container').highcharts({
            chart: {
                type: 'column'
            },
            title: {
                text: 'Compilation errors of all students'
            },
            /*
            subtitle: {
                text: 'Source: WorldClimate.com'
            },
            */
            xAxis: {
                categories: [
                    '',
                ]
            },
            
            yAxis: {
                min: 0,
                title: {
                    text: 'Number of occurrences'
                }
            },
            tooltip: {
                headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                    '<td style="padding:0"><b>{point.y} </b></td></tr>',
                footerFormat: '</table>',
                //shared: true,
                useHTML: true
            },
            plotOptions: {
                column: {
                    pointPadding: 0.2,
                    borderWidth: 0
                }
            },
            series: [
            <? for ($k = 0; $k < count($outputted_errors); $k++) { ?>
            {
                name: '<?=addslashes($outputted_errors[$k]['error'])?>',
                data: [<?=$outputted_errors[$k]['count']?>]
    
            }
	            <? 	if ($k != (count($outputted_errors) -1)) {
	            		echo ",";
					}	
	            ?>
            	 
            <? } ?>
            /*
            {
                name: 'New York',
                data: [83.6]
    
            }, {
                name: 'London',
                data: [48.9]
    
            }, {
                name: 'Berlin',
                data: [42.4]
    
            }
            */
            ]
        });
    });
    

		</script>
	</head>
	<body style="background-color: #000000; color: #FFFFFF;">
		<script src="../../highcharts/js/highcharts.js"></script>
		<script src="../../highcharts/js/modules/exporting.js"></script>
		<script src="../../highcharts/js/themes/gray.js"></script>
		<div>Error to log = <?=$error_to_log?></div>
		<div id="container" style="min-width: 400px; height: 400px; margin: 0 auto"></div>
<div class="wrapper row2">
			<div id="container" class="clear">
				<!-- Slider -->
				<div id="slider">
					<article>
						<figure>
							<div id="container" style="background-color:#404040; width: 960px; height: 360px; margin: 0 auto">
							
							</div>
						</figure>
					</article>
				</div>
				<!-- main content -->
				<div id="homepage">
					<!-- Services -->
					<section id="services" class="clear">
						<article class="one_third">
							<figure>
								<figcaption>
									<h2>Assignments</h2>
								</figcaption>
								<div id="assignments" style='background-color:#404040; min-height: 300px; width:290px;height:100%'>
									<ol id="assignments_selectable">
										<?
										//////////////////////////////////////////////////////////////////
										// LF: List all assignments which this user is the owner
										//////////////////////////////////////////////////////////////////
										$Project = new Project();
										//$assignments = $Project->GetAssignments();
										$current_user = $_SESSION['user'];
										$assignments = $Project->GetAssignmentsInTheSameCoursesOfUser($current_user);

										for ($k = 0; $k < count($assignments); $k++) {
										//$Course->id = $assignments[$k]['course'];

										?>
										<li id="<?=$assignments[$k]['name']?>" class="ui-widget-content">
											<?=$assignments[$k]['name']?>
										</li>
										<? } ?>
									</ol>
								</div>
							</figure>
						</article>
						<article class="one_third">
							<figure>
								<figcaption>
									<h2>Students</h2>
								</figcaption>
								<div id="students" style='background-color:#404040; min-height: 300px; width:290px;height:100%'>
									<ol id="students_selectable">
									<?
									foreach ($users as $user) {
										if ($user['type'] == $student_user_type) {
									?>
											
												<li id="<?=$user['username']?>" class="ui-widget-content">
													<?=$user['username']?>
												</li>
											
										<?
										}
									}
									?>
									</ol>
								</div>
							</figure>
						</article>
						<article class="one_third lastbox">
							<figure>
								<figcaption>
									<h2>Groups</h2>
								</figcaption>
								<div id="group_by" style='background-color:#404040; min-height: 300px; width:290px;height:100%'>
									<ol id="group_selectable">
										<li id="0" class="ui-widget-content">
											Don't group
										</li>
										<li id="1" class="ui-widget-content">
											Group by students
										</li>
										<li id="2" class="ui-widget-content">
											Group by assignment
										</li>
									</ol>
								</div>
							</figure>
						</article>
					</section>
					<!-- / Services -->
				</div>
				<!-- / content body -->
			</div>
		</div>
	</body>
</html>
