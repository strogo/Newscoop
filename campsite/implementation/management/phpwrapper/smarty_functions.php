<?php
# Smarty stuff
require_once 'Smarty/libs/Smarty.class.php';

global $Smarty;
$Smarty = new Smarty();

if (HOSTNAME !== 'fluter.de') {
    $Smarty->force_compile = true;
}

$Smarty->template_dir = $_SERVER['DOCUMENT_ROOT'].'/phpwrapper/templates/';
$Smarty->compile_dir  = $_SERVER['DOCUMENT_ROOT'].'/phpwrapper/templates_c/';

class Camp_Functions
{
    function camp_print($params)
    { 
        global $PARAMS;
   
        $object = strtolower(key($params)); 
        $attrib = strtolower(current($params)); 
                
        switch ($object) {
            case 'language':    
                if ($attrib === 'identifier') { 
                    return array_get_value(getCampParametersInt(), 'IdLanguage');    
                }
            break;
            
            case 'publication':    
                switch ($attrib) {
                    case 'identifier':
                        return $PARAMS['IdPublication'];  
                    break;
                    
                    case 'site': 
                        return $PARAMS['Publication Site'];
                    break; 
                }
            break;
            
            case 'issue':    
                if ($attrib === 'number') {
                    return array_get_value(parseURIPath2CSparams(), 'NrIssue');    
                }
            break;
            
            case 'section':    
                if ($attrib === 'number') {
                    return $PARAMS['NrSection'];    
                }
            break;
            
            case 'article':    
                if ($attrib === 'number') {
                    return $PARAMS['NrArticle'];    
                }
            break;
        }    
    } 
    
    function URIPath()
    {
        global $PARAMS;
        return $PARAMS['URIPath'];       
    }  
    
    function URLParameters()
    {
        global $PARAMS;
        return $PARAMS['URLParameters'];
        
    } 
    
    function URI()
    {
        global $PARAMS;
        return "{$PARAMS['URIPath']}?{$PARAMS['URLParameters']}";   
    }
    
    function URL()
    {
        global $PARAMS;
        return "http://{$PARAMS['Publication Site']}{$PARAMS['URIPath']}?{$PARAMS['URLParameters']}";   
    }
    
    function FormParameters()
    {
        global $PARAMS;
        parse_str($PARAMS['URLParameters'], $params);
        foreach($params as $key => $value) {
            $str .= "<INPUT type=\"hidden\" name=\"$key\" value=\"$value\">\n";        
        } 
        return $str;   
    }
    
    function Language($params)
    {
        setCampParameters('Language '.key($params), current($params)); 
    }
    
    function Publication($params)
    {
        setCampParameters('Publication '.key($params), current($params)); 
    }
    
    function Issue($params)
    {
        setCampParameters('Issue '.key($params), current($params)); 
    }
    
    function Section($params)
    {
        setCampParameters('Section '.key($params), current($params)); 
    }
    
    function Article($params)
    {
        setCampParameters('Article '.key($params), current($params)); 
    }
    
    function Local()
    {
        global $ParamStack, $PARAMS;
        array_push($ParamStack, $PARAMS);        
    }
    
    function EndLocal()
    {
        global $ParamStack, $PARAMS;
        $PARAMS = array_pop($ParamStack);         
    }
}

$Smarty->register_function('Print',         array('Camp_Functions', 'camp_print'));
$Smarty->register_function('URIPath',       array('Camp_Functions', 'URIPath'));
$Smarty->register_function('URLParameters', array('Camp_Functions', 'URLParameters'));
$Smarty->register_function('URI',           array('Camp_Functions', 'URI'));
$Smarty->register_function('URL',           array('Camp_Functions', 'URL'));
$Smarty->register_function('FormParameters',array('Camp_Functions', 'FormParameters'));
$Smarty->register_function('Local',         array('Camp_Functions', 'Local'));
$Smarty->register_function('EndLocal',      array('Camp_Functions', 'EndLocal'));
$Smarty->register_function('Language',      array('Camp_Functions', 'Language'));
$Smarty->register_function('Publication',   array('Camp_Functions', 'Publication'));
$Smarty->register_function('Issue',         array('Camp_Functions', 'Issue'));
$Smarty->register_function('Section',       array('Camp_Functions', 'Section'));

/*
class Smarty_import_functions
{
    function getTPL($starttpl=false)
    {
        return getTPL($starttpl);    
    }

    function getURL($setparams=null, $usestartparams=false, $allparams=false)
    {
        return getURL($setparams, $usestartparams, $allparams);    
    }    
    
    function getPostParams($setparams=null, $usestartparams=false, $allparams=false)
    {
        return getPostParams($setparams, $usestartparams, $allparams);    
    }
    
    function getGetParams($setparams=null, $usestartparams=false, $allparams=false)
    {
        return getGetParams($setparams, $usestartparams, $allparams);    
    }
    
    function assignArray($keys, $values=null, $append=null)
    {
        if (strpos($keys, ',')) {
            $keys   = explode(',', $keys);
            if (strpos($values, ',')) {
                $values = explode(',', $values);
            } 
        } else {
            $keys   = array($keys);
            $values = array($values);   
        }
        
        if (is_array($append)) {
            foreach ($append as $k=>$v) {
                $arr[trim($k)] = trim($v);   
            }    
        }   
             
        foreach ($keys as $k) {
            $k = trim($k);
            $arr[$k] = is_array($values) ? trim(current($values)) : true;
            is_array($values) ? next($values) : null;    
        } 

        return $arr;    
    } 
}

$Smarty->assign_by_ref('functions', new Smarty_import_functions());
*/
?>