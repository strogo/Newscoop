<?php
require_once 'poll_list.class.php';

new poll_list2smarty(getCampParametersInt(), $PARAMS['STATEMENT_PARAMS'], $REQUEST['poll'], &$Smarty);
$localized_template = 'poll_list_'.getCampParameters('Language Code').'.tpl';

if (file_exists($Smarty->template_dir.$localized_template)) {
    $Smarty->display($localized_template);
} else {
    $Smarty->display('poll_list_default.tpl'); 
}
?>