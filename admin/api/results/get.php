<?php
require_once "results.inc.php";

global $arr;
if (checkDatabase($arr)) {
    $sumVotesStatement = getSumVotes();
    $firstVotesStatement = getFirstVoted();
    $runoffVotesStatement = getRunoffVotes();
    $sumVotesSuccess = $sumVotesStatement->execute();
    $firstVotesSuccess = $firstVotesStatement->execute();
    $runoffVotesSuccess = $runoffVotesStatement->execute();
    if ($sumVotesSuccess || $firstVotesSuccess || $runoffVotesSuccess) {
        http_response_code(200);
        $arr = array(
            "Gesamt" => $sumVotesStatement->fetchAll(PDO::FETCH_ASSOC),
            "Reihung" => $firstVotesStatement->fetchAll(PDO::FETCH_ASSOC),
            "Stichwahl" => $runoffVotesStatement->fetchAll(PDO::FETCH_ASSOC),
        );
    } else {
        http_response_code(503);
        $arr = array("message" => "Es konnten keine Ergebnisse abgerufen werden.");
    }
}

printResult($arr);
