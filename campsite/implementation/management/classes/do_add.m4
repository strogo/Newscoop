B_HTML
INCLUDE_PHP_LIB(<<..>>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<<ManageClasses>>)

B_HEAD
	X_EXPIRES
	X_TITLE(<<Adding new class>>)
<? if ($access == 0) { ?>dnl
	X_AD(<<You do not have the right to add dictionary classes.>>)
<? } ?>dnl
E_HEAD

<? if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER(<<Adding new class>>)
B_HEADER_BUTTONS
X_HBUTTON(<<Dictionary Classes>>, <<classes/>>)
X_HBUTTON(<<Home>>, <<home.php>>)
X_HBUTTON(<<Logout>>, <<logout.php>>)
E_HEADER_BUTTONS
E_HEADER

<? todef('cName');
    todefnum('cLang', 0);
    $correct= 1;
    $created= 0;
?>dnl
<P>
B_MSGBOX(<<Adding new keyword class>>)
	X_MSGBOX_TEXT(<<
<? if ($cName == "") {
    $correct= 0; ?>
		<LI><? putGS('You must complete the $1 field.','<B>'.getGS('Name').'</B>'); ?></LI>
<? 
    }

    if ($cLang == 0) {
	$correct= 0; ?>
		<LI><? putGS('You must select a language.'); ?></LI>
<?
    }
    
    if ($correct) {
	query ("UPDATE AutoId SET ClassId=LAST_INSERT_ID(ClassId + 1)");
	if ($AFFECTED_ROWS) {
	    $AFFECTED_ROWS= 0;
	    query ("INSERT IGNORE INTO Classes SET Id=LAST_INSERT_ID(), IdLanguage='$cLang', Name='$cName'");
	    $created= ($AFFECTED_ROWS != 0);
	}
    }

    if ($created) { ?>dnl
		<LI><? putGS('The class  has been added.','<B>'.encHTML($cName).'</B>'); ?></LI>
X_AUDIT(<<81>>, <<getGS('Class $1 added',encHTML($cName))>>)
<? } else {
    if ($correct != 0) { ?>dnl
		<LI><? putGS('The class could not be added.');print('<LI></LI>'); putGS('Please check if the class does not already exist.'); ?></LI>
<?  }
}
?>dnl
		>>)
<? if ($correct && $created) { ?>dnl
	B_MSGBOX_BUTTONS
		<A HREF="X_ROOT/classes/add.php"><IMG SRC="X_ROOT/img/button/add_another.gif" BORDER="0" ALT="Add another class"></A>
		<A HREF="X_ROOT/classes/"><IMG SRC="X_ROOT/img/button/done.gif" BORDER="0" ALT="Done"></A>
	E_MSGBOX_BUTTONS
<? } else { ?>
	B_MSGBOX_BUTTONS
		<A HREF="X_ROOT/classes/add.php"><IMG SRC="X_ROOT/img/button/ok.gif" BORDER="0" ALT="OK"></A>
	E_MSGBOX_BUTTONS
<? } ?>dnl
E_MSGBOX
<P>

X_HR
X_COPYRIGHT
E_BODY
<? } ?>dnl

E_DATABASE
E_HTML
