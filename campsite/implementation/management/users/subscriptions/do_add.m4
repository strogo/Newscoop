B_HTML
INCLUDE_PHP_LIB(<*../..*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageSubscriptions*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Adding subscription*>)
<? if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to add subscriptions.*>)
<? } ?>dnl
E_HEAD

<? if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

<?
    todefnum('User');
    todefnum('cPub');
    todefnum('cActive');
    if ($cActive === "on") 
	$cActive= "Y";
    else
	$cActive= "N";
?>dnl
B_HEADER(<*Adding subscription*>)
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
B_MSGBOX(<*Adding subscription*>)
<?
    query ("INSERT IGNORE INTO Subscriptions SET IdUser=$User, IdPublication=$cPub, Active='$cActive'");
    if ($AFFECTED_ROWS) { ?>dnl
	X_MSGBOX_TEXT(<*<LI><? putGS('The subscription has been added successfully.'); ?></LI>*>)
<? } else { ?>dnl
	X_MSGBOX_TEXT(<*<LI><? putGS('The subscription could not be added.'); ?></LI><LI><? putGS("Please check if there isn't another subscription to the same publication."); ?></LI>*>)
<? } ?>dnl
	B_MSGBOX_BUTTONS
<? if ($AFFECTED_ROWS) { ?>dnl
		<A HREF="X_ROOT/users/subscriptions/?User=<? p($User); ?>"><IMG SRC="X_ROOT/img/button/done.gif" BORDER="0" ALT="Done"></A>
<? } else { ?>dnl
		<A HREF="X_ROOT/users/subscriptions/add.php?User=<? p($User); ?>"><IMG SRC="X_ROOT/img/button/ok.gif" BORDER="0" ALT="OK"></A>
<? } ?>dnl
	E_MSGBOX_BUTTONS
E_MSGBOX
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
