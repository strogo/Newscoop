<?php
require_once $DOCUMENT_ROOT.'/look/phpwrapper/settings.ini.php';
require_once $DOCUMENT_ROOT.'/look/phpwrapper/functions.php';
require_once 'poll_linker.class.php';

$moduleHandler =& new poll_linker(); 
$moduleHandler->linkpoll($_REQUEST['poll_ids'], 'article', $articleObj->getPublicationId(), $articleObj->getLanguageId(), $articleObj->getArticleNumber());
unset($moduleHandler);
?>