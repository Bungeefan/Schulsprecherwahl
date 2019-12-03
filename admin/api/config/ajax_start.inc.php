<?php
set_include_path(get_include_path() . PATH_SEPARATOR . $_SERVER['DOCUMENT_ROOT']);
require_once 'default_start.inc.php';
header("Content-Type: application/json; charset=UTF-8");

function checkDatabase(&$arr)
{
    global $database;
    $isWorking = $database->isWorking();
    if (!$isWorking) {
        http_response_code(503);
        $arr = array("message" => "Can't access database.");
    }
    return $isWorking;
}

function printResult($arr)
{
    if (!isset($arr)) {
        http_response_code(400);
        $arr = array("message" => "Unable to perform action. Data is incomplete.");
    }
    echo json_encode($arr, JSON_PRETTY_PRINT);
}

function getQMarks(&$array)
{
    $array = singeltonArray($array);
    return join(",", array_fill(0, count($array), "?"));
}

function bindArray($statement, &$array)
{
    $array = singeltonArray($array);
    for ($i = 0; $i < count($array); $i++) {
        $statement->bindValue($i + 1, $array[$i]);
    }
}

function singeltonArray($value)
{
    if (!empty($value)) {
        if (!is_array($value)) {
            if (is_string($value)) {
                $value = explode(",", $value);
            } else {
                $value = (array)$value;
            }
        }
    }
    return $value;
}