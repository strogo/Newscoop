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


### new from here ########################################################

function defineUrlType()
{
    global $DB, $env_vars;

    $query = "SELECT t.Name AS Name
              FROM Aliases as a,
                   Publications AS p,
                   URLTypes AS t 
              WHERE a.Name          = '".getenv("HTTP_HOST")."'  AND
                    a.IdPublication = p.Id AND
                    p.IdURLType     = t.Id";  
    $res = sqlRow($DB['campsite'], $query); 

    define('URLTYPE', $res['Name']);   
}

function extractParams($input)
{
    list ($mod_params, $uri) = explode('url=', $input); 
    
    parse_str(str_replace('&amp;', '&', $mod_params), $mod_param_array['STATEMENT_PARAMS']);
    
    $pieces = parse_url($uri);      
    $camp_param_array['URIPath']          = $pieces['path']; 
    $camp_param_array['Publication Site'] = $pieces['host'];
    $camp_param_array['URLParameters']    = str_replace('&amp;', '&', preg_replace('/^&(amp;)?/', '', $pieces['query']));

    return array_merge($camp_param_array, $mod_param_array);  
}

function getCampParametersInt()
{
    global $PARAMS; 
   
    if (URLTYPE === 'short names') {
        global $DB;
        $path       = $PARAMS['URIPath'];
        $site_alias = $PARAMS['Publication Site'];
        list ($empty, $language, $NrIssue, $section, $NrArticle) = explode('/', $path);
        $query = "SELECT Languages.Id    AS IdLanguage,
                         Aliases.IdPublication,
                         Sections.Number AS NrSection
                  FROM   Languages
                  LEFT JOIN Aliases     ON Aliases.Name = '$site_alias'
                  LEFT JOIN Sections    ON Sections.ShortName        = '$section' AND
                                           Sections.IdPublication    = Aliases.IdPublication AND
                                           Sections.IdLanguage       = Languages.Id              
                  WHERE Languages.Code = '$language'";
        $row = sqlRow($DB['campsite'], $query);
        
        if (!empty($row['IdLanguage']))         $camp_param_array['IdLanguage']      = $row['IdLanguage'];
        if ($row['IdPublication'] !== 'NULL')   $camp_param_array['IdPublication']   = $row['IdPublication'];
        if (!empty($NrIssue))                   $camp_param_array['NrIssue']         = $NrIssue;
        if ($row['NrSection'] !== 'NULL')       $camp_param_array['NrSection']       = $row['NrSection'];
        if (!empty($NrArticle))                 $camp_param_array['NrArticle']       = $NrArticle;

    }  else {
        parse_str(str_replace('&amp;', '&', $PARAMS['URLParameters']), $camp_param_array);   
    } 

    return $camp_param_array;
}

function setCampParameters($key, $value)
{
    global $DB, $PARAMS;
    $params = getCampParametersInt();
    
    if (URLTYPE === 'short names') {       
        switch ($key) {
            case 'Language Name':
                $query = "SELECT Code
                          FROM Languages
                          WHERE Name = '{$value}'"; 
                $row = sqlRow($DB['campsite'], $query);
                setCampURIPath('language_code', $row['Code']);        
            break;
            
            case 'Publication Name':
                $query = "SELECT Aliases.Name
                          FROM Aliases, Publications
                          WHERE Publications.Name = '{$value}' AND
                          Aliases.Id = Publications.IdDefaultAlias"; 
                $row = sqlRow($DB['campsite'], $query);
                $PARAMS['Publication Site'] = $row['Name'];        
            break;
            
            case 'Issue Number':
                setCampURIPath('NrIssue', $value);        
            break;
            
            case 'Section Number':
                $query = "SELECT ShortName
                          FROM Sections
                          WHERE Number = '{$value}' AND
                                IdPublication = '{$params['IdPublication']}'";
                $row = sqlRow($DB['campsite'], $query);
                setCampURIPath('section_shortname', $row['ShortName']);        
            break;
            
            case 'Section Name':
                $query = "SELECT ShortName
                          FROM Sections
                          WHERE Name = '{$value}' AND
                                IdPublication = '{$params['IdPublication']}'";
                $row = sqlRow($DB['campsite'], $query);
                setCampURIPath('section_shortname', $row['ShortName']);        
            break;
        }       
        
    } else {        
        switch ($key) {
            case 'Language Name':
                $query = "SELECT Id
                          FROM Languages
                          WHERE Name = '{$value}'"; 
                $row = sqlRow($DB['campsite'], $query);
                setCampURLParameters('IdLanguage', $row['Id']);        
            break;
            
            case 'Publication Name':
                $query = "SELECT Aliases.Name,
                                 Publications.Id
                          FROM Aliases, Publications
                          WHERE Publications.Name = '{$value}' AND
                          Aliases.Id = Publications.Id"; 
                $row = sqlRow($DB['campsite'], $query);
                $PARAMS['Publication Site'] = $row['Name']; 
                setCampURLParameters('IdPublication', $row['Id']);       
            break;
            
            case 'Issue Number':
                setCampURLParameters('NrIssue', $value);        
            break;
            
            case 'Section Number':
                setCampURLParameters('NrSection', $value);        
            break;
            
            case 'Section Name':
                $query = "SELECT Number
                          FROM Sections
                          WHERE Name = '{$value}' AND
                                IdPublication = '{$params['IdPublication']}'";
                $row = sqlRow($DB['campsite'], $query);
                setCampURLParameters('NrSection', $row['Number']);        
            break;
        } 
    }  
}

function setCampURIPath($key, $value)
{
    global $PARAMS; 
    list (, $language_code, $NrIssue, $section_shortname, $NrArticle) = explode('/', $PARAMS['URIPath']); 
    $$key = $value;
    $PARAMS['URIPath'] = "/$language_code/$NrIssue/$section_shortname/$NrArticle";  
}

function setCampURLParameters($key, $value)
{
    global $PARAMS;  
    parse_str($PARAMS['URLParameters'], $param_array); 
    
    if (strtolower($value) === 'off') { 
        unset($param_array[$key]);  
    } else {
        $param_array[$key] = $value;
    }
    
    foreach ($param_array as $k => $v) {
        $str .= "$k=$v&";    
    }

    $PARAMS['URLParameters'] = substr($str, 0 ,-1);
}


function getCampParameters($key)
{
    global $PARAMS;
    global $DB;
    
    $params = getCampParametersInt();
    
    switch ($key) {
        case 'Language Code':
            $query = "SELECT Code
                      FROM Languages
                      WHERE Id = '{$params['IdLanguage']}'"; 
            $row = sqlRow($DB['campsite'], $query);
            return $row['Code'];        
        break;
       
        case 'Language Name':
            $query = "SELECT Name
                      FROM Languages
                      WHERE Id = '{$params['IdLanguage']}'"; 
            $row = sqlRow($DB['campsite'], $query);
            return $row['Name'];        
        break;
        
        case 'Publication Site':
            $query = "SELECT Aliases.Name
                      FROM Aliases, Publications
                      WHERE Publications. = '{$params['IdPublication']}' AND
                      Aliases.Id = Publications.IdDefaultAlias"; 
            $row = sqlRow($DB['campsite'], $query);
            return $row['Name'];       
        break;
    }
}
?>