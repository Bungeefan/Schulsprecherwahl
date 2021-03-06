<?php
require_once "keys.inc.php";

global $arr;
if (checkDatabase($arr)) {
    $statement = getKeys($_REQUEST['voteKey'] ?? null, $_REQUEST['class'] ?? null);

    if ($statement->execute()) {
        http_response_code(200);
        $arr = $statement->fetchAll(PDO::FETCH_ASSOC);
    } else {
        http_response_code(503);
        $arr = array("message" => "Es konnten keine Keys abgerufen werden.");
    }
}

printResult($arr);
