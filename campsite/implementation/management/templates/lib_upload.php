<?
    function bufferFilesystemResult($s,$level=0){
        global $FSresult,$FSbufferImgBase;
        $img="<img src=$FSbufferImgBase/result$level.gif>";
        if ($level==1)
            $s="$img <span class=fileerror>$s</span>";
        else
            $s="$img $s";
        $FSresult.=($s."<BR><img src=$FSbufferImgBase/v.gif height=5><BR>");
    }


    function eliminLB($v,$enters="false"){
        $v=str_replace("&","&amp;",$v);
        $v=str_replace("<","&lt;",$v);
        $v=str_replace(">","&gt;",$v);
        $v=str_replace("\"","&quot;",$v);
        if ($enters=="true") $v=str_replace("\n","<BR>\n",$v);
        return $v;
    }


    function printDH($t){
        global $debugLevelHigh;
        if ($debugLevelHigh){
            $t=eliminLB($t);
            print"<SPAN class=debugh>$t</SPAN>\n";
        }
    }
    function printDL($t){
        global $debugLevelLow;
        if ($debugLevelLow){
            $t=eliminLB($t);
            print"<SPAN class=debugl>$t</SPAN>\n";
            }
    }
    function printI($t){
        print"<SPAN class=info>$t</SPAN>";
    }


	function doUpload($fileNameStr,$tmpUploadPath,$tmpName,$baseupload,$desiredName=null){
	    //$uploaded,$fninForm
		$success=true; 
		//global $baseupload;
		$fileName=$GLOBALS["$fileNameStr"];
		$file =	basename($fileName);
		printDL("The distant filename:$fileName");
		printDL( "New uploaded temporary filename=$tmpUploadPath/$file");
		$newFile = $tmpUploadPath.$tmpName; 
	
		if ( $fileName == "none" ) { 
            bufferFilesystemResult('You didn\'t specified a file for	uploading.',2);
			$success=false;	
			return;	
		} 
		if (!copy($tmpUploadPath.$file,	$newFile)) { 
            bufferFilesystemResult("Failed to copy temporary $file to $newFile",1);
			$success=false;
			return;	
		} 
		else{
			$uploaded=$newFile;
		}
	
		if ($success){
			$fninForm=$GLOBALS["$fileNameStr"."_name"];
			printDL("The filename in the form:$fninForm");
			printDH("New file at: $uploaded");
			
			$dotpos=strrpos($fninForm,".");
			$name=substr($fninForm,0,$dotpos);
			$ext=substr($fninForm,$dotpos+1);
			
			if ($desiredName!=null) $fninForm="$desiredName.$ext";
			// strip out the &, because when transmitting filename list over the todolist,
			// the & sign will be interpreted as separator, and this will destroy the
			// consistency of the todolist
			$fninForm=str_replace('&','',$fninForm);
			$fninForm=str_replace(' ','',$fninForm);
			$newname="$baseupload/".$fninForm;
			$oldname=$uploaded;
			printDH("Rename	from: $oldname to $newname");
			$renok=rename($oldname,$newname);
			printDL("Renaming result:$renok");
	
			if ($renok==true){
                bufferFilesystemResult("$fninForm : the	upload was successful !");
			}
			else{
                bufferFilesystemResult("$fninForm : the	file already exists !",1);
			}
		}
		else
        bufferFilesystemResult("Image upload not performed!",2);
		$ret["success"]=$success;
		$ret["newname"]=$fninForm;
	    return $ret;
    }

	
?>