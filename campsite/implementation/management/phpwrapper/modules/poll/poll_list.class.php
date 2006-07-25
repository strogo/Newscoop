<?php
class poll_list
{
    function poll_list($camp_params, $statement_params, $request)
    {
        $this->params   = $camp_params;
        $this->type     = $statement_params['type']; 
        $this->page     = !empty($request['page']) ? $request['page'] : 0;
    }
    
    function get()
    {
        $limit = $this->page * 10; 
        $LIMIT  = " LIMIT $limit, 10";
        $MORE   = " LIMIT ".($limit + 1).", 1";

        if ($limit) {
            $list['previousPage'] = $this->page - 1;
        }

        switch ($this->type){
            case "all":
            $query = "SELECT m.*, UNIX_TIMESTAMP(m.DateBegin) AS DateBegin_stamp, UNIX_TIMESTAMP(m.DateExpire) AS DateExpire_stamp, 
                             q.*, qd.IdLanguage AS def_IdLanguage, qd.title AS def_title, qd.Question AS def_question
                      FROM poll_main AS m
                      LEFT JOIN poll_questions AS qd ON m.id=qd.IdPoll AND qd.IdLanguage=1
                      LEFT JOIN poll_questions AS q ON m.id=q.IdPoll AND q.IdLanguage='{$this->params['IdLanguage']}'
                      WHERE m.DateBegin <= CURDATE() AND (m.DateExpire >= CURDATE() OR m.ShowAfterExpiration=1)
                      ORDER BY m.DateExpire DESC, m.id DESC";
            break;

            case "article":
            $query = "SELECT m.*, UNIX_TIMESTAMP(m.DateBegin) AS DateBegin_stamp, UNIX_TIMESTAMP(m.DateExpire) AS DateExpire_stamp, 
                             q.*, qd.IdLanguage AS def_IdLanguage, qd.title AS def_title, qd.Question AS def_question
                      FROM poll_main AS m
                      LEFT JOIN poll_questions AS qd ON m.id=qd.IdPoll AND qd.IdLanguage=1
                      LEFT JOIN poll_questions AS q ON m.id=q.IdPoll AND q.IdLanguage='{$this->params['IdLanguage']}'
                      WHERE m.DateBegin <= CURDATE() AND (m.DateExpire >= CURDATE() OR m.ShowAfterExpiration=1)
                      ORDER BY m.DateExpire DESC, m.id DESC";
            break;

            case "section":
            $query = "SELECT m.*, UNIX_TIMESTAMP(m.DateBegin) AS DateBegin_stamp, UNIX_TIMESTAMP(m.DateExpire) AS DateExpire_stamp, 
                             q.*, qd.IdLanguage AS def_IdLanguage, qd.title AS def_title, qd.Question AS def_question
                      FROM poll_main AS m, poll_section AS s
                      LEFT JOIN poll_questions AS qd ON m.id=qd.IdPoll AND qd.IdLanguage=1
                      LEFT JOIN poll_questions AS q ON m.id=q.IdPoll AND q.IdLanguage='{$this->params['IdLanguage']}'
                      WHERE (m.DateBegin <= CURDATE() AND (m.DateExpire >= CURDATE() OR m.ShowAfterExpiration=1))
                            AND s.IdPoll=m.id AND s.id_section='{$this->params['NrSection']}'
                      ORDER BY m.DateExpire DESC, m.id DESC";
            break;
            
            default:
                return false;
            break;
        }

        #echo $query;

        $res = sqlQuery($GLOBALS['DB']['modules'], $query.$LIMIT);
        $this->params['NrSection']=120; 
        
        while ($row = mysql_fetch_array($res)) {
            $list['entrys'][] = $row;
        }

        $more = sqlQuery($GLOBALS['DB']['modules'], $query.$MORE);
        if (mysql_num_rows($more)) {
            $list['nextPage'] = $this->page + 1;
        }
        
        return $list;
    }
}
?>