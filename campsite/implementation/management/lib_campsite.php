<?

$scriptBase='/var/www/script';

function query($qs,$r='',$setvars=true){
    //print $qs;
    $a=mysql_query($qs);
    //print $a;
    $fl=strtoupper(substr($qs,0,1));
    $GLOBALS['NUM_ROWS']=0;
    $GLOBALS['AFFECTED_ROWS']=0;
    if ($setvars) {
	//print $fl;
        if ($fl=='S')
	    $GLOBALS['NUM_ROWS']=mysql_num_rows($a);
        else
	    $GLOBALS['AFFECTED_ROWS']=mysql_affected_rows();
	//print "num=".$GLOBALS['NUM_ROWS']." aff=".$GLOBALS['AFFECTED_ROWS'];
    }
    if ($r!='')
        $GLOBALS[$r]=$a;
    if (isset($GLOBALS['debug'])) 
	print $qs.$a;
}

function encURL($s){
    return urlencode($s);
}

function pencURL($s){
    print urlencode($s);
}

function encHTML($s){
    return htmlentities($s);
}

function pencHTML($s){
    print htmlentities($s);
}

function todef($s,$v=''){
    if (!isset($GLOBALS[$s]))
	$GLOBALS[$s]=$v;
	//print 'f';
}

function todefnum($s,$v=0){
    if (!isset($GLOBALS[$s]))
	$GLOBALS[$s]=$v;
}

function fetchRow($q){
    $GLOBALS['fetch_'.$q]=mysql_fetch_array($q,MYSQL_ASSOC);
    
}

function fetchRowNum($q){
    //print $q;
    $GLOBALS['fetch_num_'.$q]=mysql_fetch_array($q,MYSQL_NUM);
}    

function getVar($q,$s){
    //print $s;
    $arr=$GLOBALS['fetch_'.$q];
    return $arr[$s];
}

function getNumVar($q,$n=0){
    $arr=$GLOBALS['fetch_num_'.$q];
    return $arr[$n];
}

function pgetNumVar($q,$n=0){
    $arr=$GLOBALS['fetch_num_'.$q];
    print $arr[$n];
}


function getHVar($q,$s){
//    print "::".$s;
    return encHTML(getVar($q,$s));
}

function getUVar($q,$s){
    return encURL(getVar($q,$s));
}

function getSVar($q,$s){
    return addslashes(getVar($q,$s));
}


function pgetUVar($q,$s){
    print encURL(getVar($q,$s));
}

function pgetHVar($q,$s){
    print encHTML(getVar($q,$s));
}

function pgetVar($q,$s){
    print getVar($q,$s);
}


function todefradio($s){
    if (!isset($GLOBALS[$s]))
	$GLOBALS[$s]='';
    if ($GLOBALS[$s]=='on')
	$GLOBALS[$s]='Y';
    else
	$GLOBALS[$s]='N';
}

function checkedIfY($qh,$field){
    if (getVar($qh,$field) == 'Y')
	print " CHECKED";
}

function pcomboVar($val, $actval, $toprint){
    print '<OPTION VALUE="'.encHTML($val).'"';
    if ($val==$actval)
	print ' SELECTED';
    print '>'.encHTML($toprint);
}

function encS($s){
    return addslashes($s);
}

function decS($s){
    return stripslashes($s);
}

function putGS($s){
    global $gs,$TOL_Language;
    $nr=func_num_args();
    if (!isset($gs[$s]) || ($gs[$s]==''))
	$my="$s (not translated)";
    else
        $my= $gs[$s];
    if ($nr>1)
        for ($i=1;$i<$nr;$i++){
		$name='$'.$i;
		$val=func_get_arg($i);
		$my=str_replace($name,$val,$my);
        }
    echo $my;
}

function getGS($s){
    global $gs,$TOL_Language;
    $nr=func_num_args();
    if (!isset($gs[$s]) || ($gs[$s]=='') )
	$my="$s (not translated)";
    else
        $my= $gs[$s];
    if ($nr>1)
        for ($i=1;$i<$nr;$i++){
		$name='$'.$i;
		$val=func_get_arg($i);
		$my=str_replace($name,$val,$my);
        }
    return  $my;
}

function regGS($key,$value){
    global $gs,$PHP_SELF,$TOL_Language;
    if (isset($gs[$key])) {
	if ($key!='')
		print "The global string is already set in $PHP_SELF: $key<BR>";
    }
    else{
	if (substr($value,strlen($value)-3)==":$TOL_Language"){
	    $value=substr($value,0,strlen($value)-3);
	}
	$gs[$key]=$value;
    }
}

function dSystem($s){
    print ("<BR>Executing <BR>$s<BR>");
    system($s);
}

function p($s){
    print $s;
}

function ifYthenCHECKED($q,$f){
    if (getVar($q,$f)=='Y')
	echo ' CHECKED';
}

function selectLanguageFile($path, $name){
    global $TOL_Language;
    if (!isset($TOL_Language)){
	$TOL_Language='en';
    }
    $loclang=$TOL_Language;
    return "$path/$name.$loclang.php";
}
?>