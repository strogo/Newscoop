<?php
require_once $Campsite['HTML_DIR']."/$ADMIN_DIR/modules/start.ini.php";

$access = startModAdmin ("ManagePoll", "Poll", 'Translate poll');
if ($access) {

    if (file_exists(dirname(__FILE__)."/locals.{$_REQUEST['TOL_Language']}.php")) {
        require_once "locals.{$_REQUEST['TOL_Language']}.php";
    } elseif(file_exists(dirname(__FILE__)."/locals.{$_REQUEST['TOL_Language']}.php"))  {
        require_once 'locals.en.php';
    }

    $poll         = $_REQUEST['poll'];
    $votes        = $_REQUEST['votes'];
    $title        = $_REQUEST['title'];
    $question     = $_REQUEST['question'];
    $answer       = $_REQUEST['answer'];
    $save         = $_REQUEST['save'];
    $source_lang  = $_REQUEST['source_lang'];
    $target_lang  = $_REQUEST['target_lang'];

    if ($save) {
        $query[] = "DELETE FROM poll_questions WHERE id_poll=$poll[id] AND id_language=$target_lang";
        $query[] = "INSERT INTO poll_questions (id_poll, id_language, title, question)
                VALUES ($poll[id], $target_lang, '$title', '$question')";
        $query[] = "DELETE FROM poll_answers WHERE id_poll=$poll[id] AND id_language=$target_lang";
        foreach ($answer as $num=>$answer)
        $query[] = "INSERT INTO poll_answers (id_poll, id_language, nr_answer, answer, votes)
                  VALUES ($poll[id], $target_lang, $num, '$answer', '$votes[$num]')";

        sqlQuery($DB['poll'], $query);
    }

  ?>
  <form name='language' action='translate.php'>
  <table border="0" width="100%" BGCOLOR="#C0D0FF">
  <tr>
    <th colspan="3" align="left"><?php putGS("translate"); ?></th>
    <td colspan="2" align="right">
      <?php putGS("target lang"); ?>: <?php langmenu ("target_lang"); ?>
      <input type="hidden" name="poll[id]" value="<?php print $poll[id]; ?>">
      <input type="hidden" name="source_lang" value="<?php print $source_lang; ?>">
    </td>
  </tr>
  <?php
  $query = "SELECT title, question FROM poll_questions WHERE id_poll=$poll[id] AND id_language=$source_lang";
  $source_q = sqlRow ($DB['poll'], $query);
  $existsq = "SELECT title, question FROM poll_questions WHERE id_poll=$poll[id] AND id_language=$target_lang";
  $target_q = sqlRow ($DB['poll'], $existsq);
  ?>
  <tr><td colspan="5">&nbsp;<td></tr>
  <tr>
    <td colspan="5"><?php putGS("title");  print " ".$source_q[title]; ?><br>
    <input type="text" name="title" value="<?php print htmlspecialchars ($target_q[title]); ?>" size="50"></td>
  </tr>

  <tr>
    <td colspan="5"><?php putGS("question"); print " ".$source_q[question]; ?><br>
    <input type="text" name="question" value="<?php print htmlspecialchars ($target_q[question]); ?>" size="80"></td>
  </tr>
  <tr><td colspan="5">&nbsp;<td></tr>
  <?php
  $query = "SELECT nr_answer, answer, votes FROM poll_answers WHERE id_poll=$poll[id] AND id_language=$source_lang ORDER BY nr_answer";
  $source = sqlQuery($DB['poll'], $query);
  $existsq = "SELECT answer, votes FROM poll_answers WHERE id_poll=$poll[id] AND id_language=$target_lang ORDER BY nr_answer";
  $target = sqlQuery($DB['poll'], $existsq);

  while ($source_a = mysql_fetch_array($source)) {
      $target_a = @mysql_fetch_array($target);
    ?>
    <tr>
      <td colspan="5"><?php putGS("answer"); print " $source_a[nr_answer]: $source_a[answer]"; ?><br>
      <input type="text" name="answer[<?php print $source_a[nr_answer]; ?>]" value="<?php print htmlspecialchars ($target_a[answer]); ?>" size="80"></td>
      <input type="hidden" name="votes[<?php print $source_a[nr_answer]; ?>]" value="<?php print $target_a[votes]; ?>">
    </tr>
    <?php
  }

  ?>
  <tr><td colspan="5">&nbsp;<td></tr>
  <tr><td colspan="2"><input type="button" value="<?php putGS("ready"); ?>" onClick="location.href='index.php'"></td>
      <td colspan="3" align="right"><input type="submit" name="save" value="<?php putGS("save translation"); ?>"><td>
  </tr>

  </table>
  </form>

  <?php
}
?>
