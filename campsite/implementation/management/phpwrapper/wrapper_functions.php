<?php 
function detectModules($data, $in_forum)
{
    global $PARAMS, $Smarty; 

    $identifier = '/<!\-\-_([a-zA-Z0-9-_\/ ]*)\??([^<>]*)_\-\->/';

    foreach ($data as $lost=>$line) {
        $matchstr = array();
        $modoutput = array();
        
        if (preg_match_all($identifier, $line, $matches, PREG_SET_ORDER)) { 
            #print_r($matches);
            foreach($matches as $match) {
                #print_r($match);
                  
                if  (strlen(trim($match[1]))) { 
                    $matchstr[] = '/'.addcslashes($match[0], '/\\^$.[]|(){}?*+-').'/';
                    
                    $PARAMS = extractParams(trim($match[2]));                 
                    $Smarty->assign_by_ref('PARAMS', $PARAMS);
                    
                    ob_start();
                    runModule(trim($match[1]), $in_forum); 
                    $modoutput[] = ob_get_clean();              
                }
            }
            #print_r($matchstr);
            #print_r($modoutput);
            $line = preg_replace($matchstr, $modoutput, $line, 1);
        } 
        
        if (SUPPORT_TPL_PHP === true) {
        	eval ('?>'.$line);
        } else {
        	echo $line."\n";
        }
    }
}

function runModule($module, $in_forum)
{
    global $REQUEST, $PARAMS, $ParamStack, $SYS, $debug, $Smarty; 

    $ParamStack = array();
    
    if ($debug) {
        echo "<p><small>PARAMS: "; print_r($PARAMS); echo "</small></p>";
    }

    switch ($module) {
        case 'urlparameters': 
            print(arrToParamStr(filterCsParams($PARAMS)));
        break;     

        case "poll":
        ## displays the poll where you can click and see the bars
            include 'modules/poll/poll.php';
        break;

        case "poll-list":
            ## listing all linked polls
            include 'modules/poll/poll_list.php';
        break;

        case "voting":
            ## the small box to display with the article
            include 'modules/voting/voting.php';
        break;

        case "voting-list":
            ## displaying the list of all articles from the current issue
            $PARAMS['type'] = 'list';
            include 'modules/voting/voting.php';
        break;

        case "voting-stars":
            ## displaying the stars for the articles
            $PARAMS['type'] = "stars";
            include 'modules/voting/voting.php';
        break;
    } 
    #$Smarty->assign_by_ref($module, $$module);
}
?>