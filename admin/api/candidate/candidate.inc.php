<?php
require_once __DIR__ . "/../config/ajax_start.inc.php";
require_once "upload.inc.php";

//File Status
const NOT_RECEIVED = -1;
const UPLOAD = 0;
const DELETE = 1;

function deleteOldImage($candidateID)
{
    global $database, $intern_upload_folder;
    $statement = $database->getConnection()->prepare("
SELECT ImagePath
FROM `candidates` c
WHERE `ID` = :candidateID
  AND NOT EXISTS(
        SELECT 1
        FROM `candidates`
        WHERE ImagePath = c.ImagePath
        HAVING COUNT(*) > 1
    )");
    if ($statement->execute(array(":candidateID" => $candidateID))) {
        $result = $statement->fetchColumn();
        if ($result !== null && file_exists($intern_upload_folder . $result)) {
            $imageDeleted = unlink($intern_upload_folder . $result);
        } else {
            $imageDeleted = false;
        }
    } else {
        $imageDeleted = false;
    }
    return $imageDeleted;
}

function imageReceived($data): int
{
    $fileStatus = NOT_RECEIVED;
    if (!empty($_FILES['image']['name'])) {
        $fileStatus = UPLOAD;
    } else if (empty($data->candidateImage)) {
        //user pressed delete button
        $fileStatus = DELETE;
    }
    return $fileStatus;
}

function processImage(&$imagePath, &$message)
{
    if (!empty($_FILES['image']['name'])) {
        $imagePath = processReceivedImage($message);
    }
}
