B_HTML
INCLUDE_PHP_LIB(<*../../..*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageSubscriptions*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Change subscription*>)
<? if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to change subscriptions.*>)
<? } ?>dnl
E_HEAD

<? if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

<?
    todefnum('Subs');
    todefnum('Sect');
    todefnum('Pub');
    todefnum('User');
?>dnl
B_HEADER(<*Change subscription*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Sections*>, <*users/subscriptions/sections/?User=<? p($User); ?>&Pub=<? p($Pub); ?>&Subs=<? p($Subs); ?>*>)
X_HBUTTON(<*Subscriptions*>, <*users/subscriptions/?User=<? p($User); ?>*>)
X_HBUTTON(<*Users*>, <*users/*>)
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<?
    query ("SELECT UName FROM Users WHERE Id=$User", 'q_usr');
    if ($NUM_ROWS) {
	query ("SELECT Name FROM Publications WHERE Id=$Pub", 'q_pub');
	if ($NUM_ROWS) {
	    query ("SELECT DISTINCT Sub.*, Sec.Name FROM SubsSections as Sub, Sections as Sec WHERE IdSubscription=$Subs AND SectionNumber=$Sect AND Sub.SectionNumber = Sec.Number", 'q_ssub');
	    if ($NUM_ROWS) {
		fetchRow($q_usr);
		fetchRow($q_pub);
		fetchRow($q_ssub);
?>dnl

B_CURRENT
X_CURRENT(<*User account*>, <*<B><? pgetHVar($q_usr,'UName'); ?></B>*>)
X_CURRENT(<*Publication*>, <*<B><? pgetHVar($q_pub,'Name'); ?></B>*>)
E_CURRENT

<P>
B_DIALOG(<*Change subscription*>, <*POST*>, <*do_change.php*>)

	B_DIALOG_INPUT(<*Section*>)
		<? pgetHVar($q_ssub,'Name'); ?>
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Start*>)
		<INPUT TYPE="TEXT" NAME="cStartDate" SIZE="10" VALUE="<? pgetHVar($q_ssub,'StartDate'); ?>" MAXLENGTH="10"> <? putGS('(YYYY-MM-DD)'); ?>
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Days*>)
		<INPUT TYPE="TEXT" NAME="cDays" SIZE="5" VALUE="<? pgetHVar($q_ssub,'Days'); ?>"  MAXLENGTH="5">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Paid Days*>)
		<INPUT TYPE="TEXT" NAME="cPaidDays" SIZE="5" VALUE="<? pgetHVar($q_ssub,'PaidDays'); ?>"  MAXLENGTH="5">
	E_DIALOG_INPUT
	B_DIALOG_BUTTONS
		<INPUT TYPE="HIDDEN" NAME="User" VALUE="<? p($User); ?>">
		<INPUT TYPE="HIDDEN" NAME="Subs" VALUE="<? p($Subs); ?>">
		<INPUT TYPE="HIDDEN" NAME="Sect" VALUE="<? p($Sect); ?>">
		<INPUT TYPE="HIDDEN" NAME="Pub" VALUE="<? p($Pub); ?>">
		<INPUT TYPE="IMAGE" NAME="OK" SRC="X_ROOT/img/button/save.gif" BORDER="0">
		<A HREF="X_ROOT/users/subscriptions/sections/?Pub=<? p($Pub); ?>&User=<? p($User); ?>&Subs=<? p($Subs); ?>"><IMG SRC="X_ROOT/img/button/cancel.gif" BORDER="0" ALT="Cancel"></A>
	E_DIALOG_BUTTONS
E_DIALOG
<P>

<? } else { ?>dnl
<BLOCKQUOTE>
	<LI><? putGS('No such subscription.'); ?></LI>
</BLOCKQUOTE>
<? } ?>dnl

<? } else { ?>dnl
<BLOCKQUOTE>
	<LI><? putGS('No such publication.'); ?></LI>
</BLOCKQUOTE>
<? } ?>dnl

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
