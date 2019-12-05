<?php
require_once __DIR__ . "/../default_start.inc.php";
if (!$database->isWorking()) {
    echo("Can't access database '$db_name', maybe you forgot to create it!");
}
$title = "Schulsprecherwahl " . date("Y") . "/" . (date("y") + 1);
