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
        $Smarty->register_block('PollIsDefined',        array($this, 'block_PollIsDefined'));
        $Smarty->register_block('PollIsVotable',        array($this, 'block_PollIsVotable'));
        $Smarty->register_block('PollIsNotVotable',     array($this, 'block_PollIsNotVotable'));
        $Smarty->register_block('PollListQuestion',     array($this, 'block_getQuestion'));
        $Smarty->register_block('PollListAnswer',       array($this, 'block_getAnswer'));
        
        $Smarty->register_function('PollPrint',         array($this, 'PollPrint'));        
    } 
    
    function PollPrint($params)
    {
        extract($params);
        
        switch (strtolower($subject)) {
            case 'number':
                print($this->mainData['IdPoll']);
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
            print($content);  
        }       
    } 
    
    function block_PollIsVotable($params, $content, &$Smarty, &$repeat) 
    { 
        if ($repeat) {
            return;   
        }
        if (!$this->showResult && $this->userCanVote()) { 
            print($content);    
        }  
    } 
    
    function block_PollIsNotVotable($params, $content, &$Smarty, &$repeat) 
    { 
        if ($repeat) {
            return;   
        }
        if ($this->showResult || !$this->userCanVote()) { 
            print($content);    
        } 
    }
    
    function block_getQuestion($params, $content, &$Smarty, &$repeat)
    {

        if ($repeat) {
            return;   
        }

        foreach ($this->getAnswers() as $answer) {
            $search     = array('##IdPoll##', '##NrAnswer##', '##Answer##');
            $replace    = array($this->mainData['IdPoll'], $answer['NrAnswer'], $answer['Answer']);
            print(str_replace($search, $replace, $content));    
        }
           
    }
    
    function block_getAnswer($params, $content, &$Smarty, &$repeat)
    {

        if ($repeat) {
            return;   
        }

        foreach ($this->getResult() as $answer) {
            $search     = array('##IdPoll##', '##NrAnswer##', '##Answer##', '##Percentage##');
            $replace    = array($this->mainData['IdPoll'], $answer['NrAnswer'], $answer['Answer'], $answer['Percentage']);
            print(str_replace($search, $replace, $content));    
        }
           
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
        
        $this->type          = $statement_params['type'];

        $this->showResult    = $url_params['showResult'];
        $this->IdPoll        = $url_params['IdPoll']; 
        $this->votedata      = $url_params['result']; 
        
        $this->initPoll();
    }
   
    function getQuestion()
    {
        return $this->mainData['def_question'];    
    }
    
    function getPoll()
    {
        if ($this->mainData['IdPoll']) {
            return true;
        }   
        return false;
    }
    
    function initPoll()
    {
        global $DB;
        
        $select = "SELECT m.*, 
                          q.*, 
                          UNIX_TIMESTAMP(m.DateExpire) AS DateExpire_stamp, 
                          qd.IdLanguage AS def_IdLanguage, 
                          qd.title AS def_title, 
                          qd.question AS def_question";
        $join   = "LEFT JOIN poll_questions AS qd ON m.Id = qd.IdPoll AND qd.IdLanguage = 1
                   LEFT JOIN poll_questions AS q  ON m.Id = q.IdPoll  AND q.IdLanguage  = ".$this->IdLanguage;
        $where  = "AND ((m.DateBegin <= CURDATE() AND m.DateExpire >= CURDATE()) OR (m.DateBegin <= CURDATE() AND m.ShowAfterExpiration = 1))";
        $order  = "ORDER BY m.DateExpire DESC, 
                            m.Id DESC";

        switch ($this->type) {
            case "id":
            
            if ($this->IdPoll) {
                $w = "m.Id=$this->IdPoll";
            } else {
                $w = "1";
            }

            $q = "$select
                      FROM poll_main AS m
                      $join
                      WHERE $w
                      $where
                      $order
                      LIMIT 0,1";
            $res = sqlQuery($DB['modules'], $q);
            break;

            case "article":
            $q = "$select
                      FROM poll_main AS m, poll_article AS a
                      $join
                      WHERE a.NrArticle='$this->NrArticle' AND a.IdPoll=m.Id
                      $where
                      $order";
            $res = sqlQuery($DB['modules'], $q);
            break;

            case "section":
            $q = "$select
                      FROM poll_main AS m, poll_section AS s
                      $join
                      WHERE s.NrSection='$this->NrSection' AND s.IdPoll=m.Id
                      $where
                      $order"; 
            $res = sqlQuery($DB['modules'], $q);
            break;

            case "issue":
            $q = "$select
                      FROM poll_main AS m, poll_issue AS i
                      $join
                      WHERE i.NrIssue='$this->NrIssue' AND i.IdPoll=m.Id
                      $where
                      $order";
            $res = sqlQuery($DB['modules'], $q);
            break;

            case "publication":
            $q = "$select
                      FROM poll_main AS m, poll_publication AS p
                      $join
                      WHERE p.IdPublication='$this->IdPublication' AND p.IdPoll=p.Id
                      $where
                      $order";
            $res = sqlQuery($DB['modules'], $q);
            break;
            
            default:
                return false;
            break;
        }

        if ($this->mainData = mysql_fetch_array($res, MYSQL_ASSOC)) { 

            if (!$this->mainData['IdLanguage']) {
                ## no translation, use default language

                $this->mainData['IdLanguage']    = $this->mainData['def_IdLanguage'];
                $this->mainData['title']          = $this->mainData['def_title'];
                $this->mainData['question']       = $this->mainData['def_question'];
            }
            $this->saveUserVote();

            return true;
        }
        return false;
    }

    function getAnswers()
    {
        global $DB;
        $query = "SELECT * FROM poll_answers
                      WHERE IdPoll='{$this->mainData['IdPoll']}' AND IdLanguage='{$this->mainData['IdLanguage']}' ORDER BY NrAnswer";
        $res   = sqlQuery($DB['modules'], $query);

        while ($row = mysql_fetch_array($res, MYSQL_ASSOC)) {
            $answers[] = $row;
        }

        return $answers;
    }

    function userCanVote()
    {
        if ($_SESSION['poll_vote'][$this->mainData['IdPoll']] || $this->mainData['DateExpire_stamp'] < time()) {
            return false;
        }

        return true;
    }

    function saveUserVote()
    {
        global $DB;
        if ($this->votedata['answer'][$this->mainData['IdPoll']] && $this->userCanVote()) {
            ## user can only vote actual poll
            $_SESSION['poll_vote'][$this->mainData['IdPoll']] = true;

            $query = "UPDATE poll_answers 
                      SET NrOfVotes = (NrOfVotes+1)
                      WHERE NrAnswer   = '{$this->votedata['answer'][$this->mainData['IdPoll']]}' AND 
                            IdPoll     = '{$this->mainData['IdPoll']}' AND 
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
                   WHERE IdPoll = '{$this->mainData['IdPoll']}' 
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
                   WHERE  IdPoll = '{$this->mainData['IdPoll']}'";        
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