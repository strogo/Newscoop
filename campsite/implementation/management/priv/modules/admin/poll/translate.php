<?php
require_once $Campsite['HTML_DIR']."/$ADMIN_DIR/modules/start.ini.php";
require_once $Campsite['HTML_DIR']."/classes/Input.php";

$access = startModAdmin ("ManagePoll", "Poll", 'Translate poll');
if ($access) {

    if (file_exists(dirname(__FILE__)."/locals.{$_REQUEST['TOL_Language']}.php")) {
        require_once "locals.{$_REQUEST['TOL_Language']}.php";
    } elseif(file_exists(dirname(__FILE__)."/locals.{$_REQUEST['TOL_Language']}.php"))  {
        require_once 'locals.en.php';
    }

    $poll         = Input::Get('poll', 'array');
    $NrOfVotes    = Input::Get('NrOfVotes');
    $Title        = Input::Get('Title');
    $Question     = Input::Get('Question');
    $Answers      = Input::Get('Answer', 'array', array());
    $save         = Input::Get('save');
    $source_lang  = Input::Get('source_lang');
    $target_lang  = Input::Get('target_lang');

    if ($save) {
        $query[] = "DELETE
                    FROM    mod_poll_questions 
                    WHERE   NrPoll     = {$poll['Number']} AND 
                            IdLanguage = $target_lang";
        $query[] = "INSERT
                    INTO    mod_poll_questions 
                    (NrPoll, IdLanguage, Title, Question)
                    VALUES 
                    ({$poll['Number']}, $target_lang, '$Title', '$Question')";
        
        $query[] = "DELETE
                    FROM    mod_poll_answers 
                    WHERE   NrPoll     = {$poll['Number']} AND 
                            IdLanguage = $target_lang";

        foreach ($Answers as $NrAnswer => $Answer) {
            $query[] = "INSERT
                        INTO mod_poll_answers 
                        (NrPoll, IdLanguage, NrAnswer, Answer, NrOfVotes)
                        VALUES 
                        ({$poll['Number']}, $target_lang, $NrAnswer, '$Answer', '$NrOfVotes[$num]')";
        }

        sqlQuery($DB['modules'], $query);
    }

  ?>
  <form name='language' action='translate.php'>
  <table border="0" width="100%" BGCOLOR="#C0D0FF">
  <tr>
    <th colspan="3" align="left"><?php putGS("translate"); ?></th>
    <td colspan="2" align="right">
      <?php putGS("target lang"); ?>: <?php langmenu ("target_lang"); ?>
      <input type="hidden" name="poll[Number]" value="<?php print $poll['Number']; ?>">
      <input type="hidden" name="source_lang" value="<?php print $source_lang; ?>">
    </td>
  </tr>
  <?php
  $query = "SELECT  Title, Question 
            FROM    mod_poll_questions 
            WHERE   NrPoll      = {$poll['Number']} AND 
                    IdLanguage  = $source_lang";
  $source_q = sqlRow($DB['modules'], $query);
  $existsq  = "SELECT   Title, Question 
               FROM     mod_poll_questions 
               WHERE    NrPoll = {$poll['Number']} AND 
                        IdLanguage = $target_lang";
  $target_q = sqlRow($DB['modules'], $existsq);
  ?>
  <tr><td colspan="5">&nbsp;<td></tr>
  <tr>
    <td colspan="5"><?php putGS("title");  print " ".$source_q['Title']; ?><br>
    <input type="text" name="Title" value="<?php print htmlspecialchars($target_q['Title']); ?>" size="50"></td>
  </tr>

  <tr>
    <td colspan="5"><?php putGS("question"); print " ".$source_q['Question']; ?><br>
    <input type="text" name="Question" value="<?php print htmlspecialchars ($target_q['Question']); ?>" size="80"></td>
  </tr>
  <tr><td colspan="5">&nbsp;<td></tr>
  <?php
  $query = "SELECT  NrAnswer, Answer, NrOfVotes 
            FROM    mod_poll_answers 
            WHERE   NrPoll      = {$poll['Number']} AND 
                    IdLanguage  = $source_lang 
            ORDER BY NrAnswer";
  $source = sqlQuery($DB['modules'], $query);
  $existsq = "SELECT    Answer, NrOfVotes 
              FROM      mod_poll_answers 
              WHERE     NrPoll      = {$poll['Number']} AND 
                        IdLanguage  = $target_lang 
              ORDER BY NrAnswer";
  $target = sqlQuery($DB['modules'], $existsq);

  while ($source_a = mysql_fetch_array($source)) {
      $target_a = @mysql_fetch_array($target);
    ?>
    <tr>
      <td colspan="5"><?php putGS("answer"); print " {$source_a['NrAnswer']}: {$source_a['Answer']}"; ?><br>
      <input type="text"   name="Answer[<?php print $source_a['NrAnswer']; ?>]" value="<?php print htmlspecialchars($target_a['Answer']); ?>" size="80"></td>
      <input type="hidden" name="NrOfVotes[<?php print $source_a['NrAnswer']; ?>]"  value="<?php print $target_a['NrOfVotes']; ?>">
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
