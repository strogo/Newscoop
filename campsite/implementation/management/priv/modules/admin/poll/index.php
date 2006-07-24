<?php
require_once $Campsite['HTML_DIR']."/$ADMIN_DIR/modules/start.ini.php";

$access = startModAdmin ("ManagePoll", "Poll", 'List polls');
if ($access) {

    if (file_exists(dirname(__FILE__)."/locals.{$_REQUEST['TOL_Language']}.php")) {
        require_once "locals.{$_REQUEST['TOL_Language']}.php";
    } elseif(file_exists(dirname(__FILE__)."/locals.{$_REQUEST['TOL_Language']}.php"))  {
        require_once 'locals.en.php';
    }

    $poll = $_REQUEST['poll'];
    $act  = $_REQUEST['act'];
    $lang = $_REQUEST['lang'];
    ?>
    <form name='language' action='index.php'>
    <table border="0" width="100%">
    <tr>
    <td colspan="6">
      <?php  putGS("target lang"); ?>:
      <?php
      if (!$lang) $lang = $SYS[default_lang];
      langmenu("lang");
      ?>
    </td>
    </tr>
    <tr><td colspan="5">&nbsp;</td></tr>
    <tr BGCOLOR="#C0D0FF">
    <td><b><?php putGS("title"); ?></b></td>
    <td align="center"><b><?php putGS("from"); ?></b></td>
    <td align="center"><b><?php putGS("to"); ?></b></td>
    <td align="center"><b><?php putGS("translated"); ?></b></td>
    <td align="center"><b><?php putGS("results"); ?></b></td>
    <td align="center"><b><?php putGS("delete it"); ?></b></td>
    </tr>
    <?php
    $query = "SELECT * FROM poll_main ORDER BY dto DESC";
    $data = sqlQuery($DB['poll'], $query);

    while ($row = mysql_fetch_array($data)) {
        //$row[dfrom] = transDateTo ($row[dfrom], "de");
        //$row[dto] = transDateTo ($row[dto], "de");

        $query = "SELECT title FROM poll_questions WHERE id_poll=$row[id] AND id_language='$lang'";
        $res = sqlRow ($DB['poll'], $query);
        $trans = "Yes";
        $source_lang = $lang;
        if (!$res)
        {           // no translation found
        $query = "SELECT title FROM poll_questions WHERE id_poll=$row[id] AND id_language='$SYS[default_lang]'";
        $res = sqlRow ($DB['poll'], $query);
        $trans = "No";
        $source_lang = $SYS[default_lang];
    }
    ?>
    <tr <?php if ($color) { $color=0; ?>BGCOLOR="#D0D0B0"<?php } else { $color=1; ?>BGCOLOR="#D0D0D0"<?php } ?>>
      <td><a href='edit_maindata.php?poll[id]=<?php echo $row[id]; ?>&act=change'><?php print $res[title]; ?></a></td>
      <td align="center"><?php print $row[dfrom]; ?></td><td align="center"><?php print $row[dto]; ?></td>
      <td align='center'><a href='translate.php?poll[id]=<?php print $row[id]; ?>&source_lang=<?php print $source_lang; ?>&target_lang=<?php print $lang; ?>'><?php print $trans;?></a></td>
      <td align='center'><a href='result.php?poll[id]=<?php print $row[id]; ?>&target_lang=<?php print $lang; ?>'><b>X</b></a></td>
      <td align='center'><a href='delete.php?poll[id]=<?php print $row[id]; ?>&poll[title]=<?php print urlencode ($res[title]); ?>'><font color='red'><b>X</b></font></a></td>
    </tr>
    <?php
    }
  ?>
  <tr><td colspan="5">&nbsp;</td></tr>
  <tr><td colspan='5'><input type="button" value="<?php putGS("new poll"); ?>" onClick="location.href='edit_maindata.php'"></td></tr>

  </table>
  </form>
  <?php
}
?>
