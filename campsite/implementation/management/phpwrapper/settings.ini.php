<?php
if (empty($_COOKIE[session_name()])) { 
    ## parse $_ENV['HTTP_COOKIE'] and retrive value of session id
    $cookies = explode(';', $_ENV['HTTP_COOKIE']);
    foreach($cookies as $cookie) {
        parse_str($cookie, &$_COOKIE);   
    }
    if (!empty($_COOKIE[session_name()])) {
        ## if no session cookie was found, create random session id
        session_id($_COOKIE[session_name()]);   
    } else {
        session_regenerate_id();
    }
}
session_start();

global $DB, $URLPARAMS, $Campsite;
include_once "{$_SERVER['DOCUMENT_ROOT']}/db_connect.php";

$GLOBALS['debug']       = false;

$DB['campsite']         = $Campsite['DATABASE_NAME'];
$DB['modules']          = $Campsite['DATABASE_NAME'];  

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
    'acid'              => true,
    'CommentReaderEMail' => true,
    'CommentSubject'    => true,
    'CommentContent'    => true,
    'f_captcha_code'    => true,
    'submitComment'     => true,  
);

define('SUPPORT_TPL_PHP', true);
?>
