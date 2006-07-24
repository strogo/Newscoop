<?php
require_once $DOCUMENT_ROOT.'/look/phpwrapper/settings.ini.php';
require_once $DOCUMENT_ROOT.'/look/phpwrapper/functions.php';
require_once 'poll_linker.class.php';

?>
<TR><TD>
	<!-- BEGIN COMMENTS table -->
	<TABLE width="100%" style="border: 1px solid #EEEEEE;">
	<TR>
		<TD>
			<TABLE width="100%" bgcolor="#EEEEEE" cellpadding="3" cellspacing="0">
			<TR>
				<TD align="left">
				<b><?php putGS("Link Poll"); ?></b>
				</td>
		    </TR>
		    
		    <TR><TD>
            <?php 
            $moduleHandler =& new poll_linker();
            echo $moduleHandler->selectPoll('article', $articleObj->getPublicationId(), $articleObj->getLanguageId(), $articleObj->getArticleNumber());
            unset($moduleHandler);
            ?>
            </TD></TR>
            </TABLE>
        </TD>
    </TR>
    </TABLE>
</TD></TR>