<?php
namespace Realm;


class Database {
    public $connections;
    public $errors;
    public $config;

    public function __construct() {
        $this->connections = array();
        $this->errors = array();
        $this->config = \Realm\Configuration::Load('databases');
    }


    /* Try to open a database */
    public static function Open($database = '') {
        global $REALM;

        /* Verify if this database is already opened */
        if (array_key_exists($database, $REALM->getDatabase()->connections)) {
            return true;
        }

        /* Verify if the configuration file has been found */
        if ($REALM->getDatabase()->config == null) {
            return false;
        }

        /* Verify if the database configuration data has been found */
        if (!array_key_exists($database, $REALM->getDatabase()->config)) {
            return false;
        }

        $config = $REALM->getDatabase()->config[$database];

        $dsn  = "";
        $dsn .= $config['driver'];
        $dsn .= ":host=" . $config['host'];
        if (array_key_exists('port', $config) && $config['port'] != null) {
            $dsn .= ";port=" . $config['port'];
        }
        if (array_key_exists('name', $config) && $config['name'] != null) {
            $dsn .= ";dbname=" . $config['name'];
        }

        $charset = (array_key_exists('charset', $config) && $config['charset'] != null) ? $config['charset'] : 'utf8';
        $dsn .= ";charset=" . $charset;

        $user = (array_key_exists('user', $config) && $config['user'] != null) ? $config['user'] : '';
        $pass = (array_key_exists('pass', $config) && $config['pass'] != null) ? $config['pass'] : '';

        /* Try to connect */
        try {
            $REALM->getDatabase()->connections[$database] = new \PDO($dsn, $user, $pass, array(
				\PDO::ATTR_TIMEOUT => CONNECTION_TIMEOUT,
				\PDO::ATTR_ERRMODE => \PDO::ERRMODE_SILENT,
                \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$charset}"));
            $REALM->getDatabase()->errors[$database] = array();

        } catch (\PDOException $e) {
            preg_match('/^\D*\[(\d*)\]\s\[(\d*)\]\s(.*)$/', $e->getMessage(), $matches);
            if (count($matches) > 0) {
                $error = array($matches[1], intval($matches[2]), $matches[3]);
            } else {
                $error = $e->getMessage();
            }
            $REALM->getDatabase()->errors[$database] = $error;
            
            unset($REALM->getDatabase()->connections[$database]);

            return false;
        }

        if (    version_compare(PHP_VERSION, '5.3.6') < 0 &&
                array_key_exists('charset', $config) &&
                $config['charset'] != null) {
            $REALM->getDatabase()->connections[$database]->exec('set names ' . ['charset']);
        }

        return true;
    }


    /* Prepare a SQL statement to be executed */
    public static function Prepare($database, $query) {
        global $REALM;

        /* Verify if database is open */
        if (!array_key_exists($database, $REALM->getDatabase()->connections)) {
            return;
        }

        try {
            $statement = $REALM->getDatabase()->connections[$database]->prepare($query);
        } catch (\PDOException $e) {
            $statement = false;
        }
        
        return $statement;
    }


    /* Inform the error on last called method */
    public function ErrorInfo($database) {
        global $REALM;

        /* Verify if database is open */
        if (array_key_exists($database, $REALM->getDatabase()->errors)) {
            return $REALM->getDatabase()->errors[$database];
        }

        return array();
    }
}
