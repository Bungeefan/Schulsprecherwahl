<?php
require_once "keys.inc.php";

global $arr;
if (checkDatabase($arr)) {
    $statement = getKeys(isset($_REQUEST['voteKey']) ? $_REQUEST['voteKey'] : null);

    if ($statement->execute()) {
        http_response_code(200);
        $arr = $statement->fetchAll(PDO::FETCH_ASSOC);
    } else {
        http_response_code(503);
        $arr = array("message" => "Es konnten keine Keys abgerufen werden.");
    }
}

printResult($arr);
