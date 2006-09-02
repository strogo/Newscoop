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
                WHERE NrPoll     = {$poll['Number']} AND 
                      IdLanguage = {$poll['old_DefaultIdLanguage']}";

    if ($poll['Number']) {
        //update existing Poll
        $query[] = "UPDATE poll_main 
                    SET    DefaultIdLanguage    = '{$poll['DefaultIdLanguage']}',  
                           DateBegin            = '{$poll['DateBegin']}', 
                           DateExpire           = '{$poll['DateExpire']}', 
                           NrOfAnswers          = '{$poll['NrOfAnswers']}',
                           ShowAfterExpiration  = '{$poll['ShowAfterExpiration']}'
                    WHERE  Number = '{$poll['Number']}' 
                    LIMIT 1";
        $query[] = "UPDATE poll_questions 
                    SET    Title        = '{$poll['Title']}', 
                           Question     = '{$poll['Question']}', 
                           IdLanguage   = '{$poll['DefaultIdLanguage']}'
                    WHERE  NrPoll       = '{$poll['Number']}' AND 
                           IdLanguage   = '{$poll['old_DefaultIdLanguage']}' 
                    LIMIT 1";
        sqlQuery($DB['modules'], $query);
    } else {
        //insert new Poll
        $query = "INSERT 
                  INTO poll_main
                  SET  DefaultIdLanguage    = '{$poll['DefaultIdLanguage']}', 
                       DateBegin            = '{$poll['DateBegin']}', 
                       DateExpire           = '{$poll['DateExpire']}', 
                       NrOfAnswers          = '{$poll['NrOfAnswers']}', 
                       ShowAfterExpiration  = '{$poll['ShowAfterExpiration']}'";
        sqlQuery($DB['modules'], $query);
        $poll['Number'] = lastInsertID();
        $query = "INSERT 
                  INTO poll_questions 
                  SET  NrPoll       = '{$poll['Number']}',
                       IdLanguage   = '{$poll['DefaultIdLanguage']}',
                       Title        = '{$poll['Title']}', 
                       Question     = '{$poll['Question']}'";            
        sqlQuery($DB['modules'], $query);
    }
    unset($query);
    foreach ($poll['Answer'] as $number => $val) {
        $query[] = "INSERT 
                    INTO poll_answers 
                    (NrPoll, IdLanguage, NrAnswer, Answer, NrOfVotes)
                    VALUES 
                    ({$poll['Number']}, {$poll['DefaultIdLanguage']}, $number, '$val', '{$poll['NrOfVotes'][$number]}')";
    }
    sqlQuery($DB['modules'], $query);

    print('<META HTTP-EQUIV="Refresh" CONTENT="0; URL=index.php">');
}
?>
