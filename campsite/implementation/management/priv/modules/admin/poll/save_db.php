<?php
require_once $Campsite['HTML_DIR']."/$ADMIN_DIR/modules/start.ini.php";

$access = startModAdmin ("ManagePoll", "Poll");
if ($access) {

    if (file_exists(dirname(__FILE__)."/locals.{$_REQUEST['TOL_Language']}.php")) {
        require_once "locals.{$_REQUEST['TOL_Language']}.php";
    } elseif(file_exists(dirname(__FILE__)."/locals.{$_REQUEST['TOL_Language']}.php"))  {
        require_once 'locals.en.php';
    }

    $poll = $_REQUEST['poll'];
    $act  = $_REQUEST['act'];

    foreach ($poll[answer] as $lost=>$val) {
        // all answers filled?
        if (!$val) {
            $poll[error] = "<font color=red><b>".getGS("empty fields")."</b></font>";
            die('<META HTTP-EQUIV="Refresh" CONTENT="0; URL=edit_answers.php?'.arrToParamStr(array('poll' => $poll)).'">');
        }
    }
    reset ($poll[answer]);

    $query[] = "DELETE FROM poll_answers WHERE id_poll=$poll[id] AND id_language=$SYS[default_lang]";

    if ($poll[id]) {
        //update existing Poll
        $query[] = "UPDATE poll_main SET answers='$poll[answers]', dfrom='$poll[dfrom]', dto='$poll[dto]', beyond='$poll[beyond]' WHERE id=$poll[id] LIMIT 1";
        $query[] = "UPDATE poll_questions SET title='$poll[title]', question='$poll[question]' WHERE id_poll=$poll[id] AND id_language=$SYS[default_lang] LIMIT 1";
        sqlQuery($DB['poll'], $query);
    } else {
        //insert new Poll
        $query = "INSERT INTO poll_main (default_lang, dfrom, dto, answers, beyond)
                VALUES ($SYS[default_lang], '$poll[dfrom]', '$poll[dto]', $poll[answers], $poll[beyond])";
        sqlQuery($DB['poll'], $query);
        $poll[id] = lastInsertID ();

        $query = "INSERT INTO poll_questions (id_poll, id_language, title, question)
                VALUES ($poll[id], $SYS[default_lang], '$poll[title]', '$poll[question]')";
        sqlQuery($DB['poll'], $query);
    }
    unset ($query);

    foreach ($poll[answer] as $number=>$val) {
        $val = str_replace ("\"", "&quot;", $val);
        $query[] = "INSERT INTO poll_answers (id_poll, id_language, nr_answer, answer, votes) VALUES ($poll[id], $SYS[default_lang], $number, '$val', '{$poll[votes][$number]}')";
    }

    sqlQuery($DB['poll'], $query);

    print ('<META HTTP-EQUIV="Refresh" CONTENT="0; URL=index.php">');
}
?>
