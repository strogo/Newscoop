<?php
/**
 * @package Campsite
 */

/**
 * Includes
 */
require_once($GLOBALS['g_campsiteDir'].'/db_connect.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/XR_CcClient.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Input.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Log.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Article.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/AudioclipXMLMetadata.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/AudioclipDatabaseMetadata.php');
require_once('HTTP/Client.php');


$metatagLabel = array(
    'dc:title' => 'Title',
    'dc:title' => 'Title',
    'dc:creator' => 'Creator',
    'dc:type' => 'Genre',
    'dc:format' => 'File format',
    'dcterms:extent' => 'Length',
    'dc:title' => 'Title',
    'dc:creator' => 'Creator',
    'dc:source' => 'Album',
    'ls:year' => 'Year',
    'dc:type' => 'Genre',
    'dc:description' => 'Description',
    'dc:format' => 'Format',
    'ls:bpm' => 'BPM',
    'ls:rating' => 'Rating',
    'dcterms:extent' => 'Length',
    'ls:encoded_by' => 'Encoded by',
    'ls:track_num' => 'Track number',
    'ls:disc_num' => 'Disc number',
    'ls:mood' => 'Mood',
    'dc:publisher' => 'Label',
    'ls:composer' => 'Composer',
    'ls:bitrate' => 'Bitrate',
    'ls:channels' => 'Channels',
    'ls:samplerate' => 'Sample rate',
    'ls:encoder' => 'Encoder software used',
    'ls:crc' => 'Checksum',
    'ls:lyrics' => 'Lyrics',
    'ls:orchestra' => 'Orchestra or band',
    'ls:conductor' => 'Conductor',
    'ls:lyricist' => 'Lyricist',
    'ls:originallyricist' => 'Original lyricist',
    'ls:radiostationname' => 'Radio station name',
    'ls:audiofileinfourl' => 'Audio file information web page',
    'ls:artisturl' => 'Artist web page',
    'ls:audiosourceurl' => 'Audio source web page',
    'ls:radiostationurl' => 'Radio station web page',
    'ls:buycdurl' => 'Buy CD web page',
    'ls:isrcnumber' => 'ISRC number',
    'ls:catalognumber' => 'Catalog number',
    'ls:originalartist' => 'Original artist',
    'dc:rights' => 'Copyright',
    'dc:title' => 'Title',
    'dcterms:temporal' => 'Report date/time',
    'dcterms:spatial' => 'Report location',
    'dcterms:entity' => 'Report organizations',
    'dc:description' => 'Description',
    'dc:creator' => 'Creator',
    'dc:subject' => 'Subject',
    'dc:type' => 'Genre',
    'dc:format' => 'Format',
    'dc:contributor' => 'Contributor',
    'dc:language' => 'Language',
    'dc:rights' => 'Copyright',
    'dc:title' => 'Title',
    'dc:creator' => 'Creator',
    'dcterms:extent' => 'Length',
    'dc:description' => 'Description',
    'ls:url'    => 'Stream URL',
    'ls:mtime' => 'Modified time'
);

$mask = array(
    'pages' => array(
        'Main'  => array(
            array(
                'element' => 'dc:title',
                'type' => 'text',
                'required' => TRUE,
            ),
            array(
                'element' => 'dc:creator',
                'type' => 'text',
                'required' => TRUE,
            ),
            array(
                'element'=> 'dc:type',
                'type' => 'text',
                'required' => TRUE,
            ),
            array(
                'element' => 'dc:format',
                'type' => 'select',
                'required' => TRUE,
                'options' => array(
                                'File' => 'Audioclip',
                                'live stream' => 'Webstream'
                               ),
                'attributes'=> array('disabled' => 'on'),
            ),
            array(
                'element' => 'dcterms:extent',
                'type' => 'text',
                'attributes' => array('disabled' => 'on'),
            ),
        ),
        'Music'  => array(
            array(
                'element' => 'dc:title',
                'type' => 'text',
            ),
            array(
                'element' => 'dc:creator',
                'type' => 'text',
            ),
            array(
                'element' => 'dc:source',
                'type' => 'text',
                'id3' => array('Album')
            ),
            array(
                'element' => 'ls:year',
                'type' => 'select',
                'options' => '', //_getNumArr(1900, date('Y')+5),
            ),
            array(
                'element' => 'dc:type',
                'type' => 'text',
            ),
            array(
                'element' => 'dc:description',
                'type' => 'textarea',
            ),
            array(
                'element' => 'dc:format',
                'type' => 'select',
                'options' => array(
                                'File' => 'Audioclip',
                                'live stream' => 'Webtream'
                               ),
                'attributes'=> array('disabled' => 'on'),
            ),
            array(
                'element' => 'ls:bpm',
                'type' => 'text',
                'rule' => 'numeric',
            ),
            array(
                'element' => 'ls:rating',
                'type' => 'text',
                'rule' => 'numeric',
            ),
            array(
                'element' => 'dcterms:extent',
                'type' => 'text',
                'attributes' => array('disabled' => 'on'),
            ),
            array(
                'element' => 'ls:encoded_by',
                'type' => 'text',
            ),
            array(
                'element' => 'ls:track_num',
                'type' => 'select',
                'options' => '', //_getNumArr(0, 99),
            ),
            array(
                'element' => 'ls:disc_num',
                'type' => 'select',
                'options' => '', //_getNumArr(0, 20),
            ),
            array(
                'element' => 'ls:mood',
                'type' => 'text',
            ),
            array(
                'element' => 'dc:publisher',
                'type' => 'text',
            ),
            array(
                'element' => 'ls:composer',
                'type' => 'text',
            ),
            array(
                'element' => 'ls:bitrate',
                'type' => 'text',
                'rule' => 'numeric',
            ),
            array(
                'element' => 'ls:channels',
                'type' => 'select',
                'options' => array(
                                '' => '',
                                1 => 'Mono',
                                2 => 'Stereo',
                                6 => '5.1'
                               ),
            ),
            array(
                'element' => 'ls:samplerate',
                'type' => 'text',
                'rule' => 'numeric',
                'attributes'=> array('disabled' => 'on'),
            ),
            array(
                'element' => 'ls:encoder',
                'type' => 'text',
            ),
            array(
                'element' => 'ls:crc',
                'type' => 'text',
                'rule' => 'numeric',
            ),
            array(
                'element' => 'ls:lyrics',
                'type' => 'textarea',
            ),
            array(
                'element' => 'ls:orchestra',
                'type' => 'text',
            ),
            array(
                'element' => 'ls:conductor',
                'type' => 'text',
            ),
            array(
                'element' => 'ls:lyricist',
                'type' => 'text',
            ),
            array(
                'element' => 'ls:originallyricist',
                'type' => 'text',
            ),
            array(
                'element' => 'ls:radiostationname',
                'type' => 'text',
            ),
            array(
                'element' => 'ls:audiofileinfourl',
                'type' => 'text',
                'attributes' => array('maxlength' => 256)
            ),
            array(
                'rule' => 'regex',
                'element' => 'ls:audiofileinfourl',
                'format' => '', //UI_REGEX_URL,
                'rulemsg' => 'Audio file information web page seems not to be valid URL'
            ),
            array(
                'element' => 'ls:artisturl',
                'type' => 'text',
                'attributes' => array('maxlength' => 256)
            ),
            array(
                'rule' => 'regex',
                'element' => 'ls:artisturl',
                'format' => '', //UI_REGEX_URL,
                'rulemsg' => 'Artist web page seems not to be valid URL'
            ),
            array(
                'element' => 'ls:audiosourceurl',
                'type' => 'text',
                'attributes' => array('maxlength' => 256)
            ),
            array(
                'rule' => 'regex',
                'element' => 'ls:audiosourceurl',
                'format' => '', //UI_REGEX_URL,
                'rulemsg' => 'Audio source web page seems not to be valid URL'
            ),
            array(
                'element' => 'ls:radiostationurl',
                'type' => 'text',
                'attributes' => array('maxlength' => 256)
            ),
            array(
                'rule' => 'regex',
                'element' => 'ls:radiostationurl',
                'format' => '', //UI_REGEX_URL,
                'rulemsg' => 'Radio station web page seems not to be valid URL'
            ),
            array(
                'element' => 'ls:buycdurl',
                'type' => 'text',
                'attributes' => array('maxlength' => 256)
            ),
            array(
                'rule' => 'regex',
                'element' => 'ls:buycdurl',
                'format' => '', //UI_REGEX_URL,
                'rulemsg' => 'Buy CD web page seems not to be valid URL'
            ),
            array(
                'element' => 'ls:isrcnumber',
                'type' => 'text',
                'rule' => 'numeric',
            ),
            array(
                'element' => 'ls:catalognumber',
                'type' => 'text',
                'rule' => 'numeric',
            ),
            array(
                'element' => 'ls:originalartist',
                'type' => 'text',
            ),
            array(
                'element' => 'dc:rights',
                'type' => 'text',
            ),
        ),
        'Voice' => array(
            array(
                'element' => 'dc:title',
                'type' => 'text',
            ),
            array(
                'element' => 'dcterms:temporal',
                'type' => 'text',
            ),
            array(
                'element' => 'dcterms:spatial',
                'type' => 'textarea',
            ),
            array(
                'element' => 'dcterms:entity',
                'type' => 'textarea',
            ),
            array(
                'element' => 'dc:description',
                'type' => 'textarea',
            ),
            array(
                'element' => 'dc:creator',
                'type' => 'text',
            ),
            array(
                'element' => 'dc:subject',
                'type' => 'text',
            ),
            array(
                'element' => 'dc:type',
                'type' => 'text',
            ),
            array(
                'element' => 'dc:format',
                'type' => 'select',
                'options' => array(
                                'File' => 'Audioclip',
                                'live stream' => 'Webstream'
                                ),
                'attributes'=> array('disabled' => 'on')
            ),
            array(
                'element' => 'dc:contributor',
                'type' => 'text',
            ),
            array(
                'element' => 'dc:language',
                'type' => 'text',
            ),
            array(
                'element' => 'dc:rights',
                'type' => 'text',
            ),
        )
    )
);


/**
 * @package Campsite
 */
class Audioclip {
    var $m_gunId = null;
    var $m_metaData = array();
    var $m_fileTypes = array('.mp3','.ogg','.wav');
    var $m_exists = false;


    /**
     * Constructor
     *
     * @param string $p_gunId
     *      The audioclip gunid
     */
    public function Audioclip($p_gunId = null)
    {
        if (!is_null($p_gunId)) {
            $aclipDbaseMdataObj = new AudioclipDatabaseMetadata($p_gunId);
            $this->m_metaData = $aclipDbaseMdataObj->fetch();
            if ($this->m_metaData == false || sizeof($this->m_metaData) == 0) {
                $aclipXMLMdataObj = new AudioclipXMLMetadata($p_gunId);
                $this->m_metaData = $aclipXMLMdataObj->m_metaData;
                if ($aclipXMLMdataObj->exists()) {
		    $this->m_gunId = $p_gunId;
		    $this->m_exists = true;
		    $aclipDbaseMdataObj->create($this->m_metaData);
                }
            } else {
	        $this->m_gunId = $p_gunId;
    	        $this->m_exists = true;
            }
        }
    } // constructor


    /**
     * Returns whether the audioclip exists
     *
     * @return boolean
     *      TRUE on success, FALSE on failure
     */
    public function exists()
    {
        return $this->m_exists;
    } // fn exists


    /**
     * Returns the unique identifier value for the audioclip
     *
     * @return string
     *      The audioclip global unique identifier
     */
    public function getGunId()
    {
    	return $this->m_gunId;
    } // fn getGunId


    /**
     * Returns the value of the given meta tag
     *
     * @param string $p_tagName
     *      The name of the meta tag
     *
     * @return string
     *      The meta tag value
     */
    public function getMetatagValue($p_tagName)
    {
    	$namespaces = array('dc', 'ls', 'dcterms');

	$p_tagName = trim(strtolower($p_tagName));
    	if (is_null($this->m_gunId) || sizeof($this->m_metaData) == 0) {
	    return null;
    	}
    	$splitPos = strpos($p_tagName, ':');
    	if ($splitPos !== false) {
	    $tagNs = substr($p_tagName, 0, $splitPos);
	    if (array_search($tagNs, $namespaces) === false) {
	        return PEAR_Error::PEAR_Error("Invalid metatag namespace.");
	    }
	    if (!array_key_exists($p_tagName, $this->m_metaData)) {
	        return null;
	    }
	    return $this->m_metaData[$p_tagName]->getValue();
    	}
    	foreach ($namespaces as $namespace) {
	    $tag = $namespace . ':' . $p_tagName;
	    if (array_key_exists($tag, $this->m_metaData)) {
	        return $this->m_metaData[$tag]->getValue();
	    }
    	}
    	return null;
    } // fn getMetaTagValue


    /**
     * Returns an array containing available meta tags
     *
     * @return array
     *      The available meta tags for the audioclip
     */
    public function getAvailableMetaTags()
    {
    	if (is_null($this->m_gunId) || sizeof($this->m_metaData) == 0) {
    		return null;
    	}
    	return array_keys($this->m_metaData);
    } // fn getAvailableMetaTags


    /**
     * Deletes all the metadata for the audioclip.
     * It checks whether the audioclip is attached to multiple articles
     * before deletes metadata.
     *
     * @return boolean
     *      TRUE on success, FALSE on failure
     */
    public function deleteMetadata()
    {
        if (is_null($this->m_gunId)) {
            return false;
        }
        $aclipDbaseMdataObj = new AudioclipDatabaseMetadata($this->m_gunId);
        if ($aclipDbaseMdataObj->inUse() == false) {
            return $aclipDbaseMdataObj->delete();
        }
        return true;
    } // fn deleteMetadata


    /**
     * Changes audioclip metadata on both storage and local servers.
     *
     * @param array $p_formData
     *      The form data submitted with all the audioclip metadata
     *
     * @return boolean|PEAR_Error
     *      TRUE on success, PEAR Error on failure
     */
    public function editMetadata($p_formData)
    {
        global $mask;

        if (!is_array($p_formData)) {
            return new PEAR_Error(getGS('Invalid parameter given to Audioclip::editMetadata()'));
        }

        $metaData = array();
        foreach ($mask['pages'] as $key => $val) {
            foreach ($mask['pages'][$key] as $k => $v) {
                $element_encode = str_replace(':','_',$v['element']);
                if ($p_formData['f_'.$key.'_'.$element_encode]) {
                    list($predicate_ns, $predicate) = explode(':', $v['element']);
                    $recordSet['gunid'] = $this->m_gunId;
                    $recordSet['predicate_ns'] = $predicate_ns;
                    $recordSet['predicate'] = $predicate;
                    $recordSet['object'] = $p_formData['f_'.$key.'_'.$element_encode];
                    $tmpMetadataObj = new AudioclipMetadataEntry($recordSet);
                    $metaData[strtolower($v['element'])] = $tmpMetadataObj;
                }
            }
        }

        if (sizeof($metaData) == 0) return false;

        $aclipXMLMdataObj = new AudioclipXMLMetadata($this->m_gunId);
        if ($aclipXMLMdataObj->update($metaData) == false) {
            return new PEAR_Error(getGS('Cannot update audioclip metadata on storage server'));
        }
        $aclipDbaseMdataObj = new AudioclipDatabaseMetadata($this->m_gunId);
        if ($aclipDbaseMdataObj->update($metaData) == false) {
            return new PEAR_Error(getGS('Cannot update audioclip metadata on Campsite'));
        }
        return true;
    } // fn editMetadata


    /**
     * This function should be called when an audioclip is uploaded.
     * It will save the audioclip file to the temporary directory on
     * the disk before to be sent to the Campcaster storage server.
     *
     * @param array $p_fileVar
     *      The audioclip file submited
     *
     * @return string|PEAR_Error
     *      The full pathname to the file or Error
     */
    public function onFileUpload($p_fileVar)
    {
        global $Campsite;

        if (!is_array($p_fileVar)) {
	    return false;
	}

	$filesize = filesize($p_fileVar['tmp_name']);
	if ($filesize === false) {
	    return new PEAR_Error("Audioclip::OnFileUpload(): invalid parameters received.");
	}
	if (get_magic_quotes_gpc()) {
	    $fileName = stripslashes($p_fileVar['name']);
	} else {
	    $fileName = $p_fileVar['name'];
	}
        if ($this->isValidFileType($fileName) == FALSE) {
            return new PEAR_Error("Audioclip::OnFileUpload(): invalid file type.");
        }
        $target = $Campsite['TMP_DIRECTORY'] . $fileName;
        if (!move_uploaded_file($p_fileVar['tmp_name'], $target)) {
            return new PEAR_Error(camp_get_error_message(CAMP_ERROR_CREATE_FILE, $target), CAMP_ERROR_CREATE_FILE);
        }
        chmod($target, 0644);
        return $target;
    } // fn onFileUpload


    /**
     * Validates an audioclip file by its extension.
     *
     * @param $p_fileName
     *      The name of the audioclip file
     *
     * @return boolean
     *      TRUE on success, FALSE on failure
     */
    public function isValidFileType($p_fileName)
    {
        foreach ($this->m_fileTypes as $t) {
            if (preg_match('/'.str_replace('/', '\/', $t).'$/i', $p_fileName))
                return true;
        }
        return false;
    } // fn isValidFileType


    /**
     * Retrieve a list of Audioclip objects based on the given constraints
     *
     * @param array $conditions
     *      array of struct with fields:
     *          cat: string - metadata category name
     *          op: string - operator, meaningful values:
     *              'full', 'partial', 'prefix',
     *              '=', '<', '<=', '>', '>='
     *          val: string - search value
     * @param string $operator
     *      type of conditions join (any condition matches /
     *      all conditions match), meaningful values: 'and', 'or', ''
     *      (may be empty or ommited only with less then 2 items in
     *      "conditions" field)
     * @param int $limit
     *      limit for result arrays (0 means unlimited)
     * @param int $offset
     *      starting point (0 means without offset)
     * @param string $orderby
     *      string - metadata category for sorting (optional) or array
     *      of strings for multicolumn orderby
     *      [default: dc:creator, dc:source, dc:title]
     * @param bool $desc
     *      boolean - flag for descending order (optional) or array of
     *      boolean for multicolumn orderby (it corresponds to elements
     *      of orderby field)
     *      [default: all ascending]
     *
     * @return array
     *      Array of Audioclip objects
     */
    public static function SearchAudioclips($offset = 0, $limit = 0,
                                            $conditions = array(),
                                            $operator = 'and',
					    $orderby = 'dc:creator, dc:source, dc:title',
                                            $desc = false)
    {
        global $mdefs;

        $xrc = XR_CcClient::Factory($mdefs);
	if (PEAR::isError($xrc)) {
	    return $xrc;
	}
        $sessid = camp_session_get(CS_CAMPCASTER_SESSION_VAR_NAME, '');
	$criteria = array('filetype' => 'audioclip',
			  'operator' => $operator,
			  'limit' => $limit,
			  'offset' => $offset,
			  'orderby' => $orderby,
			  'desc' => $desc,
			  'conditions' => $conditions
			  );
	$result = $xrc->xr_searchMetadata($sessid, $criteria);
	if (PEAR::isError($result)) {
	    return $result;
	}
	$clips = array();
	foreach ($result['results'] as $clipMetaData) {
	    $clip = new Audioclip($clipMetaData['gunid']);
	    if ($clip->exists()) {
	        $clips[] = $clip;
	    }
	}
    	return array($result['cnt'], $clips);
    } // fn SearchAudioclips


    /**
     * Retrieve a list of values of the give category that meet the given constraints
     *
     * @param string $p_category
     *
     * @param array $conditions
     *      array of struct with fields:
     *          cat: string - metadata category name
     *          op: string - operator, meaningful values:
     *              'full', 'partial', 'prefix',
     *              '=', '<', '<=', '>', '>='
     *          val: string - search value
     * @param string $operator
     *      type of conditions join (any condition matches /
     *      all conditions match), meaningful values: 'and', 'or', ''
     *      (may be empty or ommited only with less then 2 items in
     *      "conditions" field)
     * @param int $limit
     *      limit for result arrays (0 means unlimited)
     * @param int $offset
     *      starting point (0 means without offset)
     * @param string $orderby
     *      string - metadata category for sorting (optional) or array
     *      of strings for multicolumn orderby
     *      [default: dc:creator, dc:source, dc:title]
     * @param bool $desc
     *      boolean - flag for descending order (optional) or array of
     *      boolean for multicolumn orderby (it corresponds to elements
     *      of orderby field)
     *      [default: all ascending]
     *
     * @return array
     *      Array of Audioclip objects
     */
    public static function BrowseCategory($p_category, $offset = 0, $limit = 0,
					  $conditions = array(),
                                          $operator = 'and',
					  $orderby = 'dc:creator, dc:source, dc:title',
                                          $desc = false)
    {
        global $mdefs;

        $xrc = XR_CcClient::Factory($mdefs);
	if (PEAR::isError($xrc)) {
	    return $xrc;
	}
        $sessid = camp_session_get(CS_CAMPCASTER_SESSION_VAR_NAME, '');
	$criteria = array('filetype' => 'audioclip',
			  'operator' => $operator,
			  'limit' => $limit,
			  'offset' => $offset,
			  'orderby' => $orderby,
			  'desc' => $desc,
			  'conditions' => $conditions
			  );
	return $xrc->xr_browseCategory($sessid, $p_category, $criteria);
    } // fn BrowseCategory


    /**
     * Stores the Audioclip into the Campcaster storage server.
     *
     * @param string $p_sessId
     *      The session Id
     * @param string $p_filePath
     *      The full path name to the audioclip
     * @param array $p_formData
     *      Array of form data submited
     *
     * @return int|PEAR_Error
     *      The gunid on success, PEAR Error on failure
     */
    public static function StoreAudioclip($p_sessId, $p_filePath, $p_metaData)
    {
        if (file_exists($p_filePath) == false) {
            return new PEAR_Error(getGS('File $1 does not exist', $p_fileName));
        }
        $gunId = null;
        $checkSum = md5_file($p_filePath);
        $xmlString = Audioclip::CreateXMLTextFile($p_metaData);
        $gunId = AudioclipXMLMetadata::Upload($p_sessId, $p_filePath, $gunId,
                                              $xmlString, $checkSum);
        if (PEAR::isError($gunId)) {
            return $gunId;
        }

        $audioclipXMLMetadata = new AudioclipXMLMetadata($gunId);
        $audioclipDbMetadata = new AudioclipDatabaseMetadata();
        $audioclipDbMetadata->create($audioclipXMLMetadata->m_metaData);
        return $gunId;
    } // fn StoreAudioclip


    /**
     * Use getid3 to retrieve all the metatags for the given audio file.
     *
     * @param string $p_file
     *      The file to analyze
     *
     * @return array
     *      An array with all the id3 metatags
     */
    public static function AnalyzeFile($p_file)
    {
        require_once($GLOBALS['g_campsiteDir'].'/include/getid3/getid3.php');

        $getid3Obj = new getID3;
        return $getid3Obj->analyze($p_file);
    } // fn AnalyzeFile


    /**
     * This function should be called when an audioclip has been
     * successfully sent to the Storage server. It deletes the
     * temporary audio file on Local.
     *
     * @param string $p_fileName
     *      The temporary file to delete after stored in the Storage server
     */
    public static function OnFileStore($p_fileName)
    {
        if (file_exists($p_fileName)) {
            @unlink($p_fileName);
        }
    } // fn OnFileStore


    /**
     * Create a XML text file from an array coming from the Audioclip
     * metadata edit form submited.
     *
     * @param array $p_formData
     *      The form data submited
     *
     * @return string $xmlTextFile
     *      The XML string
     */
    public static function CreateXMLTextFile($p_metaData)
    {
        global $mask;

        $xmlTextFile = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n"
	    ."<audioClip>\n"
	    ."\t<metadata\n"
	    ."\t\txmlns=\"http://mdlf.org/campcaster/elements/1.0/\"\n"
	    ."\t\txmlns:ls=\"http://mdlf.org/campcaster/elements/1.0/\"\n"
	    ."\t\txmlns:dc=\"http://purl.org/dc/elements/1.1/\"\n"
	    ."\t\txmlns:dcterms=\"http://purl.org/dc/terms/\"\n"
	    ."\t\txmlns:xml=\"http://www.w3.org/XML/1998/namespace\"\n"
	    ."\t>\n";

        foreach($p_metaData as $key => $val) {
	    $xmlTextFile .= "\t\t" . XML_Util::createTag($key, array(), $val) . "\n";
        }
        $xmlTextFile .= "\t</metadata>\n</audioClip>\n";
        return $xmlTextFile;
    } // fn CreateXMLTextFile

} // class Audioclip

?>