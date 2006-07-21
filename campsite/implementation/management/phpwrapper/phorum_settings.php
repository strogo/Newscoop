<?php
// session is required for captcha plugin,a nd need different name then phpwrapper session
ini_set('session.name', 'PHORUM');
ini_set('session.save_path',      '/tmp/fluter_sessions');
ini_set('session.gc_maxlifetime', 14400);
session_start();
?>