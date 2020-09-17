<?php
require_once "../config/ajax_start.inc.php";

function getResults()
{
    global $database;
    $statement = $database->getConnection()->query("SELECT * FROM `candidates_types` ORDER BY ID ASC");
    $candidates_types = $statement->fetchAll(PDO::FETCH_ASSOC);

    $results = array();

    foreach ($candidates_types as $type) {
        $sumVotesStatement = getSumVotes($type);
        $firstVotesStatement = getFirstVoted($type);
        $runoffVotesStatement = getRunoffVotes($type);
        $sumVotesSuccess = $sumVotesStatement->execute();
        $firstVotesSuccess = $firstVotesStatement->execute();
        $runoffVotesSuccess = $runoffVotesStatement->execute();

        if ($sumVotesSuccess || $firstVotesSuccess || $runoffVotesSuccess) {
            $results[$type['Type']] = array(
                "Gesamt" => $sumVotesStatement->fetchAll(PDO::FETCH_ASSOC),
                "Reihung" => $firstVotesStatement->fetchAll(PDO::FETCH_ASSOC),
                "Stichwahl" => $runoffVotesStatement->fetchAll(PDO::FETCH_ASSOC),
            );
        } else {
            break;
        }
    }

    if (count($results) == count($candidates_types)) {
        return $results;
    } else {
        return null;
    }
}

function getSumVotes($type)
{
    global $database;
    $statement = $database->getConnection()->prepare("
        SELECT c.ID,
               ctypes.Type                               AS 'Typ',
               CONCAT(classes.Name, classes.SubjectArea) AS 'Klasse',
               c.FirstName                               AS 'Vorname',
               c.LastName                                AS 'Nachname',
               SUM(v.VoteCount)                          AS 'Stimmen'
        FROM   votes v
               INNER JOIN candidates c
                       ON v.CandidateID = c.ID
               INNER JOIN voting_keys k
                       ON v.VoteKey = k.VoteKey
               INNER JOIN candidates_types ctypes
                       ON c.CandidateType = ctypes.ID
               INNER JOIN classes classes
                       ON c.Class = classes.Name
        WHERE  k.Blacklisted = 0
               AND c.CandidateType = :type
               AND NOT EXISTS (SELECT votes.VoteKey,
                                      votes.VoteCount,
                                      COUNT(*)
                               FROM   votes
                                      INNER JOIN candidates c
                                              ON votes.CandidateID = c.ID
                               WHERE  votes.VoteKey = v.VoteKey
                                      AND c.CandidateType = :type
                               GROUP  BY votes.VoteCount
                               HAVING COUNT(*) > 1
                                       OR ( votes.VoteCount = 0
                                            AND (SELECT COUNT(*)
                                                 FROM   candidates
                                                 WHERE  candidates.CandidateType = :type
                                                ) <= 6
                                          )
                               ORDER  BY NULL)
        GROUP  BY v.CandidateID
        ORDER  BY Stimmen DESC
    ");
    $statement->bindValue(':type', $type['ID'], PDO::PARAM_INT);
    return $statement;
}

function getFirstVoted($type)
{
    global $database;
    $statement = $database->getConnection()->prepare("
        SELECT c.ID,
               ctypes.Type                               AS 'Typ',
               CONCAT(classes.Name, classes.SubjectArea) AS 'Klasse',
               c.FirstName                               AS 'Vorname',
               c.LastName                                AS 'Nachname',
               COUNT(v.VoteCount)                        AS 'Reihung'
        FROM   votes v
               INNER JOIN candidates c
                       ON v.CandidateID = c.ID
               INNER JOIN voting_keys k
                       ON v.VoteKey = k.VoteKey
               INNER JOIN candidates_types ctypes
                       ON c.CandidateType = ctypes.ID
               INNER JOIN classes classes
                       ON c.Class = classes.Name
        WHERE  v.VoteCount = (SELECT MAX(votes.VoteCount)
                              FROM   votes
                                     INNER JOIN candidates c
                                             ON votes.CandidateID = c.ID
                              WHERE  c.CandidateType = :type)
               AND k.Blacklisted = 0
               AND c.CandidateType = :type
               AND NOT EXISTS(SELECT votes.VoteKey,
                                     votes.VoteCount,
                                     COUNT(*)
                              FROM   votes
                                     INNER JOIN candidates c
                                             ON votes.CandidateID = c.ID
                              WHERE  votes.VoteKey = v.VoteKey
                                     AND c.CandidateType = :type
                              GROUP  BY votes.VoteCount
                              HAVING COUNT(*) > 1
                                      OR ( votes.VoteCount = 0
                                           AND (SELECT COUNT(*)
                                                FROM   candidates
                                                WHERE  candidates.CandidateType = :type)
                                               <= 6 )
                              ORDER  BY NULL)
        GROUP  BY v.CandidateID
        ORDER  BY Reihung DESC
    ");
    $statement->bindValue(':type', $type['ID'], PDO::PARAM_INT);
    return $statement;
}

function getRunoffVotes($type)
{
    global $database;
    $statement = $database->getConnection()->prepare("
        SELECT c.ID,
               ctypes.Type                               AS 'Typ',
               CONCAT(classes.Name, classes.SubjectArea) AS 'Klasse',
               c.FirstName                               AS 'Vorname',
               c.LastName                                AS 'Nachname',
               COUNT(candidateID)                        AS 'Stimmen'
        FROM   votes_runoff vr
               INNER JOIN candidates c
                       ON vr.CandidateID = c.ID
               INNER JOIN voting_keys k
                       ON vr.VoteKey = k.VoteKey
               INNER JOIN candidates_types ctypes
                       ON c.CandidateType = ctypes.ID
               INNER JOIN classes classes
                       ON c.Class = classes.Name
        WHERE  k.Blacklisted = 0
               AND c.CandidateType = :type
               AND NOT EXISTS (SELECT votes_runoff.VoteKey,
                                      COUNT(*)
                               FROM   votes_runoff
                                      INNER JOIN candidates c
                                              ON votes_runoff.CandidateID = c.ID
                               WHERE  votes_runoff.VoteKey = vr.VoteKey
                                      AND c.CandidateType = :type
                               GROUP  BY votes_runoff.VoteKey
                               HAVING COUNT(*) > 1
                               ORDER  BY NULL)
        GROUP  BY vr.CandidateID
        ORDER  BY Stimmen DESC
    ");
    $statement->bindValue(':type', $type['ID'], PDO::PARAM_INT);
    return $statement;
}
