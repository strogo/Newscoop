B_HTML
INCLUDE_PHP_LIB(<*..*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageCategories*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Deleting category*>)
<? if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to delete categories.*>)
<? } ?>dnl
E_HEAD

<? if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER(<*Deleting category*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<?
	todefnum('IdCateg');
	todefnum('DelCateg');
	todefnum('del',1);
	query ("SELECT Name FROM Categories WHERE Id=$DelCateg", 'q_cat');
	if ($NUM_ROWS) { 
	    fetchRow($q_cat);
	
	?>dnl
<P>
B_MSGBOX(<*Deleting category*>)
	X_MSGBOX_TEXT(<*
<?
	query ("SELECT COUNT(*) FROM Categories WHERE ParentId=$DelCateg", 'q_sons');
	fetchRowNum($q_sons);
	if (getNumVar($q_sons,0) != 0) {
		$del= 0; ?>dnl
		<LI><? putGS('There are $1 subcategories left.',getNumVar($q_sons,0)); ?></LI>
    <? }
    
    $AFFECTED_ROWS=0;
    
    if ($del)
	query ("DELETE FROM Categories WHERE Id=$DelCateg");

	if ($AFFECTED_ROWS) { ?>dnl
		<LI><? putGS('The category $1 has been deleted.','<B>'.getHVar($q_cat,'Name').'</B>'); ?></LI>
		X_AUDIT(<*142*>, <*getGS('Category $1 deleted',getHVar($q_cat,'Name'))*>)
	<? } else { ?>dnl
		<LI><? putGS('The category $1 could not be deleted.','<B>'.getHVar($q_cat,'Name').'</B>'); ?></LI>
	<? } ?>dnl
*>)
	
	B_MSGBOX_BUTTONS
<? if ($AFFECTED_ROWS) { ?>dnl
		<A HREF="X_ROOT/categories/index.php?IdCateg=<?p($IdCateg);?>"><IMG SRC="X_ROOT/img/button/done.gif" BORDER="0" ALT="Done"></A>
<? } else { ?>dnl
		<A HREF="X_ROOT/categories/index.php?IdCateg=<?p($IdCateg);?>"><IMG SRC="X_ROOT/img/button/ok.gif" BORDER="0" ALT="OK"></A>
<? } ?>dnl
	E_MSGBOX_BUTTONS
E_MSGBOX
<P>
<? } else { ?>dnl
<BLOCKQUOTE>
	<LI><? putGS('No such publication.'); ?></LI>
</BLOCKQUOTE>
<? } ?>

X_HR
X_COPYRIGHT
E_BODY
<? } ?>dnl

E_DATABASE
E_HTML
