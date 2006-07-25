<?php
class poll_linker
{
    function getSelected($type, $pub, $lang, $id)
    {
        global $DB;
        $polls = array();
        
        if (isset($pub) && isset($lang) && isset($id)) {
            $query = "SELECT * FROM poll_$type WHERE id_$type = $id";
            $res    = sqlQuery($DB['modules'], $query);  
            while ($row = mysql_fetch_array($res)) {
                $polls[$row['IdPoll']] = true;
            }  
        }
        return $polls;            
    }
    
    function selectPoll($type, $pub=null, $lang=null, $id=null)
    {
        global $DB;
        $selected = $this->getSelected($type, $pub, $lang, $id);
        $selector = '<select name="poll_ids[]" size="7" multiple>';
        $query = "SELECT m.id, q.title
                  FROM poll_main AS m, poll_questions AS q
                  WHERE m.id=q.IdPoll AND q.id_language=$lang";
        $polls = sqlQuery($DB['modules'], $query);
        
        while ($poll = mysql_fetch_array($polls)) {
            $selector .= "<option value='{$poll['id']}'";
            if ($selected[$poll['id']]) $selector .= " selected";
            reset($selected);
            $selector .= ">{$poll['title']}</option>";
        }
        
        $selector .= '</select>';        
        return $selector;
    }


    function LinkPoll($poll_ids, $type, $pub, $lang, $id)
    {
        global $DB;
        $query = "DELETE FROM poll_$type
                  WHERE id_$type = $id";
        sqlQuery($DB['modules'], $query);
        //echo $query.mysql_error();
        
        if (mysql_affected_rows()) {
            $created = true;
        }

        if (is_array($poll_ids)) {
            while (list($key, $val) = each($poll_ids)) {
                $query = "INSERT INTO poll_$type 
                          (id_$type, IdPoll) 
                          VALUES 
                          ($id, $val)";
                sqlQuery($DB['modules'], $query);
                //echo $query.mysql_error();
            }
            $created = true;
        }
        return $created;
    }
}
?>