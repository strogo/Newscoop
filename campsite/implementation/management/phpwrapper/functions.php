<?php
function convStr($str, $quote)
{
    $str = stripslashes($str);
    
    if ($quote) {
        $str =  str_replace ("\"", "&#34;", $str);
    }
    
    return $str;
}


function stripArr($input, $quote=false)
{
    if (is_array($input)) {
        foreach ($input as $key=>$val) { 
            if (is_array($val)) {
                $arr[$key] = stripArr($val, $quote);
            } else {
                $arr[$key] = convStr($val, $quote);
            }            
        }
        return $arr;
    } else {
        return convStr($input, $quot);
    }
}

function mysql_escape_array($input)
{
    if (is_array($input)) {
        foreach ($input as $key=>$val) { 
            if (is_array($val)) {
                $arr[mysql_escape_string($key)] = mysql_escape_array($val);
            } else {
                $arr[mysql_escape_string($key)] = mysql_escape_string($val);
            }            
        }
        return $arr;
    } else {
        return mysql_escape_string($input);
    }
}

function utfArr ($input, $do) {
    if (is_array ($input)) {
        foreach ($input as $key=>$val) {
            if (is_array ($val))
            $arr[$key] = utfArr ($val, $do);
            else {
                if ($do=="e") $arr[$key] = utf8_encode ($val);
                if ($do=="d") $arr[$key] = utf8_decode ($val);
            }
        }
        return ($arr);
    }
    else {
        if ($do=="e") return utf8_encode ($val);
        if ($do=="d") return utf8_decode ($val);
    }
}

if (!function_exists ("regGS")) {
    function regGS($key, $value)
    {
        $GLOBALS['modlang'][$key] = $value;
    }
}

function sqlQuery($db, $query) 
{
    if (is_array($query)) {
        foreach($query as $lost => $q) {
            $res[] = sqlQuery($db, $q);
        }
    } else {
        $res = mysql_db_query($db, $query); 
        if ($GLOBALS['debug'] && mysql_error()) {
            echo "<p>DB: $db Query: $query Error: ".mysql_error()."</p>";
        }
        mysql_select_db($GLOBALS['DB']['campsite']);
    }
    return $res;
}


function sqlRow($db, $query)
{
    $res = sqlQuery($db, $query); 
    return mysql_fetch_array($res);
}

function sqlArray($db, $query)
{
    $ret = false;
    $res = sqlQuery($query, $db); 
    
    while ($row = mysql_fetch_array($res, MYSQL_ASSOC)) {
        $ret[] = $row;    
    }
    
    
    return $ret;
}


function incLangFile($idlang, $idpub, $module)
{
    ## select/load lang-file ###########
    $query    = "SELECT Code FROM Languages WHERE Id = $idlang LIMIT 0,1";
    $langcode = mysql_fetch_array(sqlQuery ($query, $GLOBALS['DB']['campsite']));

    if (file_exists($_SERVER['DOCUMENT_ROOT']."/admin-files/modules/look/$module/locals.{$langcode['Code']}.php")) {
        include_once $_SERVER['DOCUMENT_ROOT']."/admin-files/modules/look/$module/locals.{$langcode['Code']}.php"; 
    } else {
        ## use default language ############  
        $query    = "SELECT l.Code FROM Languages AS l, Publications AS p WHERE p.id = $idpub LIMIT 0,1";
        $langcode = mysql_fetch_array (sqlQuery ($query, $GLOBALS['DB']['campsite']));
        include_once $_SERVER['DOCUMENT_ROOT']."/admin-files/modules/look/$module/locals.{$langcode['Code']}.php";
    }
}

function tra($str)
{
    global $modlang;
    
    if (!is_array($modlang)) {
        $modlang = array();    
    }
    
    if (array_key_exists($str, $modlang)) {
        $input = $modlang[$str];
    } else {
        $input = $str;   
    }
    
    $nr = func_num_args();
    
    if ($nr > 1) {  
        for ($i = 1; $i < $nr; $i++){
            $name  = '$'.$i;
            $val   = func_get_arg($i);
            $input = str_replace($name, $val, $input);
        }
    }
    
    return $input;
}


function mergePostParams(&$mask, $usestartparams=true, $allparams=false)
{   
    $use = prepareParams(array(), $usestartparams, $allparams);
    
    foreach ($use as $k => $v) {       
        $mask[] = array(
            'element'   => $k,
            'type'      => 'hidden',
            'constant'  => $use[$k]
        );   
    } 
}

function checkmail ($email)
{
    if (eregi("^[0-9a-z]([-_.]?[0-9a-z])*@[0-9a-z]([-.]?[0-9a-z])*\\.[a-z]{2,3}$", $email, $check))
    {
        if (getmxrr(substr(strstr($check[0], '@'), 1), $validate_email_temp)) {
            return true;
        }
        if(checkdnsrr(substr(strstr($check[0], '@'), 1),"ANY")) {
            return true;
        }
    }
    return false;
}

function convDate ($date, $toType)
{
    if ($toType=="de")
    {
        $pieces = explode ("-", $date);
        return "$pieces[2].$pieces[1].$pieces[0]";
    }
    if ($toType=="en")
    {
        $pieces = explode (".", $date);
        return "$pieces[2]-$pieces[1]-$pieces[0]";
    }
    if ($toType=="de:tt.mm.jj")
    {
        $pieces = explode ("-", $date);
        return "$pieces[2].$pieces[1].".substr ($pieces[0], 2, 2);
    }
    if ($toType=="de:tt.mm.jjjj")
    {
        $pieces = explode ("-", $date);
        return "$pieces[2].$pieces[1].$pieces[0]";
    }
}

function cropStr ($input, $length, $char)
{
    if (is_numeric ($length))
    {
        if (strpos ($input, $char))
        $len =  strrpos (substr ($input, 0, $length), $char);
        else $len = $length;
        $output = substr ($input, 0, $len);
        if (strlen ($input)>$len)
        $output .= "...";
    }
    else $output = $input;

    return $output;
}

function getUserData()
{
    if (!is_array($_SESSION['USER']) || !count($_SESSION['USER'])) {
        return false;
    }
    
    return $_SESSION['USER'];
}

/**
 *  _parseArr2Form
 *
 *  Add elements/rules/groups to an given HTML_QuickForm object
 *
 *  @param form object, reference to HTML_QuickForm object
 *  @param mask array, reference to array defining to form elements
 *  @param side string, side where the validation should beeing
 */
function parseArr2Form(&$form, &$mask, $side='client')
{
    foreach($mask as $k=>$v) {
        ## add elements ########################
        if ($v['type']=='radio') {
            foreach($v['options'] as $rk=>$rv) {
                $radio[] =& $form->createElement($v['type'], NULL, NULL, $rv, $rk, $v['attributes']);
            }
            $form->addGroup($radio, $v['element'], tra($v['label']));
            unset($radio);

        } elseif ($v['type']=='select') {
            $elem[$v['element']] =& $form->createElement($v['type'], $v['element'], tra($v['label']), $v['options'], $v['attributes']);
            $elem[$v['element']]->setMultiple($v['multiple']);
            if (isset($v['selected'])) $elem[$v['element']]->setSelected($v['selected']);
            if (!$v['groupit'])        $form->addElement($elem[$v['element']]);

        } elseif ($v['type']=='date') {
            $elem[$v['element']] =& $form->createElement($v['type'], $v['element'], tra($v['label']), $v['options'], $v['attributes']);
            if (!$v['groupit'])     $form->addElement($elem[$v['element']]);

        } elseif ($v['type']=='checkbox' || $v['type']=='static') {
            $elem[$v['element']] =& $form->createElement($v['type'], $v['element'], tra($v['label']), tra($v['text']), $v['attributes']);
            if (!$v['groupit'])     $form->addElement($elem[$v['element']]);

        } elseif (isset($v['type'])) {
            if (!is_array($v['attributes'])) $v['attributes'] = array();
            $elem[$v['element']] =& $form->createElement($v['type'], $v['element'], tra($v['label']),
                                        ($v[type]=='text' || $v['type']=='file' || $v['type']=='password') ? array_merge(array('size'=>UI_INPUT_STANDARD_SIZE, 'maxlength'=>UI_INPUT_STANDARD_MAXLENGTH), $v['attributes']) :
                                        ($v['type']=='textarea' ? array_merge(array('rows'=>UI_TEXTAREA_STANDART_ROWS, 'cols'=>UI_TEXTAREA_STANDART_COLS), $v['attributes']) :
                                        ($v['type']=='button' || $v['type']=='submit' || $v['type']=='reset' ? array_merge(array('class'=>UI_BUTTON_STYLE), $v['attributes']) : $v['attributes']))
                                    );
            if (!$v['groupit'])     $form->addElement($elem[$v['element']]);
        }
        ## add required rule ###################
        if ($v['required']) {
            $form->addRule($v['element'], isset($v['requiredmsg']) ? tra($v['requiredmsg']) : tra(FORM_MISSINGNOTE, tra($v['label'])), 'required', NULL, $side);
        }
        ## add constant value ##################
        if (isset($v['constant'])) {
            $form->setConstants(array($v['element']=>$v['constant']));
        }
        ## add default value ###################
        if (isset($v['default'])) {
            $form->setDefaults(array($v['element']=>$v['default']));
        }
        ## add other rules #####################
        if ($v['rule']) {
            $form->addRule($v['element'], isset($v['rulemsg']) ? tra($v['rulemsg']) : tra('$1 must be $2', tra($v['element']), tra($v['rule'])), $v['rule'] ,$v['format'], $side);
        }
        ## add group ###########################
        if (is_array($v['group'])) {
            foreach($v['group'] as $val) {
                $groupthose[] =& $elem[$val];
            }
            $form->addGroup($groupthose, $v['name'], tra($v['label']), $v['seperator'], $v['appendName']);
            if ($v['rule']) {
                $form->addRule($v['name'], isset($v['rulemsg']) ? tra($v['rulemsg']) : tra('$1 must be $2', tra($v['name'])), $v['rule'], $v['format'], $side);
            }
            if ($v['grouprule']) {
                $form->addGroupRule($v['name'], $v['arg1'], $v['grouprule'], $v['format'], $v['howmany'], $side, $v['reset']);
            }
            unset($groupthose);
        }
        ## check error on type file ##########
        if ($v['type']=='file') {
            if ($_POST[$v['element']]['error']) {
                $form->setElementError($v['element'], isset($v['requiredmsg']) ? tra($v['requiredmsg']) : tra('Missing value for $1', tra($v['label'])));
            }
        }
    }

    reset($mask);
    $form->validate();
    $form->setJsWarnings(FORM_JS_PREWARNING, FORM_JS_POSTWARNING);
    $form->setRequiredNote(FORM_REQUIREDNOTE);
}

function mailMime($recipients, $text=false, $html=false, $hdrs)
{
    include_once 'Mail.php';
    include_once 'Mail/mime.php';

    $crlf = "\r\n";

    $mime = new Mail_mime($crlf);
    
    if (isset($text)) {
        $mime->setTXTBody($text);
    }
    if (isset($html)) {
        $mime->setHTMLBody($html);
    }
    
    $body = $mime->get(array('head_charset' => 'UTF-8', 'text_charset' => 'UTF-8', 'html_charset' => 'UTF-8'));
    $hdrs = $mime->headers($hdrs);
    
    $mail =& Mail::factory('mail');
    
    if (is_array($recipients)) {
        foreach ($recipients as $recipient) {
            $mail->send($recipient, $hdrs, $body);
        }
    } else {
        $mail->send($recipients, $hdrs, $body);   
    }
}

function getLink2Lexica($params)
{
    if ($params['popup'] === 1) {
        return sprintf(LEXICA_LINK_POPUP, $params['guid'], $params['guid']);  
    } else {
        return sprintf(LEXICA_LINK_NOPOPUP, $params['guid'], $params['guid']); 
    } 
}

/** 
 * An alias for "print(htmlspecialchars())".
 * @param string $p_string
 * @return void
 */
function phtml($p_string = null) 
{
    print htmlspecialchars($p_string);
} // fn p

/** 
 * An alias for "print(urlencode())".
 * @param string $p_string
 * @return void
 */
function purl($p_string = null) 
{
    print urlencode($p_string);
} // fn p


if (!function_exists('array_diff_key')){
   require_once 'PHP/Compat/Function/array_diff_key.php';
}

function array_get_value(&$array, $key)
{
    return $array[$key];   
}
?>
