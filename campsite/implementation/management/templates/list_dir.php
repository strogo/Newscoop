<TABLE BORDER="0" CELLSPACING="2" CELLPADDING="0" WIDTH="100%">
<TR BGCOLOR="WHITE"><TD WIDTH="30%" VALIGN="TOP">
<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0" WIDTH="100%">
<TR BGCOLOR="#C0D0FF">
<TD  ><B> <? putGS('Folders'); ?> </B></TD>
<?
    if ( $dta != "0" ) {
	echo '<TD WIDTH="1%" ><B> '.getGS('Delete').' </B></TD>';
    }
?>
</TR>
<?
    $c="";

    $basedir="$DOCUMENT_ROOT".decURL($listbasedir);
    //print "basedir = $basedir<BR>listbasedir = $listbasedir<br>";

    $handle=opendir($basedir);
    while (($file = readdir($handle))!=false) {
	$full="$basedir/$file";
        $filetype=filetype($full);
        $isdir=false;
        $isfile=false;
        // avoiding the links
        if ($filetype=="dir") $isdir=true;
        else if ($filetype!="link") $isfile=true;
        // if it's a file
        if ($isfile){
            // filling the array
            $files[]=$file;
        }
        // if it's a directory but not the .. or .
        else if ($isdir&&$file!="."&&$file!=".."){
            // filling the array
            $dirs[]=$file;
        }
    }
    
if (isset($dirs)) {
    sort($dirs);
    for($fi=0;$fi<count($dirs);$fi++) {
	    $j=$dirs[$fi];

	    if ($c == "#D0D0D0" )
		$c="#D0D0B0";
	    else
		$c="#D0D0D0";
	    
	    print "<TR BGCOLOR='$c'><TD><TABLE BORDER='0' CELLSPACING='1' CELLPADDING='0'><TR><TD><IMG SRC='/priv/img/icon/dir.gif' BORDER='0'></TD><TD><A HREF='".encURL($j)."'>$j</A></TD></TR></TABLE></TD>";
	    
	    if ($dta != 0)
		print "<TD ALIGN='CENTER'><A HREF='/priv/templates/del.php?What=0&Path=".encURL($listbasedir)."&Name=".encURL($j)."'><IMG SRC='/priv/img/icon/x.gif' BORDER='0' ALT='".getGS('Delete folder')."'></A></TD></TR>";
	    else
		echo '</TR>';
    }
}
else{
echo '<TR><TD COLSPAN="2">'.getGS('No folders.').'</TD></TR>' ;
}
?>
</TABLE>
</TD><TD WIDTH="60%" VALIGN="TOP">
<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0" WIDTH="100%">
<TR BGCOLOR="#C0D0FF">
<TD  ><B> <? putGS('Files'); ?> </B></TD>
<?
    if($dta!= "0")
	echo '<TD WIDTH="1%" ><B> '.getGS('Delete').' </B></TD>';
?>
</TR>
<?
    $c="";

if (isset($files)) {
    sort($files);
    for($fi=0;$fi<count($files);$fi++) {
	    $j=$files[$fi];

	    if ($c == "#D0D0D0" )
		$c="#D0D0B0";
	    else
		$c="#D0D0D0";
	    
	    print "<TR BGCOLOR='$c'><TD><TABLE BORDER='0' CELLSPACING='1' CELLPADDING='0'><TR><TD><IMG SRC='/priv/img/icon/generic.gif' BORDER='0'></TD><TD>$j</TD></TR></TABLE></TD>";
	    
	    if ($dta != 0)
		print "<TD ALIGN='CENTER'><A HREF='/priv/templates/del.php?What=1&Path=".encURL($listbasedir)."&Name=".encURL($j)."'><IMG SRC='/priv/img/icon/x.gif' BORDER='0' ALT='".getGS('Delete file')."'></A></TD></TR>";
	    else
		echo '<TD ALIGN="CENTER"></td></TR>';
    }
}
else{
echo '<TR><TD COLSPAN="2">'.getGS('No templates.').'</TD></TR>' ;
}

?>
</TABLE>
</TD></TR>
</TABLE>
