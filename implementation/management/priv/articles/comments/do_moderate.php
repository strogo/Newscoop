<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/common.php');
load_common_include_files("comments");
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/camp_html.php");
require_once($_SERVER['DOCUMENT_ROOT']."/include/phorum_load.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Phorum_forum.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Phorum_message.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Phorum_user.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/ArticleComment.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Input.php');

list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}

$f_language_id = Input::Get('f_language_id', 'int', 0, true);
$f_article_number = Input::Get('f_article_number', 'int', 0);
$f_language_selected = Input::Get('f_language_selected', 'int', 0);

// Check that the article exists.
$articleObj =& new Article($f_language_id, $f_article_number);
if (!$articleObj->exists()) {
    exit;
}

// process all comments
foreach ($_REQUEST as $name => $value) {
    if (strstr($name, "comment_action_")) {
        $parts = split("_", $name);
        $messageId = $parts[2];
        $comment =& new Phorum_message($messageId);
        if (!$comment->exists()) {
            continue;
        }
        switch ($value) {
            case "inbox":
                $comment->setStatus(PHORUM_STATUS_HIDDEN);
                break;
            case "hide":
                $comment->setStatus(PHORUM_STATUS_HOLD);
                break;
            case "delete":
                $comment->delete();
                break;
            case "approve":
                $comment->setStatus(PHORUM_STATUS_APPROVED);
                break;
        }
//        $subjectStr = Input::Get('f_subject_'.$messageId, 'string', '', true);
//        $comment->setSubject($subjectStr);
//        $commentStr = Input::Get('f_comment_'.$messageId, 'string', '', true);
//        $comment->setBody($commentStr);
    }
}
header("Location: ".camp_html_article_url($articleObj, $f_language_selected, "edit.php")."#add_comment");

?>