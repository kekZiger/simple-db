<?php

namespace Albrecht\DatabaseClass;

use \PDO;
use \Exception;

/**
 * Database Class
 * 
 * @param string $host
 * @param string $database
 * @param string $user
 * @param string $password
 */
class Database {

    private $connection = null;

    public function __construct($host = "", $database = "", $user = "", $password = "") {
        try {
            $this->connection = new PDO("mysql:host={$host};dbname={$database};", $user, $password);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Error Ausgeben
            $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); // Standardmäßig immer Fetch_Assoc nutzen
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), (int)$e->getCode());
        }
    }

    /**
     * fetch result as array
     *
     * @param string $query
     * @param array $params
     * @param boolean $debug
     * @return void
     */
    public function fetchArray($query, $params = [], $debug = FALSE) {
        try {
            $pdoStatement = $this->connection->prepare($query);
            $pdoStatement->execute($params);

            if ($debug == TRUE) {
                $this->printDebug($pdoStatement);
            }

            return $pdoStatement->fetchAll();
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), (int)$e->getCode());
        }
    }

    /**
     * fetch single result, define column you want
     *
     * @param string $query
     * @param array $params
     * @param boolean $debug
     * @param integer $column
     * @return void
     */
    public function fetchSingleResult($query, $params = [], $debug = FALSE, $column = 0) {
        try {
            $pdoStatement = $this->connection->prepare($query);
            $pdoStatement->execute($params);

            if ($debug == TRUE) {
                $this->printDebug($pdoStatement);
            }

            return $pdoStatement->fetchColumn($column);
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), (int)$e->getCode());
        }
    }

    /**
     * fetch last insert id
     *
     * @param string $query preg_match checks if string contains "INSERT INTO", returns "false" if not
     * @param array $params
     * @param boolean $debug
     * @return void
     */
    public function fetchInsertId($query, $params = [], $debug = FALSE) {

        if (!preg_match("/^INSERT INTO /", $query)) return false;

        try {
            $pdoStatement = $this->connection->prepare($query);
            $pdoStatement->execute($params);

            if ($debug == TRUE) {
                $this->printDebug($pdoStatement);
            }

            return $this->connection->lastInsertId();
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), (int)$e->getCode());
        }
    }

    /**
     * if query runs, returns true
     *
     * @param [type] $query
     * @param array $params
     * @param boolean $debug
     * @return void
     */
    public function fetchNoResult($query, $params = [], $debug = FALSE) {
        try {
            $pdoStatement = $this->connection->prepare($query);
            $pdoStatement->execute($params);

            if ($debug == TRUE) {
                $this->printDebug($pdoStatement);
            }

            return true;
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), (int)$e->getCode());
        }
    }

    private function debugParams($stmt) {
        ob_start();
        $stmt->debugDumpParams();
        $r = ob_get_contents();
        ob_end_clean();
        return $r;
    }

    private function printDebug($pdoStatement) {
        echo 'DEBUG-START: <br><br>';
        print_r(htmlspecialchars($this->debugParams($pdoStatement)));
        echo '<br><br>DEBUG-END';
    }
}
