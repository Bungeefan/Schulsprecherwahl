<?php
require_once __DIR__ . "/../default_start.inc.php";
global $database;
if (!$database->isWorking()) {
    $errorMessage = $database->getLastError();
}
$title = "Schulsprecherwahl " . date("Y") . "/" . (date("y") + 1);
