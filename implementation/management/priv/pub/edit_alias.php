<?PHP
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/pub/pub_common.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/Alias.php");

// Check permissions
list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}

if (!$User->hasPermission('ManagePub')) {
	camp_html_display_error(getGS("You do not have the right to manage publications."));
	exit;
}

$Pub = Input::Get('Pub', 'int');
$Alias = Input::Get('Alias', 'int');
$publicationObj =& new Publication($Pub);
$alias =& new Alias($Alias);

$crumbs = array(getGS("Publication Aliases") => "aliases.php?Pub=$Pub");
camp_html_content_top(getGS("Edit alias"), array("Pub" => $publicationObj), true, false, $crumbs);

?>

<P>
<FORM NAME="dialog" METHOD="POST" ACTION="do_edit_alias.php"  >
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="6" CLASS="table_input">
<TR>
	<TD COLSPAN="2">
		<B><?php  putGS("Edit alias"); ?></B>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>
<INPUT TYPE=HIDDEN NAME=cPub VALUE="<?php  p($Pub); ?>">
<INPUT TYPE=HIDDEN NAME=cAlias VALUE="<?php  p($Alias); ?>">
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Name"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" NAME="cName" VALUE="<?php echo htmlspecialchars($alias->getName()); ?>" SIZE="32" MAXLENGTH="255">
	</TD>
</TR>
<TR>
	<TD COLSPAN="2">
	<DIV ALIGN="CENTER">
	<INPUT TYPE="submit" class="button" NAME="Save" VALUE="<?php  putGS('Save'); ?>">
<!--	<INPUT TYPE="button" class="button" NAME="Cancel" VALUE="<?php  putGS('Cancel'); ?>" ONCLICK="location.href='/admin/pub/aliases.php?Pub=<?php  p($Pub); ?>'">
-->	</DIV>
	</TD>
</TR>
</TABLE>
</FORM>
<P>
<?php camp_html_copyright_notice(); ?>