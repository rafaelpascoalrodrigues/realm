<?php
namespace Realm;


class DatabaseStatement {
    private $database;
    private $connected;
    private $pre_query;
    private $query;
    private $statement;
    private $error;

    public function __construct($database = '') {
        $this->database = $database;
        $this->connected = \Realm\Database::Open($database);

        if ($this->connected == false) {
            $this->error = \Realm\Database::ErrorInfo($database);
        }
    }


    /* Prepare a SQL statement to be executed */
    private function _prepare($query = '') {
        $this->query = $query;

        if ($this->connected == false) {
            $this->error = \Realm\Database::ErrorInfo($this->database);
            return false;
        }

        try  {
            $this->statement = \Realm\Database::Prepare($this->database, $this->query);
        } catch (\PDOException $e) {
            preg_match('/^\D*\[(\d*)\]\s\[(\d*)\]\s(.*)$/', $e->getMessage(), $matches);
            if (count($matches) > 0) {
                $error = array($matches[1], intval($matches[2]), $matches[3]);
            } else {
                $error = $e->getMessage();
            }
            
            $this->error  = $error;

            return false;
        }
        
        if ($this->statement == false) {
            return false;
        }
    }

    public function Prepare($query = '') {
        $this->pre_query = $query;
        self::_prepare($query);
    }


    /* Bind a constant value on a SQL statement to be executed */
    public function BindConstant($parameter, $value = null) {
        if (!is_array($parameter)) {
            $parameter = array($parameter => $value);
        }

        $query = $this->pre_query;
        foreach ($parameter as $key => $value) {
            $query = str_replace(':' . $key, $value, $query);
        }
        if ($this->connected == false) {
            return false;
        }

        self::_prepare($query);
        if ($this->statement == false) {
            return false;
        }
    }

    /* Bind a value on a SQL statement to be executed */
    public function BindValue($parameter, $value, $data_type = \PDO::PARAM_STR) {
        if ($this->statement == false) {
            return false;
        }

        return $this->statement->bindValue($parameter, $value, $data_type);
    }


    /* Bind a variable to receive a return value on a SQL statement to be executed */
    public function BindResult($parameter, &$value, $data_type = \PDO::PARAM_STR) {
        if ($this->statement == false) {
            return false;
        }

        return $this->statement->bindColumn($parameter, $value, $data_type);
    }


    /* Execute a SQL statement */
    public function Execute() {
        if ($this->statement == false) {
            return false;
        }

        $result = $this->statement->execute();
        if (!$result) {
            $this->error = $this->statement->ErrorInfo();
        } else {
            $this->error = array();
        }

        return $result;
    }


    /* Fetch a row of an executed SQL statement */
    public function Fetch() {
        if ($this->statement == false) {
            return false;
        }

        $this->result = $this->statement->fetch();
        return $this->result;
    }


    /* Inform the error on last called method */
    public function ErrorInfo() {
        return $this->error;
    }
}
