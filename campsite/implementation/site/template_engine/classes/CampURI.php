<?php
/**
 * @package Campsite
 */


/**
 * @package Campsite
 */
final class CampURI {
    /**
     * Holds the CampURI object
     *
     * @var object
     */
    private static $m_instance = null;

    /**
     * The URI value
     *
     * @var string
     */
    var $m_uri = null;

    /**
     * The URI parts
     *
     * @var array
     */
    var $m_parts = array('scheme',
                         'user',
                         'password',
                         'host',
                         'port',
                         'path',
                         'query',
                         'fragment');

    /**
     * @var string
     */
    var $m_scheme = null;

    /**
     * @var string
     */
    var $m_host = null;

    /**
     * @var int
     */
    var $m_port = null;

    /**
     * @var string
     */
    var $m_user = null;

    /**
     * @var string
     */
    var $m_password = null;

    /**
     * @var string
     */
    var $m_path = null;

    /**
     * @var string
     */
    var $m_query = null;

    /**
     * @var string
     */
    var $m_fragment = null;

    /**
     * @var array
     */
    var $m_queryArray = null;


    /**
     * Class constructor
     *
     * @param string
     *    p_uri The full URI string
     */
    private function __construct($p_uri = null)
    {
        if (!empty($p_uri)) {
            $this->parser($p_uri);
        }
    } // fn __construct


    /**
     * Builds an instance object of this class only if there is no one.
     *
     * @param string
     *    p_uri The full URI string, default value 'SELF' indicates it
     *          will be fetched from the server itself
     *
     * @return object
     *    m_instance A CampURI object
     */
    public static function singleton($p_uri = 'SELF')
    {
        if (!isset(self::$m_instance)) {
            // was an uri string passed?
            if (isset($p_uri) && $p_uri != 'SELF') {
                $uriString = $p_uri;
            } else {
                // ... otherwise we build the uri from the server itself.
                //
                // checks whether the site is being queried through SSL
                if (isset($_SERVER['HTTPS'])
                        && !empty($_SERVER['HTTPS'])
                        && (strtolower($_SERVER['HTTPS']) != 'off')) {
                    $ssl = 's://';
                } else {
                    $ssl = '://';
                }

                // this works at least for apache, some research is needed
                // in order to support other web servers.
                if (!empty($_SERVER['PHP_SELF'])
                        && !empty($_SERVER['REQUEST_URI'])) {
                    $uriString = 'http' . $ssl . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
                }

                // some cleaning directives
                $uriString = urldecode($uriString);
                $uriString = str_replace('"', '&quot;', $uriString);
                $uriString = str_replace('<', '&lt;', $uriString);
                $uriString = str_replace('>', '&gt;', $uriString);
                $uriString = preg_replace('/eval\((.*)\)/', '', $uriString);
                $uriString = preg_replace('/[\\\"\\\'][\\s]*javascript:(.*)[\\\"\\\']/', '""', $uriString);
            }

            // instanciates a new CampURI object
            self::$m_instance = new CampURI($uriString);
        }

        return self::$m_instance;
    } // fn singleton


    /**
     * Parses the given URI.
     *
     * @param string
     *    p_uri The URI string
     *
     * @return mixed
     *    true on success, false on failure
     */
    private function parser($p_uri)
    {
        $success = false;
        if (empty($p_uri)) {
            return $success;
        }

        $this->m_uri = $p_uri;
        $p_uri = urldecode($p_uri);

        if ($parts = parse_url($p_uri)) {
            $success = true;
        }

        // sets the value for every URI part
        foreach ($this->m_parts as $part) {
            $member = 'm_'.$part;
            $this->$member = (isset($parts[$part])) ? $parts[$part] : null;
        }

        // populates the query array
        if (isset($parts['query'])) {
            parse_str($parts['query'], $this->m_queryArray);
        }

        return $success;
    } // fn parser


    /**
     * Builds a URI string from the given parts.
     *
     * @param array
     *    p_parts The array of URI parts
     *
     * @return string
     *    uriString The rendered URI
     */
    protected function render($p_parts = array())
    {
        if (empty($p_parts)) {
            $p_parts = $this->m_parts;
        }
        $uriString = '';
        foreach ($p_parts as $part) {
            $member = 'm_'.$part;
            if (!empty($this->$member)) {
                $uriString .= ($part == 'scheme') ? $this->$member.'://' : '';
                $uriString .= ($part == 'user') ? $this->$member : '';
                $uriString .= ($part == 'password') ? ':'.$this->$member.'@' : '';
                $urlString .= ($part == 'host') ? $this->$member : '';
                $urlString .= ($part == 'port') ? ':'.$this->$member : '';
                $urlString .= ($part == 'path') ? $this->$member : '';
                $urlString .= ($part == 'query') ? '?'.$this->$member : '';
                $urlString .= ($part == 'fragment') ? '#'.$this->$member : '';
            }
        }

        return $uriString;
    } // fn render


    /**
     * Gets the URI base, it is the scheme, host and (if exists) port.
     *
     * @return string
     *    base The URI base
     */
    public function getBase()
    {
        $base = $this->getScheme().'://'.$this->getHost();
        if (is_numeric($this->getPort())) {
            $base .= ':'.$this->getPort();
        }

        return $base;
    } // fn getBase


    /**
     * Gets the base plus the path from the current URI.
     *
     * @return string
     *    The URI base path
     */
    public function getBasePath()
    {
        return $this->render(array('scheme','host','port','path'));
    } // fn getBasePath


    /**
     * Gets the query part from the current URI.
     *
     * @return string
     *    m_query The query part
     */
    public function getQuery()
    {
        return $this->m_query;
    } // fn getQuery


    /**
     * Gets the given var from the URI query.
     *
     * @param string
     *    p_varName The var name
     *
     * @return mixed
     *    The var value
     */
    public function getQueryVar($p_varName)
    {
        if (!isset($this->m_queryArray[$p_varName])) {
            return null;
        }

        return $this->m_queryArray[$p_varName];
    } // fn getQueryVar


    /**
     * Gets the array containing the query variables.
     *
     * @return array
     *    m_queryArray The array of query vars
     */
    public function getQueryArray()
    {
        return $this->m_queryArray;
    } // fn getQueryArray


    /**
     * Gets the scheme part from the current URI.
     *
     * @return string
     *    m_scheme The scheme value
     */
    public function getScheme()
    {
        return $this->m_scheme;
    } // fn getScheme


    /**
     * Gets the host part from the current URI.
     *
     * @return string
     *    m_host The host value
     */
    public function getHost()
    {
        return $this->m_host;
    } // fn getHost


    /**
     * Gets the port part from the current URI.
     *
     * @return int
     *    m_port The port value
     */
    public function getPort()
    {
        return $this->m_port;
    } // fn getPort


    /**
     * Gets the user part from the current URI.
     *
     * @return string
     *    m_username The username value
     */
    public function getUser()
    {
        return $this->m_user;
    } // fn getUser


    /**
     * Gets the password part from the current URI.
     *
     * @return string
     *    m_password The password value
     */
    public function getPassword()
    {
        return $this->m_password;
    } // fn getPassword


    /**
     * Gets the path part from the current URI.
     *
     * @return string
     *    m_path The path value
     */
    public function getPath()
    {
        return $this->m_path;
    } // fn getPath


    /**
     * Gets the fragment part from the current URI.
     *
     * @return string
     *    m_fragment The fragment value
     */
    public function getFragment()
    {
        return $this->m_fragment;
    } // fn getFragment


    /**
     * Gets the query part from the current URI.
     *
     * @return string
     *    m_query The query value
     */
    public function setQuery($p_query)
    {
        $this->m_query = $p_query;
        parse_str($p_query, $this->m_queryArray);
    } // fn setQuery


    /**
     *
     */
    public function setQueryVar($p_varName, $p_value)
    {
        $this->m_queryArray[$p_varName] = $p_value;
        $this->m_query = CampURI::QueryArrayToString($this->m_queryArray);
    } // fn setQueryVar


    /**
     *
     */
    public function setScheme($p_scheme)
    {
        $this->m_scheme = $p_scheme;
    } // fn setScheme


    /**
     *
     */
    public function setHost($p_host)
    {
        $this->m_host = $p_host;
    } // fn setHost


    /**
     *
     */
    public function setPort($p_port)
    {
        $this->m_port = (int)$p_port;
    } // fn setPort


    /**
     *
     */
    public function setUser($p_user)
    {
        $this->m_user = $p_user;
    } // fn setUser


    /**
     *
     */
    public function setPassword($p_password)
    {
        $this->m_password = $p_password;
    } // fn setPassword


    /**
     *
     */
    public function setPath($p_path)
    {
        $this->m_path = $p_path;
    } // fn setPath


    /**
     *
     */
    public function setFragment($p_fragment)
    {
        $this->m_fragment = $p_fragment;
    } // fn setFragment


    /**
     * Returns whether the site is running over SSL or not.
     *
     * @return boolean
     *    true on success, false on failure
     */
    public function isSSL()
    {
        return ($this->getScheme() == 'https') ? true : false;
    } // fn isSSL


    /**
     * Builds a URI query string from the given query array.
     *
     * @param array
     *    p_queryArray An array of query variables
     *
     * @return string
     *    queryString The generated query string
     */
    static function QueryArrayToString($p_queryArray)
    {
        if (!is_array($p_queryArray) || sizeof($p_queryArray) < 1) {
            return false;
        }

        $queryString = '';
        $queryVars = array();
        foreach ($p_queryArray as $var => $value) {
            $queryVars[] = $var.'='.urlencode($value);
        }
        $queryString = implode('&', $queryVars);

        return $queryString;
    } // fn QueryArrayToString

} // class CampURI

?>