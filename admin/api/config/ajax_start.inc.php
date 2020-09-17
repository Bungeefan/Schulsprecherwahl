<?php
require_once __DIR__ . "/../../../default_start.inc.php";

function checkDatabase(&$arr)
{
    global $database;
    $isWorking = $database->isWorking();
    if (!$isWorking) {
        http_response_code(503);
        $arr = array("message" => $database->getLastError());
    }
    return $isWorking;
}

function printResult($arr)
{
    header("Content-Type: application/json; charset=UTF-8");
    if (!isset($arr)) {
        http_response_code(400);
        $arr = array("message" => "Unable to perform action. Data is incomplete.");
    }
    echo json_encode($arr, JSON_PRETTY_PRINT);
}

function getQMarks(&$array)
{
    $array = singeltonArray($array);

    $func = function ($value) {
        global $database;
        return $database->getConnection()->quote($value);
    };
    array_map($func, $array);
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
