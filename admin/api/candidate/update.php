<?php
require_once "../config/ajax_start.inc.php";
require_once "candidate.inc.php";
header("Access-Control-Allow-Methods: POST");

$data = (object)array_map(function ($v) {
    return trim(htmlspecialchars($v));
}, $_POST);

global $arr;

if (checkDatabase($arr)) {
    if (
        !empty($data->firstName) &&
        !empty($data->lastName)
    ) {
        $message = "";
        $imagePath = true;
        $imageReceived = processImageIfReceived($data, $imagePath, $message);
        if ($imageReceived) {
            $imageDeleted = deleteOldImage($data->candidateID);
        }

        $statement = $database->getConnection()->prepare("UPDATE `candidates` SET FirstName = :firstName, LastName = :lastName, AdditionalText = :additionalText" . ($imageReceived ? " , ImagePath = :imagePath" : "") . " WHERE ID = :candidateID");
        $statement->bindValue(":candidateID", $data->candidateID, PDO::PARAM_INT);
        $statement->bindValue(":firstName", $data->firstName, PDO::PARAM_STR);
        $statement->bindValue(":lastName", $data->lastName, PDO::PARAM_STR);
        $statement->bindValue(":additionalText", $data->additionalText, PDO::PARAM_STR);
        if ($imageReceived) {
            $statement->bindValue(":imagePath", $imagePath, PDO::PARAM_STR);
        }

        if ($imagePath !== false && $statement->execute()) {
            http_response_code(200);
            $arr = array("message" => "Kandidat (ID: $data->candidateID) wurde aktualisiert.");
        } else {
            http_response_code(503);
            $arr = array("message" => "Kandidat (ID: $data->candidateID) konnte nicht aktualisiert werden." . (!empty($message) ? " Fehler: " . $message : ""));
        }
    }
}

printResult($arr);