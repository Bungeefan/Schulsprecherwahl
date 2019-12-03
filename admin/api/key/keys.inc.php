<?php
require_once "../config/ajax_start.inc.php";

function getKeys($voteKeys = null)
{
    global $database;
    $statement = $database->getConnection()->prepare("
SELECT `VoteKey`,
       `Blacklisted`,
       `Used`,
       (SELECT Count(*) > 0
        FROM (SELECT `Votekey`
              FROM votes
              UNION ALL
              SELECT `Votekey`
              FROM votes_runoff) AS A
        WHERE A.votekey = k.voteKey) AS 'Voted'
FROM voting_keys k" . (!empty($voteKeys) ? " WHERE `VoteKey` IN (" . getQmarks($voteKeys) . ")" : "") . "
ORDER BY LENGTH(VoteKey), VoteKey ASC
");
    if (!empty($voteKeys)) {
        bindArray($statement, $voteKeys);
    }
    return $statement;
}
