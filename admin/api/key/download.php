<?php
require_once "keys.inc.php";
header("Content-Disposition: attachment; filename=\"keys_exports_" . date("Y-m-d_H:i:s") . ".csv\"");
header("Content-Type: application/octet-stream");
header("Connection: close");

if (checkDatabase($arr)) {
    $statement = getKeys();

    $statementSuccess = $statement->execute();
    $outputString = "Key;Gesperrt;Benutzt;Abgestimmt\r\n";
    if ($statementSuccess) {
        http_response_code(200);
        foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $item) {
            $outputString .= $item['VoteKey'] . ";" . $item['Blacklisted'] . ";" . ($item['Used'] ?? "Unbenutzt") . ";" . $item['Voted'] . "\r\n";
        }
        header("Content-Length: " . mb_strlen($outputString));
    } else {
        http_response_code(503);
        $outputString = "Es konnten keine Keys abgerufen werden.";
    }
    echo $outputString;
} else {
    print_r($arr);
}

