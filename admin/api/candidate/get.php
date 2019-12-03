<?php
require_once "../config/ajax_start.inc.php";

global $arr;
if (checkDatabase($arr)) {
    $statement = $database->getConnection()->prepare("SELECT " . (isset($_REQUEST['minimized']) ? "ID, FirstName, LastName" : "*") . " FROM `candidates`" . (isset($_REQUEST['candidateID']) ? " WHERE `ID` = :candidateID" : "") . " ORDER BY ID ASC");
    if (isset($_REQUEST['candidateID'])) {
        $candidateID = $_REQUEST['candidateID'];
        $statement->bindValue(":candidateID", $candidateID, PDO::PARAM_INT);
    }
    if ($statement->execute()) {
        http_response_code(200);
        $arr = $statement->fetchAll(PDO::FETCH_ASSOC);
    } else {
        http_response_code(503);
        $arr = array("message" => "Es konnten keine Kandidaten abgerufen werden.");
    }
}

printResult($arr);
