B_HTML
INCLUDE_PHP_LIB(<*..*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*DeleteTempl*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Delete templates*>)
<? if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to delete templates.*>)
<? } ?>dnl
E_HEAD

<? if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

<?
    todef('Path');
    todef('Name');
    todefnum('What');
?>dnl
B_HEADER(<*Delete templates*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Templates*>, <*templates/?Path=<? pencURL(decS($Path)); ?>*>)
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

B_CURRENT
X_CURRENT(<*Path:*>, <*<B><? pencHTML(decURL($Path)); ?></B>*>)
E_CURRENT

<P>

B_MSGBOX(<*Delete templates*>)
<? if ($What == 0) { ?>dnl
	X_MSGBOX_TEXT(<*<LI><? putGS('Are you sure you want to delete the folder $1 from $2?','<B>'.encHTML(decS($Name)).'</B>','<B>'.encHTML(decURL(decS($Path))).'</B>'); ?></LI>*>)
<? } else { ?>dnl
	X_MSGBOX_TEXT(<*<LI><? putGS('Are you sure you want to delete the template $1 from folder $2?','<B>'.encHTML(decS($Name)).'</B>','<B>'.encHTML(decURL(decS($Path))).'</B>'); ?></LI>*>)
<? } ?>dnl
	B_MSGBOX_BUTTONS
		<FORM METHOD="POST" ACTION="do_del.php">
		<INPUT TYPE="HIDDEN" NAME="Path" VALUE="<? pencHTML(decS($Path)); ?>">
		<INPUT TYPE="HIDDEN" NAME="Name" VALUE="<? pencHTML(decS($Name)); ?>">
		<INPUT TYPE="HIDDEN" NAME="What" VALUE="<? p($What); ?>">
		<INPUT TYPE="IMAGE" SRC="X_ROOT/img/button/yes.gif" BORDER="0" NAME="Yes"></A>
		<A HREF="<? pencHTML(decS($Path)); ?>"><IMG SRC="X_ROOT/img/button/no.gif" BORDER="0" ALT="No"></A>
		</FORM>
	E_MSGBOX_BUTTONS
E_MSGBOX
<P>

X_HR
X_COPYRIGHT
E_BODY
<? } ?>dnl

E_DATABASE
E_HTML
