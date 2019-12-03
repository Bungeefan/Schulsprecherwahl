<?php
require_once "../config/ajax_start.inc.php";

global $arr;
http_response_code(200);
$arr = array(
    "loginDisabled" => isLoginDisabled(),
    "voteDisabled" => isVoteDisabled(),
);

printResult($arr);

