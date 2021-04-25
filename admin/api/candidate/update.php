<?php
require_once "../config/ajax_start.inc.php";
require_once "candidate.inc.php";
header("Access-Control-Allow-Methods: POST");

$data = (object)array_map(function ($v) {
    return trim(htmlspecialchars($v));
}, $_POST);

global $arr, $database;
if (!empty($data->lastName) &&
    !empty($data->firstName) &&
    checkDatabase($arr)) {
    $message = "";
    $imagePath = null;

    $imageStatus = imageReceived($data);
    $imageReceived = $imageStatus !== NOT_RECEIVED;
    if ($imageReceived) {
        $imageDeleted = deleteOldImage($data->candidateID);

        if ($imageStatus === UPLOAD) {
            processImage($imagePath, $message);
            if ($imagePath === null) {
                http_response_code(503);
                $arr = array("message" => "Kandidat (ID: $data->candidateID) konnte nicht aktualisiert werden." . (!empty($message) ? " Fehler: " . $message : ""));
            }
        }
    }

    if (empty($message)) {
        $statement = $database->getConnection()->prepare("UPDATE `candidates` SET CandidateType = :candidateType, Class = :class, FirstName = :firstName, LastName = :lastName, AdditionalText = :additionalText" . ($imageReceived ? " , ImagePath = :imagePath" : "") . " WHERE ID = :candidateID");
        $statement->bindValue(":candidateType", $data->candidateType, PDO::PARAM_INT);
        $statement->bindValue(":class", $data->candidateClass);
        $statement->bindValue(":candidateID", $data->candidateID, PDO::PARAM_INT);
        $statement->bindValue(":firstName", $data->firstName);
        $statement->bindValue(":lastName", $data->lastName);
        $statement->bindValue(":additionalText", $data->additionalText);
        if ($imageReceived) {
            $statement->bindValue(":imagePath", $imagePath);
        }

        if ($statement->execute()) {
            http_response_code(200);
            $arr = array("message" => "Kandidat (ID: $data->candidateID) wurde aktualisiert.");
        } else {
            http_response_code(503);
            $arr = array("message" => "Kandidat (ID: $data->candidateID) konnte nicht aktualisiert werden." . (!empty($message) ? " Fehler: " . $message : ""));
        }
    }
}

printResult($arr);
