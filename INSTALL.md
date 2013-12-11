# ACIDE INSTALLATION
----------------------------------------------------------------------

To install simply place the contents of the system in a web accessible folder.


## Write capabilities

Ensure that the following directories/files have write capabilities:

    /config.php
    /data (including subfolders)
    /workspace
    /javaws_workspace

## Java WS Configuration

For the 'java' command work properly in the terminal and the generated `.class` file run by the browser automatically, you have to configure the system so it can generate and sign the required jar files.

Change to the "javawx_workspace" directory and follow the steps below.

#### Creating the keystore file

To sign a jar, we must first have a keystore (a private key) to do the signing. 

To generate a keystore we use the keytool that is part of the Java SDK.

    keytool -genkey -keystore keystore_file.keys -alias http://your_website.ca/ -validity 365

You will have to provide your information and a password.

This will create a new keystore in the file keystore_file.keys. 

The alias is typically the domain of the site you want to sign for, and the validity is the number of days until the keys will expire. 

In this case we would have to generate a new key in a year.

#### (Optional) Compile your own Console.jar
    
The console is used to run all java files created in ACIDE on the user's local computer. The console is itself downloaded as a jar file when the user executes a command using 'java classname'. The source code is included, so that the Console can be customized. The provided Console.jar file contains a precompiled and packaged Console.
    
    `javac Console.java`
    `jar cf Console.jar *.class`
    
    Add permission attributes to the MANIFEST file inside this jar
    `jar ufm Console.jar BASE-MANIFEST.MF`
    
    
#### Sign the Console.jar file

To sign the Console.jar file using the keystore you just created, use the following command (Make sure you: overwrite 'password' with the password you just used to create the keystore and that you are in the website root's directory):

    jarsigner -keystore ./javaws_workspace/keystore_file.keys -storepass 'password' \
    ./javaws_workspace/jnlp_xml/Console.jar http://your_website.ca/


#### Updating the file `term.php` to point to the `.keys` file path

Open the file `components/terminal/emulator/term.php`.

Go to line `266`:
    `system("jarsigner -keystore /var/codiad_files/jaxb.keys -storepass 'keystore password' " . $jar_path_and_name . " http://your_website.ca/");`

In line `266` do the following:
  - Overwrite `/var/codiad_files/jaxb.keys` to point to the `.keys` file you just created.
  - Overwrite `keystore password` with the password you used to generate the keystore.
  - Overwrite `http://your_website.ca/` with your own website address.
  
## Update config.php
In the base directory update the `config.php` file to contain the correct BASE_PATH, WEB_BASE_PATH, TIMEZONE, etc.


## MongoDB Installtion

ACIDE uses the database system MongoDB and the MongoDB driver for PHP. 

Both have to be installed in your server.

#### Installing MongoDB

Follow the tutorial in the **[MongoDB website](http://docs.mongodb.org/manual/tutorial/install-mongodb-on-ubuntu/)** .

#### Installing the MongoDB PHP driver.

Follow the tutorial in the **[PHP website](http://php.net/manual/en/mongo.installation.php)**.

## System Installation
    
Open the URL corresponding to where the system is placed and the
installer screen will appear. If any dependencies have not been met the
system will alert you.

Enter the requested information to create an administrator account:

    - Username (whithout spaces).
    - Password
    - E-mail
    - Database Name (without spaces)
    - Timezone
    
and submit the form using the 'Submit' button in the bottom.
    
If everything goes as planned 
you will be greeted with a login screen.

Log in using the administrator account you just created.

After logging in as the admin:

 - Create a new course.
 - Create a user in the professor role and add them to the course.
 - Logged in as the professor add students to the course.
 
#### DO NOT use the admin account to manage courses, it has not been tested. Courses should be adminstered by accounts in the professor role.
