<?php
require_once "../config/database.php";
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
        if ($result != null) {
            $imageDeleted = unlink($intern_upload_folder . $result);
        } else {
            $imageDeleted = false;
        }
    } else $imageDeleted = false;
    return $imageDeleted;
}

function processImageIfReceived($data, &$imagePath, &$message)
{
    $imageReceived = false;
    if (!empty($_FILES['image']['name'])) {
        $imagePath = processImage($message);
        $imageReceived = true;
    } else if (empty($data->candidateImage)) {
        $imagePath = null;
        $imageReceived = true;
    }
    return $imageReceived;
}