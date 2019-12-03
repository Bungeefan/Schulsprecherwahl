<?php
require_once "../config/ajax_start.inc.php";
require_once "candidate.inc.php";
header("Access-Control-Allow-Methods: PUT, POST");

//parse_str(file_get_contents("php://input"), $data);
//$data = (object)array_map(function ($v) {
//  return trim(htmlspecialchars($v));
//}, $data);
//php://input ist nicht mit formdata kompatibel
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

        $statement = $database->getConnection()->prepare("INSERT INTO `candidates` (`FirstName`, `LastName`, `AdditionalText`" . ($imageReceived ? " , `ImagePath`" : "") . ") VALUES (:firstName, :lastName, :additionalText" . ($imageReceived ? " , :imagePath" : "") . ")");
        $statement->bindValue(":firstName", $data->firstName, PDO::PARAM_STR);
        $statement->bindValue(":lastName", $data->lastName, PDO::PARAM_STR);
        $statement->bindValue(":additionalText", $data->additionalText, PDO::PARAM_STR);
        if ($imageReceived) {
            $statement->bindValue(":imagePath", $imagePath, PDO::PARAM_STR);
        }

        if ($statement->execute()) {
            http_response_code(201);
            $arr = array("message" => "Kandidat wurde erstellt.");
        } else {
            http_response_code(503);
            $arr = array("message" => "Kandidat konnte nicht erstellt werden." . (!empty($message) ? " Fehler: " . $message : ""));
        }
    }
}

printResult($arr);
