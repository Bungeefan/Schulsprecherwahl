<?php
require_once __DIR__ . "/../config/ajax_start.inc.php";
require_once "upload.inc.php";

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
        if ($result != null && file_exists($intern_upload_folder . $result)) {
            $imageDeleted = unlink($intern_upload_folder . $result);
        } else {
            $imageDeleted = false;
        }
    } else $imageDeleted = false;
    return $imageDeleted;
}

function imageReceived($data)
{
    $imageReceived = false;
    if (!empty($_FILES['image']['name'])) {
        $imageReceived = true;
    } else if (empty($data->candidateImage)) {
        $imageReceived = true;
    }
    return $imageReceived;
}

function processImage($data, &$imagePath, &$message)
{
    if (!empty($_FILES['image']['name'])) {
        $imagePath = processReceivedImage($message);
        $imageReceived = true;
    } else if (empty($data->candidateImage)) {
        $imagePath = null;
    }
}