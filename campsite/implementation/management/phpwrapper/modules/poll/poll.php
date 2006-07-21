<?php
require_once 'poll.class.php';

$Smarty->assign_by_ref('poll', new poll($PARAMS, 0));
$Smarty->display('poll.tpl');
?>
