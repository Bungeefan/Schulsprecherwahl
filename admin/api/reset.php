<?php
require_once "config/ajax_start.inc.php";
header("Access-Control-Allow-Methods: DELETE, POST");

$data = array();
parse_str(file_get_contents("php://input"), $data);
$data = (object)$data;

global $arr, $database, $intern_upload_folder;
if (!empty($data->type) && checkDatabase($arr)) {
    if ($data->type === "votes") {
        $result =
            $database->getConnection()->query("TRUNCATE `votes`")->execute() &&
            $database->getConnection()->query("TRUNCATE `votes_runoff`")->execute();
        if ($result) {
            http_response_code(200);
            $arr = array("message" => "Abstimmungsergebnis wurde gelöscht.");
        } else {
            http_response_code(503);
            $arr = array("message" => "Abstimmungsergebnis konnten nicht gelöscht werden.");
        }
    } else if ($data->type === "candidates") {
        if (checkVotesEmpty()) {
            array_map('unlink', glob("$intern_upload_folder/*.*"));
            $result = $database->getConnection()->query("DELETE FROM `candidates`")->execute();
            if ($result) {
                http_response_code(200);
                $arr = array("message" => "Alle Kandidaten wurde gelöscht.");
            } else {
                http_response_code(503);
                $arr = array("message" => "Kandidaten konnten nicht gelöscht werden.");
            }
        } else {
            http_response_code(428);
            $arr = array("message" => "Kandidaten können nicht gelöscht werden, da noch Stimmen vorhanden sind.");
        }
    } else if ($data->type === "keys") {
        if (checkVotesEmpty()) {
            $result = $database->getConnection()->query("DELETE FROM `voting_keys`")->execute();
            if ($result) {
                http_response_code(200);
                $arr = array("message" => "Alle Keys wurde gelöscht.");
            } else {
                http_response_code(503);
                $arr = array("message" => "Keys konnten nicht gelöscht werden.");
            }
        } else {
            http_response_code(428);
            $arr = array("message" => "Keys können nicht gelöscht werden, da noch Stimmen vorhanden sind.");
        }
    } else {
        http_response_code(400);
        $arr = array("message" => "Type invalid.");
    }
}

printResult($arr);

function checkVotesEmpty()
{
    global $database;
    $votesStatement = $database->getConnection()->prepare("SELECT COUNT(1) FROM `votes`");
    $votesRunoffStatement = $database->getConnection()->prepare("SELECT COUNT(1) FROM `votes_runoff`");
    $votesStatement->execute();
    $votesRunoffStatement->execute();
    return $votesStatement->fetchColumn() === 0 && $votesRunoffStatement->fetchColumn() === 0;
}
