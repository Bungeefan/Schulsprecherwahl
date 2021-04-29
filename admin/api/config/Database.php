<?php
require_once __DIR__ . "/../../../default_start.inc.php";

const SAMPLE_CONFIG_FILE = __DIR__ . "/../../../config.sample.inc.php";
const CONFIG_FILE = __DIR__ . "/../../../config.inc.php";

if (file_exists(CONFIG_FILE)) {
    include_once CONFIG_FILE;
} else {
    include_once SAMPLE_CONFIG_FILE;
    if (file_exists(SAMPLE_CONFIG_FILE) && is_writable(dirname(SAMPLE_CONFIG_FILE))) {
        copy(SAMPLE_CONFIG_FILE, CONFIG_FILE);
    }
}

class Database
{
    private $conn;

    private $working = true;

    private $lastError;

    public function __construct()
    {
        $this->getConnection();
    }

    public function getConnection(): PDO|null
    {
        if ($this->conn === null) {
            try {
                $this->conn = new PDO("mysql:host=" . DB_HOST . ";charset=" . DB_CHAR, DB_USER, DB_PASS, array(
//                    PDO::ATTR_PERSISTENT => true,
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                ));
                try {
                    $this->conn->exec("USE " . DB_NAME);
                } catch (PDOException $exception) {
                    $this->lastError = $exception->getMessage();
                    $this->working = false;
                }
            } catch (PDOException $exception) {
                $this->lastError = "PDO Connection error: " . $exception->getMessage();
                $this->working = false;
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

    public function getLastError()
    {
        return $this->lastError;
    }
}
