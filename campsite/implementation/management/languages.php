<?

function registerLanguage($name,$code,$charset){
    global $languages;
    $languages["$code"]=array('name'=>$name,'charset'=>$charset);
}


registerLanguage('English','en','ISO-8859-1');
registerLanguage('Russian','ru','ISO-8859-5');

?>