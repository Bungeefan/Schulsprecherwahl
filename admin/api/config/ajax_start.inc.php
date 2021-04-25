<?php
require_once __DIR__ . "/../../../default_start.inc.php";

set_exception_handler(function ($exception) {
    http_response_code(500);
    $arr = array("message" => "Error: " . $exception);
    printResult($arr);
});

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
    echo json_encode($arr, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT);
}

function getQMarks(&$array)
{
    $array = singeltonArray($array);

    $func = static function ($value) {
        global $database;
        return $database->getConnection()->quote($value);
    };
    array_map($func, $array);
    return implode(",", array_fill(0, count($array), "?"));
}

function bindArray($statement, &$array)
{
    $array = singeltonArray($array);
    foreach ($array as $i => $value) {
        $statement->bindValue($i + 1, $value);
    }
}

function singeltonArray($value)
{
    if (!empty($value) && !is_array($value)) {
        if (is_string($value)) {
            $value = explode(",", $value);
        } else {
            $value = (array)$value;
        }
    }
    return $value;
}
