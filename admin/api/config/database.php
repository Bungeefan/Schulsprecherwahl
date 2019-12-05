<?php

if (file_exists(__DIR__ . "/database_credentials.php")) {
    include_once "database_credentials.php";
} else {
    include_once "database_credentials_default.php";
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
        global $host, $db_char, $db_name, $db_user, $db_pass;
        if ($this->conn === null) {
            try {
                $this->conn = new PDO("mysql:host=" . $host . ";charset=" . $db_char, $db_user, $db_pass, array(
//                    PDO::ATTR_PERSISTENT => true,
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                ));
                try {
                    $this->conn->query("USE $db_name");
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
