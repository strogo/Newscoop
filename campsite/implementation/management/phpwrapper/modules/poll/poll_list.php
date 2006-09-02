<?php
require_once 'poll_list.class.php';

new poll_list2smarty(getCampParametersInt(), $PARAMS['STATEMENT_PARAMS'], $REQUEST['poll'], &$Smarty);
$Smarty->display('poll_list.tpl');
?>