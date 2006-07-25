<?php
function transDateTo($input, $to)
{
    if ($to === 'de') {
        $p = explode("-", $input);
        return "$p[2].$p[1].$p[0]";
    } elseif ($to === 'en') {
        $p = explode(".", $input);
        return "$p[2]-$p[1]-$p[0]";
    }
}

function langMenu ($name)
{
    print "<select name='$name' onChange='this.form.submit()'>\n";
    $res = sqlQuery($GLOBALS['DB']['campsite'], "SELECT Id, OrigName FROM Languages ORDER BY Id");
    
    while ($row = mysql_fetch_array ($res)) {
        //if (!$GLOBALS[$name]) $GLOBALS[$name]=$row[Id];
        if ($GLOBALS[$name]==$row[Id]) $sel=" selected ";
        print "<option value='$row[Id]'$sel>$row[OrigName]\n";
        unset ($sel);
    }
    
    print "</select>\n";
}

function lastInsertID ()
{
    $res    = mysql_query('SELECT LAST_INSERT_ID()');
    $id     = mysql_fetch_row($res);
    
    return $id[0];
}


function dateSelectMenu ($name, $key, $curr)
{
    print "\n<select name='".$name."[$key][day]'>\n";
    for ($z=1; $z<=31; $z++)
    {
        unset ($sel);
        if (strlen ($z)==1) $v = "0$z";
        else $v = "$z";
        if ($curr[$key][day] == $v) $sel = " selected ";
        print "<option value='$v'$sel>$v.</option>\n";
    }
    print "</select>
         \n<select name='".$name."[$key][month]'>\n";
    for ($z=1; $z<=12; $z++)
    {
        unset ($sel);
        if (strlen ($z)==1) $v = "0$z";
        else $v = "$z";
        if ($curr[$key][month] == $v) $sel = " selected ";
        print "<option value='$v'$sel>$v.</option>\n";
    }

    print "</select>
         \n<select name='".$name."[$key][year]'>\n";

    $starty = date ("Y"); print $curr[$key][year];
    if (isset ($curr[$key][year]) && $curr[$key][year] < $starty) $starty = $curr[$key][year];

    for ($z=$starty; $z<=date ("Y")+5; $z++)
    {
        unset ($sel);
        if ($curr[$key][year] == $z) $sel = " selected ";
        echo "<option value='$z'$sel>$z</option>\n";
    }
    print "</select>\n";
}



function startModAdmin($permission, $modname, $actionname=null) {
    global $ADMIN, $g_user;

    /*
    list($access, $g_user) = check_basic_access($_REQUEST);
    if (!$access) {
        header("Location: /$ADMIN/logout.php");
        exit;
    }
    */
    
    if (!$g_user->hasPermission($permission)) {
        header("Location: /$ADMIN/logout.php");
        exit;
    }

    ?>
    <TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0" bgcolor="#D5E2EE" width="100%">
    <?php 
    if ($modname) {
        ?>
        <TR>
            <TD align="left" style="border-top: 1px solid #8BAED1; padding-left: 1.25em; padding-top: 3px;">
                <span><SPAN CLASS='breadcrumb'>Modules</SPAN></span>
                <span CLASS='breadcrumb_separator'>&nbsp;</span>
                <span class='breadcrumb_intra_separator'><A HREF='/admin/modules/admin/<?php p(strtolower(str_replace(' ', '_', $modname))); ?>/index.php' class='breadcrumb'><?php putGS($modname); ?></A></span>
            </TD>
        </TR>
        <?php
    }
    if ($actionname) {
        ?>
        <TR>
            <TD align="left" style="padding-left: 1.4em; padding-bottom: 2px; border-bottom: 1px solid black; padding-top: 3px;">
                <span class='breadcrumb_intra_separator'><SPAN CLASS='breadcrumb_active'><?php putGS($actionname); ?></SPAN>
                </span><span>&nbsp;</spanTD>
            </TD>
        </TR>
        <?php
    }
    ?>
    </TABLE>
    <?php

    return true;
}

function endModAdmin ()
{
    # just relict
}

?>