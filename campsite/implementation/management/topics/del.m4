B_HTML
INCLUDE_PHP_LIB(<*..*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageCategories*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Delete category*>)
<? if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to delete categories.*>)
<? } ?>dnl
E_HEAD

<? if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER(<*Delete category*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<?
    todefnum('IdCateg');
    todefnum('DelCateg');
    query ("SELECT * FROM Categories WHERE Id=$DelCateg", 'p');
    if ($NUM_ROWS) {
	fetchRow($p);
?>dnl

B_CURRENT
	<?
		$crtCat = $IdCateg;
		while($crtCat != 0){
			query ("SELECT * FROM Categories WHERE Id = $crtCat", 'q_cat');
			fetchRow($q_cat);									//should I release the resource ?
			$Path= getVar($q_cat,'Name')."/".$Path;
			$crtCat =getVar($q_cat, 'ParentId');
		}
		if($Path == '') $Path="/";
	?>
	X_CURRENT(<*Category:*>, <*<B><?p($Path);?></B>*>)
E_CURRENT

<P>
B_MSGBOX(<*Delete category*>)
	X_MSGBOX_TEXT(<*<LI><? putGS('Are you sure you want to delete the category $1?','<B>'.getHVar($p,'Name').'</B>'); ?></LI>*>)
	B_MSGBOX_BUTTONS
		<FORM METHOD="POST" ACTION="do_del.php">
		<INPUT TYPE="HIDDEN" NAME="IdCateg" VALUE="<? p($IdCateg); ?>">
		<INPUT TYPE="HIDDEN" NAME="DelCateg" VALUE="<? p($DelCateg); ?>">
		<INPUT TYPE="IMAGE" NAME="Yes" SRC="X_ROOT/img/button/yes.gif" BORDER="0">
		<A HREF="X_ROOT/categories/index.php?IdCateg=<?p($IdCateg);?>"><IMG SRC="X_ROOT/img/button/no.gif" BORDER="0" ALT="No"></A>
		</FORM>
	E_MSGBOX_BUTTONS
E_MSGBOX
<P>
<? } else { ?>dnl
<BLOCKQUOTE>
	<LI><? putGS('No such category.'); ?></LI>
</BLOCKQUOTE>
<? } ?>dnl

X_HR
X_COPYRIGHT
E_BODY
<? } ?>dnl

E_DATABASE
E_HTML
