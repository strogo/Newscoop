<?php
camp_load_translation_strings("article_images");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Article.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Image.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Issue.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Section.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Language.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Publication.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Log.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Input.php');

if (!$g_user->hasPermission('AddImage')) {
	camp_html_display_error(getGS('You do not have the right to add images.' ), null, true);
	exit;
}

$f_language_id = Input::Get('f_language_id', 'int', 0);
$f_language_selected = Input::Get('f_language_selected', 'int', 0);
$f_article_number = Input::Get('f_article_number', 'int', 0);
$f_image_template_id = Input::Get('f_image_template_id', 'int', 0);
$f_image_description = Input::Get('f_image_description');
$f_image_photographer = Input::Get('f_image_photographer');
$f_image_place = Input::Get('f_image_place');
$f_image_date = Input::Get('f_image_date');
$f_image_url = Input::Get('f_image_url', 'string', '', true);
$BackLink = Input::Get('BackLink', 'string', null, true);

if (!Input::IsValid()) {
	camp_html_display_error(getGS('Invalid input: $1', Input::GetErrorString()), null, true);
	exit;
}

$articleObj =& new Article($f_language_selected, $f_article_number);

// If the template ID is in use, dont add the image.
if (ArticleImage::TemplateIdInUse($f_article_number, $f_image_template_id)) {
	header('Location: '.camp_html_article_url($articleObj, $f_language_id, 'images/popup.php'));
	exit;
}

$attributes = array();
$attributes['Description'] = $f_image_description;
$attributes['Photographer'] = $f_image_photographer;
$attributes['Place'] = $f_image_place;
$attributes['Date'] = $f_image_date;
if (!empty($f_image_url)) {
	$image = Image::OnAddRemoteImage($f_image_url, $attributes, $g_user->getUserId());
} elseif (!empty($_FILES['f_image_file']) && !empty($_FILES['f_image_file']['name'])) {
	$image = Image::OnImageUpload($_FILES['f_image_file'], $attributes, $g_user->getUserId());
} else {
	camp_html_display_error(getGS("You must select an image file to upload."), $BackLink, true);
	exit;
}

// Check if image was added successfully
if (!is_object($image)) {
	camp_html_display_error($image, $BackLink, true);
	exit;
}

ArticleImage::AddImageToArticle($image->getImageId(), $articleObj->getArticleNumber(), $f_image_template_id);

?>
<script>
window.opener.document.forms.article_edit.onsubmit();
window.opener.document.forms.article_edit.submit();
window.close();
</script>
