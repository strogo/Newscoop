<?php

require_once("database_conf.php");
require_once("install_conf.php");
if (!is_array($Campsite)) {
	echo "Invalid configuration file(s)";
	exit(1);
}

$db_name = $Campsite['DATABASE_NAME'];
$db_user = $Campsite['DATABASE_USER'];
$db_passwd = $Campsite['DATABASE_PASSWORD'];
$db_host = $Campsite['DATABASE_SERVER_ADDRESS'];

if (!mysql_connect($db_host, $db_user, $db_passwd)) {
	die("Unable to connect to the database.\n");
}

if (!mysql_select_db($db_name)) {
	die("Unable to use the database " . $db_name . ".\n");
}

// 
// populate the Events table
//
$sql = "INSERT INTO Events (Id, Name, Notify, IdLanguage) VALUES ('116','Rename Template','N','1'),('117','Move Template','N','1')";
if (!($res = mysql_query($sql))) {
	die("Unable to write to the database.\n");
}

?>