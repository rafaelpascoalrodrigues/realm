<?php
namespace Realm;

class Core {
    protected $realm;

    private $__data;
    private $__path;

    protected $viewer;
    protected $database;

    protected $classes_loaded;
    protected $time_start;


    public function __get($index) {
        $retval = null;
        if (array_key_exists($index, $this->__path)) {
            if (is_array($this->__path[$index])) {
                $retval = $this;
                $this->__path =& $this->__path[$index];
            } else {
                $retval = $this->__path[$index];
                $this->__path =& $this->__data;
            }
        }

        return $retval;
    }

    public function __set($index, $value) {
        $this->__path[$index] = $value;
        $this->__path =& $this->__data;

        return true;
    }


    public function __construct($realm = "") {
        $this->time_start = microtime(false);

        $this->__data = array();
        $this->__path =& $this->__data;

        $this->realm = empty($realm) ? 'realm' : $realm;
        

        $this->domain    = !empty($_GET['domain'])    ? $_GET['domain']    : '' ;
        $this->subdomain = !empty($_GET['subdomain']) ? $_GET['subdomain'] : '' ;
        $this->request   = !empty($_GET['request'])   ? $_GET['request']   : '' ;
        $this->fullpath  = !empty($_GET['fullpath'])  ? $_GET['fullpath']  : '' ;

        $this->query = empty($_GET) ? array() : $_GET;

        $this->uri = self::GetUri();

        return true;
    }

    private function GetUri() {
        $router = array();

        if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
            $router['ssl'] = true; 
        } else {
            $router['ssl'] = false;
        }

        $router['protocol'] = substr(strtolower($_SERVER['SERVER_PROTOCOL']), 0, strpos($_SERVER['SERVER_PROTOCOL'], '/'));  
        $router['protocol'] .= ($router['ssl']) ? 's' : '';

        if (isset($_SERVER['HTTP_X_FORWARDED'])) {
            $router['host'] = $_SERVER['HTTP_X_FORWARDED_HOST'];
        } elseif (isset($_SERVER['HTTP_HOST'])) {
            $router['host'] = $_SERVER['HTTP_HOST'];
        } else {
            $router['host'] = $_SERVER['SERVER_NAME'];
        }

        $router['port'] = intval($_SERVER['SERVER_PORT']);

        $router['path'] = preg_replace('/\/index\.php$/', '', $_SERVER['SCRIPT_NAME']);

        $router['local'] = dirname($_SERVER['SCRIPT_FILENAME']);

        $router['full']  = "";
        $router['full'] .= $router['protocol'] . '://';
        $router['full'] .= $router['host'];

        if (!(!$router['ssl'] && $router['port'] == 80) && !($router['ssl'] && $router['port'] == 443)) {
            $router['full'] .= ':' . $router['port'];
        }

        $router['full'] .= $router['path'];


        return $router;
    }


    public function InitializeDatabaseConnection() {
        $this->database = new \Realm\Database();
    }

    public function getDatabase() {
        return $this->database;
    }


    public function InitializeViewer() {
        $this->viewer = new \Realm\Viewer();
    }


    public function Show() {
        $this->viewer->Show();
    }


    private static function MicrotimeToFloat($microtime) {
        if (gettype($microtime) != 'string') {
            return 0;
        } else {
            $time_string = explode(' ', $microtime);
            if (count($time_string) != 2) {
                return 0;
            } else {
                return floatval($time_string[1]) + floatval($time_string[0]);
            }
        }
    }


    public function TimeProcessing($time_start = null, $time_end = null) {
        if ($time_start == null) {
            $time_start = $this->time_start;
        }

        if ($time_end == null) {
            $time_end = microtime(false);
        }

        return self::MicrotimeToFloat($time_end) - self::MicrotimeToFloat($time_start);
    }
}
