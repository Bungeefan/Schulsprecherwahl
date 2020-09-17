<?php
require_once __DIR__ . "/../config/ajax_start.inc.php";

function getKeys($voteKeys = null, $class = null)
{
    global $database;
    $statement = $database->getConnection()->prepare("
SELECT `VoteKey`,
       (SELECT CONCAT(Name, SubjectArea) FROM classes WHERE Name = k.Class) AS 'Class',
       `Blacklisted`,
       `Used`,
       (SELECT Count(*) > 0
        FROM (SELECT `Votekey`
              FROM votes
              UNION ALL
              SELECT `Votekey`
              FROM votes_runoff) AS A
        WHERE A.votekey = k.voteKey) AS 'Voted'
FROM voting_keys k" . (!empty($voteKeys) ? " WHERE `VoteKey` IN (" . getQmarks($voteKeys) . ")" :
            (isset($class) ? " WHERE k.Class = " . $database->getConnection()->quote($class) : "")) . " ORDER BY LENGTH(VoteKey), Class, VoteKey ASC
");
    if (!empty($voteKeys)) {
        bindArray($statement, $voteKeys);
    }
    return $statement;
}

function generateNumericOTP($n = 8)
{
    //all possible key chars without small l and big I
    $generator = str_shuffle("0123456789abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNOPQRSTUVWXYZ");

    $result = "";
    for ($i = 1; $i <= $n; $i++) {
        $result .= substr($generator, (rand() % (strlen($generator))), 1);
    }
    return $result;
}
