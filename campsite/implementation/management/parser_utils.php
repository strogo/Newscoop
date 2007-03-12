<?php

require_once($_SERVER['DOCUMENT_ROOT'].'/include/pear/XML/Util.php');

global $DEBUG;

$DEBUG = false;

if (!function_exists('array_walk_recursive')){
   require_once 'PHP/Compat/Function/array_walk_recursive.php';
}
	
/**
 * Send the request message to the template engine and print the result to output.
 *
 * @return void
 */
function camp_send_request_to_parser($p_env_vars, $p_parameters, $p_cookies)
{
	camp_debug_msg("request method: " . getenv("REQUEST_METHOD"));

	$msg = camp_create_url_request_message($p_env_vars, $p_parameters, $p_cookies);
	for ($i = 1; $i <= 3; $i++) {
		$size_read = camp_wrap_parser_output(camp_send_message_to_parser($msg));
		if ($size_read > 0) {
			break;
		}
		usleep(1000000);
	}
}


/**
 * Start the campsite parser daemon.
 *
 * @return void
 */
function camp_start_parser()
{
	global $Campsite;

	$binFile = $Campsite['BIN_DIR'] . "/campsite_server";
	$args = " -i " . $Campsite['DATABASE_NAME'];
	if (!file_exists($binFile)) {
		$p_output[] = "Can't find the campsite_server binary; please check your Campsite install.";
		return -1;
	}
	$childOutput = popen("$binFile$args", "r");
	usleep(500000);
	pclose($childOutput);
} // fn camp_start_parser


/**
 * Stop the campsite parser daemon.
 *
 * @return void
 */
function camp_stop_parser()
{
	global $Campsite;

	$instanceName = $Campsite['DATABASE_NAME'];
	$cmd = "ps -o pid=pid,cmd=command -C campsite_server";
	exec($cmd, $output, $returnValue);
	foreach ($output as $line) {
		$line = trim($line);
		if (strncmp($line, "pid", 3) == 0) {
			continue;
		}
		$elements = explode(" ", $line);
		$elements = array_map('trim', $elements);
		for ($i = 0; $i < sizeof($elements); $i++) {
			if ($i == 0) {
				$processId = $elements[$i];
			}
			if ($elements[$i] == '-i') {
				$currentInstance = $elements[$i + 1];
				if ($instanceName == $currentInstance) {
					return posix_kill($processId, 15);
				}
				break;
			}
		}
	}
	return true;
} // camp_stop_parser


/**
 * Send a message to the campsite parser.  Note to third-party developers:
 * use the ParserCom class for this.  These will be merged together in the
 * future.
 *
 * @param string $p_msg
 * @param boolean $p_closeSocket
 * @return mixed
 * 		If $p_closeSocket is TRUE, return NULL, if it is FALSE, return
 * 		a socket.
 */
function camp_send_message_to_parser($p_msg, $p_closeSocket = false)
{
	global $Campsite;

	camp_debug_msg("URL request message:");
	camp_debug_msg("<pre>\n" . htmlspecialchars($p_msg) . "</pre>", false);

	$size = sprintf("%04x", strlen($p_msg));
	camp_debug_msg("size: " . $size);
	camp_debug_msg("parser port: " . $Campsite['PARSER_PORT']);

	$errno = 0;
	$errstr = "";
	for ($i = 1; $i <= 3; $i++) {
		@$socket = fsockopen('127.0.0.1', $Campsite['PARSER_PORT'], $errno, $errstr, 30);
		if (!$socket) {
			camp_debug_msg("Can't open parser socket: restaring the parser...");
			camp_start_parser();
		} else {
			camp_debug_msg("Success opening connection to the parser.");
			break;
		}
	}
	if (!$socket) {
		camp_debug_msg("Can't open parser socket after 3 restarts. Giving up...");
		exit(0);
	}
	$final_msg = "0001 $size $p_msg";
	camp_debug_msg("final msg size: " . strlen($final_msg));
	$size_wrote = fwrite($socket, $final_msg);
	camp_debug_msg("wrote: $size_wrote");

	if ($p_closeSocket) {
		fclose($socket);
		return NULL;
	}
	return $socket;
} // fn camp_send_message_to_parser


/**
 * Read a response from the parser and discard it.
 *
 * @param socket $p_socket
 * @return int
 * 		The number of bytes read.
 */
function camp_read_parser_output($p_socket)
{
	$size_read = 0;
	stream_set_timeout($p_socket, 10);
	do {
		$str = fread($p_socket, 1000);
		$size_read += strlen($str);
		echo $str;
	} while ($str != "");
	fclose($p_socket);
	camp_debug_msg("size read: $size_read");
	return $size_read;
} // fn camp_read_parser_output

function camp_wrap_parser_output($p_socket)
{
	$size_read = 0;
	stream_set_timeout($p_socket, 10);
	
	do {
		$char = fread($p_socket, 1);
		if ($char !== "\n") { 
		   $line .= $char;    
		} else {
	       $size_read += strlen($line);
    	   $output .= $line."\n"; 
    	   $line = '';   
		}
	} while ($char != "");
	detectModules(&$output, false);
	unset($output);
	fclose($p_socket);
	camp_debug_msg("size read: $size_read");
	return $size_read;          
}


function camp_xmlescape($p_message)
{
	return htmlspecialchars($p_message);
} // fn camp_xmlescape


/**
 * Create a request message to be sent to the parser.
 *
 * @param array $p_envVars
 * @param array $p_parameters
 * @param array $p_cookies
 * @return unknown
 */
function camp_create_url_request_message($p_envVars, $p_parameters, $p_cookies)
{
	$data = array(
				  'HTTPHost' => $p_envVars['HTTP_HOST'],
				  'DocumentRoot' => $p_envVars['DOCUMENT_ROOT'],
				  'RemoteAddress' => $p_envVars['REMOTE_ADDR'],
				  'PathTranslated' => $p_envVars['PATH_TRANSLATED'],
				  'RequestMethod' => $p_envVars['REQUEST_METHOD'],
				  'RequestURI' => $p_envVars['REQUEST_URI'],
				  'ServerPort' => $p_envVars['SERVER_PORT']
				 );

	$xmlMessage = "<CampsiteMessage MessageType=\"URLRequest\">\n";
	foreach ($data as $paramName => $paramValue) {
		$xmlMessage .= "\t" . XML_Util::createTag($paramName, array(), $paramValue) . "\n";
	}
	if (sizeof($p_parameters) > 0) {
		$xmlMessage .= "\t<Parameters>\n";
		foreach ($p_parameters as $paramName => $paramValue) {
			$attributes = array("Name"=>$paramName, "Type"=>"string");
			$xmlMessage .= "\t\t" . XML_Util::createTag("Parameter",
														$attributes,
														$paramValue) . "\n";
		}
		$xmlMessage .= "\t</Parameters>\n";
	} else {
		$xmlMessage .= "\t<Parameters />\n";
	}
	if (sizeof($p_cookies) > 0) {
		$xmlMessage .= "\t<Cookies>\n";
		foreach ($p_cookies as $cookieName => $cookieValue) {
			$attributes = array("Name"=>$cookieName);
			$xmlMessage .= "\t\t" . XML_Util::createTag("Cookie",
														$attributes,
														$cookieValue) . "\n";
		}
		$xmlMessage .= "\t</Cookies>\n";
	} else {
		$xmlMessage .= "\t<Cookies />\n";
	}
	$xmlMessage .= "</CampsiteMessage>\n";
	return $xmlMessage;
}


/**
 * @param string $p_queryString
 */
function camp_read_parameters(&$p_queryString, $p_decodeURL = false)
{
	switch (getenv("REQUEST_METHOD")) {
	case "GET":
		return camp_read_get_parameters($p_queryString, $p_decodeURL);
		break;
	case "POST":
		return camp_read_post_parameters($p_queryString, $p_decodeURL);
		break;
	default:
		echo "<p>Unable to process " . getenv("REQUEST_METHOD") . " request method</p>";
		exit(0);
	}
} // fn camp_read_parameters


/**
 * @param string $p_queryString
 */
function camp_read_get_parameters(&$p_queryString, $p_decodeURL = false)
{
	if ($p_queryString == "") {
		$p_queryString = getenv("QUERY_STRING");
	}

	parse_str($p_queryString, $parameters);	
	array_walk_recursive(&$parameters, 'camp_urldecode_array');
	
	camp_filter_wrapper_parameters(&$parameters);

	return $parameters;
} // fn camp_read_get_parameters

/**
 * @param string $p_key
 * @param string $p_value
 */
function camp_urldecode_array($p_key, $p_value)
{
    return array(urldecode($p_key) => urldecode($p_value));    
} // fn camp_urldecode_array

/**
 * @param array $p_parameters
 */
function camp_filter_wrapper_parameters(&$p_parameters)
{
    global $REQUEST, $URLPARAMS; 
    
    foreach($p_parameters as $key => $value) {
        if (!array_key_exists($key, $URLPARAMS)) { 
            $REQUEST[$key] = $value; 
            unset($p_parameters[$key]);   
        }   
    } 
}

/**
 * @param string $p_arrayItem
 * @param string $p_key
 */
function camp_stripslashes_callback(&$p_arrayItem, $p_key)
{
	$p_arrayItem = stripslashes($p_arrayItem);
	return true;
} 


/**
 * @param string $p_queryString
 */
function camp_read_post_parameters(&$p_queryString, $p_decodeURL = false)
{
	global $_POST;
	$query_string = file_get_contents("php://stdin");
	if (trim($query_string) == "" && isset($_POST) && is_array($_POST)) {
		$copyOfPost = $_POST;
		if (get_magic_quotes_gpc()) {
			array_walk_recursive($copyOfPost, 'camp_stripslashes_callback');
		}
        camp_filter_wrapper_parameters(&$copyOfPost);
		return $copyOfPost;
	}
	return camp_read_get_parameters($query_string, $p_decodeURL);
} // fn camp_read_post_parameters


/**
 * @param string $p_cookiesString
 */
function camp_read_cookies(&$p_cookiesString)
{
	if ($p_cookiesString == "") {
		$p_cookiesString = getenv("HTTP_COOKIE");
	}
	$cookies = array();
	$pairs = explode(";", $p_cookiesString);
	foreach ($pairs as $pair) {
		$pair_array = explode("=", $pair);
		if (trim($pair_array[0]) != "") {
			$cookies[trim($pair_array[0])] = trim($pair_array[1]);
		}
	}
	return $cookies;
} // fn camp_read_cookies


/**
 * TODO: merge this into the Language class.
 *
 * @param unknown_type $p_document_root
 */
function camp_create_language_links($p_document_root = "")
{
	global $Campsite;

	$document_root = $p_document_root != "" ? $p_document_root : $_SERVER['DOCUMENT_ROOT'];
	require_once("$document_root/database_conf.php");

	// connect to database to read the image file name
	if (!mysql_connect($Campsite['DATABASE_SERVER_ADDRESS'], $Campsite['DATABASE_USER'], $Campsite['DATABASE_PASSWORD'])) {
		exit(0);
	}
	if (!mysql_select_db($Campsite['DATABASE_NAME'])) {
		exit(0);
	}

	if (!$res = mysql_query("select Code from Languages")) {
		exit(0);
	}
	$index_file = "$document_root/index.php";
	while (($row = mysql_fetch_array($res)) != null) {
		$lang_code = $row["Code"];
		$link = "$document_root/$lang_code.php";
		if (file_exists($link) && !is_link($link)) {
			unlink($link);
		}
		if (!is_link($link)) {
			symlink($index_file, $link);
		}
		chown($link, $Campsite['APACHE_USER']);
		chgrp($link, $Campsite['APACHE_GROUP']);
	}
} // fn camp_create_language_links


function camp_debug_msg($msg, $format_html = true)
{
	global $DEBUG;

	if (!$DEBUG) {
		return;
	}

	if ($format_html) {
		echo "<p>";
	}

	echo $msg;
	if ($format_html) {
		echo "</p>\n";
	}
} // fn camp_debug_msg

?>
