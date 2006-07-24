<?php
require_once $Campsite['HTML_DIR']."/$ADMIN_DIR/modules/start.ini.php";

$access = startModAdmin ("ManagePoll", "Poll", 'Edit answers');
if ($access) {

    if (file_exists(dirname(__FILE__)."/locals.{$_REQUEST['TOL_Language']}.php")) {
        require_once "locals.{$_REQUEST['TOL_Language']}.php";
    } elseif(file_exists(dirname(__FILE__)."/locals.{$_REQUEST['TOL_Language']}.php"))  {
        require_once 'locals.en.php';
    }

    $poll = $_REQUEST['poll'];
    $act  = $_REQUEST['act'];

    $poll[dfrom] = $poll[dfrom][year]."-".$poll[dfrom][month]."-".$poll[dfrom][day];
    $poll[dto] = $poll[dto][year]."-".$poll[dto][month]."-".$poll[dto][day];

    if (!$poll[title] || !$poll[question] || !$poll[answers] || !$poll[dfrom] || !$poll[dto]) {
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
        $query = "SELECT answer, nr_answer, votes FROM poll_answers WHERE id_poll=$poll[id] AND id_language=$SYS[default_lang] ORDER by nr_answer";
        if ($votes = sqlQuery($DB['poll'], $query)) {
            // get answer data if exist
            while ($row = mysql_fetch_array ($votes)) {
                $poll[vote][$row[nr_answer]] = $row[votes];
            }
            mysql_data_seek ($votes, 0);

            if (!is_array ($poll[answer])) {
                // only answers not edited yet
                while ($row = mysql_fetch_array ($votes)) {
                    $poll[answer][$row[nr_answer]] = $row[answer];
                }
            }
        }

        for ($i=1; $i<=$poll[answers]; $i++) {
            echo "<tr><td>"; putGS("answer"); echo " $i:</td><td><input type='text' name='poll[answer][$i]' value=\"{$poll[answer][$i]}\" size='80' maxlength='255'>
            <input type='hidden' name='poll[votes][$i]' value='{$poll[vote][$i]}'></td></tr>\n";
        }
    ?>
    <tr><td><?php print $poll[error]; ?></td><td align="right"><input type="submit" name="poll[ready]" value="<?php putGS("save poll"); ?>"></td></tr>
    <?php
    foreach ($poll as $key=>$val) {
        if ($key<>"vote" && $key<>"answer")
        print "<input type='hidden' name='poll[$key]' value=\"".htmlspecialchars (stripslashes ($val))."\">\n";
    }
    ?>
    </table>
    </form>
    <?php
    }
}
?>

