<?php
/**
 * @package Campsite
 *
 * @author Holman Romero <holman.romero@gmail.com>
 * @author Mugur Rus <mugur.rus@gmail.com>
 * @copyright 2007 MDLF, Inc.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @version $Revision$
 * @link http://www.campware.org
 */

/**
 * Includes
 *
 * We indirectly reference the DOCUMENT_ROOT so we can enable
 * scripts to use this file from the command line, $_SERVER['DOCUMENT_ROOT']
 * is not defined in these cases.
 */
$g_documentRoot = $_SERVER['DOCUMENT_ROOT'];

require_once($g_documentRoot.'/include/smarty/Smarty.class.php');


/**
 * Class CampTemplate
 */
final class CampTemplate extends Smarty
{
    /**
     * Holds instance of the class.
     *
     * @var object
     */
	private static $m_instance = null;


	/**
	 * Holds the context object;
	 *
	 * @var object
	 */
	private $m_context = null;


    private function __construct()
    {
        parent::Smarty();

        $config = CampSite::GetConfigInstance();

        $this->caching = $config->getSetting('smarty.caching');
        $this->debugging = $config->getSetting('smarty.debugging');
        $this->force_compile = $config->getSetting('smarty.force_compile');
        $this->compile_check = $config->getSetting('smarty.compile_check');
        $this->use_sub_dirs = $config->getSetting('smarty.use_subdirs');

        $this->left_delimiter = $config->getSetting('smarty.left_delimeter');
        $this->right_delimiter = $config->getSetting('smarty.right_delimeter');

        $this->cache_dir = CS_PATH_SITE.DIR_SEP.'cache';
        $this->config_dir = CS_PATH_SMARTY.DIR_SEP.'configs';
        $this->plugins_dir = array(CS_PATH_SITE.DIR_SEP.'smarty_camp_plugins',
                                   CS_PATH_SMARTY.DIR_SEP.'plugins');
        $this->template_dir = CS_PATH_SITE.DIR_SEP.CS_PATH_SMARTY_TEMPLATES;
        $this->compile_dir = CS_PATH_SITE.DIR_SEP.'templates_c';
    } // fn __constructor


    /**
     * Singleton function that returns the global class object.
     *
     * @return CampTemplate object
     */
    public static function singleton()
    {
        if (!isset(self::$m_instance)) {
            self::$m_instance = new CampTemplate();
        }

        return self::$m_instance;
    } // fn singleton


    /**
     * Returns the template context object.
     *
     * @return CampContext object
     */
    public function &context()
    {
    	if (!isset($this->m_context)) {
    		$this->m_context = new CampContext();
    	}
    	return $this->m_context;
    } // fn context


    public function setTemplateDir($p_dir)
    {
        $this->template_dir = $p_dir;
    } // fn setTemplateDir


    /**
     * Inserts an error message into the errors list.
     *
     * @param string $p_message
     * @param object $p_smarty
     *
     * @return void
     */
    public function trigger_error($p_message, $p_smarty = null)
    {
    	if (is_object($p_smarty)) {
    		$p_smarty->trigger_error($p_message);
    	} else {
    		trigger_error("Campsite error: $p_message");
    	}
    } // fn trigger_error

} // class CampTemplate

?>