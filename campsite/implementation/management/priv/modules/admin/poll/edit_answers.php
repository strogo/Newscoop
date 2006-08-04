<?php
require_once $Campsite['HTML_DIR']."/$ADMIN_DIR/modules/start.ini.php";
require_once $Campsite['HTML_DIR']."/classes/Input.php";

$access = startModAdmin ("ManagePoll", "Poll", 'Edit answers');
if ($access) {

    if (file_exists(dirname(__FILE__)."/locals.{$_REQUEST['TOL_Language']}.php")) {
        require_once "locals.{$_REQUEST['TOL_Language']}.php";
    } elseif(file_exists(dirname(__FILE__)."/locals.{$_REQUEST['TOL_Language']}.php"))  {
        require_once 'locals.en.php';
    }
    $poll = Input::Get('poll', 'array', array());
    $act  = Input::Get('act');

    $poll[DateBegin]  = $poll[DateBegin][year]."-".$poll[DateBegin][month]."-".$poll[DateBegin][day];
    $poll[DateExpire] = $poll[DateExpire][year].  "-".$poll[DateExpire][month].  "-".$poll[DateExpire][day];

    if (!$poll['Title'] || !$poll['Question'] || !$poll['NrOfAnswers'] || !$poll['DateBegin'] || !$poll['DateExpire']) {
        // empty maindata-fields
        $poll[error] = '<b><font color=red>'.getGS("empty fields").'</font></b>';
        die('<META HTTP-EQUIV="Refresh" CONTENT="0; URL=edit_maindata.php?'.arrToParamStr(array('poll' => $poll)).'">');
    }  else {
        // maindata complete, print answer-fields
        ?>
        <form name="poll_answers" action="save_db.php" method="post">
        <table border="0" width="100%" BGCOLOR="#C0D0FF">
    
        <tr><td colspan="2"><b><?php putGS("edit answers"); ?></b><br><br></td></tr>
        <?php
        $query = "SELECT Answer,
                         NrAnswer,
                         NrOfVotes 
                  FROM   poll_answers 
                  WHERE  NrPoll     = '{$poll['Number']}' AND 
                         IdLanguage = '{$poll['DefaultIdLanguage']}' 
                  ORDER by NrAnswer";
        
        if ($votes = sqlQuery($DB['modules'], $query)) {
            // get answer data if exist
            while ($row = mysql_fetch_array($votes)) {
                $poll['vote'][$row['NrAnswer']] = $row['NrOfVotes'];
            }
            @mysql_data_seek($votes, 0);

            if (!is_array($poll['Answer'])) {
                // only answers not edited yet
                while ($row = mysql_fetch_array($votes)) {
                    $poll['Answer'][$row['NrAnswer']] = $row['Answer'];
                }
            }
        }

        for ($i=1; $i<=$poll[NrOfAnswers]; $i++) {
            echo "<tr><td>"; putGS("answer"); echo " $i:</td><td><input type='text' name='poll[Answer][$i]' value=\"{$poll['Answer'][$i]}\" size='80' maxlength='255'>
            <input type='hidden' name='poll[NrOfVotes][$i]' value='{$poll['vote'][$i]}'></td></tr>\n";
        }
    ?>
    <tr><td><?php print $poll[error]; ?></td><td align="right"><input type="submit" name="poll[ready]" value="<?php putGS("save poll"); ?>"></td></tr>
    <?php
    foreach ($poll as $key=>$val) {
        if ($key !== "vote" && $key !== "Answer")
        print "<input type='hidden' name='poll[$key]' value=\"".htmlspecialchars (stripslashes ($val))."\">\n";
    }
    ?>
    </table>
    </form>
    <?php
    }
}
?>

