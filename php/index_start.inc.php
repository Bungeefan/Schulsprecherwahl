<?php
require_once __DIR__ . "/../default_start.inc.php";
if (!$database->isWorking()) {
    echo("Can't access database '" . DB_NAME . "', you can (re)create it in the admin panel!");
}
$title = "Schulsprecherwahl " . date("Y") . "/" . (date("y") + 1);
