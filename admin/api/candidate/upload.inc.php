<?php
require_once __DIR__ . "/../config/ajax_start.inc.php";

function processReceivedImage(&$message)
{
    global $intern_upload_folder;
    if (!file_exists($intern_upload_folder) &&
        !mkdir($intern_upload_folder, 0777, true) &&
        !is_dir($intern_upload_folder)) {
        $message = "Upload Ordner konnte nicht erstellt werden! " . error_get_last()['message'];
    }
    $fileName = pathinfo($_FILES['image']['name'], PATHINFO_FILENAME);
    $fileExtension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

    $allowed_file_extensions = [
        'apng',
        'bmp',
        'gif',
        'ico', 'cur',
        'jpg', 'jpeg', 'jfif', 'pjpeg', 'pjp',
        'png',
        'svg',
        'tif', 'tiff',
        'webp'
    ];
    if (!in_array($fileExtension, $allowed_file_extensions)) {
        $message = "Ungültige Dateiendung. Nur " . implode(", ", $allowed_file_extensions) . " Dateien sind erlaubt!";
    }

    if (function_exists('exif_imagetype')) { //exif_imagetype erfordert die exif-Erweiterung
        $allowed_file_types = array(IMAGETYPE_PNG, IMAGETYPE_JPEG, IMAGETYPE_GIF);
        $detected_file_type = exif_imagetype($_FILES['image']['tmp_name']);
        if (!in_array($detected_file_type, $allowed_file_types, true)) {
            $message = "Nur der Upload von Bilddateien ist gestattet";
        }
    }

    $newFileName = $fileName . '.' . $fileExtension;
    $moved = move_uploaded_file($_FILES['image']['tmp_name'], $intern_upload_folder . $newFileName);
    if (empty($message)) {
        if ($moved) {
            return $newFileName;
        }

        $message = "Das Bild konnte nicht gespeichert werden!";
    }

    return null;
}
