B_HTML
INCLUDE_PHP_LIB(<<..>>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<<ManageClasses>>)

B_HEAD
	X_EXPIRES
	X_TITLE(<<Delete class>>)
<? if ($access == 0) { ?>dnl
		X_AD(<<You do not have the right to delete dictionary classes.>>)
<? } ?>dnl
E_HEAD

<? if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER(<<Delete class>>)
B_HEADER_BUTTONS
X_HBUTTON(<<Dictionary Classes>>, <<classes/>>)
X_HBUTTON(<<Home>>, <<home.php>>)
X_HBUTTON(<<Logout>>, <<logout.php>>)
E_HEADER_BUTTONS
E_HEADER

<?
    todefnum('Class');
    todefnum('Lang');
    query ("SELECT Name FROM Classes WHERE Id=$Class AND IdLanguage=$Lang", 'c');
?>dnl
<P>
<? if ($NUM_ROWS) { 
    fetchRow($c);
?>dnl
B_MSGBOX(<<Delete class>>)
	X_MSGBOX_TEXT(<<<LI><? putGS('Are you sure you want to delete the class $1?','<B>'.getHVar($c,'Name').'</B>'); ?></LI>>>)
	B_MSGBOX_BUTTONS
		<FORM METHOD="POST" ACTION="do_del.php">
		<INPUT TYPE="HIDDEN" NAME="Class" VALUE="<? print encHTML(decS($Class)); ?>">
		<INPUT TYPE="HIDDEN" NAME="Lang" VALUE="<? print encHTML(decS($Lang)); ?>">
		<INPUT TYPE="HIDDEN" NAME="cName" VALUE="<? pgetVar($c,'Name'); ?>">
		<INPUT TYPE="IMAGE" NAME="Yes" SRC="X_ROOT/img/button/yes.gif" BORDER="0">
		<A HREF="X_ROOT/classes/"><IMG SRC="X_ROOT/img/button/no.gif" BORDER="0" ALT="No"></A>
		</FORM>
	E_MSGBOX_BUTTONS
E_MSGBOX
<? } else { ?>dnl
	<LI><? putGS('No such class.'); ?></LI>
<? } ?>dnl
<P>

X_HR
X_COPYRIGHT
E_BODY
<? } ?>dnl

E_DATABASE
E_HTML

