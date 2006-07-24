<?php
require_once $DOCUMENT_ROOT.'/look/phpwrapper/settings.ini.php';
require_once $DOCUMENT_ROOT.'/look/phpwrapper/functions.php';
require_once 'poll_linker.class.php';

$moduleHandler =& new poll_linker();
$moduleHandler->linkPoll($_REQUEST['poll_ids'], 'section', $sectionObj->getPublicationId(), $sectionObj->getLanguageId(), $sectionObj->getSectionNumber());
unset($moduleHandler);
?>