<?php
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
    
    function getProfileImgPathByUsername($username)
    {
        require_once 'modules/profile/profile.class.php';
        $user = Profile::getUserDataByName($username);  
        
        return $user['image_path'];  
    }   
}

# Smarty stuff
require_once 'Smarty/libs/Smarty.class.php';

global $Smarty;
$Smarty = new Smarty();

if (HOSTNAME !== 'fluter.de') {
    $Smarty->force_compile = true;
}

$Smarty->assign_by_ref('functions', new Smarty_import_functions());  
$Smarty->assign('SYS', $SYS); 
?>