B_HTML
INCLUDE_PHP_LIB(<*../..*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageSubscriptions*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Add new subscription*>)
<? if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to add subscriptions.*>)
<? } ?>dnl
E_HEAD

<? if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

<? todefnum('User'); ?>dnl
B_HEADER(<*Add new subscription*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Subscriptions*>, <*users/subscriptions/?User=<? p($User); ?>*>)
X_HBUTTON(<*Users*>, <*users/*>)
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<?
    query ("SELECT UName FROM Users WHERE Id=$User", 'q_usr');
    if ($NUM_ROWS) { 
	fetchRow($q_usr);
    ?>dnl

B_CURRENT
X_CURRENT(<*User account:*>, <*<B><? pgetHVar($q_usr,'UName'); ?></B>*>)
E_CURRENT

<P>
B_DIALOG(<*Add new subscription*>, <*POST*>, <*do_add.php*>)
	B_DIALOG_INPUT(<*Publication:*>)
<?
    query ("SELECT Id, Name FROM Publications ORDER BY Name", 'q_pub'); 
    $nr=$NUM_ROWS;
?>dnl
		<SELECT NAME="cPub">
		<?
		    for($loop=0;$loop<$nr;$loop++) {
			fetchRow($q_pub);
			pComboVar(getHVar($q_pub,'Id'),'',getHVar($q_pub,'Name'));
		    }
		?>dnl
		</SELECT>
	E_DIALOG_INPUT
	B_X_DIALOG_INPUT(<*<INPUT TYPE="CHECKBOX" NAME="cActive">*>)
		Active
	E_DIALOG_INPUT
	B_DIALOG_BUTTONS
		<INPUT TYPE="HIDDEN" NAME="User" VALUE="<? p($User); ?>">
		<INPUT TYPE="IMAGE" NAME="OK" SRC="X_ROOT/img/button/save.gif" BORDER="0">
		<A HREF="X_ROOT/users/subscriptions/?User=<? p($User); ?>"><IMG SRC="X_ROOT/img/button/cancel.gif" BORDER="0" ALT="Cancel"></A>
	E_DIALOG_BUTTONS
E_DIALOG
<P>

<? } else { ?>dnl
<BLOCKQUOTE>
	<LI><? putGS('No such user account.'); ?></LI>
</BLOCKQUOTE>
<? } ?>dnl

X_HR
X_COPYRIGHT
E_BODY
<? } ?>dnl

E_DATABASE
E_HTML
