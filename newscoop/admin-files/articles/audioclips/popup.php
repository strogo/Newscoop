<?php
camp_load_translation_strings("article_audioclips");
camp_load_translation_strings('api');
require_once($GLOBALS['g_campsiteDir'].'/classes/SystemPref.php');
require_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/articles/article_common.php");

if (SystemPref::Get("UseCampcasterAudioclips") != 'Y') {
    camp_html_display_error(getGS("Campcaster integration is disabled"), null, true);
    exit;
}

if (!$g_user->hasPermission("AttachAudioclipToArticle")) {
	$errorStr = getGS('You do not have the right to attach audio clips to articles.');
	camp_html_display_error($errorStr, null, true);
	exit;
}

$f_publication_id = Input::Get('f_publication_id', 'int', 0);
$f_issue_number = Input::Get('f_issue_number', 'int', 0);
$f_section_number = Input::Get('f_section_number', 'int', 0);
$f_language_id = Input::Get('f_language_id', 'int', 0);
$f_language_selected = Input::Get('f_language_selected', 'int', 0);
$f_article_number = Input::Get('f_article_number', 'int', 0);
$f_audio_attach_mode = camp_session_get('f_audio_attach_mode', 'new');
$f_audio_search_mode = camp_session_get('f_audio_search_mode', 'browse');

if (!Input::IsValid()) {
	camp_html_display_error(getGS('Invalid input: $1', Input::GetErrorString()), $_SERVER['REQUEST_URI'], true);
	exit;
}

$articleObj = new Article($f_language_selected, $f_article_number);
?>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <meta http-equiv="Expires" content="now" />
  <meta http-equiv="Cache-Control" content="no-cache" />
  <meta http-equiv="Pragma" content="no-cache" />
  <link rel="stylesheet" type="text/css" href="<?php echo $Campsite['ADMIN_STYLE_URL']; ?>/admin_stylesheet.css">
  <title><?php putGS("Attach Audioclip To Article"); ?></title>
</head>
<body>
<?php camp_html_display_msgs(); ?>
<table style="margin-top: 10px; margin-left: 5px; margin-right: 5px;" cellpadding="0" cellspacing="0" width="98%">
<tr>
	<?php if ($g_user->hasPermission('AddAudioclip')) { ?>
	<td style="padding: 3px; border-top: 1px solid #8baed1; border-left: 1px solid #8baed1; <?php if ($f_audio_attach_mode != "new") { ?>border-bottom: 1px solid #8baed1; background-color: #BBB;<?php } else { ?>background-color: #EEE;<?php } ?>"><a href="<?php echo camp_html_article_url($articleObj, $f_language_id, "audioclips/popup.php", "", "&f_audio_attach_mode=new"); ?>"><img src="<?php p($Campsite['ADMIN_IMAGE_BASE_URL']); ?>/add.png" border="0"><b><?php putGS("Attach New Audio"); ?></b></a></td>
	<?php
          } else {
              $f_audio_attach_mode = 'existing';
          }
    ?>
	<td style="padding: 3px; border-top: 1px solid #8baed1; border-right: 1px solid #8baed1; border-left: 1px solid #8baed1; <?php if ($f_audio_attach_mode != "existing") { ?>border-bottom: 1px solid #8baed1; background-color: #BBB;<?php } else { ?>background-color: #EEE;<?php } ?>"><a href="<?php echo camp_html_article_url($articleObj, $f_language_id, "audioclips/popup.php", "", "&f_audio_attach_mode=existing"); ?>"><img src="<?php p($Campsite['ADMIN_IMAGE_BASE_URL']); ?>/add.png" border="0"><b><?php putGS("Attach Existing Audio"); ?></b></a></td>
</tr>
<tr>
	<td colspan="2" style="background-color: #EEE; padding-top: 5px; border-bottom: 1px solid #8baed1; border-right: 1px solid #8baed1; border-left: 1px solid #8baed1;">
		<?php
		if ($f_audio_attach_mode == "existing") {
		?>
        <table style="margin-top: 10px; margin-left: 5px; margin-right: 5px; margin-bottom: 5px;" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td style="padding: 3px; border-top: 1px solid #8baed1; border-left: 1px solid #8baed1; <?php if ($f_audio_search_mode != "browse") { ?>border-bottom: 1px solid #8baed1; background-color: #BBB;<?php } else { ?>background-color: #EEE;<?php } ?>"><a href="<?php echo camp_html_article_url($articleObj, $f_language_id, "audioclips/popup.php", "", "&f_audio_search_mode=browse"); ?>"><img src="<?php p($Campsite['ADMIN_IMAGE_BASE_URL']); ?>/add.png" border="0"><b><?php putGS("Browse"); ?></b></a></td>
            <td style="padding: 3px; background-color: #EEE; border-top: 1px solid #8baed1; border-right: 1px solid #8baed1; border-left: 1px solid #8baed1; <?php if ($f_audio_search_mode != "search") { ?>border-bottom: 1px solid #8baed1; background-color: #BBB;<?php } else { ?>background-color: #EEE;<?php } ?>"><a href="<?php echo camp_html_article_url($articleObj, $f_language_id, "audioclips/popup.php", "", "&f_audio_search_mode=search"); ?>"><img src="<?php p($Campsite['ADMIN_IMAGE_BASE_URL']); ?>/add.png" border="0"><b><?php putGS("Search"); ?></b></a></td>
        </tr>
        <tr>
            <td colspan="2" style="background-color: #EEE; padding-top: 5px; border-bottom: 1px solid #8baed1; border-right: 1px solid #8baed1; border-left: 1px solid #8baed1;">
		    <?php
			if ($f_audio_search_mode == 'search') {
				include("search.php");
			} else {
				include("browse.php");
			}
		    ?>
            </td>
        </tr>
        </table>
		<?php
		} else {
			include("add.php");
		}?>
	</td>
</tr>
</table>

</body>
</html>