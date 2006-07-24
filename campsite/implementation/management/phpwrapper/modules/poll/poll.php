<?php
require_once 'poll.class.php'; 
$Smarty->assign_by_ref('poll', new poll(getCampParametersInt(), $PARAMS['STATEMENT_PARAMS'], $REQUEST['poll']));

$local_template = 'poll_'.getCampParameters('Language Code').'.tpl';
$Smarty->display($local_template);
?>
