<?php
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/issues/issue_common.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Template.php');

// Check permissions
list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}

if (!$User->hasPermission('ManageIssue')) {
	camp_html_display_error(getGS('You do not have the right to change issue details.'));
	exit;
}
$Pub = Input::Get('Pub', 'int');
$Issue = Input::Get('Issue', 'int');
$Language = Input::Get('Language', 'int');

if (!Input::IsValid()) {
	camp_html_display_error(getGS('Invalid Input: $1', Input::GetErrorString()));
	exit;
}
$publicationObj =& new Publication($Pub);

if (!$publicationObj->exists()) {
	camp_html_display_error(getGS('Publication does not exist.'));
	exit;
}
$issueObj =& new Issue($Pub, $Language, $Issue);
if (!$issueObj->exists()) {
	camp_html_display_error(getGS('Issue does not exist.'));
	exit;
}

$allLanguages = Language::GetLanguages();

// Get translations of this issue
$issueTranslations = Issue::GetIssues($Pub, null, $Issue);
$excludeLanguageIds = DbObjectArray::GetColumn($issueTranslations, 'IdLanguage');

$allTemplates = Template::GetAllTemplates();

camp_html_content_top(getGS('Change issue details'), array('Pub' => $publicationObj, 'Issue' => $issueObj), true, true);

$url_args1 = "Pub=$Pub";
$url_args2 = $url_args1."&Issue=$Issue&Language=$Language";

if (Issue::GetNumIssues($Pub) <= 0) {
	$url_add = "add_new.php";
} else {
	$url_add = "qadd.php";
}
?>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" class="action_buttons" style="padding-top: 5px;">
<TR>
	<TD><A HREF="/<?php echo $ADMIN; ?>/issues/?Pub=<?php  p($Pub); ?>"><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/left_arrow.png" BORDER="0"></A></TD>
	<TD><A HREF="/<?php echo $ADMIN; ?>/issues/?Pub=<?php  p($Pub); ?>"><B><?php  putGS("Issue List"); ?></B></A></TD>
	<TD style="padding-left: 20px;"><A HREF="/<?php echo $ADMIN; ?>/sections/?Pub=<?php  p($Pub); ?>&Issue=<?php  p($issueObj->getIssueNumber()); ?>&Language=<?php p($issueObj->getLanguageId()); ?>"><B><?php  putGS("Go To Sections"); ?></B></A></TD>
	<TD><A HREF="/<?php echo $ADMIN; ?>/sections/?Pub=<?php  p($Pub); ?>&Issue=<?php  p($issueObj->getIssueNumber()); ?>&Language=<?php p($issueObj->getLanguageId()); ?>"><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/go_to.png" BORDER="0"></A></TD>
</TR>
</TABLE>

<P>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" class="action_buttons">
<TR>
	<TD><A HREF="<?php p($url_add); ?>?<?php p($url_args1); ?>" ><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/add.png" BORDER="0"></A></TD>
	<TD><A HREF="<?php p($url_add); ?>?<?php p($url_args1); ?>" ><B><?php  putGS("Add new issue"); ?></B></A></TD>

	<TD style="padding-left: 20px;"><A HREF="" ONCLICK="window.open('preview.php?<?php p($url_args2); ?>', 'fpreview', 'resizable=yes, menubar=no, toolbar=yes, width=800, height=600'); return false;"><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/preview.png" BORDER="0"></A></TD>
	<TD><A HREF="" ONCLICK="window.open('preview.php?<?php p($url_args2); ?>', 'fpreview', 'resizable=yes, menubar=no, toolbar=yes, width=800, height=600'); return false;"><B><?php  putGS("Preview"); ?></B></A></TD>

	<TD style="padding-left: 20px;"><A HREF="autopublish.php?<?php p($url_args2); ?>" ><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/automatic_publishing.png" BORDER="0"></A></TD>
	<TD><A HREF="autopublish.php?<?php p($url_args2); ?>" ><B><?php  putGS("Scheduled Publishing"); ?></B></A></TD>

	<TD style="padding-left: 20px;"><A HREF="translate.php?<?php p($url_args2); ?>" ><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/translate.png" BORDER="0"></A></TD>
	<TD><A HREF="translate.php?<?php p($url_args2); ?>" ><B><?php  putGS("Translate"); ?></B></A></TD>

	<TD style="padding-left: 20px;"><A HREF="do_del.php?<?php p($url_args2); ?>" onclick="return confirm('<?php putGS('Are you sure you want to delete the issue $1?', htmlspecialchars($issueObj->getName())); ?>');"><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/delete.png" BORDER="0"></A></TD>
	<TD><A HREF="do_del.php?<?php p($url_args2); ?>" onclick="return confirm('<?php putGS('Are you sure you want to delete the issue $1?', htmlspecialchars($issueObj->getName())); ?>');"><B><?php  putGS("Delete"); ?></B></A></TD>
</TR>
</TABLE>

<P>
<FORM METHOD="POST" ACTION="do_edit.php" onsubmit="return validateForm(this, 0, 1, 0, 1, 8);">
<INPUT TYPE="HIDDEN" NAME="f_publication_id" VALUE="<?php p($Pub); ?>">
<INPUT TYPE="HIDDEN" NAME="f_issue_number" VALUE="<?php p($Issue); ?>">
<INPUT TYPE="HIDDEN" NAME="f_current_language_id" VALUE="<?php p($Language); ?>">
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="6" CLASS="table_input">
<TR>
	<TD COLSPAN="2">
		<B><?php  putGS("Change issue details"); ?></B>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>

<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Number"); ?>:</TD>
	<TD>
		<?php  p($issueObj->getIssueNumber()); ?>
	</TD>
</TR>

<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Name"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="f_issue_name" SIZE="32" MAXLENGTH="64" value="<?php  p(htmlspecialchars($issueObj->getName())); ?>" alt="blank" emsg="<?php putGS('You must complete the $1 field.',"'".getGS('Name')."'"); ?>">
	</TD>
</TR>

<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Language"); ?>:</TD>
	<TD>
	    <SELECT NAME="f_new_language_id" class="input_select">
		<?php
		foreach ($allLanguages as $tmpLanguage) {
			$langId = $tmpLanguage->getLanguageId();
			if (($langId == $issueObj->getLanguageId()) || !in_array($langId, $excludeLanguageIds)) {
				camp_html_select_option($langId, $issueObj->getLanguageId(), $tmpLanguage->getNativeName());
			}
	    }
		?>
		</SELECT>
	</TD>
</TR>

<?php if ($issueObj->getWorkflowStatus() == 'Y') {?>
<TR>
	<TD ALIGN="RIGHT"><?php  putGS("Publication date<BR><SMALL>(yyyy-mm-dd)</SMALL>"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="f_publication_date" SIZE="11" MAXLENGTH="10" value="<?php  p(htmlspecialchars($issueObj->getPublicationDate())); ?>">
	</TD>
</TR>
<?php } ?>

<TR>
	<TD ALIGN="RIGHT"><?php  putGS("Front Page Template"); ?>:</TD>
	<TD>
		<SELECT NAME="f_issue_template_id" class="input_select">
		<OPTION VALUE="0">---</OPTION>
		<?php
		foreach ($allTemplates as $template) {
			camp_html_select_option($template->getTemplateId(), $issueObj->getIssueTemplateId(), $template->getName());
		}
		?>
		</SELECT>
	</TD>
</TR>

<TR>
	<TD ALIGN="RIGHT"><?php  putGS("Section Template"); ?>:</TD>
	<TD>
		<SELECT NAME="f_section_template_id" class="input_select">
		<OPTION VALUE="0">---</OPTION>
		<?php
		foreach ($allTemplates as $template) {
			camp_html_select_option($template->getTemplateId(), $issueObj->getSectionTemplateId(), $template->getName());
		}
		?>
		</SELECT>
	</TD>
</TR>

<TR>
	<TD ALIGN="RIGHT"><?php  putGS("Article Template"); ?>:</TD>
	<TD>
		<SELECT NAME="f_article_template_id" class="input_select">
		<OPTION VALUE="0">---</OPTION>
		<?php
		foreach ($allTemplates as $template) {
			camp_html_select_option($template->getTemplateId(), $issueObj->getArticleTemplateId(), $template->getName());
		}
		?>
		</SELECT>
	</TD>
</TR>

<TR>
	<TD ALIGN="RIGHT"><?php  putGS("URL Name"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="f_url_name" SIZE="32" MAXLENGTH="32" value="<?php  p(htmlspecialchars($issueObj->getUrlName())); ?>" alt="alnum|1|A|true|false|_" emsg="<?php putGS('The $1 field may only contain letters, digits and underscore (_) character.', "'" . getGS('URL Name') . "'"); ?>">
	</TD>
</TR>

<TR>
	<TD COLSPAN="2" align="center">
		<INPUT TYPE="submit" class="button" NAME="Save" VALUE="<?php  putGS('Save'); ?>">
		<!--<INPUT TYPE="button" class="button" NAME="Cancel" VALUE="<?php  putGS('Cancel'); ?>" ONCLICK="location.href='/<?php p($ADMIN); ?>/issues/?Pub=<?php p($Pub); ?>'">-->
	</TD>
</TR>
</TABLE>
</FORM>
<P>

<?php camp_html_copyright_notice(); ?>