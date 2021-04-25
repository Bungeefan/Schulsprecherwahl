<?php
require_once "keys.inc.php";
header("Access-Control-Allow-Methods: POST");

$data = (object)array_map(function ($v) {
    return trim(htmlspecialchars($v));
}, $_POST);

global $arr, $database;
if (isset($data->amount, $data->class) && checkDatabase($arr)) {
    $inserts = "";
    if ($data->amount <= 500) {
        for ($i = 0; $i < $data->amount; $i++) {
            $generatedOTP = generateNumericOTP();
            $result = $database->getConnection()->query("SELECT 1 FROM `voting_keys` WHERE VoteKey = '$generatedOTP'");
            if ($result->rowCount() === 0) {
                $inserts .= "('$generatedOTP', " . $database->getConnection()->quote($data->class) . ")";
            } else {
                $i--;
            }
            if ($i < $data->amount - 1) {
                $inserts .= ", ";
            }
        }
        $statement = $database->getConnection()->prepare("INSERT INTO `voting_keys` (`VoteKey`, `Class`) VALUES " . $inserts);
        if ($statement->execute()) {
            http_response_code(201);
            $arr = array("message" => ("$data->amount Key(s) für '$data->class' wurde(n) generiert."));
        } else {
            http_response_code(503);
            $arr = array("message" => ("Es konnten keine Keys generiert werden."));
        }
    } else {
        http_response_code(400);
        $arr = array("message" => ("Es können nicht mehr als 500 Keys auf einmal generiert werden."));
    }
}

printResult($arr);
