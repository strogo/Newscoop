B_HTML
INCLUDE_PHP_LIB(<*..*>)
B_DATABASE

CHECK_BASIC_ACCESS

B_HEAD
	X_EXPIRES
	X_TITLE(<*Categories*>)
<? if ($access == 0) { ?>dnl
	X_LOGOUT
<? } 
    query ("SELECT * FROM Categories WHERE 1=0", 'categ');
?>dnl
E_HEAD

<? if ($access) { 
SET_ACCESS(<*mca*>, <*ManageCategories*>)
?>dnl

B_STYLE
E_STYLE

B_BODY

B_HEADER(<*Categories*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<?
    todefnum('IdCateg');
//    print "crtCateg = $IdCateg<p>";
    todef('Path');
?>dnl

B_CURRENT
	<?
		$crtCat = $IdCateg;
		while($crtCat != 0){
			query ("SELECT * FROM Categories WHERE Id = $crtCat", 'q_cat');
			fetchRow($q_cat);									//should I release the resource ?
			$Path= "<A HREF=index.php?IdCateg=".getVar($q_cat, 'ParentId')."> ".getVar($q_cat,'Name')."</A>/".$Path;
			$crtCat =getVar($q_cat, 'ParentId');
		}
		if($Path == '') $Path="/";
	?>
	X_CURRENT(<*Category:*>, <*<B><?p($Path);?></B>*>)
E_CURRENT

<P>X_NEW_BUTTON(<*Add new category*>, <*add.php?IdCateg=<?p($IdCateg);?>&Back=<? pencURL($REQUEST_URI); ?>*>)

<P><?
	todefnum('CatOffs');
	if ($CatOffs < 0) $CatOffs= 0;
	$lpp=10;
    
	query ("SELECT * FROM Categories WHERE ParentId = $IdCateg ORDER BY Name LIMIT $CatOffs, ".($lpp+1), 'categ');
	if ($NUM_ROWS) {
		$nr= $NUM_ROWS;
		$i= $lpp;
		$color= 0;
	?>dnl
B_LIST
	B_LIST_HEADER
		X_LIST_TH(<*Name*>)
		X_LIST_TH(<*Change*>, <*1%*>)
		X_LIST_TH(<*Delete*>, <*1%*>)
	E_LIST_HEADER
<?
    for($loop=0;$loop<$nr;$loop++) {
	fetchRow($categ);
	if ($i) { ?>dnl
	B_LIST_TR
		B_LIST_ITEM
			<A HREF="index.php?IdCateg=<?pgetVar($categ,'Id');?>"><? pgetVar($categ,'Name'); ?></A>
		E_LIST_ITEM
		B_LIST_ITEM(<*CENTER*>)
			Change
		E_LIST_ITEM
		B_LIST_ITEM(<*CENTER*>)
			X_BUTTON(<*<? putGS('Delete category $1',getHVar($categ,'Name')); ?>*>, <*icon/x.gif*>, <*categories/del.php?Pub=<? pgetVar($categ,'Id'); ?>*>)
		E_LIST_ITEM
    E_LIST_TR
<?
    $i--;
    }
} ?>dnl
	B_LIST_FOOTER
<? if ($CatOffs <= 0) { ?>dnl
		X_PREV_I
<? } else { ?>dnl
		X_PREV_A(<*index.php?IdCateg=<?p($IdCateg);?>&CatOffs=<? print ($CatOffs - $lpp); ?>*>)
<? } ?>dnl
<? if ($nr < $lpp+1) { ?>dnl
		X_NEXT_I
<? } else { ?>dnl
		X_NEXT_A(<*index.php?IdCateg=<?p($IdCateg);?>&CatOffs=<? print ($CatOffs + $lpp); ?>*>)
<? } ?>dnl
	E_LIST_FOOTER
E_LIST
<? } else { ?>dnl
<BLOCKQUOTE>
	<LI><? putGS('No categories.'); ?></LI>
</BLOCKQUOTE>
<? } ?>dnl

X_HR
X_COPYRIGHT
E_BODY
<? } ?>dnl

E_DATABASE
E_HTML
