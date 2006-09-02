<?php
class poll2smarty extends poll
{
    function poll2smarty($camp_params, $statement_params, $url_params, &$Smarty)
    {
        $this->poll($camp_params, $statement_params, $url_params);
        $this->assignSmartyFunctions(&$Smarty);  
    }
    
    function assignSmartyFunctions(&$Smarty)
    {
        $Smarty->register_block('Poll_IsDefined',        array(&$this, 'block_PollIsDefined'));
        $Smarty->register_block('Poll_IsVotable',        array(&$this, 'block_PollIsVotable'));
        $Smarty->register_block('Poll_IsNotVotable',     array(&$this, 'block_PollIsNotVotable'));
        $Smarty->register_block('Poll_ListQuestion',     array(&$this, 'block_getQuestion'));
        $Smarty->register_block('Poll_ListAnswer',       array(&$this, 'block_getAnswer'));
        
        $Smarty->register_function('Poll_Print',         array(&$this, 'PollPrint'));        
    } 
    
    function PollPrint($params)
    {
        extract($params);
        
        switch (strtolower($subject)) {
            case 'number':
                print($this->mainData['NrPoll']);
            break;
            
            case 'question':
                print($this->getQuestion());
            break; 
            
            case 'sum':
                $sum = $this->getSum();
                print($sum['allsum']);
            break;   
        };   
    } 

    function block_PollIsDefined($params, $content, &$Smarty, &$repeat)
    {         
        if ($repeat) {
            return;   
        }
        if ($this->getPoll()) { 
            return $content;  
        }       
    } 
    
    function block_PollIsVotable($params, $content, &$Smarty, &$repeat) 
    { 
        if ($repeat) {
            return;   
        }
        if (!$this->showResult && $this->userCanVote()) { 
            return $content;    
        }  
    } 
    
    function block_PollIsNotVotable($params, $content, &$Smarty, &$repeat) 
    { 
        if ($repeat) {
            return;   
        }
        if ($this->showResult || !$this->userCanVote()) { 
            return $content;    
        } 
    }
    
    function block_getQuestion($params, $content, &$Smarty, &$repeat)
    {

        if ($repeat) {
            return;   
        }

        foreach ($this->getAnswers() as $answer) {
            $search     = array('##NrPoll##', '##NrAnswer##', '##Answer##');
            $replace    = array($this->mainData['NrPoll'], $answer['NrAnswer'], $answer['Answer']);
            $str .= str_replace($search, $replace, $content);    
        }
        return $str;   
    }
    
    function block_getAnswer($params, $content, &$Smarty, &$repeat)
    {

        if ($repeat) {
            return;   
        }

        foreach ($this->getResult() as $answer) {
            $search     = array('##NrPoll##', '##NrAnswer##', '##Answer##', '##Percentage##');
            $replace    = array($this->mainData['NrPoll'], $answer['NrAnswer'], $answer['Answer'], $answer['Percentage']);
            $str .= str_replace($search, $replace, $content);    
        }
        return $str;   
    }
}

class poll
{
    function poll($camp_params, $statement_params, $url_params)
    { 
        $this->IdLanguage    = $camp_params['IdLanguage'];
        $this->IdPublication = $camp_params['IdPublication'];
        $this->NrIssue       = $camp_params['NrIssue'];
        $this->NrSection     = $camp_params['NrSection'];
        $this->NrArticle     = $camp_params['NrArticle'];
        
        $this->type          = $statement_params['assign'];

        $this->showResult    = $url_params['showResult'];
        $this->NrPoll        = $url_params['NrPoll']; 
        $this->votedata      = $url_params['result']; 
        
        $this->initPoll(); 
    }
   
    function getQuestion()
    {
        return $this->mainData['def_question'];    
    }
    
    function getPoll()
    {
        if ($this->mainData['NrPoll']) {
            return true;
        }   
        return false;
    }
    
    function initPoll()
    {
        global $DB;
        
        $select = "SELECT m.*, 
                          q.*, 
                          UNIX_TIMESTAMP(m.DateExpire) AS DateExpire";
        $where  = "AND m.Number = q.NrPoll  AND q.IdLanguage  = {$this->IdLanguage} AND ((m.DateBegin <= CURDATE() AND m.DateExpire >= CURDATE()) OR (m.DateBegin <= CURDATE() AND m.ShowAfterExpiration = 1))";
        $order  = "ORDER BY m.DateExpire DESC, 
                            m.Number DESC";

        switch ($this->type) {
            case "number":
            
            if ($this->NrPoll) {
                $w = "m.Number=$this->NrPoll";
            } else {
                $w = "1";
            }

            $q = "$select
                      FROM poll_main AS m, poll_questions as q
                      WHERE $w
                      $where
                      $order
                      LIMIT 0,1";
            $res = sqlQuery($DB['modules'], $q);
            break;

            case "article":
            $q = "$select
                      FROM poll_main AS m, poll_questions as q, poll_article AS a
                      WHERE a.NrArticle='$this->NrArticle' AND a.NrPoll=m.Number
                      $where
                      $order";
            $res = sqlQuery($DB['modules'], $q);
            break;

            case "section":
            $q = "$select
                      FROM poll_main AS m, poll_questions as q, poll_section AS s
                      WHERE s.NrSection='$this->NrSection' AND s.NrPoll=m.Number
                      $where
                      $order"; 
            $res = sqlQuery($DB['modules'], $q);
            break;

            case "issue":
            $q = "$select
                      FROM poll_main AS m, poll_questions as q, poll_issue AS i
                      WHERE i.NrIssue='$this->NrIssue' AND i.NrPoll=m.Number
                      $where
                      $order";
            $res = sqlQuery($DB['modules'], $q);
            break;

            case "publication":
            $q = "$select
                      FROM poll_main AS m, poll_questions as q, poll_publication AS p
                      WHERE p.IdPublication='$this->IdPublication' AND p.NrPoll=p.Number
                      $where
                      $order";
            $res = sqlQuery($DB['modules'], $q);
            break;
            
            default:
                return false;
            break;
        }

        if ($this->mainData = mysql_fetch_array($res, MYSQL_ASSOC)) { 
            $this->saveUserVote();

            return true;
        }
        return false;
    }

    function getAnswers()
    {
        global $DB;
        $query = "SELECT * FROM poll_answers
                      WHERE NrPoll='{$this->mainData['NrPoll']}' AND IdLanguage='{$this->mainData['IdLanguage']}' ORDER BY NrAnswer";
        $res   = sqlQuery($DB['modules'], $query);

        while ($row = mysql_fetch_array($res, MYSQL_ASSOC)) {
            $answers[] = $row;
        }

        return $answers;
    }

    function userCanVote()
    {
        if ($_SESSION['poll_vote'][$this->mainData['NrPoll']] || $this->mainData['DateExpire'] < time()) {
            return false;
        }

        return true;
    }

    function saveUserVote()
    {
        global $DB;
        if ($this->votedata['answer'][$this->mainData['NrPoll']] && $this->userCanVote()) {
            ## user can only vote actual poll
            $_SESSION['poll_vote'][$this->mainData['NrPoll']] = true;

            $query = "UPDATE poll_answers 
                      SET NrOfVotes = (NrOfVotes+1)
                      WHERE NrAnswer   = '{$this->votedata['answer'][$this->mainData['NrPoll']]}' AND 
                            NrPoll     = '{$this->mainData['NrPoll']}' AND 
                            IdLanguage = '{$this->mainData['IdLanguage']}'";

            sqlQuery($DB['modules'], $query);

            return true;
        }
        return false;
    }

    function getVotes()
    {
        global $DB;
        // sum of NrOfVotes depending to NrAnswer, independing from IdLanguage
        $query  = "SELECT NrAnswer, 
                          SUM(NrOfVotes) as rowsum 
                   FROM   poll_answers
                   WHERE NrPoll = '{$this->mainData['NrPoll']}' 
                   GROUP BY NrAnswer ORDER BY NrAnswer";
        $result = sqlQuery($DB['modules'], $query);

        while($row = mysql_fetch_array($result)) {
            $NrOfVotes[] = $row;
        }

        return $NrOfVotes;
    }

    function getSum()
    {
        global $DB;
        // sum of NrOfVotes
        $query  = "SELECT SUM(NrOfVotes) AS allsum 
                   FROM   poll_answers
                   WHERE  NrPoll = '{$this->mainData['NrPoll']}'";        
        $result = sqlQuery($DB['modules'], $query);

        $sum = mysql_fetch_array($result);

        return $sum;
    }

    function getResult()
    {
        $answers   = $this->getAnswers();
        $NrOfVotes = $this->getVotes();
        $sum       = $this->getSum();

        foreach ($answers as $k=>$v) {
            $vote = current($NrOfVotes);

            if ($vote[rowsum]) {
                $answers[$k]['Percentage'] = round(100 / $sum['allsum'] * $vote['rowsum'], 1);
            } else {
                $answers[$k]['Percentage'] = 0;
            }
            next($NrOfVotes);
        }
        #print_r($answers);
        return $answers;
    }
}
?>