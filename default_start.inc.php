<?php
ini_set('html_errors', false);
define("PROJECT_PATH", str_replace($_SERVER["DOCUMENT_ROOT"] . "/", "", str_replace("\\", "/", __DIR__)));

$ua = htmlentities($_SERVER['HTTP_USER_AGENT'], ENT_QUOTES, 'UTF-8');
if (basename($_SERVER["SCRIPT_FILENAME"], '.inc.php') !== "not_supported" && (preg_match('~MSIE|Internet Explorer~i', $ua)
        || (str_contains($ua, 'Trident/7.0') && str_contains($ua, 'rv:11.0')))) {
    $browser = "Internet Explorer";
    include_once("php/not_supported.inc.php");
    die();
}

require_once __DIR__ . "/admin/api/config/Database.php";

$configFile = __DIR__ . "/settings.cfg.json";
$config = new stdClass();
$upload_folder = "uploads/files/";
$intern_upload_folder = __DIR__ . "/" . $upload_folder;

$database = new Database();

readConfig();

function checkKey($key, $checkUsed = true): bool
{
    global $errorMessage;
    if (isset($key)) {
        global $database;
        if ($database->isWorking()) {
            $statement = $database->getConnection()->prepare("SELECT * FROM `voting_keys` WHERE VoteKey = :voteKey");
            $statement->execute(array(':voteKey' => $key));
            $result = $statement->fetch(PDO::FETCH_ASSOC);
            if ($result !== null) {
                if (!$result['Blacklisted']) {
                    if ($checkUsed) {
                        if ($result['Used'] === null) {
                            return true;
                        }

                        $errorMessage = "Dieser Key wurde schon benutzt!";
                    } else {
                        return true;
                    }
                } else {
                    $errorMessage = "Dieser Key wurde gesperrt!";
                }
            }
        }
    }
    if (empty($errorMessage)) {
        $errorMessage = "Dieser Key ist ungültig!";
    }
    return false;
}

function checkKeyVotes($key): bool
{
    global $errorMessage;
    $result = getKeyVotes($key);
    if ($result < 2) {
        return true;
    }

    $errorMessage = "Mit diesem Key wurde bereits abgestimmt!";
    return false;
}

function getKeyVotes($key)
{
    $result = 0;
    if (isset($key)) {
        global $database;
        $statement = $database->getConnection()->prepare("SELECT COUNT(DISTINCT c.CandidateType) FROM `votes` v INNER JOIN candidates c on c.ID = v.CandidateID WHERE VoteKey = :voteKey");
        $statement->execute(array(':voteKey' => $key));
        $result = $statement->fetchColumn();
        $statement = $database->getConnection()->prepare("SELECT COUNT(DISTINCT c.CandidateType) FROM `votes_runoff` v INNER JOIN candidates c on c.ID = v.CandidateID WHERE VoteKey = :voteKey");
        $statement->execute(array(':voteKey' => $key));
        $result += $statement->fetchColumn();
    }
    return $result;
}

function updateKeyUsedTime($key)
{
    global $database;
    $statement = $database->getConnection()->prepare("UPDATE `voting_keys` SET Used = NOW() WHERE VoteKey = :voteKey");
    $statement->execute(array(':voteKey' => $key));
}

function logout($key)
{
    if (isset($key)) {
        session_destroy();
    }
}

function getCandidates($currentType): PDOStatement
{
    global $database;
    if ($currentType['DependingOnClass']) {
        $statement = $database->getConnection()->prepare("
SELECT *
FROM   `candidates` ca
       INNER JOIN `classes` c
               ON ca.Class = c.Name
WHERE  CandidateType = :type
       AND c.SubjectArea = (SELECT SubjectArea
                            FROM   classes cla
                                   INNER JOIN voting_keys k
                                           ON cla.Name = k.Class
                            WHERE  VoteKey = :key)
ORDER  BY ID ASC
");
//    $statement = $database->getConnection()->prepare("SELECT * FROM `candidates` WHERE CandidateType = :type" .
//        ($currentType['DependingOnClass'] ? " AND Class = " . $_SESSION['key']['Class'] : "") .
//        " ORDER BY ID ASC");
        $statement->bindValue(":key", $_SESSION['key']);
    } else {
        $statement = $database->getConnection()->prepare("SELECT * FROM `candidates` WHERE CandidateType = :type ORDER BY ID ASC");
    }
    $statement->bindValue(":type", $currentType['ID'], PDO::PARAM_INT);
    $statement->execute();
    return $statement;
}

function isLoginDisabled(): bool
{
    global $config;
    return $config['loginDisabled'] ?? false;
}

function setLoginDisabled($value)
{
    global $config;
    $config['loginDisabled'] = $value;
}

function isVoteDisabled(): bool
{
    global $config;
    return $config['voteDisabled'] ?? false;
}

function setVoteDisabled($value)
{
    global $config;
    $config['voteDisabled'] = $value;
}

/**
 * @throws JsonException
 */
function saveConfig()
{
    global $configFile, $config;
    $openConfigFile = fopen($configFile, 'wb');
    if ($openConfigFile) {
        fwrite($openConfigFile, json_encode($config, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT));
        fclose($openConfigFile);
    }
}

function readConfig()
{
    global $configFile, $config;
    if (file_exists($configFile)) {
        $fileContent = file_get_contents($configFile);
        if ($fileContent !== false) {
            try {
                $config = json_decode($fileContent, true, 512, JSON_THROW_ON_ERROR);
            } catch (JsonException) {
            }
        }
    }
    if (!isset($config)) {
        $config = new stdClass();
    }
}
