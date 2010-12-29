<?php
/**
 * @package Campsite
 *
 * @author Sebastian Goebel <devel@yellowsunshine.de>
 * @copyright 2007 MDLF, Inc.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @version $Revision$
 * @link http://www.sourcefabric.org
 */


/**
 * Class CampPlugin
 */


class CampPlugin extends DatabaseObject
{
	const CACHE_KEY_PLUGINS_LIST = 'campsite_plugins_list';

	const CACHE_KEY_PLUGINS_ALL = 'campsite_plugins_all';

    public $m_keyColumnNames = array('Name');

    public $m_dbTableName = 'Plugins';

    public $m_columnNames = array('Name', 'Version', 'Enabled');

    static private $m_allPlugins = null;

    static protected $m_pluginsInfo = null;

    public function CampPlugin($p_name = null, $p_version = null)
    {
        parent::DatabaseObject($this->m_columnNames);
        $this->m_data['Name'] = $p_name;

        if (!is_null($p_version)) {
            $this->m_data['Version'] = $p_version;
        }
        if (!is_null($p_name)) {
            $this->fetch();
        }
    } // constructor

    public function create($p_name, $p_version, $p_enabled = true)
    {
        // Create the record
        $this->m_data['Name'] = $p_name;

        $values = array(
            'Version' => $p_version,
            'Enabled' => $p_enabled ? 1 : 0
        );


        $success = parent::create($values);
        if (!$success) {
            return false;
        }
    }

    static public function GetAll($p_reload = false)
    {
        global $g_ado_db;

        if (!$p_reload && is_array(self::$m_allPlugins)) {
        	return self::$m_allPlugins;
        }

        if (!$p_reload && CampCache::IsEnabled()) {
            $cacheListObj = new CampCacheList(array(), self::CACHE_KEY_PLUGINS_ALL);
            self::$m_allPlugins = $cacheListObj->fetchFromCache();
            if (self::$m_allPlugins !== false && is_array(self::$m_allPlugins)) {
                return self::$m_allPlugins;
            }
        }

        $CampPlugin = new CampPlugin();
        $query = "SELECT Name FROM `" . $CampPlugin->m_dbTableName . "`";
        $res = $g_ado_db->execute($query);
        if (!$res) {
        	return array();
        }

        self::$m_allPlugins = array();
        while ($row = $res->FetchRow()) {
        	self::$m_allPlugins[] = new CampPlugin($row['Name']);;
        }

        if (!$p_reload && CampCache::IsEnabled()) {
            $cacheListObj->storeInCache(self::$m_allPlugins);
        }

        return self::$m_allPlugins;
    }

    static public function GetEnabled($p_reload = false)
    {
        $plugins = array();

        foreach (self::GetAll($p_reload) as $CampPlugin) {
            if ($CampPlugin->isEnabled()) {
                $plugins[] = $CampPlugin;
            }
        }
        return $plugins;
    }

    public function getBasePath()
    {
        return CS_PLUGINS_DIR.DIR_SEP.$this->getName();
    }

    public function getName()
    {
        return $this->getProperty('Name');
    }

    public function getDbVersion()
    {
        return $this->getProperty('Version');
    }


    public function getFsVersion()
    {
        $info = self::GetPluginsInfo();
        return $info[$this->getName()]['version'];
    }

    public function isEnabled()
    {
        return $this->getProperty('Enabled') == 1 ? true : false;
    }

    static public function IsPluginEnabled($p_name, $p_version = null)
    {
        $plugin = new CampPlugin($p_name, $p_version);

        return $plugin->isEnabled();
    }

    public function install()
    {
        $info = $this->getPluginInfo();
        if (function_exists($info['install'])) {
            call_user_func($info['install']);
        }
        MetaAction::DeleteActionsFromCache();
        self::ClearPluginsInfo();
    }

    public function enable()
    {
        $this->setProperty('Enabled', 1);

        $info = $this->getPluginInfo();
        if (function_exists($info['enable'])) {
            call_user_func($info['enable']);
        }
        MetaAction::DeleteActionsFromCache();
        self::ClearPluginsInfo();
    }

    public function disable()
    {
        $this->setProperty('Enabled', 0);

        $info = $this->getPluginInfo();
        if (function_exists($info['disable'])) {
            call_user_func($info['disable']);
        }
        MetaAction::DeleteActionsFromCache();
        self::ClearPluginsInfo();
    }

    public function uninstall()
    {
        $info = $this->getPluginInfo();
        if (function_exists($info['uninstall'])) {
            call_user_func($info['uninstall']);
        }

        self::ClearPluginsInfo();

        $this->delete();
        MetaAction::DeleteActionsFromCache();
        self::ClearPluginsInfo();
    }

    public function update()
    {
        $info = $this->getPluginInfo();
        if (function_exists($info['update'])) {
            call_user_func($info['update']);
        }
    }

    /**
     * Return a list or available or activated plugins.
     * The method have to return an (empty) array.
     *
     * @param boolean $p_selectEnabled
     * @param boolean $p_reload
     * @return array
     */
    static public function GetPluginsInfo($p_selectEnabled = false, $p_reload = false)
    {
        $p_selectEnabled = $p_selectEnabled ? 'enabled' : 'available';

        if ($p_reload) {
            self::FetchFilePluginsInfo();
        }

        if (is_array(self::$m_pluginsInfo) && is_array(self::$m_pluginsInfo[$p_selectEnabled])) {
            $ret = self::$m_pluginsInfo[$p_selectEnabled];
            return $ret;
        } else {
            if (self::FetchCachePluginsInfo() && is_array(self::$m_pluginsInfo[$p_selectEnabled])) {
                $ret = self::$m_pluginsInfo[$p_selectEnabled];
                return $ret;
            }
            if (self::FetchFilePluginsInfo() && is_array(self::$m_pluginsInfo[$p_selectEnabled])) {
                $ret = self::$m_pluginsInfo[$p_selectEnabled];
                return $ret;
            }
        }
        self::$m_pluginsInfo = array('available' => array(), 'enabled' => array());
        return array();
    }

    /**
     * Fetch plugin infos from the %plugin.info files.
     *
     * @return boolen plugins were found
     */
    private static function FetchFilePluginsInfo()
    {
        if (!is_dir(CS_PATH_PLUGINS)) {
            return false;
        }

        $pluginsInfo = array('available' => null, 'enabled' => array());

        $enabledPluginsNames = array();
        $enabledPlugins = self::GetEnabled();
        foreach ($enabledPlugins as $plugin) {
            $enabledPluginsNames[] = $plugin->getName();
        }

        $handle=opendir(CS_PATH_PLUGINS);
        while ($entry = readdir($handle)) {
            if ($entry != "." && $entry != ".." && $entry != '.svn' && is_dir(CS_PATH_PLUGINS.DIR_SEP.$entry)) {
                if (file_exists(CS_PATH_PLUGINS.DIR_SEP.$entry.DIR_SEP.$entry.'.info.php')) {
                    include (CS_PATH_PLUGINS.DIR_SEP.$entry.DIR_SEP.$entry.'.info.php');
                    $pluginsInfo['available'][$entry] = $info;
                    if (array_search($entry, $enabledPluginsNames) !== false) {
                        $pluginsInfo['enabled'][$entry] = $info;
                    }
                }
            }
        }
        closedir($handle);

	    self::$m_pluginsInfo = $pluginsInfo;
        self::StoreCachePluginsInfo();

        if (is_array($pluginsInfo['available'])) {
			return true;
        };
        return false;
    }

    /**
     * Fetch plugin infos from cache.
     * The method have to validate if plugins still exists in filesystem.
     *
     * @return boolean plugins were found in cache
     */
    private static function FetchCachePluginsInfo()
    {
    	if (CampCache::IsEnabled()) {
    		$pluginsInfo = CampCache::singleton()->fetch(self::CACHE_KEY_PLUGINS_LIST);
    		if ($pluginsInfo !== false && is_array($pluginsInfo['available'])) {
    		    foreach ($pluginsInfo['available'] as $entry => $info) {
    		        if (!file_exists(CS_PATH_PLUGINS.DIR_SEP.$entry.DIR_SEP.$entry.'.info.php')) {
    		            unset($pluginsInfo['available'][$entry]);
    		            unset($pluginsInfo['enabled'][$entry]);
    		        }
    		    }
    			self::$m_pluginsInfo = $pluginsInfo;
    			return true;
    		}
    	}
    	return false;
    }


    private static function StoreCachePluginsInfo()
    {
    	if (CampCache::IsEnabled()) {
            return CampCache::singleton()->add(self::CACHE_KEY_PLUGINS_LIST, self::$m_pluginsInfo);
        }
        return false;
    }


    private static function DeleteCachePluginsInfo()
    {
        if (CampCache::IsEnabled()) {
        	$cacheListObj = new CampCacheList(array(), self::CACHE_KEY_PLUGINS_ALL);
        	$cacheListObj->deleteFromCache();
            return CampCache::singleton()->delete(self::CACHE_KEY_PLUGINS_LIST);
        }
        return false;
    }


    public static function ClearPluginsInfo()
    {
    	self::DeleteCachePluginsInfo();
        self::$m_pluginsInfo = null;
        self::$m_allPlugins = null;
    }


    public function getPluginInfo($p_plugin_name = '')
    {
        if (!empty($p_plugin_name)) {
            $name = $p_plugin_name;
        } elseif (isset($this) && is_a($this, 'CampPlugin')) {
            $name = $this->getName();
        } else {
            return false;
        }

        $infos = self::GetPluginsInfo();
        $info = $infos[$name];

        return $info;
    }

    static public function ExtendNoMenuScripts(&$p_no_menu_scripts)
    {
        foreach (self::GetPluginsInfo() as $info) {
            if (is_array($info['no_menu_scripts']) && CampPlugin::IsPluginEnabled($info['name'])) {
                $p_no_menu_scripts = array_merge($p_no_menu_scripts, $info['no_menu_scripts']);
            }
        }
    }

    static public function CreatePluginMenu()
    {
        global $ADMIN;
        global $g_user;

        $root_menu = false;
        $plugin_infos = self::GetPluginsInfo(true);

        if ($g_user->hasPermission('plugin_manager')) {
            $root_menu = true;
        }

        foreach ($plugin_infos as $info) {
        	if (isset($info['menu']['permission']) && $g_user->hasPermission($info['menu']['permission'])) {
        		$root_menu = true;
        	} elseif (is_array($info['menu']['sub'])) {
        		foreach ($info['menu']['sub'] as $menu_info) {
        			if ($g_user->hasPermission($menu_info['permission'])) {
        				$root_menu = true;
        			}
        		}
        	}
        }

        if (empty($root_menu)) {
            return;
        }

        $menu_modules =& DynMenuItem::Create(getGS('Plugins'), '',
            array('icon' => '', 'id' => 'plugins'));

        if ($g_user->hasPermission('plugin_manager')) {
            $menu_item =& DynMenuItem::Create(getGS('Manage Plugins'),
                "/$ADMIN/plugins/manage.php",
                array('icon' => ''));
            $menu_modules->addItem($menu_item);
        }

        foreach ($plugin_infos as $info) {
            if (CampPlugin::IsPluginEnabled($info['name'])) {
                $menu_plugin = null;
                $parent_menu = false;

                $Plugin = new CampPlugin($info['name']);

                if (isset($info['menu']['permission']) && $g_user->hasPermission($info['menu']['permission'])) {
                    $parent_menu = true;
                } elseif (is_array($info['menu']['sub'])) {
                    foreach ($info['menu']['sub'] as $menu_info) {
                        if ($g_user->hasPermission($menu_info['permission'])) {
                            $parent_menu = true;
                        }
                    }
                }

                if ($parent_menu && isset($info['menu'])) {
                    $menu_plugin =& DynMenuItem::Create(getGS($info['menu']['label']),
                        isset($info['menu']['path']) ? "/$ADMIN/".$info['menu']['path'] : null,
                        array('icon' => ''));
                }

                if (isset($info['menu']['sub']) && is_array($info['menu']['sub'])) {
                    foreach ($info['menu']['sub'] as $menu_info) {
                        if ($g_user->hasPermission($menu_info['permission'])) {
                            $menu_item =& DynMenuItem::Create(getGS($menu_info['label']),
                                is_null($menu_info['path']) ? null : "/$ADMIN/".$menu_info['path'],
                                array('icon' => ''));
                            $menu_plugin->addItem($menu_item);
                        }
                    }
                }

                if (is_object($menu_plugin)) {
                    $menu_modules->addItem($menu_plugin);
                }
            }
        }
        return $menu_modules;
    }

    static public function ExtractPackage($p_uploaded_package, &$p_log = null)
    {
        $plugin_name = false;

        require_once('Archive/Tar.php');
        $tar = new Archive_Tar($p_uploaded_package);


        if (($file_list = $tar->ListContent()) == 0) {
            $p_log = getGS('The uploaded file format is unsupported.');
            return false;
        } else {
            foreach ($file_list as $v) {

                if (preg_match('/[^\/]+\/([^.]+)\.info\.php/', $v['filename'], $matches)) {
                    $plugin_name = $matches[1];
                }

                #$p_log .= sprintf("Name: %s  Size: %d   modtime: %s mode: %s<br>", $v['filename'], $v['size'], $v['mtime'], $v['mode']);
            }
        }

        if ($plugin_name === false) {
            $p_log = getGS('The uploaded archive does not contain an valid campsite plugin.');
            return false;
        }

        $tar->extract(CS_PATH_PLUGINS);

        self::ClearPluginsInfo();
        CampPlugin::GetPluginsInfo(false, true);
    }

    public static function PluginAdminHooks($p_filename, $p_area=null)
    {
        global $ADMIN, $ADMIN_DIR, $Campsite, $g_user;

        $paths = array();

        $filename = realpath($p_filename);
        $admin_path = realpath(CS_PATH_SITE.DIR_SEP.$ADMIN_DIR);
        $script = str_replace($admin_path, '', $filename);

        foreach (self::GetEnabled() as $plugin) {
            $filepath = $plugin->getBasePath().DIR_SEP.'admin-files'.DIR_SEP.'include'.DIR_SEP.$script;
            if (file_exists($filepath))  {
                include $filepath;
            }
        }
    }

    public static function GetNeedsUpdate()
    {
        $upgradable = false;

        foreach (self::GetEnabled(true) as $CampPlugin) {
            if ($CampPlugin->getFsVersion() != $CampPlugin->getDbVersion()) {
                $upgradable[$CampPlugin->getName()]  = array(
                                                            'db' => $CampPlugin->getDbVersion(),
                                                            'current' => $CampPlugin->getFsVersion()
                                                        );
            }
        }
        return $upgradable;
    }
}

?>
