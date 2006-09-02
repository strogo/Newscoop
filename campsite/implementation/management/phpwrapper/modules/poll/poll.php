<?php
require_once 'poll.class.php'; 

new poll2smarty(getCampParametersInt(), $PARAMS['STATEMENT_PARAMS'], $REQUEST['poll'], &$Smarty); 
$local_template = 'poll_'.getCampParameters('Language Code').'.tpl';
$Smarty->display($local_template);
?>
