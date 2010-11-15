<?php
/**
 * @package Campsite
 */

/**
 * Includes
 */
require_once($GLOBALS['g_campsiteDir'].'/db_connect.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/DatabaseObject.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/DbObjectArray.php');

/**
 * @package Campsite
 */
class AuthorBiography extends DatabaseObject
{
    var $m_dbTableName = 'AuthorBiographies';
    var $m_keyColumnNames = array('id');
    var $m_keyIsAutoIncrement = true;
    var $m_columnNames = array('id', 'fk_author_id', 'fk_language_id',
                               'biography', 'first_name', 'last_name');

    /**
     * Constructor.
     *
     * @param int $p_idOrName
     */
    public function __construct($p_id, $p_authorId, $p_languageId)
    {

    } // fn constructor


    /**
     * Wrapper around DatabaseObject::setProperty
     *
     * @see classes/DatabaseObject#setProperty($p_dbColumnName, $p_value, $p_commit, $p_isSql)
     */
    public function setProperty($p_dbColumnName, $p_value, $p_commit = true, $p_isSql = false)
    {
        if ($p_dbColumnName == 'alias') {
            $this->m_keyColumnNames = array('alias');
            $this->resetCache();
            $this->m_keyColumnNames = array('id');
        }
        return parent::setProperty($p_dbColumnName, $p_value);
    } // fn setProperty


    /**
     * @return int
     */
    public function getId()
    {
        return $this->m_data['id'];
    } // fn getId


    /**
     * @return int
     */
    public function getAuthorId()
    {
        return $this->m_data['fk_author_id'];
    } // fn getAuthorId


    /**
     * @return int
     */
    public function getLanguageId()
    {
        return $this->m_data['fk_language_id'];
    } // fn getLanguageId


    /**
     * @return string
     */
    public function getBiography()
    {
        return $this->m_data['biography'];
    } // fn getBiography


    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->m_data['first_name'];
    } // fn getFirstName


    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->m_data['last_name'];
    } // fn getLastName


    /**
     *
     */
    public function setBiography($p_biography)
    {
        return $this->setProperty('biography', $p_biography);
    } // fn setBiography


    /**
     * Get the author biography.
     * Biography is returned in the given language if exists.
     * If not any specific language is given it returns all the available translations.
     *
     * @param int $p_authorId
     * @param string $p_languageId
     * @return array
     */
    public static function GetBiographies($p_authorId, $p_languageId = null)
    {
        $constraints = array();
        if (!is_null($p_authorId)) {
            $constraints[] = array("fk_author_id", $p_authorId);
        }
        if (!is_null($p_languageId)) {
            $constraints[] = array("fk_language_id", $p_languageId);
        }
        return DatabaseObject::Search('AuthorBiographies', $constraints);
    } // fn GetBiographies

} // class AuthorBiography

?>