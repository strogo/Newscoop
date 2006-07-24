<?php
require_once $Campsite['HTML_DIR']."/$ADMIN_DIR/modules/start.ini.php";

$access = startModAdmin ("ManagePoll", "Poll", 'Poll result');
if ($access) {

    if (file_exists(dirname(__FILE__)."/locals.{$_REQUEST['TOL_Language']}.php")) {
        require_once "locals.{$_REQUEST['TOL_Language']}.php";
    } elseif(file_exists(dirname(__FILE__)."/locals.{$_REQUEST['TOL_Language']}.php"))  {
        require_once 'locals.en.php';
    }

    $poll         = $_REQUEST['poll'];
    $target_lang  = $_REQUEST['target_lang'];
  ?>
  <form name='language' action='result.php'>
  <table border="0" width="100%">
  <tr bgcolor="#C0D0FF">
    <td><b><?php putGS ("results"); ?></b></td>
    <td align="right">
      <?php putGS("target lang"); ?>:
      <?php
      if (!$lang) $lang = $SYS[default_lang];
      langmenu ("target_lang");
      ?>
      <input type="hidden" name="poll[id]" value="<?php print $poll[id]; ?>">
    </td>
  </tr>

  <?php             
  ##### query target_language
  $query = "SELECT * FROM poll_questions
            WHERE id_poll=$poll[id] AND id_language=$target_lang LIMIT 0,1";
  $quest = sqlROW ($DB['poll'], $query);

  if (!is_array ($quest)) {
      // if not user query default langauge
      $query = "SELECT * FROM poll_questions
                  WHERE id_poll=$poll[id] AND id_language=$SYS[default_lang] LIMIT 0,1";
      $quest = sqlROW ($DB['poll'], $query);
  }
  ?>
  <tr <?php if ($color) { $color=0; ?>BGCOLOR="#D0D0B0"<?php } else { $color=1; ?>BGCOLOR="#D0D0D0"<?php } ?>>
    <td><?php putGS("title"); ?></td><td><?php print $quest[title]; ?></td>
  </tr>
  <tr <?php if ($color) { $color=0; ?>BGCOLOR="#D0D0B0"<?php } else { $color=1; ?>BGCOLOR="#D0D0D0"<?php } ?>>
    <td><?php putGS("question"); ?></td><td><?php print $quest[question]; ?></td>
  </tr>
  <tr <?php if ($color) { $color=0; ?>BGCOLOR="#D0D0B0"<?php } else { $color=1; ?>BGCOLOR="#D0D0D0"<?php } ?>>
    <td colspan="2">&nbsp;</td>
  </tr>
  <?php
  ##### overall languages #########################

  ##### get answer-terms in given/default langauge
  $query = "SELECT * FROM poll_answers
            WHERE id_poll=$poll[id] AND id_language=$quest[id_language] ORDER BY nr_answer";
  $answers = sqlQuery($DB['poll'], $query);

  while ($ans = mysql_fetch_array($answers)) {
      print "<tr ";
      if ($color) { $color=0; ?>BGCOLOR="#D0D0B0"<?php } else { $color=1; ?>BGCOLOR="#D0D0D0"<?php }
      print "><td>".getGS("answer")." $ans[nr_answer])</td><td>$ans[answer]</td></tr>";
  }

  ##### sum of votes
  $query = "SELECT SUM(votes) AS allsum FROM poll_answers
            WHERE id_poll=$poll[id]";
  $sum = sqlRow ($DB['poll'], $query);

  ##### sum of votes depending to nr_answer, independing from id_language
  $query = "SELECT nr_answer, SUM(votes) as rowsum FROM poll_answers
           WHERE id_poll=$poll[id] GROUP BY nr_answer ORDER BY nr_answer";
  $votes = sqlQuery($DB['poll'], $query);
  ?>
  <tr <?php if ($color) { $color=0; ?>BGCOLOR="#D0D0B0"<?php } else { $color=1; ?>BGCOLOR="#D0D0D0"<?php } ?>><td colspan="2">&nbsp;</td></tr>
  <tr <?php if ($color) { $color=0; ?>BGCOLOR="#D0D0B0"<?php } else { $color=1; ?>BGCOLOR="#D0D0D0"<?php } ?>>
    <td colspan="2">Votes Overall Languages (<?php echo $sum[allsum]; ?> Votes)</td>
  </tr>
  <?php
  mysql_data_seek ($answers, 0);
  while ($polld = mysql_fetch_array($answers)) {
      $vote = mysql_fetch_array ($votes);
      if ($vote[rowsum]) $prozent = round (100/$sum[allsum]*$vote[rowsum],1);
      else $prozent = 0;
    ?>
    <tr <?php
    if ($color) { $color=0; ?>BGCOLOR="#D0D0B0"<?php }
    else { $color=1; ?>BGCOLOR="#D0D0D0"<?php }
      ?>>
      <td><?php echo "$polld[nr_answer]) Votes:  $vote[rowsum] ($prozent %)"; ?></td>
      <td>
      <?php
      for ($n=0; $n<=$prozent; $n++) echo "I";
      ?></td>
    </tr>
    <?php
  }
  ##### end overall languages #########################
  /*
  ##### Diff by languages #############################
  $langq = "SELECT pq.id_language, cl.Name
  FROM poll_questions AS pq, campsite.Languages AS cl
  WHERE id_poll=$poll[id] AND pq.id_language=cl.Id
  ORDER BY id_language";

  $langr = sqlQuery($DB[poll], $langq);
  while ($lang = mysql_fetch_array ($langr))
  {
  ##### sum of votes dep. to language
  $query = "SELECT SUM(votes) AS allsum FROM poll_answers
  WHERE id_poll=$poll[id] AND id_language=$lang[id_language]";
  $sumlang = sqlRow ($DB['poll'], $query);

  ##### sum of votes dep. to nr_answer, depending from id_language
  $query = "SELECT nr_answer, SUM(votes) as rowsum FROM poll_answers
  WHERE id_poll=$poll[id] AND id_language=$lang[id_language]
  GROUP BY nr_answer ORDER BY nr_answer";
  $votes = sqlQuery($DB['poll'], $query);

  ?>
  <tr <?php if ($color) { $color=0; ?>BGCOLOR="#D0D0B0"<?php } else { $color=1; ?>BGCOLOR="#D0D0D0"<?php } ?>><td colspan="2">&nbsp;</td></tr>
  <tr <?php if ($color) { $color=0; ?>BGCOLOR="#D0D0B0"<?php } else { $color=1; ?>BGCOLOR="#D0D0D0"<?php } ?>>
  <td colspan="2">Votes on <?php echo "$lang[Name]: $sumlang[allsum] (".(100/$sum[allsum]*$sumlang[allsum])."% of Overall)"; ?></td>
  </tr>
  <?php
  mysql_data_seek ($answers, 0);
  while ($polld = mysql_fetch_array ($answers))
  {
  $vote = mysql_fetch_array ($votes);
  if ($vote[rowsum]) $prozent = round (100/$sumlang[allsum]*$vote[rowsum],1);
  else $prozent = 0;
  ?>
  <tr <?php
  if ($color) { $color=0; ?>BGCOLOR="#D0D0B0"<?php }
  else { $color=1; ?>BGCOLOR="#D0D0D0"<?php }
  ?>>
  <td><?php echo "$polld[nr_answer]) Votes:  $vote[rowsum] ($prozent %)"; ?></td>
  <td>
  <?php
  for ($n=0; $n<=$prozent; $n++) echo "I";
  ?></td>
  </tr>
  <?php
  }
  ##### end diff by languages #########################
  }
  */
  ?>

  </table>
  </form>
  <?php
}
?>
