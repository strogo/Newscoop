B_HTML
INCLUDE_PHP_LIB(<*..*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManagePub*>)

B_HEAD
	X_TITLE(<*Add new publication*>)
<? if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to add publications.*>)
<? } ?>dnl
E_HEAD

<? if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER(<*Add new publication*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Publications*>, <*pub/*>)
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<P>
B_DIALOG(<*Add new publication*>, <*POST*>, <*do_add.php*>)
	B_DIALOG_INPUT(<*Name:*>)
		<INPUT TYPE="TEXT" NAME="cName" SIZE="32" MAXLENGTH="32">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Site:*>)
		<INPUT TYPE="TEXT" NAME="cSite" VALUE="<? pencHTML($HTTP_HOST); ?>" SIZE="32" MAXLENGTH="128">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Default language:*>)
	    <SELECT NAME="cLanguage">
<? 
    query ("SELECT Id, Name FROM Languages", 'q_lang');
		    $nr=$NUM_ROWS;
		    for($loop=0;$loop<$nr;$loop++) {
			fetchRow($q_lang);
			pcomboVar(getHVar($q_lang,'Id'),'',getHVar($q_lang,'Name'));
		    }
		?>
	    </SELECT>
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Pay time:*>)
		<INPUT TYPE="TEXT" NAME="cPayTime" VALUE="1" SIZE="5" MAXLENGTH="5"> days
	E_DIALOG_INPUT
	B_DIALOG_BUTTONS
		<INPUT TYPE="IMAGE" NAME="OK" SRC="X_ROOT/img/button/save.gif" BORDER="0">
<?
    todef('Back');
    if ($Back != "") { ?>dnl
		<A HREF="<? print($Back); ?>"><IMG SRC="X_ROOT/img/button/cancel.gif" BORDER="0" ALT="Cancel"></A>
<? } else { ?>dnl
		<A HREF="X_ROOT/pub/"><IMG SRC="X_ROOT/img/button/cancel.gif" BORDER="0" ALT="Cancel"></A>
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
