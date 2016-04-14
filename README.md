# WSArduino
Webservice REST for arduino robot project

When someone clones this project, it will create a new directory called "configs" which contains config files:
- mkdir v1/configs

Then you must to create a new file on this folder called "db.inc.php" with the following lines:
<?php 

define("HOST_NAME", "localhost"); //IP of server
define("DATABASE_NAME", "arduino"); //Database name
define("USER", "root"); //user with rights on the database
define("PASSWORD", "abc123"); //password of user

Run the REST webservice.

