<?php
namespace Realm;

class Core {
    protected $realm;

    private $registry;

    protected $viewer;

    protected $classes_loaded;
    protected $time_start;


    public function __get($index) {
        return (isset($this->registry[$index]) ? $this->registry[$index] : null);
    }


    public function __construct($realm = "") {
        $this->time_start = microtime(false);

        $this->registry = array();

        $this->realm = empty($realm) ? 'realm' : $realm;

        
        $this->registry['domain']    = !empty($_GET['domain'])    ? $_GET['domain']    : '' ;
        $this->registry['subdomain'] = !empty($_GET['subdomain']) ? $_GET['subdomain'] : '' ;
        $this->registry['request']   = !empty($_GET['request'])   ? $_GET['request']   : '' ;
        $this->registry['fullpath']  = !empty($_GET['fullpath'])  ? $_GET['fullpath']  : '' ;
  
        $this->registry['query'] = empty($_GET) ? array() : $_GET;


        return true;
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
