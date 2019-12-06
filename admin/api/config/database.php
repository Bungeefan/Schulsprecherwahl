<?php
require_once __DIR__ . "/../../../default_start.inc.php";

if (file_exists("config.inc.php")) {
    include_once "config.inc.php";
} else {
    include_once "config.inc.default.php";
}

class Database
{
    private $conn;

    private $working = true;

    function __construct()
    {
        $this->getConnection();
    }

    public function getConnection(): PDO
    {
        if ($this->conn === null) {
            try {
                $this->conn = new PDO("mysql:host=" . DB_HOST . ";charset=" . DB_CHAR, DB_USER, DB_PASS, array(
//                    PDO::ATTR_PERSISTENT => true,
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                ));
                try {
                    $this->conn->query("USE " . DB_NAME);
                } catch (PDOException $exception) {
                    $this->working = false;
                }
            } catch (PDOException $exception) {
                die("PDO Connection error: " . $exception->getMessage());
            }
        }
        return $this->conn;
    }

    public function isWorking(): bool
    {
        return $this->working;
    }

    public function setWorking(bool $working): void
    {
        $this->working = $working;
    }
}
