B_HTML
INCLUDE_PHP_LIB(<<..>>)
<?    
    require('./lib_upload.php');
?>
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<<ManageTempl>>)

B_HEAD
	X_EXPIRES
	X_TITLE(<<Uploading template>>)
<? if ($access == 0) { ?>dnl
	X_AD(<<You do not have the right to upload templates.>>)
<? } else {
	//dSystem( "$scriptBase/process_t '$Id'");
    $debugLevelHigh=false;
    $debugLevelLow=false;
    

    doUpload("File",'/tmp/',"camp".uniqid(''),$DOCUMENT_ROOT.'/'.stripslashes($Path));

} ?>dnl
E_HEAD

<? if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

<? todefnum('Id'); ?>dnl
B_HEADER(<<Uploading template>>)
B_HEADER_BUTTONS
X_HEADER_NO_BUTTONS
E_HEADER_BUTTONS
E_HEADER

<P>
B_MSGBOX(<<Uploading template...>>)
	B_MSGBOX_BUTTONS
		<A HREF="<? pencHTML(decS($Path)); ?>"><IMG SRC="X_ROOT/img/button/done.gif" BORDER="0" ALT="Done"></A>

	E_MSGBOX_BUTTONS
E_MSGBOX
<P>


X_AUDIT(<<111>>, <<getGS('Template uploaded')>>)

X_HR
X_COPYRIGHT
E_BODY
<? } ?>dnl

E_DATABASE
E_HTML