<?php
include_once "{$_SERVER['DOCUMENT_ROOT']}/look/phpwrapper/settings.ini.php";
unset($debug);

include_once $Campsite['HTML_DIR'] . "/$ADMIN_DIR/lib_campsite.php";
require_once $Campsite['HTML_DIR'] . "/$ADMIN_DIR/modules/priv_functions.php";
include_once "{$_SERVER['DOCUMENT_ROOT']}/look/phpwrapper/functions.php";

$SYS['default_lang']  = 5;
$SYS['debug']         = false;
?>
