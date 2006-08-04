<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/phpwrapper/settings.ini.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/phpwrapper/functions.php';
require_once $_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/modules/include/poll/poll_linker.class.php";

?>

<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Link Poll"); ?>:</TD>
	<TD>
    <?php 
    $moduleHandler =& new poll_linker();
    echo $moduleHandler->selectPoll(null, $f_language_id);
    unset($moduleHandler);
    ?>
 	</TD>
</TR>