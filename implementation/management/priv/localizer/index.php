<?php
require_once($_SERVER['DOCUMENT_ROOT']."/configuration.php");
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/camp_html.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/Input.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/common.php");
load_common_include_files("localizer");
require_once('Localizer.php');

global $g_translationStrings;
global $g_localizerConfig;

// Check permissions
list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}

if (!$User->hasPermission('ManageLocalizer')) {
	camp_html_display_error(getGS("You do not have the right to manage the localizer."));
	exit;
}

$crumbs = array();
$crumbs[] = array("Configure", "");
$crumbs[] = array("Localizer", "");
echo camp_html_breadcrumbs($crumbs);

require_once("translate.php");
translationForm($_REQUEST);

?>
</body>
</html>