<?php
require_once 'settings.ini.php';
require_once 'functions.php';
require_once 'smarty_functions.php';
require_once 'wrapper_functions.php';

if (eregi(basename($_SERVER['SCRIPT_NAME']), basename($_SERVER['REQUEST_URI']))) {
    die();
}

if (isPhorum($_SERVER['REQUEST_URI'])) { 
    session('forum');
    loginLogout();
    $data = getData("http://{$_SERVER['HTTP_HOST']}{$_SESSION['last_tpl_uri']}", $_SERVER['HTTP_REFERER']);
    detectModules($data, TRUE);
} else {
    session('tpl'); 
    loginLogout();
    $data = getData("http://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}", $_SERVER['HTTP_REFERER']);
    detectModules($data, FALSE);
}
?>