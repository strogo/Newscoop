<?php
require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/topics/topics_common.php");

list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}

if (!$User->hasPermission('ManageTopics')) {
	camp_html_display_error(getGS("You do not have the right to change topic name."));
	exit;
}

$f_topic_edit_id = Input::Get('f_topic_edit_id', 'int', 0);
$f_topic_language_id = Input::Get('f_topic_language_id', 'int', 0);
$editTopic =& new Topic($f_topic_edit_id);
$path = camp_topic_path($editTopic, $f_topic_language_id);

$crumbs = array();
$crumbs[] = array(getGS("Configure"), "");
$crumbs[] = array(getGS("Topics"), "/$ADMIN/topics/");
$crumbs[] = array(getGS("Change topic name"), "");
echo camp_html_breadcrumbs($crumbs);

?>
<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/fValidate/fValidate.config.js"></script>
    <script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/fValidate/fValidate.core.js"></script>
    <script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/fValidate/fValidate.lang-enUS.js"></script>
    <script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/fValidate/fValidate.validators.js"></script>

<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="1" WIDTH="100%" class="current_location_table">
<TR>
	<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP" class="current_location_title">&nbsp;<?php  putGS("Topic"); ?>:</TD>
	<TD VALIGN="TOP" class="current_location_content"><?php p($path);?></TD>
</TR>
</TABLE>

<P>
<FORM NAME="dialog" METHOD="POST" ACTION="do_edit.php"  onsubmit="return validateForm(this, 0, 1, 0, 1, 8);">
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="6" CLASS="table_input">
<TR>
	<TD COLSPAN="2">
		<B><?php  putGS("Change topic name"); ?></B>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Name"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="f_name" VALUE="<?php p(htmlspecialchars($editTopic->getName($f_topic_language_id))); ?>" SIZE="32" MAXLENGTH="255" alt="blank" emsg="<?php putGS('You must complete the $1 field.',getGS('Name')); ?>">
	</TD>
</TR>
<TR>
	<TD COLSPAN="2">
	<DIV ALIGN="CENTER">
	<INPUT TYPE="HIDDEN" NAME="f_topic_edit_id" VALUE="<?php  p($f_topic_edit_id); ?>">
	<INPUT TYPE="HIDDEN" NAME="f_topic_language_id" VALUE="<?php  p($f_topic_language_id); ?>">
	<INPUT TYPE="submit" class="button" NAME="Save" VALUE="<?php  putGS('Save'); ?>">
	<INPUT TYPE="button" class="button" NAME="Cancel" VALUE="<?php  putGS('Cancel'); ?>" ONCLICK="location.href='/<?php p($ADMIN); ?>/topics/index.php'">
	</DIV>
	</TD>
</TR>
</TABLE>
</FORM>
<P>
<?php camp_html_copyright_notice(); ?>