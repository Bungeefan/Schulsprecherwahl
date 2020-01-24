<?php
ini_set('html_errors', false);
define("PROJECT_PATH", str_replace($_SERVER["DOCUMENT_ROOT"] . "/", "", str_replace("\\", "/", __DIR__)));
set_include_path(get_include_path() . PATH_SEPARATOR . PROJECT_PATH . PATH_SEPARATOR . __DIR__);
chdir(__DIR__);

$ua = htmlentities($_SERVER['HTTP_USER_AGENT'], ENT_QUOTES, 'UTF-8');
if (basename($_SERVER["SCRIPT_FILENAME"], '.inc.php') != "not_supported" && (preg_match('~MSIE|Internet Explorer~i', $ua) || (strpos($ua, 'Trident/7.0') !== false && strpos($ua, 'rv:11.0') !== false))) {
    $browser = "Internet Explorer";
    include_once("php/not_supported.inc.php");
    die();
}

require_once __DIR__ . "/admin/api/config/database.php";

$configFile = PROJECT_PATH . "/settings.cfg.json";
$config = new stdClass();
$upload_folder = "uploads/files/";
$intern_upload_folder = __DIR__ . "/" . $upload_folder;

$database = new Database();

readConfig();

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

function checkKey($key, $checkUsed = true)
{
    global $errorMessage;
    if (isset($key)) {
        global $database;
        if ($database->isWorking()) {
            $statement = $database->getConnection()->prepare("SELECT * FROM `voting_keys` WHERE VoteKey = :voteKey");
            $statement->execute(array(':voteKey' => $key));
            $result = $statement->fetch(PDO::FETCH_ASSOC);
            if ($result != null) {
                if (!$result['Blacklisted']) {
                    if ($checkUsed) {
                        if ($result['Used'] == null) {
                            return true;
                        } else {
                            $errorMessage = "Dieser Key wurde schon benutzt!";
                        }
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

function checkKeyVotes($runoff, $key)
{
    global $errorMessage;
    if (isset($key)) {
        global $database;
        $statement = $database->getConnection()->prepare("SELECT COUNT(*) FROM `" . (!$runoff ? "votes" : "votes_runoff") . "` WHERE VoteKey = :voteKey");
        $statement->execute(array(':voteKey' => $key));
        $result = $statement->fetchColumn();
        if ($result == 0) {
            return true;
        } else {
            $errorMessage = "Mit diesem Key wurde bereits abgestimmt!";
        }
    }
    return false;
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

function isRunoff()
{
    global $database;
    if ($database->isWorking()) {
        $statement = $database->getConnection()->prepare("SELECT COUNT(*) FROM `candidates`");
        $statement->execute();
        $result = $statement->fetchColumn();
        if ($result <= 2) {
            return true;
        }
    }
    return false;
}

function isLoginDisabled()
{
    global $config;
    return $config->loginDisabled ?? false;
}

function setLoginDisabled($value)
{
    global $config;
    $config->loginDisabled = $value;
}

function isVoteDisabled()
{
    global $config;
    return $config->voteDisabled ?? false;
}

function setVoteDisabled($value)
{
    global $config;
    $config->voteDisabled = $value;
}

function saveConfig()
{
    global $configFile, $config;
    $openConfigFile = fopen($configFile, "w");
    fwrite($openConfigFile, json_encode($config, JSON_PRETTY_PRINT));
    fclose($openConfigFile);
}

function readConfig()
{
    global $configFile, $config;
    if (file_exists($configFile)) {
        $fileContent = file_get_contents($configFile);
        if ($fileContent !== false) {
            $config = json_decode($fileContent);
            return;
        }
    }
    $config = new stdClass();
}
