<?php
require_once "../config/ajax_start.inc.php";

global $arr, $database, $upload_folder;
if (checkDatabase($arr)) {
    $statement = $database->getConnection()->prepare("SELECT " . (isset($_REQUEST['minimized']) ? "ID, FirstName, LastName" : "*") . " FROM `candidates`" . (isset($_REQUEST['candidateID']) ? " WHERE `ID` = :candidateID" : "") . " ORDER BY ID ASC");
    if (isset($_REQUEST['candidateID'])) {
        $candidateID = $_REQUEST['candidateID'];
        $statement->bindValue(":candidateID", $candidateID, PDO::PARAM_INT);
    }
    if ($statement->execute()) {
        http_response_code(200);
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);

        $arr = [];
        foreach ($result as $key => $candidate) {
            if (isset($candidate['ImagePath'])) {
                $candidate['ImagePath'] = $upload_folder . $candidate['ImagePath'];
            }
            $arr[$key] = $candidate;
        }
    } else {
        http_response_code(503);
        $arr = array("message" => "Es konnten keine Kandidaten abgerufen werden.");
    }
}

printResult($arr);
