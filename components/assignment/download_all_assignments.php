<?
	/*
    *  Copyright (c) UPEI sbateman@upei.ca lrosa@upei.ca
    */
    
	require_once('../../common.php');
    require_once('../user/class.user.php');
	require_once('../project/class.project.php');
	require_once('class.assignment.php');
	
	//////////////////////////////////////////////////////////////////
    // Verify Session or Key
    //////////////////////////////////////////////////////////////////
    checkSession();
	if (!isset($_POST['id'])) {
		echo "Error: No Assignment defined.";	
	} else {
		$directory =  dirname($_SERVER['SCRIPT_NAME']);
		$directories = explode('/', $directory);
		$root_directory = "";
		
		if ($directories[1] != "") {
			$root_directory = "/" . $directories[1];
		}
		
		function zip_files($sources, $destination, $include_dir = false)
		{
			for ($i = 0; $i < count($sources); $i++) {
				$source = $sources[$i];
	
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
				
			}

		    return $zip->close();
		}

		$Project = new Project();
		$assignment_id = $_POST['id'];
		
		$assignmentName = $Project->GetAssignmentName($assignment_id);
		$projects = $Project->GetProjectsForAssignment($assignment_id);
		
		$assignment_sources = array();
		
		$directory =  dirname($_SERVER['SCRIPT_NAME']);
		$directories = explode('/', $directory);
		if ($directories[1] != "") {
			$root_directory = "/" . $directories[1];
		}
		
		
		for ($k = 0; $k < count($projects); $k++) {
			
			$assignment_sources[] = "../../data/assignments/submitted/" . $projects[$k]["assignment"]["project_file_name"]; 
		}
		
		// Zipping the file
		$zipFile = '../../data/assignments/submitted/' . $assignment_id . ' .zip';
		
		$include_dir = true;
		@unlink($zipFile);
		
		if (zip_files($assignment_sources, $zipFile, $include_dir)) {
			
			$file = '../../data/assignments/submitted/' . $assignment_id . ' .zip';
			$filename = $assignment_id . ' .zip';

			if (file_exists($file)) {
			    header('Content-Description: File Transfer');
			    header('Content-Type: application/octet-stream');
			    header('Content-Disposition: attachment; filename='.basename($file));
			    header('Content-Transfer-Encoding: binary');
			    header('Expires: 0');
			    header('Cache-Control: must-revalidate');
			    header('Pragma: public');
			    header('Content-Length: ' . filesize($file));
			    ob_clean();
			    flush();
			    readfile($file);
			    exit;
			}
			
		}
	}
?>