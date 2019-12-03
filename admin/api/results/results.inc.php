<?php
require_once "../config/ajax_start.inc.php";

function getSumVotes()
{
    global $database;
    return $database->getConnection()->prepare("
        SELECT c.ID,
               c.FirstName      AS 'Vorname',
               c.LastName       AS 'Nachname',
               SUM(v.VoteCount) AS 'Stimmen'
        FROM   votes v
               INNER JOIN candidates c
                       ON v.CandidateID = c.ID
               INNER JOIN voting_keys k
                       ON v.VoteKey = k.VoteKey
        WHERE  k.Blacklisted = 0
               AND NOT EXISTS (SELECT votes.VoteKey,
                                      votes.VoteCount,
                                      COUNT(*)
                               FROM   votes
                               WHERE  votes.VoteKey = v.VoteKey
                               GROUP  BY votes.VoteCount
                               HAVING COUNT(*) > 1
                                       OR ( votes.VoteCount = 0
                                            AND (SELECT COUNT(*)
                                                 FROM   candidates) <= 6 )
                               ORDER  BY NULL)
        GROUP  BY v.CandidateID
        ORDER  BY Stimmen DESC 
    ");
}

function getFirstVoted()
{
    global $database;
    return $database->getConnection()->prepare("
        SELECT c.ID,
               c.FirstName        AS 'Vorname',
               c.LastName         AS 'Nachname',
               Count(v.VoteCount) AS 'Reihung'
        FROM   votes v
               INNER JOIN candidates c
                       ON v.CandidateID = c.ID
               INNER JOIN voting_keys k
                       ON v.VoteKey = k.VoteKey
        WHERE  v.VoteCount = (SELECT Max(votes.VoteCount)
                              FROM   votes)
               AND k.Blacklisted = 0
               AND NOT EXISTS (SELECT votes.VoteKey,
                                      votes.VoteCount,
                                      COUNT(*)
                               FROM   votes
                               WHERE  votes.VoteKey = v.VoteKey
                               GROUP  BY votes.VoteCount
                               HAVING COUNT(*) > 1
                                       OR ( votes.VoteCount = 0
                                            AND (SELECT COUNT(*)
                                                 FROM   candidates) <= 6 )
                               ORDER  BY NULL)
        GROUP  BY v.CandidateID
        ORDER  BY Reihung DESC 
    ");
}

function getRunoffVotes()
{
    global $database;
    return $database->getConnection()->prepare("
        SELECT c.ID,
               c.FirstName        AS 'Vorname',
               c.LastName         AS 'Nachname',
               Count(candidateID) AS 'Stimmen'
        FROM   votes_runoff vr
               INNER JOIN candidates c
                       ON vr.CandidateID = c.ID
               INNER JOIN voting_keys k
                       ON vr.VoteKey = k.VoteKey
        WHERE  k.Blacklisted = 0
               AND NOT EXISTS (SELECT votes_runoff.VoteKey,
                                      COUNT(*)
                               FROM   votes_runoff
                               WHERE  votes_runoff.VoteKey = vr.VoteKey
                               GROUP  BY votes_runoff.VoteKey
                               HAVING COUNT(*) > 1
                               ORDER  BY NULL)
        GROUP  BY vr.CandidateID
        ORDER  BY Stimmen DESC 
    ");
}
