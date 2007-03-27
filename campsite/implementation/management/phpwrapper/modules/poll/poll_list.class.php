<?php
class poll_list2smarty extends poll_list
{  
    var $list = array();
    
    function poll_list2smarty($camp_params, $statement_params, $url_params, &$Smarty)
    {
        $this->poll_list($camp_params, $statement_params, $url_params);         
        $this->assignSmartyFunctions(&$Smarty);
    }
    
    function assignSmartyFunctions(&$Smarty)
    {
        $Smarty->register_block('PollList_Get',            array(&$this, 'block_PollList_Get'));  
        $Smarty->register_block('PollList_IfNextItem',     array(&$this, 'block_PollList_IfNextItem'));  
        $Smarty->register_block('PollList_IfPrevItem',     array(&$this, 'block_PollList_IfPrevItem'));
        $Smarty->register_function('PollList_Print',       array(&$this, 'PollList_Print'));  
    } 
    
    function block_PollList_Get($params, $content, &$Smarty, &$repeat)
    {  
        if ($repeat) { 
            ## load the polllist
            $this->list = $this->get($params['length']);
            return;
        } 
       
        $search = array('##NrPoll##', '##Title##', '##Question##', '##DateBegin##', '##DateExpire##');
  
        foreach ($this->list['entrys'] as $entry) { 
            $replacement = array($entry['NrPoll'], $entry['Title'], $entry['Question'], $entry['DateBegin'], $entry['DateExpire']);   
            print(str_replace($search, $replacement, $content));
        }
    }

    function block_PollList_IfNextItem($params, $content, &$Smarty, &$repeat)
    {
        if ($repeat) {
            return;   
        } 
        if (isset($this->list['nextItem'])) { 
            return str_replace('##nextItem##', $this->list['nextItem'], $content);    
        } 
    }
    
    function block_PollList_IfPrevItem($params, $content, &$Smarty, &$repeat)
    {
        if ($repeat) {
            return;   
        }  
        if (isset($this->list['prevItem'])) {
            return str_replace('##prevItem##', $this->list['prevItem'], $content);    
        }        
    }
    
    function PollList_Print($params)
    {
        extract($params);
        $element = current($this->list['entrys']);

        switch ($object) {
            case 'NrPoll':
            case 'Title':
            case 'Question':
                return $element[$object];
            break;
            
            case 'DateBegin':
            case 'DateExpire':
                return strftime($format, $element[$object]);
            break;     
        }    
        
    }  
}

class poll_list
{
    function poll_list($camp_params, $statement_params, $url_params)
    {
        $this->params   = $camp_params;
        $this->type     = $statement_params['assign']; 
        $this->page     = !empty($url_params['page']) ? $url_params['page'] : 0;
    }
    
    function get($rows=10)
    {
        $limit  = $this->page * $rows; 
        $LIMIT  = " LIMIT $limit, $rows";
        $MORE   = " LIMIT ".($limit + $rows).", 1";

        if ($limit) {
            $list['prevItem'] = $this->page - 1;
        }

        switch ($this->type){
            case "all":
            $query = "SELECT m.*, 
                             UNIX_TIMESTAMP(m.DateBegin)  AS DateBegin, 
                             UNIX_TIMESTAMP(m.DateExpire) AS DateExpire, 
                             q.*
                      FROM   mod_poll_main      AS m,
                             mod_poll_questions AS q 
                      WHERE  m.Number     = q.NrPoll                            AND 
                             q.IdLanguage = '{$this->params['IdLanguage']}' AND
                             m.DateBegin <= CURDATE()                       AND 
                             (m.DateExpire >= CURDATE() OR m.ShowAfterExpiration=1)
                      ORDER BY m.DateExpire DESC, m.Number DESC";
            break;

            case "article":
            $query = "SELECT m.*, 
                             UNIX_TIMESTAMP(m.DateBegin)  AS DateBegin, 
                             UNIX_TIMESTAMP(m.DateExpire) AS DateExpire, 
                             q.*
                      FROM   mod_poll_main      AS m,
                             mod_poll_questions AS q,
                             mod_poll_article   AS pa 
                      WHERE  m.Number     = q.NrPoll                        AND 
                             q.IdLanguage = '{$this->params['IdLanguage']}' AND
                             m.Number     = pa.NrPoll                       AND
                             m.IdLanguage = ps.IdLanguage                   AND
                             m.DateBegin <= CURDATE()                       AND 
                             (m.DateExpire >= CURDATE() OR m.ShowAfterExpiration=1)
                      ORDER BY m.DateExpire DESC, m.Number DESC";
            break;

            case "section":
            $query = "SELECT m.*, 
                             UNIX_TIMESTAMP(m.DateBegin)  AS DateBegin, 
                             UNIX_TIMESTAMP(m.DateExpire) AS DateExpire, 
                             q.*
                      FROM   mod_poll_main      AS m,
                             mod_poll_questions AS q,
                             mod_poll_section   AS ps 
                      WHERE  m.Number     = q.NrPoll                        AND 
                             q.IdLanguage = '{$this->params['IdLanguage']}' AND
                             m.Number     = ps.NrPoll                       AND
                             m.IdLanguage = ps.IdLanguage                   AND
                             m.DateBegin <= CURDATE()                       AND 
                             (m.DateExpire >= CURDATE() OR m.ShowAfterExpiration=1)
                      ORDER BY m.DateExpire DESC, m.Number DESC";
            break;
            
            default:
                return false;
            break;
        }

        #echo $query;

        $res = sqlQuery($GLOBALS['DB']['modules'], $query.$LIMIT);
        #$this->params['NrSection']=120; 
        
        while ($row = mysql_fetch_array($res)) {
            $list['entrys'][] = $row;
        }

        $more = sqlQuery($GLOBALS['DB']['modules'], $query.$MORE);
        if (mysql_num_rows($more)) {
            $list['nextItem'] = $this->page + 1;
        }

        return $list;
    }
}
?>