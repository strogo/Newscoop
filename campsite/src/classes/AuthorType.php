<?php
/**
 * @package Campsite
 */

/**
 * Includes
 */
require_once($GLOBALS['g_campsiteDir'].'/db_connect.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/DatabaseObject.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Log.php');

/**
 * @package Campsite
 */
class AuthorType extends DatabaseObject
{
    var $m_dbTableName = 'AuthorTypes';
    var $m_keyColumnNames = array('id');
    var $m_keyIsAutoIncrement = true;
    var $m_columnNames = array('id',
                               'type');
    var $m_exists = false;


    /**
     * Constructor
     *
     * @param string
     *    $p_authorTypeId (optional) The author type identifier
     *
     * @return void
     */
    public function __construct($p_authorTypeId = null)
    {
        parent::DatabaseObject($this->m_columnNames);
        if (is_numeric($p_authorTypeId) && $p_authorTypeId > 0) {
            $this->m_data['id'] = $p_authorTypeId;
            if ($this->keyValuesExist()) {
                $this->fetch();
            }
        }
    } // constructor


    /**
     * Get the id of the author type.
     *
     * @return int
     */
    public function getId()
    {
        return $this->m_data['id'];
    } // gn getId


    /**
     * Get the name of the author type.
     *
     * @return string
     */
    public function getName()
    {
        return $this->m_data['type'];
    } // fn getName


    /**
     * Get all the author types.
     *
     * @return array
     *    An array of AuthorType objects.
     */
    public static function GetAuthorTypes()
    {
        global $g_ado_db;

        $queryStr = 'SELECT id FROM AuthorTypes';
        $res = $g_ado_db->GetAll($queryStr);
        if (!$res) {
            return array();
        }

        $authorTypes = array();
        foreach ($res as $authorType) {
            $tmpAuthorType = new AuthorType($authorType['id']);
            $authorTypes[] = $tmpAuthorType;
        }
        return $authorTypes;
    } // fn GetAuthorTypes

} // class AuthorType

?>