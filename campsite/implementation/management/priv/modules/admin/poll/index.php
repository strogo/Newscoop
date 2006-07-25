<?php
require_once $Campsite['HTML_DIR']."/$ADMIN_DIR/modules/start.ini.php";
require_once $Campsite['HTML_DIR']."/classes/Input.php";

$access = startModAdmin ("ManagePoll", "Poll", 'List polls');
if ($access) {

    if (file_exists(dirname(__FILE__)."/locals.{$_REQUEST['TOL_Language']}.php")) {
        require_once "locals.{$_REQUEST['TOL_Language']}.php";
    } elseif(file_exists(dirname(__FILE__)."/locals.{$_REQUEST['TOL_Language']}.php"))  {
        require_once 'locals.en.php';
    }

    $poll = Input::Get('poll', 'array', array());
    $act  = Input::Get('act');
    $lang = Input::Get('lang');
    ?>
    <form name='language' action='index.php'>
    <table border="0" width="100%">
    <tr>
    <td colspan="6">
      <?php  putGS("target lang"); ?>:
      <?php 
      if (!$lang) {
          $lang = $defaultIdLanguage;
      }
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
    $query = "SELECT *
              FROM poll_main 
              ORDER BY DateExpire DESC"; 
    $data = sqlQuery($DB['modules'], $query);

    while ($row = mysql_fetch_array($data)) {

        $query = "SELECT Title
                  FROM poll_questions 
                  WHERE IdPoll = {$row['Id']} AND 
                        IdLanguage = '$lang'";
        $translation = sqlRow($DB['modules'], $query);
        $trans = "Yes";
        $source_lang = $lang;

        if (!$translation) {
            // no translation found
            $query = "SELECT Title
                      FROM poll_questions 
                      WHERE IdPoll = {$row['Id']} AND 
                            IdLanguage = '$defaultIdLanguage'";
            $translation = sqlRow($DB['modules'], $query);
            $trans = "No";
            $source_lang = $defaultIdLanguage;
        }
        ?>
        <tr <?php if ($color) { $color=0; ?>BGCOLOR="#D0D0B0"<?php } else { $color=1; ?>BGCOLOR="#D0D0D0"<?php } ?>>
          <td><a href='edit_maindata.php?poll[Id]=<?php p($row['Id']); ?>&act=change'><?php print $translation['Title']; ?></a></td>
          <td align="center"><?php print $row[DateBegin]; ?></td><td align="center"><?php print $row['DateExpire']; ?></td>
          <td align='center'><a href='translate.php?poll[Id]=<?php p($row['Id']); ?>&source_lang=<?php print $source_lang; ?>&target_lang=<?php print $lang; ?>'><?php print $trans;?></a></td>
          <td align='center'><a href='result.php?poll[Id]=<?php p($row['Id']); ?>&target_lang=<?php print $lang; ?>'><b>X</b></a></td>
          <td align='center'><a href='delete.php?poll[Id]=<?php p($row['Id']); ?>&poll[Title]=<?php print urlencode($translation['Title']); ?>'><font color='red'><b>X</b></font></a></td>
        </tr>
        <?php
    }
  ?>
  <tr><td colspan="5">&nbsp;</td></tr>
  <tr><td colspan='5'><input type="button" value="<?php putGS("New Poll"); ?>" onClick="location.href='edit_maindata.php'"></td></tr>

  </table>
  </form>
  <?php
}
?>
