<?php
global $Campsite;
global $PHORUM;
define( "PHORUM", "5.1-dev" );

require_once($_SERVER['DOCUMENT_ROOT'].'/classes/SystemPref.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/include/phorum/include/constants.php');

$PHORUM['DBCONFIG']=array(

    // Database connection.
    'type'          =>  'mysql',
    'name'          =>  'campsite_fake_remote', // $Campsite['DATABASE_NAME'],
    'server'        =>  SystemPref::Get('DBReplicationHost') . ':' . SystemPref::Get('DBReplicationPort'),
    'user'          =>  SystemPref::Get('DBReplicationUser'),
    'password'      =>  SystemPref::Get('DBReplicationPass'),
    'table_prefix'  =>  'phorum',

    // 'down_page'     => 'http://www.example.com/phorum/down.html',

    // 1=enabled, 0=disabled
    // (always disable this option for MySQL versions prior to 4.0.18!)
    'mysql_use_ft'  =>  '1'
);

require_once($_SERVER['DOCUMENT_ROOT']."/include/phorum/include/db/mysql.php");

?>
