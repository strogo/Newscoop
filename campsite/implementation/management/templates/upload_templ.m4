B_HTML
INCLUDE_PHP_LIB(<*..*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageTempl*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Upload template*>)
<? if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to upload templates.*>)
<? } ?>dnl
E_HEAD

<? if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER(<*Upload template*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Templates*>, <*templates/?Path=<? pencURL(decS($Path)); ?>*>)
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<? todef('Path'); ?>dnl
<P>
B_DIALOG(<*Upload template*>, <*POST*>, <*do_upload_templ.php*>, <*multipart/form-data*>)
	B_DIALOG_INPUT(<*File:*>)
		<INPUT TYPE="HIDDEN" NAME="Path" VALUE="<? pencHTML(decS($Path)); ?>">
		<INPUT TYPE="HIDDEN" NAME="UNIQUE_ID" VALUE="1">
		<INPUT TYPE="FILE" NAME="File" SIZE="32" MAXLENGTH="128">
	E_DIALOG_INPUT
	B_DIALOG_BUTTONS
		<INPUT TYPE="IMAGE" NAME="OK" SRC="X_ROOT/img/button/save.gif" BORDER="0">
<? todef('Back');
    if ($Back != "") { ?>dnl
		<A HREF="<? p($Back); ?>"><IMG SRC="X_ROOT/img/button/cancel.gif" BORDER="0" ALT="Cancel"></A>
<? } else { ?>dnl
		<A HREF="X_ROOT/templates/?Path=<? pencURL(decS($Path)); ?>"><IMG SRC="X_ROOT/img/button/cancel.gif" BORDER="0" ALT="Cancel"></A>
<? } ?>dnl
	E_DIALOG_BUTTONS
E_DIALOG
<P>

X_HR
X_COPYRIGHT
E_BODY
<? } ?>dnl

E_DATABASE
E_HTML
