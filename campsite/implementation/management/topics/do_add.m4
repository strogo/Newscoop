B_HTML
INCLUDE_PHP_LIB(<*..*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageTopics*>)

B_HEAD
	X_TITLE(<*Add new topic*>)
<? if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to add topics.*>)
<? } ?>dnl
E_HEAD

<? if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER(<*Add new topic*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER
<?
	todefnum('IdCateg');
	todef('cName');
	$correct=1;
	$created=0;
?>dnl
<P>

B_MSGBOX(<*Adding new topic*>)
	X_MSGBOX_TEXT(<*
<?
    $cName=trim($cName);

    if ($cName == "" || $cName == " ") {
	$correct= 0; ?>dnl
		<LI><? putGS('You must fill in the $1 field.','<B>'.getGS('Name').'</B>'); ?></LI>
    <? }

	 if ($correct) {
		$AFFECTED_ROWS=0;
		query ("UPDATE AutoId SET TopicId=LAST_INSERT_ID(TopicId + 1)");
		query ("INSERT IGNORE INTO Topics SET Id = LAST_INSERT_ID(), Name='".decS($cName)."', ParentId = '$IdCateg', LanguageId = 1");
		$created= ($AFFECTED_ROWS > 0);
	}

	if ($created) { ?>dnl
		<LI><? putGS('The topic $1 has been successfuly added.',"<B>".decS($cName)."</B>"); ?></LI>
		X_AUDIT(<*141*>, <*getGS('Topic $1 added',$cName)*>)
	<?
	} else {
	if ($correct != 0) { ?>dnl
		<LI><? putGS('The topic could not be added.'); ?></LI>
	<? }
}
?>dnl
		*>)
<? if ($correct && $created) { ?>dnl
	B_MSGBOX_BUTTONS
		<A HREF="X_ROOT/topics/add.php?IdCateg=<?p($IdCateg);?>"><IMG SRC="X_ROOT/img/button/add_another.gif" BORDER="0" ALT="Add another topic"></A>
		<A HREF="X_ROOT/topics/index.php?IdCateg=<?p($IdCateg);?>"><IMG SRC="X_ROOT/img/button/done.gif" BORDER="0" ALT="Done"></A>
	E_MSGBOX_BUTTONS
<? } else { ?>
	B_MSGBOX_BUTTONS
		<A HREF="X_ROOT/topics/add.php?IdCateg=<?p($IdCateg);?>"><IMG SRC="X_ROOT/img/button/ok.gif" BORDER="0" ALT="OK"></A>
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
