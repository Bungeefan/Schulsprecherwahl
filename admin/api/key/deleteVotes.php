<?php
require_once "../config/ajax_start.inc.php";
header("Access-Control-Allow-Methods: DELETE, POST");

$data = (object)array_map(function ($v) {
    return trim(htmlspecialchars($v));
}, $_POST);

global $arr;
if (checkDatabase($arr)) {
    if (!empty($data->voteKey)) {
        try {
            if ($database->getConnection()->beginTransaction()) {
                $qmarks = getQMarks($data->voteKey);
                $firstStatement = $database->getConnection()->prepare("DELETE FROM `votes` WHERE `VoteKey` IN (" . $qmarks . ")");
                $secondStatement = $database->getConnection()->prepare("DELETE FROM `votes_runoff` WHERE `VoteKey` IN (" . $qmarks . " )");
                bindArray($firstStatement, $data->voteKey);
                bindArray($secondStatement, $data->voteKey);
                if ($firstStatement->execute() && $secondStatement->execute()) {
                    http_response_code(200);
                    $arr = array("message" => "KeyVotes " . (is_array($data->voteKey) ? "" : "($data->voteKey) ") . "wurden entfernt.");
                } else {
                    http_response_code(503);
                    $arr = array("message" => "KeyVotes " . (is_array($data->voteKey) ? "" : "($data->voteKey) ") . "konnten nicht entfernt werden.");
                }
                $database->getConnection()->commit();
            } else {
                $arr = array("message" => "Transaction begin failed!");
            }
        } catch (Exception $e) {
            $database->getConnection()->rollBack();
            $arr = array("message" => $e->getMessage());
        }
    }
}

printResult($arr);
