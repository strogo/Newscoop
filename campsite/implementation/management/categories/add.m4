B_HTML
INCLUDE_PHP_LIB(<*..*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageCategories*>)

B_HEAD
	X_TITLE(<*Add new category*>)
<? if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to add categories.*>)
<? } ?>dnl
E_HEAD

<? if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER(<*Add new category*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Categories*>, <*categories/*>)
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER
<?
    todefnum('IdCateg');
?>dnl

<P>
B_DIALOG(<*Add new category*>, <*POST*>, <*do_add.php*>)
	B_DIALOG_INPUT(<*Name:*>)
		<INPUT TYPE="TEXT" NAME="cName" SIZE="32" MAXLENGTH="32">
		<INPUT TYPE="HIDDEN" NAME="IdCateg" VALUE="<?p($IdCateg);?>">
	E_DIALOG_INPUT
	
	B_DIALOG_BUTTONS
		<INPUT TYPE="IMAGE" NAME="OK" SRC="X_ROOT/img/button/save.gif" BORDER="0">
<?
    todef('Back');
    if ($Back != "") { ?>dnl
		<A HREF="<? print($Back); ?>"><IMG SRC="X_ROOT/img/button/cancel.gif" BORDER="0" ALT="Cancel"></A>
<? } else { ?>dnl
		<A HREF="X_ROOT/categories/index.php?IdCateg=<?p($IdCateg);?>"><IMG SRC="X_ROOT/img/button/cancel.gif" BORDER="0" ALT="Cancel"></A>
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
