<?php
require_once "results.inc.php";
header("Content-Disposition: attachment; filename=\"results_exports_" . date("Y-m-d_H:i:s") . ".csv\"");
header("Content-Type: application/octet-stream");
header("Connection: close");

if (checkDatabase($arr)) {
    $sumVotesStatement = getSumVotes();
    $firstVotesStatement = getFirstVoted();
    $runoffVotesStatement = getRunoffVotes();

    $sumVotesSuccess = $sumVotesStatement->execute();
    $firstVotesSuccess = $firstVotesStatement->execute();
    $runoffVotesSuccess = $runoffVotesStatement->execute();
    $outputString = "";
    if ($sumVotesSuccess || $firstVotesSuccess || $runoffVotesSuccess) {
        http_response_code(200);

        $outputString .= "Stimmen\r\n";
        $sumVotes = $sumVotesStatement->fetchAll(PDO::FETCH_ASSOC);
        if (!empty($sumVotes)) {
            $outputString .= implode(";", array_keys($sumVotes[0])) . "\r\n";
            foreach ($sumVotes as $item) {
                $outputString .= implode(";", $item) . "\r\n";
            }
        }

        $outputString .= "\r\n\r\nKandidat mit den meisten Erstreihungen\r\n";
        $firstVotes = $firstVotesStatement->fetchAll(PDO::FETCH_ASSOC);
        if (!empty($firstVotes)) {
            $outputString .= implode(";", array_keys($firstVotes[0])) . "\r\n";
            foreach ($firstVotes as $item) {
                $outputString .= implode(";", $item) . "\r\n";
            }
        }

        $outputString .= "\r\n\r\nStichwahl\r\n";
        $runoffVotes = $runoffVotesStatement->fetchAll(PDO::FETCH_ASSOC);
        if (!empty($runoffVotes)) {
            $outputString .= implode(";", array_keys($runoffVotes[0])) . "\r\n";
            foreach ($runoffVotes as $item) {
                $outputString .= implode(";", $item) . "\r\n";
            }
        }

        header("Content-Length: " . mb_strlen($outputString));
    } else {
        http_response_code(503);
        $outputString .= "Es konnten keine Ergebnisse abgerufen werden.";
    }
    echo $outputString;
} else {
    print_r($arr);
}
