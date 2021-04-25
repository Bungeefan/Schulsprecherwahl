<?php
require_once "../config/ajax_start.inc.php";
header("Access-Control-Allow-Methods: PUT, POST");

$data = (object)array_map(function ($v) {
    return trim(htmlspecialchars($v));
}, $_POST);

global $arr, $database;
if (isset($data->voteKey) && checkDatabase($arr)) {
    $statement = $database->getConnection()->prepare("UPDATE `voting_keys` SET Blacklisted = " . (isset($data->blacklisted) ? "true" : "false") . ", Used = " . (isset($data->used) ? "NOW()" : "NULL") . " WHERE VoteKey IN (" . getQMarks($data->voteKey) . ")");
    bindArray($statement, $data->voteKey);
    if ($statement->execute()) {
        http_response_code(200);
        $arr = array("message" => (is_array($data->voteKey) ? "Keys" : "Key ($data->voteKey)") . " wurde(n) aktualisiert.");
    } else {
        http_response_code(503);
        $arr = array("message" => (is_array($data->voteKey) ? "Keys" : "Key ($data->voteKey)") . " konnte(n) nicht aktualisiert werden.");
    }
}

printResult($arr);

