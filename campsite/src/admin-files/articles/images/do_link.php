<?php
camp_load_translation_strings("article_images");
require_once($GLOBALS['g_campsiteDir'].'/classes/Article.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Image.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/User.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Log.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Input.php');

if (!SecurityToken::isValid()) {
    camp_html_display_error(getGS('Invalid security token!'));
    exit;
}

$f_language_selected = Input::Get('f_language_selected', 'int', 0);
$f_article_number = Input::Get('f_article_number', 'int', 0);
$f_image_id = Input::Get('f_image_id', 'int', 0);

if (!Input::IsValid()) {
	camp_html_display_error(getGS('Invalid input: $1', Input::GetErrorString()), null, true);
	exit;
}

$articleObj = new Article($f_language_selected, $f_article_number);
if (!$articleObj->exists()) {
	camp_html_display_error(getGS('Article does not exist.'), null, true);
	exit;
}

$imageObj = new Image($f_image_id);
if (!$imageObj->exists()) {
	camp_html_display_error(getGS('Image does not exist.'), null, true);
	exit;
}

// This file can only be accessed if the user has the right to change articles
// or the user created this article and it hasnt been published yet.
if (!$g_user->hasPermission('AttachImageToArticle')) {
	camp_html_display_error(getGS("You do not have the right to attach images to articles."), null, true);
	exit;
}

ArticleImage::AddImageToArticle($f_image_id, $f_article_number);

?>
<script type="text/javascript">
try {
parent.$.fancybox.reload = true;
parent.$.fancybox.close();
} catch (e) {}
</script>
