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

    if (count($results) === count($candidates_types)) {
        return $results;
    }

    return null;
}

function getSumVotes($type)
{
    global $database;
    $statement = $database->getConnection()->prepare("
        SELECT c.ID,
               ctypes.Type                               AS 'Typ',
               CONCAT(classes.Name, classes.SubjectArea) AS 'Klasse',
               classes.SubjectArea                       AS '_SubjectArea',
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
                                      INNER JOIN candidates c3
                                              ON votes.CandidateID = c3.ID
                                      INNER JOIN candidates_types ctypes3
                                              ON c3.CandidateType = ctypes3.ID
                                      INNER JOIN classes class3
                                              ON c3.Class = class3.Name
                               WHERE  votes.VoteKey = v.VoteKey
                                      AND c3.CandidateType = :type
                                      AND ( ctypes3.DependingOnClass = 0
                                             OR class3.SubjectArea =
                                          classes.SubjectArea )
                               GROUP  BY votes.VoteCount
                               HAVING COUNT(*) > 1
                                       OR votes.VoteCount = 0
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
               classes.SubjectArea                       AS '_SubjectArea',
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
                                     INNER JOIN candidates c2
                                             ON votes.CandidateID = c2.ID
                                     INNER JOIN candidates_types ctypes2
                                             ON c2.CandidateType = ctypes2.ID
                                     INNER JOIN classes class2
                                             ON c2.Class = class2.Name
                              WHERE  c2.CandidateType = :type
                                     AND ( ctypes2.DependingOnClass = 0
                                            OR class2.SubjectArea =
                                         classes.SubjectArea ))
               AND k.Blacklisted = 0
               AND c.CandidateType = :type
               AND NOT EXISTS (SELECT votes.VoteKey,
                                      votes.VoteCount,
                                      COUNT(*)
                               FROM   votes
                                      INNER JOIN candidates c3
                                              ON votes.CandidateID = c3.ID
                                      INNER JOIN candidates_types ctypes3
                                              ON c3.CandidateType = ctypes3.ID
                                      INNER JOIN classes class3
                                              ON c3.Class = class3.Name
                               WHERE  votes.VoteKey = v.VoteKey
                                      AND c3.CandidateType = :type
                                      AND ( ctypes3.DependingOnClass = 0
                                             OR class3.SubjectArea =
                                          classes.SubjectArea )
                               GROUP  BY votes.VoteCount
                               HAVING COUNT(*) > 1
                                       OR votes.VoteCount = 0
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
               classes.SubjectArea                       AS '_SubjectArea',
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
                                      INNER JOIN candidates_types ctypes
                                              ON c.CandidateType = ctypes.ID
                                      INNER JOIN classes class
                                              ON c.Class = class.Name
                               WHERE  votes_runoff.VoteKey = vr.VoteKey
                                      AND c.CandidateType = :type
                                      AND ( ctypes.DependingOnClass = 0
                                             OR class.SubjectArea =
                                          classes.SubjectArea )
                               GROUP  BY votes_runoff.VoteKey
                               HAVING COUNT(*) > 1
                               ORDER  BY NULL)
        GROUP  BY vr.CandidateID
        ORDER  BY Stimmen DESC
    ");
    $statement->bindValue(':type', $type['ID'], PDO::PARAM_INT);
    return $statement;
}
