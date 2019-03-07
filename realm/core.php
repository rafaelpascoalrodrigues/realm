<?php
namespace Realm;

class Core {
    protected $realm;

    protected $classes_loaded;
    protected $time_start;
    

    public function __construct($realm = "") {
        $this->time_start = microtime(false);

        $this->realm = empty($realm) ? 'realm' : $realm;

        return true;
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
