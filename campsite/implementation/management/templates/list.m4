#!/bin/sh

DOCUMENT_ROOT=$4
PATH=/usr/bin
echo '<TABLE BORDER="0" CELLSPACING="2" CELLPADDING="0" WIDTH="100%">'
echo '<TR BGCOLOR="WHITE"><TD WIDTH="30%" VALIGN="TOP">'
echo 'B_LIST'
echo 'B_LIST_HEADER'
echo 'X_LIST_TH(<<Folders>>)'
if [ $3 != "0" ] ; then
echo 'X_LIST_TH(<<Delete>>, <<1%>>)'
fi
echo 'E_LIST_HEADER'
c=""
x="0"
v=0
for i in $(X_SCRIPT_BIN/ls_url d "$DOCUMENT_ROOT" "$1"); do
	if [ "$x" = "0" ] ; then
		j=$i
		x=1
	else
		if [ "$c" = "#D0D0D0" ] ; then
			c="#D0D0B0"
		else
			c="#D0D0D0"
		fi
		echo '<TR BGCOLOR="'$c'"><TD><TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0"><TR><TD><IMG SRC="X_ROOT/img/icon/dir.gif" BORDER="0"></TD><TD><A HREF="'$j'/">'$j'</A></TD></TR></TABLE></TD>'
		if [ $3 != "0" ] ; then  
			echo '<TD ALIGN="CENTER">X_BUTTON(<<Delete folder>>, <<icon/x.gif>>, <<templates/del.php?What=0&Path='$1'&Name='$i'>>)</TD></TR>'
		else
			echo '</TR>'
		fi
		x=0
	fi
	v=1
done
if [ "$v" = "0" ] ; then echo '<TR><TD COLSPAN="2">No folders.</TD></TR>' ; fi
echo 'E_LIST'
echo '</TD><TD WIDTH="60%" VALIGN="TOP">'
echo 'B_LIST'
echo 'B_LIST_HEADER'
echo 'X_LIST_TH(<<Files>>)'
if [ $3 != "0" ] ; then
echo 'X_LIST_TH(<<Delete>>, <<1%>>)'
fi   
echo 'E_LIST_HEADER'
c=""
x="0"
v=0
for i in $(X_SCRIPT_BIN/ls_url f "$DOCUMENT_ROOT" "$1"); do
	if [ "$x" = "0" ] ; then
		j=$i
		x=1
	else
		if [ "$c" = "#D0D0D0" ] ; then
			c="#D0D0B0"
		else
			c="#D0D0D0"
		fi
		echo '<TR BGCOLOR="'$c'"><TD><TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0"><TR><TD><IMG SRC="X_ROOT/img/icon/generic.gif" BORDER="0"></TD><TD>'$j'</TD></TR></TABLE></TD>'
		if [ $3 != "0" ] ; then
			echo '<TD ALIGN="CENTER">X_BUTTON(<<Delete file>>, <<icon/x.gif>>, <<templates/del.php?What=1&Path='$1'&Name='$i'>>)</TD></TR>'
		else
			echo '<TD ALIGN="CENTER"></td></TR>'
		fi
		x=0
	fi
	v=1
done
if [ "$v" = "0" ] ; then echo '<TR><TD COLSPAN="2">No templates.</TD></TR>' ; fi
echo 'E_LIST'
echo '</TD></TR>'
echo '</TABLE>'
