B_HTML
INCLUDE_PHP_LIB(<*..*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageCategories*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Changing category name*>)
<? if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to change category name.*>)
<? } ?>dnl
E_HEAD

<? if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

<?
	todefnum('IdCateg');
	todefnum('EdCateg');
	todef('cName');
?>dnl
B_HEADER(<*Changing category name*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<?
	$correct= 1;
	$created= 0;
	query ("SELECT * FROM Categories WHERE Id=$EdCateg", 'q_cat');
    if ($NUM_ROWS) { 
	fetchRow($q_cat);
?>dnl

B_CURRENT
X_CURRENT(<*Category:*>, <*<B><? pgetHVar($q_cat,'Name'); ?></B>*>)
E_CURRENT

<P>
B_MSGBOX(<*Changing category name*>)
	X_MSGBOX_TEXT(<*
<?
	$cName=trim($cName);
	 if ($cName == "" || $cName== " ") {
		$correct=0; ?>dnl
		<LI><? putGS('You must complete the $1 field.','<B>'.getGS('Name').'</B>'); ?></LI>
	<? }

	if ($correct) {
		query ("UPDATE Categories SET Name='".encHTML($cName)."' WHERE Id=$EdCateg");
		$created= ($AFFECTED_ROWS != 0);
	}

	if ($created) { ?>dnl
		<LI><? putGS('The category $1 has been successfuly updated.',"<B>$cName</B>"); ?></LI>
		X_AUDIT(<*143*>, <*getGS('Category $1 changed',$cName)*>)
	<? } else {

	if ($correct != 0) { ?>dnl
		<LI><? putGS('The category name could not be updated.'); ?></LI>
	<? }
    } ?>dnl
*>)
	B_MSGBOX_BUTTONS
<? if ($correct && $created) { ?>dnl
		<A HREF="X_ROOT/categories/index.php?IdCateg=<?p($IdCateg);?>"><IMG SRC="X_ROOT/img/button/done.gif" BORDER="0" ALT="Done"></A>
<? } else { ?>
		<A HREF="X_ROOT/categories/edit.php?IdCateg=<? p($IdCateg); ?>&EdCateg=<? p($EdCateg); ?>"><IMG SRC="X_ROOT/img/button/ok.gif" BORDER="0" ALT="OK"></A>
<? } ?>dnl
	E_MSGBOX_BUTTONS
E_MSGBOX
<P>
<? } else { ?>dnl
<BLOCKQUOTE>
	<LI><? putGS('No such publication.'); ?></LI>
</BLOCKQUOTE>
<? } ?>dnl

X_HR
X_COPYRIGHT
E_BODY
<? } ?>dnl

E_DATABASE
E_HTML
