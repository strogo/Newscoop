<?php
/**
 * @package Campsite
 */

/**
 * Includes
 */
// We indirectly reference the DOCUMENT_ROOT so we can enable
// scripts to use this file from the command line, $_SERVER['DOCUMENT_ROOT']
// is not defined in these cases.
if (!isset($g_documentRoot)) {
    $g_documentRoot = $_SERVER['DOCUMENT_ROOT'];
}
require_once($g_documentRoot.'/db_connect.php');
require_once($g_documentRoot.'/classes/DatabaseObject.php');
require_once($g_documentRoot.'/classes/DbObjectArray.php');
require_once($g_documentRoot.'/classes/Log.php');
require_once($g_documentRoot.'/classes/Article.php');
require_once($g_documentRoot.'/classes/ArticleImage.php');
require_once('HTTP/Client.php');

/**
 * @package Campsite
 */
class Image extends DatabaseObject {
	var $m_keyColumnNames = array('Id');
	var $m_keyIsAutoIncrement = true;
	var $m_dbTableName = 'Images';
	var $m_columnNames = array(
		'Id',
		'Description',
		'Photographer',
		'Place',
		'Caption',
		'Date',
		'ContentType',
		'Location',
		'URL',
		'ThumbnailFileName',
		'ImageFileName',
		'UploadedByUser',
		'LastModified',
		'TimeCreated');

	/**
	 * An image is both the orginal image, plus a thumbnail image,
	 * plus metadata.
	 *
	 * @param int $p_imageId
	 */
	function Image($p_imageId = null)
	{
		parent::DatabaseObject($this->m_columnNames);
		$this->m_data['Id'] = $p_imageId;
		if ($this->keyValuesExist()) {
			$this->fetch();
		}
	} // constructor


	/**
	 * Update the image data in the database.
	 *
	 * @param array $p_columns
	 * @param boolean $p_commit
	 * @param boolean $p_isSql
	 * @return boolean
	 */
	function update($p_columns = null, $p_commit = true, $p_isSql = false)
	{
		$success = parent::update($p_columns, $p_commit, $p_isSql);
		if ($success) {
			if (function_exists("camp_load_translation_strings")) {
				camp_load_translation_strings("api");
			}
			$logtext = getGS('Changed image properties of $1', $this->m_data['Id']);
			Log::Message($logtext, null, 43);
		}
		return $success;
	} // fn update


	/**
	 * Delete the row from the database, all article references to this image,
	 * and the file(s) on disk.
	 *
	 * @return boolean
	 *		TRUE (1) if the record was deleted,
	 * 		On failed, return the sum of:
	 * 		-1 if the database query failed
	 * 		-2 if the image file could not be deleted
	 * 		-4 if the thumbnail could not be deleted
	 */
	function delete()
	{
		if (function_exists("camp_load_translation_strings")) {
			camp_load_translation_strings("api");
		}

		// Delete all the references to this image.
		ArticleImage::OnImageDelete($this->getImageId());

		// Delete the record in the database
		$errorMsgs = array();
		if (!parent::delete()) {
			$errorMsgs[] = getGS("Could not delete record from the database.");
		}

		// Delete the images from disk
		$thumb = $this->getThumbnailStorageLocation();
		if (file_exists($thumb) && is_file($thumb)) {
			if (!unlink($thumb)) {
	            $errorMsgs[] = getGS("Could not delete thumbnail file $1", $thumb);
			}
		}

		$imageFile = $this->getImageStorageLocation();
		if (file_exists($imageFile)	&& is_file($imageFile)) {
			if (!unlink($imageFile)) {
	            $errorMsgs[] = getGS("Could not delete image file $1", $imageFile);
			}
		}

		if (count($errorMsgs) == 0) {
			$logtext = getGS('Image $1 deleted', $this->m_data['Id']);
			Log::Message($logtext, null, 42);
			return 1;
		} else {
			return $errorMsgs;
		}
	} // fn delete


	/**
	 * Commit current values to the database.
	 * The values "TimeCreated" and "LastModified" are ignored.
	 *
	 * @return boolean
	 *		Return TRUE if the database was updated, false otherwise.
	 */
	function commit()
	{
		return parent::commit(array("TimeCreated", "LastModified"));
	} // fn commit


	/**
	 * Return true if the image is being used by an article.
	 *
	 * @return boolean
	 */
	function inUse()
	{
		global $g_ado_db;
		// It is in use only if there is an entry in both
		// the ArticleImages table and the Articles table.
		$queryStr = 'SELECT Number FROM Articles, ArticleImages '
					.' WHERE IdImage='.$this->getImageId()
					.' AND Articles.Number=ArticlesImage.NrArticle';
		if ($g_ado_db->GetOne($queryStr)) {
			return true;
		} else {
			return false;
		}
	} // fn inUse


	/**
	 * @return int
	 */
	function getImageId()
	{
		return $this->m_data['Id'];
	} // fn getImageId


	/**
	 * @return string
	 */
	function getDescription()
	{
		return $this->m_data['Description'];
	} // fn getDescription


	/**
	 * @return string
	 */
	function getPhotographer()
	{
		return $this->m_data['Photographer'];
	} // fn getPhotographer


	/**
	 * @return string
	 */
	function getPlace()
	{
		return $this->m_data['Place'];
	} // fn getPlace


	/**
	 * @return string
	 */
	function getDate()
	{
		return $this->m_data['Date'];
	} // fn getDate


	/**
	 * @return string
	 */
	function getLocation()
	{
		return $this->m_data['Location'];
	} // fn getLocation


	/**
	 * @return string
	 */
	function getUrl()
	{
		return $this->m_data['URL'];
	} // fn getUrl


	/**
	 * @return string
	 */
	function getContentType()
	{
		return $this->m_data['ContentType'];
	} // fn getContentType


	/**
	 * Return the full path to the image file.
	 * @return string
	 */
	function getImageStorageLocation()
	{
		global $Campsite;
		if ($this->m_data['Location'] == 'local') {
			return $Campsite['IMAGE_DIRECTORY'].$this->m_data['ImageFileName'];
		} else {
			return $this->m_data['URL'];
		}
	} // fn getImageStorageLocation


	/**
	 * Return the full path to the thumbnail file.
	 * @return string
	 */
	function getThumbnailStorageLocation()
	{
		global $Campsite;
		return $Campsite['THUMBNAIL_DIRECTORY'].$this->m_data['ThumbnailFileName'];
	} // fn getThumbnailStorageLocation


	/**
	 * Generate the full path to the thumbnail storage location on disk.
	 * @param string $p_fileExtension
	 *		The file extension for the filename.
	 * @return string
	 */
	function generateThumbnailStorageLocation($p_fileExtension)
	{
		global $Campsite;
	    $thumbnailStorageLocation = $Campsite['THUMBNAIL_DIRECTORY']
	    	.$Campsite['THUMBNAIL_PREFIX'].sprintf('%09d', $this->getImageId())
	    	.'.'.$p_fileExtension;
	    return $thumbnailStorageLocation;
	} // fn generateThumbnailStorageLocation


	/**
	 * Generate the full path to the image storage location on disk.
	 * @param string $p_fileExtension
	 *		The file extension for the filename.
	 * @return string
	 */
	function generateImageStorageLocation($p_fileExtension)
	{
		global $Campsite;
	    $imageStorageLocation = $Campsite['IMAGE_DIRECTORY']
	    	.$Campsite['IMAGE_PREFIX'].sprintf('%09d', $this->getImageId())
	    	.'.'.$p_fileExtension;
	    return $imageStorageLocation;
	} // fn generateImageStorageLocation


	/**
	 * Return the full URL to the image image.
	 * @return string
	 */
	function getImageUrl()
	{
		global $Campsite;
		if ($this->m_data['Location'] == 'local') {
			return $Campsite['IMAGE_BASE_URL'].$this->m_data['ImageFileName'];
		} else {
			return $this->m_data['URL'];
		}
	} // fn getImageUrl


	/**
	 * Get the full URL to the thumbnail image.
	 * @return string
	 */
	function getThumbnailUrl()
	{
		global $Campsite;
		return $Campsite['THUMBNAIL_BASE_URL'].$this->m_data['ThumbnailFileName'];
	} // fn getThumbnailUrl


	/**
	 * @return int
	 */
	function GetMaxId()
	{
		global $g_ado_db;
		$queryStr = 'SHOW TABLE STATUS LIKE "Images"';
		$result = $g_ado_db->GetRow($queryStr);
		return $result['Auto_increment'];
	} // fn GetMaxId


	/**
	 * @return int
	 */
	function GetTotalImages()
	{
		global $g_ado_db;
		$queryStr = 'SHOW TABLE STATUS LIKE "Images"';
		$result = $g_ado_db->GetRow($queryStr);
		return $result['Rows'];
	} // fn GetTotalImages


	function __ImageTypeToExtension($p_imageType)
	{
		$extension = '';
		switch($p_imageType) {
           case 1:  $extension = 'gif'; break;
           case 2:  $extension = 'jpg'; break;
           case 3:  $extension = 'png'; break;
           case 4:  $extension = 'swf'; break;
           case 5:  $extension = 'psd'; break;
           case 6:  $extension = 'bmp'; break;
           case 7:  $extension = 'tiff'; break;
           case 8:  $extension = 'tiff'; break;
           case 9:  $extension = 'jpc'; break;
           case 10: $extension = 'jp2'; break;
           case 11: $extension = 'jpx'; break;
           case 12: $extension = 'jb2'; break;
           case 13: $extension = 'swc'; break;
           case 14: $extension = 'aiff'; break;
           case 15: $extension = 'wbmp'; break;
           case 16: $extension = 'xbm'; break;
        }
        return $extension;
	}


	/**
	 * This function should be called when an image is uploaded.  It will save
	 * the image to the appropriate place on the disk, create a thumbnail for it,
	 * and create a database entry for the file.
	 *
	 * @param array $p_fileVar
	 *     <pre>
	 * 		The variable from the $_FILES array.  The array specifies the following:
	 *		$a["name"] = original name of the file.
	 * 		$a["type"] = the MIME type of the file, e.g. image/gif
	 *		$a["tmp_name"] = the temporary storage location on disk of the file
	 *		$a["size"] = size of the file, in bytes (not required)
	 *		$a["error"] = 0 (zero) if there was no error
	 *     </pre>
	 *
	 * @param array $p_attributes
	 *		Optional attributes which are stored in the database.
	 *		Indexes can be the following: 'Description', 'Photographer', 'Place', 'Date'
	 *
	 * @param int $p_userId
	 *		The user who uploaded the file.
	 *
	 * @param int $p_id
	 *		If the image already exists and we just want to update it, specify the
	 *		current image ID here.
	 *
	 * @return mixed
	 *		The Image object that was created or updated.
	 *		NULL if there was an error.
	 */
	function OnImageUpload($p_fileVar, $p_attributes, $p_userId = null, $p_id = null, $p_isLocalFile = false)
	{
		global $Campsite;
		if (function_exists("camp_load_translation_strings")) {
			camp_load_translation_strings("api");
		}

		if (!is_array($p_fileVar)) {
			return "Invalid arguments given to Image::OnImageUpload()";
		}

		// Verify its a valid image file.
		$imageInfo = @getimagesize($p_fileVar['tmp_name']);
		if ($imageInfo === false) {
			return getGS("The file is not recognized as an image.");
		}
		$extension = Image::__ImageTypeToExtension($imageInfo[2]);

		// Are we updating or creating?
	 	if (!is_null($p_id)) {
	 		// Updating the image
	 		$image =& new Image($p_id);
	 		$image->update($p_attributes);
	    	// Remove the old image & thumbnail because
			// the new file may have a different file extension.
			if (file_exists($image->getImageStorageLocation())) {
	    		unlink($image->getImageStorageLocation());
			}
			if (file_exists($image->getThumbnailStorageLocation())) {
	    		unlink($image->getThumbnailStorageLocation());
			}
	    } else {
	    	// Creating the image
	    	$image =& new Image();
	    	$image->create($p_attributes);
			$image->setProperty('TimeCreated', 'NULL', true, true);
			$image->setProperty('LastModified', 'NULL', true, true);
	    }
	    $image->setProperty('Location', 'local', false);
	    // If we are using PHP version >= 4.3
	    if (isset($imageInfo['mime'])) {
	    	$image->setProperty('ContentType', $imageInfo['mime'], false);
	    } else {
			$image->setProperty('ContentType', $p_fileVar['type'], false);
	    }
		if (!is_null($p_userId)) {
			$image->setProperty('UploadedByUser', $p_userId, false);
		}
        if (!isset($p_attributes['Date'])) {
        	$image->setProperty('Date', 'NOW()', true, true);
        }
	    $target = $image->generateImageStorageLocation($extension);
	    $thumbnail = $image->generateThumbnailStorageLocation($extension);
	    $image->setProperty('ImageFileName', basename($target), false);
	    $image->setProperty('ThumbnailFileName', basename($thumbnail), false);

	    if ($p_isLocalFile) {
	    	if (!copy($p_fileVar['tmp_name'], $target)) {
	        	if (is_null($p_id)) {
	        		$image->delete();
	        	}
	    		return getGS("Could not copy image file to directory $1", dirname($target));
	    	}
	    } else {
	        if (!move_uploaded_file($p_fileVar['tmp_name'], $target)) {
	        	if (is_null($p_id)) {
	        		$image->delete();
	        	}
	            return getGS("Could not copy image file to directory $1", dirname($target));
	        }
	    }
		chmod($target, 0644);
        if ($Campsite['IMAGEMAGICK_INSTALLED']) {
            $cmd = $Campsite['THUMBNAIL_COMMAND'].' '.$target.' '.$thumbnail;
            system($cmd);
            if (file_exists($thumbnail)) {
            	chmod($thumbnail, 0644);
            }
        }
        $image->commit();
		$logtext = getGS('The image $1 has been added.',
						$image->m_data['Description']." (".$image->m_data['Id'].")");
		Log::Message($logtext, null, 41);

        return $image;
	} // fn OnImageUpload


	/**
	 * Download the remote file and save it to disk, create a thumbnail for it,
	 * and create a database entry for the file.
	 *
	 * @param string $p_url
	 *		The remote location of the file. ("http://...");
	 *
	 * @param array $p_attributes
	 *		Optional attributes which are stored in the database.
	 *		Indexes can be the following: 'Description', 'Photographer', 'Place', 'Date'
	 *
	 * @param int $p_userId
	 *		The user ID of the user who uploaded the image.
	 *
	 * @param int $p_id
	 *		If you are updating an image, specify its ID here.
	 *
	 * @return mixed
	 * 		Return an Image object on success, return an error string otherwise.
	 */
	function OnAddRemoteImage($p_url, $p_attributes, $p_userId = null, $p_id = null)
	{
		global $Campsite;
	    $client =& new HTTP_Client();
	    $client->get($p_url);
	    $response = $client->currentResponse();
	    if ($response['code'] != 200) {
	    	return getGS("Unable to fetch image from remote server.");
	    }
	    foreach ($response['headers'] as $headerName => $value) {
	    	if (strtolower($headerName) == "content-type") {
	    		$ContentType = $value;
	    		break;
	    	}
	    }

        // Check content type
        if (!preg_match('/image/', $ContentType)) {
            // wrong URL
            return getGS('URL "$1" is invalid or is not an image.', $p_url);
        }

    	// Save the file
        $tmpname = $Campsite['TMP_DIRECTORY'].'img'.md5(rand());
        if ($tmphandle = fopen($tmpname, 'w')) {
            fwrite($tmphandle, $response['body']);
            fclose($tmphandle);
        } else {
            return getGS('Cannot create file "$1"', $tmpname);
        }

        // Check if it is really an image file
        $imageInfo = getimagesize($tmpname);
        if ($imageInfo === false) {
        	unlink($tmpname);
            return getGS('URL "$1" is not an image.', $cURL);
        }

        // content-type = image
        if (!is_null($p_id)) {
        	// Updating the image
        	$image =& new Image($p_id);
        	$image->update($p_attributes);
	    	// Remove the old image & thumbnail because
	    	// the new file might have a different file extension.
	    	if (file_exists($image->getImageStorageLocation())) {
	    		unlink($image->getImageStorageLocation());
	    	}
	    	if (file_exists($image->getThumbnailStorageLocation())) {
	    		unlink($image->getThumbnailStorageLocation());
	    	}
        } else {
        	// Creating the image
        	$image =& new Image();
        	$image->create($p_attributes);
        	$image->setProperty('TimeCreated', 'NULL', true, true);
        	$image->setProperty('LastModified', 'NULL', true, true);
        }
        if (!isset($p_attributes['Date'])) {
        	$image->setProperty('Date', 'NOW()', true, true);
        }
        $image->setProperty('Location', 'remote', false);
        $image->setProperty('URL', $p_url, false);
	    if (isset($imageInfo['mime'])) {
	    	$image->setProperty('ContentType', $imageInfo['mime'], false);
	    }

        // Remember who uploaded the image
        if (!is_null($p_userId)) {
			$image->setProperty('UploadedByUser', $p_userId, false);
        }

        if ($Campsite['IMAGEMAGICK_INSTALLED']) {
		    // Set thumbnail file name
		    $extension = Image::__ImageTypeToExtension($imageInfo[2]);
		    $thumbnail = $image->generateThumbnailStorageLocation($extension);
		    $image->setProperty('ThumbnailFileName', basename($thumbnail), false);

		    // Create the thumbnail
            $cmd = $Campsite['THUMBNAIL_COMMAND'].' '
            	. $tmpname . ' ' . $image->getThumbnailStorageLocation();
            system($cmd);
            if (file_exists($image->getThumbnailStorageLocation())) {
            	chmod($image->getThumbnailStorageLocation(), 0644);
            }
        }
        unlink($tmpname);
        $image->commit();

		if (function_exists("camp_load_translation_strings")) {
			camp_load_translation_strings("api");
		}
		$logtext = getGS('The image $1 has been added.',
						$image->m_data['Description']." (".$image->m_data['Id'].")");
		Log::Message($logtext, null, 41);

	    return $image;
	} // fn OnAddRemoteImage


	/**
	 * Get an array of users who have uploaded images.
	 * @return array
	 */
	function GetUploadUsers()
	{
		$tmpUser =& new User();
		$columnNames = $tmpUser->getColumnNames();
		$queryColumnNames = array();
		foreach ($columnNames as $columnName) {
			$queryColumnNames[] = 'Users.'.$columnName;
		}
		$queryColumnNames = implode(",", $queryColumnNames);
		$queryStr = 'SELECT DISTINCT Users.Id, '.$queryColumnNames
					.' FROM Images, Users WHERE Images.UploadedByUser = Users.Id';
		$users = DbObjectArray::Create('User', $queryStr);
		return $users;
	} // fn GetUploadUsers


	/**
	 * Fetch an image object by matching the URL.
	 * @param string $p_url
	 * @return Image
	 */
	function GetByUrl($p_url)
	{
		global $g_ado_db;
		$queryStr = "SELECT * FROM Images WHERE URL='".mysql_real_escape_string($p_url)."'";
		$row = $g_ado_db->GetRow($queryStr);
		$image =& new Image();
		$image->fetch($row);
		return $image;
	} // fn GetByUrl


	/**
	 * Return an array that can be used in a template.
	 *
	 * @return array
	 */
	function toTemplate()
	{
		$template = array();
		$template['id'] = $this->getImageId();
		$template['description'] = $this->getDescription();
		$template['photographer'] = $this->getPhotographer();
		$template['place'] = $this->getPlace();
		$template['date'] = $this->getDate();
		$template['content_type'] = $this->getContentType();
		$template['image_url'] = $this->getImageUrl();
		$template['thumbnail_url'] = $this->getThumbnailUrl();
		return $template;
	} // fn toTemplate

} // class Image
?>