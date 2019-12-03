<?php
require_once "../config/ajax_start.inc.php";
header("Access-Control-Allow-Methods: POST");

$data = (object)$_POST;

global $arr;
try {
    setLoginDisabled(isset($data->loginDisabled));
    setVoteDisabled(isset($data->voteDisabled));
    saveConfig();
    http_response_code(200);
    $arr = array("message" => "Einstellungen wurden aktualisiert.");
} catch (Exception $e) {
    http_response_code(503);
    $arr = array("message" => "Einstellungen konnten nicht aktualisiert werden.", "error" => $e->getMessage());
}

printResult($arr);
