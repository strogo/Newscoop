<?php
function paramStrToArr($str)
{
    parse_str(str_replace('&amp;', '&', $str), $params);
    return $params;
}

function arrSerialize($array, $k)
{
    if (is_array($array)) {
        foreach ($array as $key=>$val) {
            if (is_array($val)) {
                $str .= arrSerialize($val, $k.'['.$key.']');
            } else {
                $str .= urlencode($k.'['.$key.']').'='.urlencode($val).'&amp;';
            }
        }
        return $str;
    }
}

function arrToParamStr($array)
{
    if (is_array($array)) {
        foreach ($array as $key=>$val) {        
            if (is_array($val)) {
                $str .= arrSerialize($val, $key);
            } else {
                $str .= urlencode($key).'='.urlencode($val).'&amp;';
            }
        }
        return substr($str, 0, -5);
    }
}

function arrSerializeAsInput($array, $k)
{
    if (is_array($array)) {
        foreach ($array as $key=>$val) {
            if (is_array($val)) {
                $str .= arrSerializeAsInput($val, $k.'['.$key.']');
            } else {
                $str .= "<INPUT type=\"hidden\" name=\"{$k}[$key]\" value=\"{$val}\">\n"; 
            }
        }
        return $str;
    }
}

function arrToInputFields($array)
{   
    foreach ($array as $k => $v) { 
        if (is_array($v)) {
            $return .= arrSerializeAsInput($v, $k);    
        } else { 
            $return .= "<INPUT type=\"hidden\" name=\"$k\" value=\"{$array[$k]}\">\n";
        }
    }
    return $return;
}

function getTPL($starttpl=false)
{
    if ($GLOBALS['SYS']['module_tpl'] && $starttpl === false) {
        return $GLOBALS['SYS']['module_tpl'];
    }
    
    $parse = parse_url($_SERVER['REQUEST_URI']);
    return $parse['path'];
}

function getURL($setparams=null, $usestartparams=false, $allparams=false)
{
    return getTPL($usestartparams)."?".getGetParams($setparams, $usestartparams, $allparams);
}

function getGetParams($setparams=null, $usestartparams=false, $allparams=false)
{
    $use = prepareParams($setparams, $usestartparams, $allparams);
       
    return arrToParamStr($use);        
}

function getPostParams($setparams=array(), $usestartparams=false, $allparams=false)
{
    $use = prepareParams($setparams, $usestartparams, $allparams);
    
    return arrToInputFields($use);
}

function prepareParams($setparams, $usestartparams, $allparams)
{
    ## $usestartparams=TRUE: get the start parameters instead of current parameters ##
        
    $excludeparams = array('LoginUName' => 1, 'LoginPassword' => 1, 'login' => 1, 'logout' => 1, 'Login' => 1);
    
    if ($usestartparams) {
        if ($allparams) {
            $use = array_merge($_POST, $_GET);    
        } else {
            $use = filterCsParams(array_merge($_POST, $_GET));    
        }
    } else { 
        $use = filterCsParams($GLOBALS['PARAMS']);    
    }
    
    if (is_array($setparams)) {
        foreach ($setparams as $var=>$val) {
            $use[$var] = $val;
        }
    } 
    
    foreach ($excludeparams as $var=>$val) {
        unset($use[$var]);
    }   
    
    return $use;   
}

function filterCsParams($params)
{
    global $URLPARAMS;
 
    foreach($params as $key=>$val) {
        if ($URLPARAMS[$key] === true) {
            $filteredParams[$key] = $val;    
        }   
    } 
    return $filteredParams;  
}

function defineUrlType()
{
    global $DB, $env_vars;

    $query = "SELECT t.Name AS Name
              FROM Aliases as a,
                   Publications AS p,
                   URLTypes AS t 
              WHERE a.Name          = '{$env_vars["HTTP_HOST"]}'  AND
                    a.IdPublication = p.Id AND
                    p.IdURLType     = t.Id";  
    $res = sqlRow($DB['campsite'], $query);

    define('URLTYPE', $res['Name']);   
}
?>