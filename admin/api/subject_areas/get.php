<?php
require_once "../config/ajax_start.inc.php";

global $arr, $database;
if (checkDatabase($arr)) {
    $statement = $database->getConnection()->prepare("SELECT DISTINCT SubjectArea FROM `classes`");
    if ($statement->execute()) {
        http_response_code(200);
        $arr = $statement->fetchAll(PDO::FETCH_COLUMN);
    } else {
        http_response_code(503);
        $arr = array("message" => "Es konnten keine Fachgebiete abgerufen werden.");
    }
}

printResult($arr);
