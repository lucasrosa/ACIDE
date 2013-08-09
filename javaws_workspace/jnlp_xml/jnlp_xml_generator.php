<?
	echo "Header: application/x-java-jnlp-file";


	echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>";

?>
<!--- we may need to update the codebase path --->
<jnlp spec="1.0" codebase="http://hci.csit.upei.ca/javaws-test/">
  <information>
    <title>Console</title>
    <vendor>UPEI</vendor>
    <offline/>
  </information>
  <resources>
    <j2se version="1.6+"/>
    <!--- there can be a path relative to the codebase for the jar files below --->
    <jar href="../relative/path/to/Console.jar"/>
    <jar href="../relative/path/to/UserProj.jar"/> <!--- this needs to be the jar file that we created for the user--->
  </resources>
  <security>
    <all-permissions/>
  </security>
  <!--- the main class will stay the same, this is the console ---->
  <application-desc main-class="Console">
        <argument>Classname</argument> <!--- This argument must be set to the class with the main method to be executed in the terminal --->
        <argument>argument for main method of User's Class</argument> <!--- if the user types any arguments to their class on the web console, these would be listed here --->
  </application-desc>
  </jnlp>