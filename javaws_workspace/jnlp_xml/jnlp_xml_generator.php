<?
    if(file_exists('../../config.php')){ require_once('../../config.php'); }
		
	$jar_name	= $_GET['jar_name'];
	$class_name = $_GET['class_name'];
	if (isset($_GET['arguments'])) {
		$arguments  = $_GET['arguments'];
	} else {
		$arguments  = array();
	}
	
	$filename = $class_name;	
	
	header('Content-Disposition: inline; filename="'. $filename .'.jnlp"');
	header('Content-type: application/x-java-jnlp-file');
	
	$pageURL = WEB_BASE_PATH . "/javaws_workspace";
?>
<?="<?xml version=\"1.0\" encoding=\"utf-8\"?>"?>
<!--- we may need to update the codebase path --->
<jnlp spec="1.0" codebase="<?=$pageURL?>">
  <information>
    <title>Console</title>
    <vendor>UPEI</vendor>
    <offline/>
  </information>
  <resources>
    <j2se version="1.6+"/>
    <!--- there can be a path relative to the codebase for the jar files below --->
    <jar href="jnlp_xml/Console.jar"/>
    <jar href="<?=$jar_name?>"/> <!--- this needs to be the jar file that we created for the user--->
  </resources>
  <security>
    <all-permissions/>
  </security>
  <!--- the main class will stay the same, this is the console ---->
  <application-desc main-class="Console">
        <argument><?=$class_name?></argument> <!--- This argument must be set to the class with the main method to be executed in the terminal --->
        <?
        for ($i = 0; $i < count($arguments); $i++) {
        ?>
        	<argument><?=$arguments[$i]?></argument> <!--- if the user types any arguments to their class on the web console, these would be listed here --->
        <?
		}
		?>
  </application-desc>
</jnlp>
