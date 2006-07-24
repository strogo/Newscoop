<?php
require_once $DOCUMENT_ROOT.'/look/phpwrapper/settings.ini.php';
require_once $DOCUMENT_ROOT.'/look/phpwrapper/functions.php';
require_once 'poll_linker.class.php';

?>

<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Link Poll"); ?>:</TD>
	<TD>
    <?php 
    $moduleHandler =& new poll_linker();
    echo $moduleHandler->selectPoll('section', $sectionObj->getPublicationId(), $sectionObj->getLanguageId(), $sectionObj->getSectionNumber());
    unset($moduleHandler);
    ?>
 	</TD>
</TR>