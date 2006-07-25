<?php
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
        $this->IdPoll        = $url_params['Id']; 
        $this->votedata      = $url_params['result']; 
    }

    function getPoll()
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
                      WHERE IdPoll='{$this->mainData['Id']}' AND IdLanguage='{$this->mainData['IdLanguage']}' ORDER BY NrAnswer";
        $res   = sqlQuery($DB['modules'], $query);

        while ($row = mysql_fetch_array($res, MYSQL_ASSOC)) {
            $answers[] = $row;
        }

        return $answers;
    }

    function userCanVote()
    {
        if ($_SESSION['poll_vote'][$this->mainData['Id']] || $this->mainData['DateExpire_stamp'] < time()) {
            return false;
        }

        return true;
    }

    function saveUserVote()
    {
        global $DB;
        if ($this->votedata['answer'][$this->mainData['Id']] && $this->userCanVote()) {
            ## user can only vote actual poll
            $_SESSION['poll_vote'][$this->mainData['Id']] = true;

            $query = "UPDATE poll_answers 
                      SET NrOfVotes = (NrOfVotes+1)
                      WHERE NrAnswer   = '{$this->votedata['answer'][$this->mainData['Id']]}' AND 
                            IdPoll     = '{$this->mainData['Id']}' AND 
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
                   WHERE IdPoll = '{$this->mainData['Id']}' 
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
                   WHERE  IdPoll = '{$this->mainData['Id']}'";        
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
                $answers[$k]['percent'] = round(100 / $sum['allsum'] * $vote['rowsum'], 1);
            } else {
                $answers[$k]['percent'] = 0;
            }
            next($NrOfVotes);
        }
        #print_r($answers);
        return $answers;
    }
}
?>