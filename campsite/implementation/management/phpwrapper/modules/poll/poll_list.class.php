<?php
class poll_list
{
    function poll_list($params, $type)
    {
        $this->params   = $params;
        $this->type     = $type;
    }
    
    function get()
    {
        global $POLL, $page, $PARAMS;
        
        #inclangfile($this->params['IdLanguage'], $this->params['IdPublication'], "poll");

        $limit = $page * $POLL['pollsPP']; 

        $LIMIT  = " LIMIT $limit, ".$POLL['pollsPP'];
        $MORE   = " LIMIT ".($limit + 1).", 1";

        if ($limit) {
            $list['prevlink'] = getUrl($this->params).'&page='.($page-1);
        }

        switch ($this->type){
            case "all":
            $query = "SELECT m.*, UNIX_TIMESTAMP(m.dfrom) AS stampfrom, UNIX_TIMESTAMP(m.dto) AS stampto, 
                             q.*, qd.id_language AS def_id_language, qd.title AS def_title, qd.question AS def_question
                      FROM poll_main AS m
                      LEFT JOIN poll_questions AS qd ON m.id=qd.id_poll AND qd.id_language=1
                      LEFT JOIN poll_questions AS q ON m.id=q.id_poll AND q.id_language='{$this->params['IdLanguage']}'
                      WHERE m.dfrom <= CURDATE() AND (m.dto >= CURDATE() OR m.beyond=1)
                      ORDER BY m.dto DESC, m.id DESC";
            break;

            case "article":
            $query = "SELECT m.*, UNIX_TIMESTAMP(m.dfrom) AS stampfrom, UNIX_TIMESTAMP(m.dto) AS stampto, 
                             q.*, qd.id_language AS def_id_language, qd.title AS def_title, qd.question AS def_question
                      FROM poll_main AS m
                      LEFT JOIN poll_questions AS qd ON m.id=qd.id_poll AND qd.id_language=1
                      LEFT JOIN poll_questions AS q ON m.id=q.id_poll AND q.id_language='{$this->params['IdLanguage']}'
                      WHERE m.dfrom <= CURDATE() AND (m.dto >= CURDATE() OR m.beyond=1)
                      ORDER BY m.dto DESC, m.id DESC";
            break;

            case "section":
            $query = "SELECT m.*, UNIX_TIMESTAMP(m.dfrom) AS stampfrom, UNIX_TIMESTAMP(m.dto) AS stampto, 
                             q.*, qd.id_language AS def_id_language, qd.title AS def_title, qd.question AS def_question
                      FROM poll_main AS m, poll_section AS s
                      LEFT JOIN poll_questions AS qd ON m.id=qd.id_poll AND qd.id_language=1
                      LEFT JOIN poll_questions AS q ON m.id=q.id_poll AND q.id_language='{$this->params['IdLanguage']}'
                      WHERE (m.dfrom <= CURDATE() AND (m.dto >= CURDATE() OR m.beyond=1))
                            AND s.id_poll=m.id AND s.id_section='{$this->params['NrSection']}'
                      ORDER BY m.dto DESC, m.id DESC";
            break;
            
            default:
                return false;
            break;
        }

        #echo $query;

        $res = sqlQuery($GLOBALS['DB']['poll'], $query.$LIMIT);
        $this->params['NrSection']=120; 
        
        while ($row = mysql_fetch_array($res)) {

            if ($row['title']) {
                if ($PARAMS['title']=='on') {
                    $t = $row['title'].' ';
                }
                $q = $row['question'];
            } else {
                if ($PARAMS['title']=='on') {
                    $t = $row['def_title'].' ';
                }
                $t = $row['def_question'];
            }

            $list['entrys'][] = array(
                'link' => getURL($p)."&pollid={$row['id']}&page={$page}",
                'text' => "$t$q",
                'when' => utf8_encode(strftime ("%d.%b.%Y", $row['stampfrom'])." bis ".strftime ("%d.%b.%Y", $row['stampto']))
            );
        }

        $more = sqlQuery($GLOBALS['DB']['poll'], $query.$MORE);
        if (mysql_num_rows($more)) {
            $list['nextlink'] = getUrl($this->params).'&page='.($page+1);
        }
        
        return $list;
    }
}
?>