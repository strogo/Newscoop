B_HTML
INCLUDE_PHP_LIB(<<..>>)
B_DATABASE

<?
    query ("SELECT Id, Name FROM Languages WHERE 1=0", 'q_lang');
?>dnl
CHECK_BASIC_ACCESS
CHECK_ACCESS(<<ManagePub>>)

B_HEAD
	X_EXPIRES
	X_TITLE(<<Change publication information>>)
<? if ($access == 0) { ?>dnl
	X_AD(<<You do not have the right to edit publication information.>>)
<? }
    query ("SELECT Id, Name FROM Languages WHERE 1=0", 'q_lang');
    query ("SELECT Unit, Name FROM TimeUnits WHERE 1=0", 'q_unit');
?>dnl
E_HEAD

<? if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

<?
    todefnum('Pub');
?>
B_HEADER(<<Change publication information>>)
B_HEADER_BUTTONS
X_HBUTTON(<<Publications>>, <<pub/>>)
X_HBUTTON(<<Home>>, <<home.php>>)
X_HBUTTON(<<Logout>>, <<logout.php>>)
E_HEADER_BUTTONS
E_HEADER

<?
    query ("SELECT * FROM Publications WHERE Id=$Pub", 'q_pub');
    if ($NUM_ROWS) { 
	fetchRow($q_pub);
?>dnl
B_CURRENT
X_CURRENT(<<Publication:>>, <<<B><? pgetHVar($q_pub,'Name'); ?></B>>>)
E_CURRENT

<P>
B_DIALOG(<<Change publication information>>, <<POST>>, <<do_edit.php>>)
	B_DIALOG_INPUT(<<Name:>>)
		<INPUT TYPE="TEXT" NAME="cName" VALUE="<? pgetHVar($q_pub,'Name'); ?>" SIZE="32" MAXLENGTH="32">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<<Site:>>)
		<INPUT TYPE="TEXT" NAME="cSite" VALUE="<? pgetHVar($q_pub,'Site'); ?>" SIZE="32" MAXLENGTH="128">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<<Default language:>>)
	    <SELECT NAME="cLanguage">
	    <?
		query ("SELECT Id, Name FROM Languages", 'q_lang');
		    $nr=$NUM_ROWS;
		    for($loop=0;$loop<$nr;$loop++) {
			fetchRow($q_lang);
			pcomboVar(getVar($q_lang,'Id'),getVar($q_pub,'IdDefaultLanguage'),getVar($q_lang,'Name'));
		    }
	    ?>dnl	
	    </SELECT>
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<<Pay time:>>)
		<INPUT TYPE="TEXT" NAME="cPayTime" VALUE="<? pgetHVar($q_pub,'PayTime'); ?>" SIZE="5" MAXLENGTH="5"> <? putGS('days'); ?>
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<<Time Unit:>>)
	    <SELECT NAME="cTimeUnit">
<?
    query ("SELECT Unit, Name FROM TimeUnits", 'q_unit');
		    $nr=$NUM_ROWS;
		    for($loop=0;$loop<$nr;$loop++) {
			fetchRow($q_unit);
			pcomboVar(getVar($q_unit,'Unit'),getVar($q_pub,'TimeUnit'),getVar($q_unit,'Name'));
		    }
		?>dnl	
	    </SELECT>
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<<Unit Cost:>>)
		<INPUT TYPE="TEXT" NAME="cUnitCost" VALUE="<? pgetHVar($q_pub,'UnitCost'); ?>" SIZE="20" MAXLENGTH="32">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<<Currency:>>)
		<INPUT TYPE="TEXT" NAME="cCurrency" VALUE="<? pgetHVar($q_pub,'Currency'); ?>" SIZE="20" MAXLENGTH="32">
	E_DIALOG_INPUT
	B_DIALOG_BUTTONS
		<INPUT TYPE="HIDDEN" NAME="Pub" VALUE="<? pencHTML($Pub); ?>">
		<INPUT TYPE="IMAGE" NAME="OK" SRC="X_ROOT/img/button/save.gif" BORDER="0">
		<A HREF="X_ROOT/pub/"><IMG SRC="X_ROOT/img/button/cancel.gif" BORDER="0" ALT="Cancel"></A>
	E_DIALOG_BUTTONS
E_DIALOG
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
