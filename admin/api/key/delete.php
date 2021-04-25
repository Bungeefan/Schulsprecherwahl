<?php
require_once "../config/ajax_start.inc.php";
header("Access-Control-Allow-Methods: DELETE, POST");

$data = (object)array_map(function ($v) {
    return trim(htmlspecialchars($v));
}, $_POST);

global $arr, $database;
if (!empty($data->voteKey) && checkDatabase($arr)) {
    $voteKeys = $data->voteKey;
    $statement = $database->getConnection()->prepare("DELETE FROM `voting_keys` WHERE `VoteKey` IN (" . getQMarks($data->voteKey) . ")");
    bindArray($statement, $data->voteKey);
    if ($statement->execute()) {
        http_response_code(200);
        $arr = array("message" => (is_array($data->voteKey) ? "Keys" : "Key ($data->voteKey)") . " wurde(n) entfernt.");
    } else {
        http_response_code(503);
        $arr = array("message" => (is_array($data->voteKey) ? "Keys" : "Key ($data->voteKey)") . " konnte(n) nicht entfernt werden.");
    }
}

printResult($arr);
