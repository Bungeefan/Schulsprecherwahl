<?php
require_once "../config/ajax_start.inc.php";
require_once "candidate.inc.php";
header("Access-Control-Allow-Methods: DELETE, POST");

parse_str(file_get_contents("php://input"), $data);
$data = (object)array_map(function ($v) {
    return trim(htmlspecialchars($v));
}, $data);

global $arr, $database;
if (!empty($data->candidateID) && checkDatabase($arr)) {
    $params = array(":candidateID" => $data->candidateID);
    $imageDeleted = deleteOldImage($data->candidateID);

    $statement = $database->getConnection()->prepare("DELETE FROM `candidates` WHERE ID = :candidateID");
    if ($statement->execute($params)) {
        http_response_code(200);
        $arr = array("message" => "Kandidat (ID: $data->candidateID) wurde gelöscht.");
    } else {
        http_response_code(503);
        $arr = array("message" => "Kandidat (ID: $data->candidateID) konnte nicht gelöscht werden.");
    }
}

printResult($arr);
