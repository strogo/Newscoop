<?php

if (!is_array($GLOBALS['argv'])) {
	echo "Can't read command line arguments\n";
	exit(1);
}

$etc_dir = isset($GLOBALS['argv'][1]) ? trim($GLOBALS['argv'][1]) : "";
$type = isset($GLOBALS['argv'][2]) ? trim($GLOBALS['argv'][2]) : "";
$arg3 = isset($GLOBALS['argv'][3]) ? trim($GLOBALS['argv'][3]) : "";
//$options = getopt("a:i:");

if ($type == "-a") {
	$archive_file = $arg3;
}
if ($type == "-i") {
	$instance_name = $arg3;
}

if ($etc_dir == "" || $type == "" || ($type == "-a" && $archive_file == "")
	|| ($type == "-i" && $instance_name == "") || ($type != '-a' && $type != '-i')) {
	echo "Invalid parameters.  Usage:\n"
		. "    campsite-restore-instance -a <backup_file>  [-i <instance_name>]\n"
		. "\n"
		. "    where:\n"
		. "    [-a <backup_file>]   = Restore the instance from the backup file.\n"
		. "                           Give the full or relative path to the file.\n"
		. "    [-i <instance_name>] = Dont use the instance name specified in the\n"
		. "                           backup file, instead replace a currently installed\n"
		. "                           instance.  Note that the backup database will\n"
		. "                           be upgraded if necessary.\n";
	exit(1);
}

// include install configuration file
require_once("$etc_dir/install_conf.php");
require_once($Campsite['BIN_DIR'] . "/cli_script_lib.php");

$adviceOnError = "Please run this script as 'root' or as '" . $Campsite['APACHE_USER'] . "'.";

if ($type == "-a") {
	// copy the archive to a temporary directory to read the instance name
	// create temporary directory
	$tmp_dir = tempnam($Campsite['CAMPSITE_DIR'] . "/backup", "tmp");
	camp_create_dir($tmp_dir, "Unable to create temporary directory.");
	camp_exec_command("rm -f $tmp_dir/*", $adviceOnError);

	// unarchive the backup
	$cmd = "tar xfC " . escapeshellarg($archive_file) . " " . escapeshellarg($tmp_dir);
	camp_exec_command($cmd, $adviceOnError);

	// read instance name from database package
	$db_file = glob("$tmp_dir/*-database.tar.gz");
	if (sizeof($db_file) != 1) {
		camp_exit_with_error("Archive $archive_file is invalid.");
	}
	$db_file_name = basename($db_file[0]);
	$instance_name = substr($db_file_name, 0, strrpos($db_file_name, '-'));

	// move files to instance backup directory and remove temporary directory
	$backup_dir = $Campsite['CAMPSITE_DIR'] . "/backup/$instance_name";
	camp_create_dir($backup_dir, "Unable to create instance backup directory.");
	camp_exec_command("mv -f $tmp_dir/* " . escapeshellarg($backup_dir), $adviceOnError);
	camp_exec_command("rmdir " . escapeshellarg($tmp_dir), $adviceOnError);
}

if ($type == "-i") {
	// look for the archive in backup directory
	$backup_dir = $Campsite['CAMPSITE_DIR'] . "/backup/$instance_name";
	$archive_file = "$backup_dir/$instance_name-bak.tar";
	if (!is_file($archive_file)) {
		camp_exit_with_error("Archive file for instance $instance_name does not exist.");
	}

	// unarchive the backup
	$cmd = "pushd " . escapeshellarg($backup_dir) . " > /dev/null && tar xf "
		. escapeshellarg($archive_file) . " &> /dev/null && popd > /dev/null";
	camp_exec_command($cmd, $adviceOnError);
}

// call campsite-create-instance
$bin_dir = $Campsite['BIN_DIR'];
camp_exec_command("$bin_dir/campsite-create-instance --db_name $instance_name --no_database",
			 $adviceOnError);

// extract packages
$html_dir = $Campsite['WWW_DIR'] . "/$instance_name/html";
$packages = glob("$backup_dir/$instance_name-*.tar.gz");
foreach ($packages as $index=>$package) {
	$package_name = basename($package);
	switch ($package_name) {
	case "$instance_name-database.tar.gz": $package = ""; break;
	case "$instance_name-conf.tar.gz": $dest_dir = $etc_dir; break;
	default: $dest_dir = $html_dir; break;
	}
	if ($package == "") {
		continue;
	}
	$cmd = "pushd " . escapeshellarg($dest_dir) . " && tar xzf "
		. escapeshellarg($package) . " && popd > /dev/null";
	camp_exec_command($cmd, $adviceOnError);
}

$database_dump_file = "$backup_dir/$instance_name-database.sql";
require_once("$etc_dir/$instance_name/database_conf.php");

// backup old database dump if exists
if (is_file($database_dump_file) && camp_backup_file($database_dump_file, $output) != 0) {
	camp_exit_with_error($output);
}

// backup the old database if exists
if (($res = camp_connect_to_database()) != 0) {
	camp_exit_with_error($res);
}
if (camp_database_exists($instance_name)) {
	camp_backup_database($instance_name, $database_dump_file, $output);
	if (camp_backup_file($database_dump_file, $output) != 0) {
		camp_exit_with_error($output);
	}
}

// extract the database dump file now
$cmd = "pushd " . escapeshellarg($backup_dir) . " && tar xzf "
	. escapeshellarg("$backup_dir/$instance_name-database.tar.gz") . " && popd > /dev/null";
camp_exec_command($cmd, $adviceOnError);

// restore the database and create language links
if (($res = restore_database($instance_name, $database_dump_file)) !== 0) {
	camp_exit_with_error($res);
}
require_once("$html_dir/parser_utils.php");
camp_create_language_links($html_dir);

// remove packages
camp_exec_command("rm -f $backup_dir/*.tar.gz", $adviceOnError);
camp_exec_command("rm -f $backup_dir/$instance_name-database.sql", $adviceOnError);



function restore_database($p_db_name, $dump_file)
{
	global $Campsite, $adviceOnError;

	if (!is_file($dump_file)) {
		return "Can't restore database: dump file not found";
	}

	if (camp_database_exists($p_db_name)) {
		camp_clean_database($p_db_name);
	} else {
		if (!mysql_query("CREATE DATABASE $p_db_name")) {
			return "Can't create database $p_db_name";
		}
	}

	$cmd = "mysql -u " . $Campsite['DATABASE_USER'] . " --host="
		. $Campsite['DATABASE_SERVER_ADDRESS'] . " --port="
		. $Campsite['DATABASE_SERVER_PORT'];
	if ($Campsite['DATABASE_PASSWORD'] != "") {
		$cmd .= " --password=\"" . $Campsite['DATABASE_PASSWORD'] . "\"";
	}
	$cmd .= " $p_db_name < \"$dump_file\"";
	camp_exec_command($cmd, $adviceOnError);

	return 0;
}

?>