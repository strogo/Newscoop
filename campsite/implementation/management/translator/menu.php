<?
    

function parseFolder($dirname, $depth){
    $handle=opendir($dirname);
    $space=3;
    
    while (($file = readdir($handle))!==false) {
        $fullname=$dirname."/".$file;
        $filetype=filetype($fullname);
        $isdir=false;
        $isfile=false;
        // avoiding the links
        if ($filetype=="dir") $isdir=true;
        else if ($filetype!="link") $isfile=true;
        // if it's a file
        if ($isfile){
            // filling the array
            $files[]=$file;
        }
        // if it's a directory but not the .. or .
        else if ($isdir&&$file!="."&&$file!=".."){
            // filling the array
            $dirs[]=$file;
        }
    }
    if (isset($files)){
        for($i=0;$i<count($files);$i++){
	    $filen=$files[$i];
	    if ( ( (strpos($filen,'locals')===0) ||
	           (strpos($filen,'globals')===0) )
	       &&
	       (substr($filen,strlen($filen)-4)==='.php')
	       &&
	       (substr($filen,strlen($filen)-6)!='en.php')
	       ){
		
	       print str_repeat(' ',$depth*$space)."<a href='display.php?file=$filen&dir=$dirname' target=panel>$filen</a>\n";
	    }
        }
    }
    if (isset($dirs)){
	for($i=0;$i<count($dirs);$i++){
	    print str_repeat(' ',$depth*$space).strtoupper($dirs[$i])."\n";
	    parseFolder("$dirname/".$dirs[$i],$depth+1);
        }
    }
}

print '<PRE>';
parseFolder('..', 0);
print '</PRE>';
?>