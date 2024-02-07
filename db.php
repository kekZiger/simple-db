<?php

namespace Marcel\DB;

use \PDO;
use \Exception;

// Datenbankverbindung herstellen, objektorientiert.
// Mal zum testen, ich mag ja Klassen zu benutzen.
class Database {

    private $connection = null;

    public function __construct($host = "", $database = "", $user = "", $password = "") {
        try {
            $this->connection = new PDO("mysql:host={$host};dbname={$database};", $user, $password);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Error Ausgeben
            $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); // Standardmäßig immer Fetch_Assoc nutzen
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function fetchArray($query, $params = [], $debug = FALSE) {
        try {
            $pdoStatement = $this->connection->prepare($query);
            $pdoStatement->execute($params);

            if ($debug == TRUE) {
                $this->printDebug($pdoStatement);
            }

            return $pdoStatement->fetchAll();
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function fetchSingleResult($query, $params = [], $debug = FALSE, $column = 0) {
        try {
            $pdoStatement = $this->connection->prepare($query);
            $pdoStatement->execute($params);

            if ($debug == TRUE) {
                $this->printDebug($pdoStatement);
            }

            return $pdoStatement->fetchColumn($column);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

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
            throw new Exception($e->getMessage());
        }
    }

    public function fetchNoResult($query, $params = [], $debug = FALSE) {
        try {
            $pdoStatement = $this->connection->prepare($query);
            $pdoStatement->execute($params);

            if ($debug == TRUE) {
                $this->printDebug($pdoStatement);
            }

            return true;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
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
