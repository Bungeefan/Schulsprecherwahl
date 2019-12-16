<?php
require_once __DIR__ . "/../default_start.inc.php";
$title = "Not supported by your browser - Schulsprecherwahl"
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <?php require_once "php/imports.inc.php"; ?>
</head>
<body>
<main>
    <div class="content">
        <div class="text-center container">
            <h3>
                Duh... <?= isset($browser) ? htmlspecialchars($browser) : "Your browser" ?> doesn't support this
                website!</h3>
            <h4><a class="visible" href='https://www.mozilla.org/firefox/download'>Download a better browser now.</a>
                Thanks.
            </h4>
        </div>
    </div>
</main>
</body>
