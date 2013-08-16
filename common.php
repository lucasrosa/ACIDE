<?php
    /*
    *  Copyright (c) Codiad & Kent Safranski (codiad.com), distributed
    *  as-is and without warranty under the MIT License. See
    *  [root]/license.txt for more. This information must remain intact.
    */
     
    Common::startSession();
               
    //////////////////////////////////////////////////////////////////
    // Common Class
    //////////////////////////////////////////////////////////////////
    
    class Common {

        //////////////////////////////////////////////////////////////////
        // PROPERTIES
        //////////////////////////////////////////////////////////////////
        
        public static $debugMessageStack = array();
        
        //////////////////////////////////////////////////////////////////
        // METHODS
        //////////////////////////////////////////////////////////////////

        // -----------------------------||----------------------------- //

        //////////////////////////////////////////////////////////////////
        // Construct
        //////////////////////////////////////////////////////////////////

        public static function construct(){
            global $cookie_lifetime;
            $path = str_replace("index.php", "", $_SERVER['SCRIPT_FILENAME']);
            foreach (array("components","plugins") as $folder) {
                if(strpos($_SERVER['SCRIPT_FILENAME'], $folder)) {
                    $path = substr($_SERVER['SCRIPT_FILENAME'],0, strpos($_SERVER['SCRIPT_FILENAME'], $folder));
                    break;
                }
            }
            
            if(file_exists($path.'config.php')){ require_once($path.'config.php'); }
        
            if(!defined('BASE_PATH')) {
                define('BASE_PATH', rtrim(str_replace("index.php", "", $_SERVER['SCRIPT_FILENAME']),"/"));
            }
            
            if(!defined('COMPONENTS')) {
                define('COMPONENTS', BASE_PATH . '/components');
            }
            
            if(!defined('PLUGINS')) {
                define('PLUGINS', BASE_PATH . '/plugins');
            }
            
            if(!defined('DATA')) {
                define('DATA', BASE_PATH . '/data');
            }
            
            if(!defined('THEMES')){
                define("THEMES", BASE_PATH . "/themes");
            }
            
            if(!defined('THEME')){
                define("THEME", "default");
            }
            
            global $lang;
            if (isset($_SESSION['lang'])) {
                include BASE_PATH."/languages/{$_SESSION['lang']}.php";
            } else {  
                include BASE_PATH."/languages/en.php";
            }
        }
        
        //////////////////////////////////////////////////////////////////
        // SESSIONS
        //////////////////////////////////////////////////////////////////
        
        public static function startSession() {
            Common::construct();
            global $cookie_lifetime;
            if(isset($cookie_lifetime) && $cookie_lifetime != "") {
                ini_set("session.cookie_lifetime", $cookie_lifetime);
            }
            
            //Set a Session Name
            session_name(md5(BASE_PATH));

            session_start();
        }
            
        //////////////////////////////////////////////////////////////////
        // Log debug message
        // Messages will be displayed in the console when the response is 
        // made with the formatJSEND function.
        //////////////////////////////////////////////////////////////////
        
        public static function debug($message) {
            Common::$debugMessageStack[] = $message;
        }
        
        //////////////////////////////////////////////////////////////////
        // Localization
        //////////////////////////////////////////////////////////////////
                
        public static function i18n($key) {
            echo Common::get_i18n($key);
        }
        
        public static function get_i18n($key) {
            global $lang;
            $key = ucwords(strtolower($key)); //Test, test TeSt and tESt are exacly the same
            return isset($lang[$key]) ? $lang[$key] : $key;
        }
        
        //////////////////////////////////////////////////////////////////
        // Check Session / Key
        //////////////////////////////////////////////////////////////////

        public static function checkSession(){
            // Set any API keys
            $api_keys = array();
            // Check API Key or Session Authentication
            $key = "";
            if(isset($_GET['key'])){ $key = $_GET['key']; }
            if(!isset($_SESSION['user']) && !in_array($key,$api_keys)){
				$pageURL = 'http';
				if (@$_SERVER["HTTPS"] == "on") {
					$pageURL .= "s";
				}
				$pageURL .= "://";
				$first_path = explode("/", $_SERVER["REQUEST_URI"]);
				$first_path = "/" .$first_path[1];
				if ($_SERVER["SERVER_PORT"] != "80") {
					 $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$first_path;
				} else {
					$pageURL .= $_SERVER["SERVER_NAME"].$first_path;
				}
				
				header('Location: '.$pageURL) ;
                exit('{"status":"error","message":"Authentication Error"}');
            }
        }

        //////////////////////////////////////////////////////////////////
        // Get JSON
        //////////////////////////////////////////////////////////////////

        public static function getJSON($file,$namespace=""){
            $path = DATA . "/";
            if($namespace != ""){
                $path = $path . $namespace . "/";
                $path = preg_replace('#/+#','/',$path);
            }
            
            $json = file_get_contents($path . $file);
            $json = str_replace("|*/?>","",str_replace("<?php/*|","",$json));
            $json = json_decode($json,true);
            return $json;
        }

        //////////////////////////////////////////////////////////////////
        // Save JSON
        //////////////////////////////////////////////////////////////////

        public static function saveJSON($file,$data,$namespace=""){
            $path = DATA . "/";
            if($namespace != ""){
                $path = $path . $namespace . "/";
                $path = preg_replace('#/+#','/',$path);
                if(!is_dir($path)) mkdir($path);
            }
            
            $data = "<?php/*|" . json_encode($data) . "|*/?>";
            $write = fopen($path . $file, 'w') or die("can't open file ".$path.$file);
            fwrite($write, $data);
            fclose($write);
        }
		
        //////////////////////////////////////////////////////////////////
        // LF: Zip JSON : Zips a workspace project into a .zip file in the project directory
        //////////////////////////////////////////////////////////////////
		
		public static function zipJSON($folderName, $projectName) {
			// Zipping the file
			$path = "../../workspace/" . $folderName . "/";

			//$zipFile = './' . $projectName. ".zip";
			$zipFile = '../../data/assignments/submitted/' . $projectName. ".zip";
			
			$zipArchive = new ZipArchive();
			$include_dir = true;
			
			Common::Zip($path, $zipFile, $include_dir);
        }

        //////////////////////////////////////////////////////////////////
        // Format JSEND Response
        //////////////////////////////////////////////////////////////////

        public static function formatJSEND($status,$data=false){

            /// Debug /////////////////////////////////////////////////
            $debug = "";
            if(count(Common::$debugMessageStack) > 0) {
                $debug .= ',"debug":';
                $debug .= json_encode(Common::$debugMessageStack);
            }

            // Success ///////////////////////////////////////////////
            if($status=="success"){
                if($data){
                    $jsend = '{"status":"success","data":'.json_encode($data).$debug.'}';
                }else{
                    $jsend = '{"status":"success","data":null'.$debug.'}';
                }

            // Error /////////////////////////////////////////////////
            }else{
                $jsend = '{"status":"error","message":"'.$data.'"'.$debug.'}';
            }

            // Return ////////////////////////////////////////////////
            return $jsend;

        }
        
        //////////////////////////////////////////////////////////////////
        // Check Function Availability
        //////////////////////////////////////////////////////////////////

        public static function checkAccess() {
            return !file_exists(DATA . "/" . $_SESSION['user'] . '_acl.php');
        }
        
        //////////////////////////////////////////////////////////////////
        // Check Function Availability
        //////////////////////////////////////////////////////////////////

        public static function isAvailable($func) {
            if (ini_get('safe_mode')) return false;
            $disabled = ini_get('disable_functions');
            if ($disabled) {
                $disabled = explode(',', $disabled);
                $disabled = array_map('trim', $disabled);
                return !in_array($func, $disabled);
            }
            return true;
        }
        
        //////////////////////////////////////////////////////////////////
        // Check If Path is absolute
        //////////////////////////////////////////////////////////////////
            
        public static function isAbsPath( $path ) {
            return ($path[0] === '/')?true:false;
        }
		
        //////////////////////////////////////////////////////////////////
        // LF: Recursively Zip a directory :: Used to submit a project
        //////////////////////////////////////////////////////////////////
		
		public static function Zip($source, $destination, $include_dir = false)
		{

		    if (!extension_loaded('zip') || !file_exists($source)) {
		        return false;
		    }

		    if (file_exists($destination)) {
		        unlink ($destination);
		    }

		    $zip = new ZipArchive();
		    if (!$zip->open($destination, ZIPARCHIVE::CREATE)) {
		        return false;
		    }
		    $source = str_replace('\\', '/', realpath($source));

		    if (is_dir($source) === true)
		    {

		        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);

		        if ($include_dir) {

		            $arr = explode("/",$source);
		            $maindir = $arr[count($arr)- 1];

		            $source = "";
		            for ($i=0; $i < count($arr) - 1; $i++) { 
		                $source .= '/' . $arr[$i];
		            }

		            $source = substr($source, 1);

		            $zip->addEmptyDir($maindir);

		        }

		        foreach ($files as $file)
		        {
		            $file = str_replace('\\', '/', $file);

		            // Ignore "." and ".." folders
		            if( in_array(substr($file, strrpos($file, '/')+1), array('.', '..')) )
		                continue;

		            $file = realpath($file);

		            if (is_dir($file) === true)
		            {
		                $zip->addEmptyDir(str_replace($source . '/', '', $file . '/'));
		            }
		            else if (is_file($file) === true)
		            {
		                $zip->addFromString(str_replace($source . '/', '', $file), file_get_contents($file));
		            }
		        }
		    }
		    else if (is_file($source) === true)
		    {
		        $zip->addFromString(basename($source), file_get_contents($source));
		    }

		    return $zip->close();
		}

		//////////////////////////////////////////////////////////////////
        // LF: Return the database
        //////////////////////////////////////////////////////////////////
        
		public static function getDatabase() {
			// connect
			$mongo_client = new MongoClient();
			// select a database
			return $mongo_client->codiad_database;
		}
		
        public function getProjectsForUser($username) {
        	$mongo_client = new MongoClient();
			$collection = $mongo_client->codiad_database->users;
			
			$projects = array();
			
			/*
			 * Create a new user to verify the user type
			 */
			$This_user = new User();
			$This_user->username = $username;
			$This_user->Load();
			
			// Get the projects from other users that are being shared with this user
			$users = $collection->find();
			$user = '';
			foreach ($users as $user) {
				if (isset($user["projects"][0])) {
					for ($i = 0; $i < count($user["projects"]); $i++) {
						for ($j = 0; $j < count($user["projects"][$i]["group_members"]); $j++) {
							if ((($user["projects"][$i]["group_members"][$j]['username'] == $username) 
								|| $user["projects"][$i]['privacy'] == 'public')
								&& $This_user->type == "student" #get if the user is a student
								) {
									
								if ($user["projects"][$i]['privacy'] == 'shared' && count($user['projects'][$i]["group_members"]) > 1) {
									$user['projects'][$i]['name'] .= " (". $user['username'] . ")";
								}
								
								array_push($projects, $user["projects"][$i]);
							} else if ($This_user->type == "admin") {
								$user['projects'][$i]['name'] .= " (". $user['username'] . ")";
								$project_already_inserted = FALSE;
								foreach ($projects as $this_project) {
									if ($this_project['path'] == $user["projects"][$i]['path']) {
										$project_already_inserted = TRUE;
									}
								}
 								if (!$project_already_inserted) {
 									array_push($projects, $user["projects"][$i]);	
 								}
							} else if ($This_user->type == "professor") {
								if ($user["type"] != "admin" && array_intersect($This_user->courses, $user['courses'])) {
									//if (isset($user["projects"][$i]['privacy']['assignment'])) {
									$user['projects'][$i]['name'] .= " (". $user['username'] . ")";
									//}
									
									array_push($projects, $user["projects"][$i]);
								}
							}
						}
					}
				}			
			}
			
			return $projects;	 
    	}   
    }
    
    //////////////////////////////////////////////////////////////////
    // Wrapper for old method names
    //////////////////////////////////////////////////////////////////
    
    function debug($message) { Common::debug($message); }   
    function i18n($key) { echo Common::i18n($key); }
    function get_i18n($key) { return Common::get_i18n($key); }
    function checkSession(){ Common::checkSession(); }
    function getJSON($file,$namespace=""){ return Common::getJSON($file,$namespace); }
    function saveJSON($file,$data,$namespace=""){ Common::saveJSON($file,$data,$namespace); }
	function zipJSON($folderName, $projectName) { Common::zipJSON($folderName, $projectName); }
    function formatJSEND($status,$data=false){ return Common::formatJSEND($status,$data); }
    function checkAccess() { return Common::checkAccess(); }
    function isAvailable($func) { return Common::isAvailable($func); }
	function getDatabase(){ return Common::getDatabase(); }
	function getProjectsForUser($username) { return Common::getProjectsForUser($username); }
?>
