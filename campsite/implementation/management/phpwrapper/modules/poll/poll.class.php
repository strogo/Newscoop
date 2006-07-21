<?php
class poll
{
    function poll($params, $debug)
    {
        $this->IDlanguage    = $params['IdLanguage'];
        $this->IDpublication = $params['IdPublication'];
        $this->IDissue       = $params['NrIssue'];
        $this->IDsection     = $params['NrSection'];
        $this->IDarticle     = $params['NrArticle'];
        $this->linklist      = $params['linklist'];
        $this->type          = $params['type'];

        $this->pollid        = $_REQUEST['pollid'];
        $this->votedata      = $_REQUEST['pollres'];
        
        $this->debug         = $debug;
    }

    function getPoll()
    {
        $select = "SELECT m.*, q.*, UNIX_TIMESTAMP(m.dto) AS dto_stamp, qd.id_language AS def_id_language, qd.title AS def_title, qd.question AS def_question";
        $join   = "LEFT JOIN poll_questions AS qd ON m.id=qd.id_poll AND qd.id_language=1
                       LEFT JOIN poll_questions AS q ON m.id=q.id_poll AND q.id_language=".$this->IDlanguage;
        $where  = "AND ((m.dfrom <= CURDATE() AND m.dto >= CURDATE()) OR (m.dfrom <= CURDATE() AND m.beyond=1))";
        $order  = "ORDER BY m.dto DESC, m.id DESC";

        switch ($this->type) {
            case "id":
            
            if ($this->pollid) {
                $w = "m.id=$this->pollid";
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
            $query = sqlQuery($GLOBALS[DB][poll], $q);
            break;

            case "article":
            $q = "$select
                      FROM poll_main AS m, poll_article AS a
                      $join
                      WHERE a.id_article='$this->IDarticle' AND a.id_poll=m.id
                      $where
                      $order";
            $query = sqlQuery($GLOBALS[DB][poll], $q);
            break;

            case "section":
            $q = "$select
                      FROM poll_main AS m, poll_section AS s
                      $join
                      WHERE s.id_section='$this->IDsection' AND s.id_poll=m.id
                      $where
                      $order"; 
            $query = sqlQuery($GLOBALS[DB][poll], $q);
            break;

            case "issue":
            $q = "$select
                      FROM poll_main AS m, poll_issue AS i
                      $join
                      WHERE i.id_issue='$this->IDissue' AND i.id_poll=m.id
                      $where
                      $order";
            $query = sqlQuery($GLOBALS[DB][poll], $q);
            break;

            case "publication":
            $q = "$select
                      FROM poll_main AS m, poll_publication AS p
                      $join
                      WHERE p.id_publication='$this->IDpublication' AND p.id_poll=p.id
                      $where
                      $order";
            $query = sqlQuery($GLOBALS['DB']['poll'], $q);
            break;
            
            default:
                return false;
            break;
        }

        if ($this->mainData = mysql_fetch_array($query, MYSQL_ASSOC)) {
            if (!$this->mainData['id_language']) {
                ## no translation, use default language

                $this->mainData['id_language']    = $this->mainData['def_id_language'];
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
        $query = "SELECT * FROM poll_answers
                      WHERE id_poll='{$this->mainData['id']}' AND id_language='{$this->mainData['id_language']}' ORDER BY nr_answer";
        $res   = sqlQuery($GLOBALS['DB']['poll'], $query);

        while ($row = mysql_fetch_array($res, MYSQL_ASSOC)) {
            $answers[] = $row;
        }

        return $answers;
    }

    function userCanVote()
    {
        if ($_SESSION['poll_vote'][$this->mainData['id']] || $this->mainData['dto_stamp'] < time()) {
            return false;
        }

        return true;
    }

    function saveUserVote()
    {
        if ($this->votedata['answer'][$this->mainData['id']] && $this->userCanVote()) {
            ## user can only vote actual poll
            $_SESSION['poll_vote'][$this->mainData['id']] = true;

            $query = "UPDATE poll_answers SET votes=(votes+1)
                      WHERE nr_answer='{$this->votedata['answer'][$this->mainData['id']]}'
                      AND id_poll='{$this->mainData['id']}' AND id_language='{$this->mainData['id_language']}'";

            sqlQuery($GLOBALS['DB']['poll'], $query);

            return true;
        }
        return false;
    }

    function getVotes()
    {
        // sum of votes depending to nr_answer, independing from id_language
        $query  = "SELECT nr_answer, SUM(votes) as rowsum FROM poll_answers
                  WHERE id_poll='{$this->mainData['id']}' GROUP BY nr_answer ORDER BY nr_answer";
        $result = sqlQuery($GLOBALS['DB']['poll'], $query);

        while($row = mysql_fetch_array($result)) {
            $votes[] = $row;
        }

        return $votes;
    }

    function getSum()
    {
        // sum of votes
        $query  = "SELECT SUM(votes) AS allsum FROM poll_answers
                  WHERE id_poll='{$this->mainData['id']}'";        
        $result = sqlQuery($GLOBALS['DB']['poll'], $query);

        $sum = mysql_fetch_array($result);

        return $sum;
    }

    function getResult()
    {
        $answers = $this->getAnswers();
        $votes   = $this->getVotes();
        $sum     = $this->getSum();

        foreach ($answers as $k=>$v) {
            $vote = current($votes);

            if ($vote[rowsum]) {
                $answers[$k][prozent] = round(100/$sum['allsum']*$vote['rowsum'], 1);
            } else {
                $answers[$k][prozent] = 0;
            }
            next($votes);
        }
        #print_r($answers);
        return $answers;
    }
}
?>