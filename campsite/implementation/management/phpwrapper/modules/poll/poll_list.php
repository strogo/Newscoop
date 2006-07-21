<?php
require_once 'poll_list.class.php';

$Smarty->assign_by_ref('poll_list', new poll_list($PARAMS, $PARAMS['type']));
$Smarty->display('poll_list.tpl');
?>