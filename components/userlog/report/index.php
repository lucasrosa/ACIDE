<?
	// TODO Close all the sections that are open and passed the timeout
	
	
	require_once('../../../common.php');
    require_once('../class.userlog.php');
	require_once('../../user/class.user.php');
	
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
			
			foreach($sessions as $session) {
				//$total_time_system += (strtotime($session['last_update_timestamp']) - strtotime($session['start_timestamp']));
				$date1 = new DateTime($session['start_timestamp']);
				$date2 = new DateTime($session['last_update_timestamp']);
				
				$interval = $date1->diff($date2);
				
				$total_time_system->add($interval);
				echo "Total time user spend in session " . $session['_id'] . ": <br>";
				printf("&nbsp;&nbsp;&nbsp; %d years, %d months, %d days, %d hours, %d minutes, %d seconds <br>", $interval->y, $interval->m, $interval->d, $interval->h, $interval->i, $interval->s);
							
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
			//$total_time_system = date("h:i:s", $total_time_system);
			
				
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
		
?>