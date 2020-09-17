<?php
require_once "results.inc.php";
header("Content-Disposition: attachment; filename=\"results_exports_" . date("Y-m-d_H:i:s") . ".txt\"");
header("Content-Type: application/octet-stream");
header("Connection: close");

if (checkDatabase($arr)) {
    $results = getResults();

    $outputString = "";
    if ($results != null) {
        http_response_code(200);
        foreach ($results as $key => $result) {
            $outputString .= "$key\r\n\r\n";

            $outputString .= "Stimmen\r\n";
            $sumVotes = $result[array_keys($result)[0]];
            if (!empty($sumVotes)) {
                $outputString .= implode(";", array_keys($sumVotes[0])) . "\r\n";
                foreach ($sumVotes as $item) {
                    $outputString .= implode(";", $item) . "\r\n";
                }
            }

            $outputString .= "\r\n\r\nKandidat mit den meisten Erstreihungen\r\n";
            $firstVotes = $result[array_keys($result)[1]];
            if (!empty($firstVotes)) {
                $outputString .= implode(";", array_keys($firstVotes[0])) . "\r\n";
                foreach ($firstVotes as $item) {
                    $outputString .= implode(";", $item) . "\r\n";
                }
            }

            $outputString .= "\r\n\r\nStichwahl\r\n";
            $runoffVotes = $result[array_keys($result)[2]];
            if (!empty($runoffVotes)) {
                $outputString .= implode(";", array_keys($runoffVotes[0])) . "\r\n";
                foreach ($runoffVotes as $item) {
                    $outputString .= implode(";", $item) . "\r\n";
                }
            }

            $outputString .= "\r\n\r\n";
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
