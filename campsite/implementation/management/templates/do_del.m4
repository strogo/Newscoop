B_HTML
INCLUDE_PHP_LIB(<*..*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*DeleteTempl*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Deleting template*>)
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
B_HEADER(<*Deleting template*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Templates*>, <*templates/?Path=<? pencURL(decS($Path)); ?>*>)
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<P>
B_MSGBOX(<*Deleting template*>)
	X_MSGBOX_TEXT(<*<LI>
<?
    $dir=decS($Path).decS($Name);
    
    //print $dir.'--'.$What;

    if ($What=='0') {
	$msg_ok="The folder has been deleted.";
	$msg_fail="The folder could not be deleted.";
    } else {
	$msg_ok="The template has been deleted.";
	$msg_fail="The template could not be deleted.";
    }

    $exestring=("/bin/rm -r \"$DOCUMENT_ROOT$dir\"");
    $php_errormsg='';
    @exec($exestring);
    if ($php_errormsg!='')
	$msg=$msg_fail;
    else 
	$msg=$msg_ok;

    putGS($msg);
    
    
?>
X_AUDIT(<*112*>, <*getGS('Templates deleted from $1',encHTML(decS($Path)).encHTML(decS($Name)) )*>)
	</LI>*>)
	B_MSGBOX_BUTTONS
		<A HREF="<? p(decS($Path)); ?>"><IMG SRC="X_ROOT/img/button/done.gif" BORDER="0" ALT="Done"></A>
	E_MSGBOX_BUTTONS
E_MSGBOX
<P>

X_HR
X_COPYRIGHT
E_BODY
<? } ?>dnl

E_DATABASE
E_HTML

