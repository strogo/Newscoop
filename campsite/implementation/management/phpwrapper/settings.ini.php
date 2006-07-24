<?php
global $DB, $URLPARAMS;
include_once "{$_SERVER['DOCUMENT_ROOT']}/db_connect.php";

$GLOBALS['debug']       = false;

$DB['campsite']         = 'campsite';
$DB['modules']          = 'campsite_modules';  

## set the url type
defineURLType();

$URLPARAMS = array(
    'IdLanguage'        => true,
    'IdPublication'     => true, 
    'NrIssue'           => true, 
    'NrSection'         => true, 
    'NrArticle'         => true, 
    'NrImage'           => true,
    'subtitle'          => true, 
    'ILStart'           => true, 
    'SLStart'           => true,
    'ALStart'           => true,
    'SrLStart'          => true, 
    'StLStart'          => true,
    'class'             => true, 
    'cb_subs'           => true, 
    'tx_subs'           => true, 
    'subscribe'         => true, 
    'useradd'           => true,
    'usermodify'        => true,
    'login'             => true, 
    'SubsType'          => true, 
    'keyword'           => true, 
    'search'            => true, 
    'RememberUser'      => true, 
    'tpid'              => true, 
    'tpl'               => true, 
    'SearchKeywords'    => true, 
    'SearchLevel'       => true, 
    'preview'           => true, 
    'debug'             => true,   
);
?>