B_HTML
INCLUDE_PHP_LIB(<<../..>>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<<ManageSubscriptions>>)

B_HEAD
	X_EXPIRES
	X_TITLE(<<Delete subscription>>)
<? if ($access == 0) { ?>dnl
	X_AD(<<You do not have the right to delete subscriptions.>>)
<? } ?>dnl
E_HEAD

<? if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

<?
    todefnum('User');
    todefnum('Subs');
?>dnl
B_HEADER(<<Delete subscription>>)
B_HEADER_BUTTONS
X_HBUTTON(<<Subscriptions>>, <<users/subscriptions/?User=<? p($User); ?>>>)
X_HBUTTON(<<Users>>, <<users/>>)
X_HBUTTON(<<Home>>, <<home.php>>)
X_HBUTTON(<<Logout>>, <<logout.php>>)
E_HEADER_BUTTONS
E_HEADER

<?
    query ("SELECT UName FROM Users WHERE Id=$User", 'q_usr');
    if ($NUM_ROWS) {
	query ("SELECT Active, IdPublication FROM Subscriptions WHERE Id=$Subs", 'q_subs');
	if ($NUM_ROWS) {
	    fetchRow($q_usr);
	    fetchRow($q_subs);
	    query ("SELECT Name FROM Publications WHERE Id=".getSVar($q_subs,'IdPublication'), 'q_pub');
	    if ($NUM_ROWS) {
		fetchRow($q_pub);
	?>dnl

B_CURRENT
X_CURRENT(<<User account:>>, <<<B><? pgetHVar($q_usr,'UName'); ?></B>>>)
X_CURRENT(<<Publication:>>, <<<B><? pgetHVar($q_pub,'Name'); ?></B>>>)
E_CURRENT

<P>
B_MSGBOX(<<Delete subscription>>)
	X_MSGBOX_TEXT(<<<LI><? putGS('Are you sure you want to delete the subscription to the publication $1?','<B>'.getHVar($q_pub,'Name').'</B>'); ?></LI>>>)
	B_MSGBOX_BUTTONS
		<FORM METHOD="POST" ACTION="do_del.php">
		<INPUT TYPE="HIDDEN" NAME="User" VALUE="<? p($User); ?>">
		<INPUT TYPE="HIDDEN" NAME="Subs" VALUE="<? p($Subs); ?>">
		<INPUT TYPE="IMAGE" NAME="Yes" SRC="X_ROOT/img/button/yes.gif" BORDER="0">
		<A HREF="X_ROOT/users/subscriptions/?User=<? p($User); ?>"><IMG SRC="X_ROOT/img/button/no.gif" BORDER="0" ALT="No"></A>
		</FORM>
	E_MSGBOX_BUTTONS
E_MSGBOX
<P>

<? } else { ?>dnl
<BLOCKQUOTE>
	<LI><? putGS('No such publication.'); ?></LI>
</BLOCKQUOTE>
<? } ?>dnl

<? } else { ?>dnl
<BLOCKQUOTE>
	<LI><? putGS('No such subscription.'); ?></LI>
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
