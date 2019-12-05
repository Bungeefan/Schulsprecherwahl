<?php
set_include_path(get_include_path() . PATH_SEPARATOR . $_SERVER['DOCUMENT_ROOT']);
require_once "default_start.inc.php";
if (!$database->isWorking()) {
    echo("Can't access database '$db_name', maybe you forgot to create it!");
}
$title = "Schulsprecherwahl " . date("Y") . "/" . (date("y") + 1);
