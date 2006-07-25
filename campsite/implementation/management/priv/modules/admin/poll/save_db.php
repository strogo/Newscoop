<?php
require_once $Campsite['HTML_DIR']."/$ADMIN_DIR/modules/start.ini.php";
require_once $Campsite['HTML_DIR']."/classes/Input.php";

$access = startModAdmin ("ManagePoll", "Poll");
if ($access) {

    if (file_exists(dirname(__FILE__)."/locals.{$_REQUEST['TOL_Language']}.php")) {
        require_once "locals.{$_REQUEST['TOL_Language']}.php";
    } elseif(file_exists(dirname(__FILE__)."/locals.{$_REQUEST['TOL_Language']}.php"))  {
        require_once 'locals.en.php';
    }

    $poll = Input::Get('poll', 'array', array());
    $act  = Input::Get('act');

    foreach ($poll['Answer'] as $lost => $val) {
        // all NrOfAnswers filled?
        if (!$val) {
            $poll['error'] = "<font color=red><b>".getGS("empty fields")."</b></font>";
            die('<META HTTP-EQUIV="Refresh" CONTENT="0; URL=edit_answers.php?'.arrToParamStr(array('poll' => $poll)).'">');
        }
    }
    reset($poll['Answer']);

    $query[] = "DELETE 
                FROM  poll_answers 
                WHERE IdPoll     = {$poll['Id']} AND 
                      IdLanguage = $defaultIdLanguage";

    if ($poll['Id']) {
        //update existing Poll
        $query[] = "UPDATE poll_main 
                    SET    NrOfAnswers = '{$poll['NrOfAnswers']}', 
                           DateBegin   = '{$poll['DateBegin']}', 
                           DateExpire  = '{$poll['DateExpire']}', 
                           ShowAfterExpiration = '{$poll['ShowAfterExpiration']}'
                    WHERE  Id = '{$poll['Id']}' 
                    LIMIT 1";
        $query[] = "UPDATE poll_questions 
                    SET    title    = '{$poll['Title']}', 
                           question = '{$poll['Question']}' 
                    WHERE  IdPoll     = {$poll['Id']} AND 
                           IdLanguage = $defaultIdLanguage 
                    LIMIT 1";
        sqlQuery($DB['modules'], $query);
    } else {
        //insert new Poll
        $query = "INSERT 
                  INTO poll_main 
                  (defaultIdLanguage, DateBegin, DateExpire, NrOfAnswers, ShowAfterExpiration)
                  VALUES 
                  ($defaultIdLanguage, '{$poll['DateBegin']}', '{$poll['DateExpire']}', {$poll['NrOfAnswers']}, {$poll['ShowAfterExpiration']})";
        sqlQuery($DB['modules'], $query);
        $poll['Id'] = lastInsertID();

        $query = "INSERT 
                  INTO poll_questions 
                  (IdPoll, IdLanguage, Title, Question)
                  VALUES 
                  ({$poll['Id']}, $defaultIdLanguage, '{$poll['Title']}', '{$poll['Question']}')";
        sqlQuery($DB['modules'], $query);
    }
    unset ($query);

    foreach ($poll['Answer'] as $number => $val) {
        $val = str_replace ("\"", "&quot;", $val);
        $query[] = "INSERT 
                    INTO poll_answers 
                    (IdPoll, IdLanguage, NrAnswer, Answer, NrOfVotes)
                    VALUES 
                    ({$poll['Id']}, $defaultIdLanguage, $number, '$val', '{$poll['NrOfVotes'][$number]}')";
    }

    sqlQuery($DB['modules'], $query);

    print ('<META HTTP-EQUIV="Refresh" CONTENT="0; URL=index.php">');
}
?>
