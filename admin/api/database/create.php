<?php
require_once "../config/ajax_start.inc.php";
$sqlCreateFile = __DIR__ . "/../config/voting_system_create.sql";

global $arr, $database;
if (file_exists($sqlCreateFile)) {
    $result = $database->getConnection()->exec(implode("\n", array_filter(file($sqlCreateFile), static function ($line) {
        return !str_contains($line, "-");
    })));
    if ($result !== false) {
        http_response_code(200);
        $arr = array("message" => "Database created.");
        $database->setWorking(true);
    } else {
        http_response_code(503);
        $arr = array("message" => "Database couldn't be created.");
    }
} else {
    http_response_code(503);
    $arr = array("message" => "SQL File not found.");
}

printResult($arr);
