<?php
require_once "results.inc.php";

global $arr;
if (checkDatabase($arr)) {
    $results = getResults();

    if ($results != null) {
        http_response_code(200);
        $arr = $results;
    } else {
        http_response_code(503);
        $arr = array("message" => "Es konnten keine Ergebnisse abgerufen werden.");
    }
}

printResult($arr);
