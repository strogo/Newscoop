B_HTML
INCLUDE_PHP_LIB(<<..>>)
B_DATABASE

CHECK_BASIC_ACCESS

B_HEAD
	X_EXPIRES
	X_TITLE(<<Menu>>)
E_HEAD

<? if ($access) { ?>dnl
B_STYLE
E_STYLE

<?
    todefnum('lang');
    todefnum('pub');
    todefnum('iss');
    todefnum('ssect');
?>dnl
<FRAMESET ROWS="70, *" BORDER="0">
    <FRAME SRC="art.php?lang=<? pencURL($lang); ?>&pub=<? pencURL($pub); ?>&iss=<? pencURL($iss); ?>&ssect=<? pencURL($ssect); ?>" NAME="fart" FRAMEBORDER="0" MARGINHEIGHT="0" NORESIZE SCROLLING="NO">
    <FRAME SRC="empty.php?bg=0" NAME="f5" FRAMEBORDER="0" MARGINHEIGHT="0" NORESIZE SCROLLING="NO">
</FRAMESET>

<? } ?>dnl

E_DATABASE
E_HTML
