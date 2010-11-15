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
class AuthorAlias extends DatabaseObject
{
    var $m_dbTableName = 'AuthorAliases';
    var $m_keyColumnNames = array('id');
    var $m_keyIsAutoIncrement = true;
    var $m_columnNames = array('id', 'fk_author_id', 'alias');

    /**
     * Constructor.
     *
     * @param int $p_idOrName
     */
    public function __construct($p_idOrName = null)
    {
        parent::DatabaseObject($this->m_columnNames);
        if (is_numeric($p_idOrName)) {
            $this->m_data['id'] = $p_idOrName;
            $this->fetch();
        } elseif (!empty($p_idOrName)) {
            $this->m_keyColumnNames = array('alias');
            $this->m_data['alias'] = $p_idOrName;
            $this->fetch();
            $this->m_keyColumnNames = array('id');
        }
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
     * @return string
     */
    public function getName()
    {
        return $this->m_data['alias'];
    } // fn getName


    /**
     *
     */
    public function setName($p_name)
    {
        return $this->setProperty('alias', $p_name);
    } // fn setName


    /**
     * @return int
     */
    public function getAuthorId()
    {
        return $this->m_data['fk_author_id'];
    } // fn getAuthorId


    /**
     * @param int $p_value
     * @return boolean
     */
    public function setAuthorId($p_value)
    {
        return $this->setProperty('fk_author_id', $p_value);
    } // fn setAuthorId


    /**
     * Get all the author aliases that match the given criteria.
     *
     * @param int $p_id
     * @param int $p_authorId
     * @param string $p_name
     * @return array
     */
    public static function GetAuthorAliases($p_id = null, $p_authorId = null, $p_name = null)
    {
        $constraints = array();
        if (!is_null($p_authorId)) {
            $constraints[] = array("fk_author_id", $p_authorId);
        }
        if (!is_null($p_name)) {
            $constraints[] = array("alias", $p_name);
        }
        if (!is_null($p_id)) {
            $constraints[] = array("id", $p_id);
        }
        return DatabaseObject::Search('AuthorAlias', $constraints);
    } // fn GetAuthorAliases

} // class AuthorAlias

?>