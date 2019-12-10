<?php
require_once "php/index_start.inc.php";
session_start();
$runoff = isRunoff();
$loginEnabled = !isLoginDisabled();

if (isset($_GET['logout']) || isset($_SESSION['key']) && !checkKeyVotes($runoff, $_SESSION['key'])) {
    logout($_SESSION['key']);
    header("Location: index.php");//reload
    exit;
}
$formWasSubmitted = $_SERVER['REQUEST_METHOD'] == 'POST';

if ($formWasSubmitted && !isset($_SESSION['key'])) {
    if (isset($_POST['key'])) {
        $key = $_POST['key'];

        if ($loginEnabled) {
            if (!empty($key)) {
                if (checkKey($key) && checkKeyVotes($runoff, $key)) {
                    $_SESSION['key'] = $key;
                    updateKeyUsedTime($key);
                    session_regenerate_id();
                    header("Location: voting.php");
                    exit;
                }
            } else {
                $errorMessage = "Dieser Key ist ungültig!";
            }
        } else {
            $errorMessage = "Das Login ist zurzeit deaktiviert!";
        }
    } else {
        $errorMessage = "Key konnte nicht überprüft werden!!";
    }
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <?php require_once "php/imports.inc.php"; ?>
</head>
<body>
<main>
    <div class="content">
        <h1 class="title"><?= (!isset($_SESSION['key']) ? 'Willkommen zur ' : "") . $title ?></h1>
        <?php if (isset($errorMessage)) { ?>
            <div class="message error"><?= $errorMessage ?></div>
        <?php } ?>
        <?php if (!isset($_SESSION['key'])) { ?>
            <div class="login">
                <form class="bigForm" method="post">
                    <label class="credentials">
                        <input id="key" type="text" placeholder="Key eingeben" name="key" required
                               autocomplete="off" autofocus/>
                    </label>
                    <button class="style-btn" type="submit">Key prüfen</button>
                </form>
            </div>
        <?php } else { ?>
            <div class="text-center container">
                <h3>Mit diesem Key wurde noch nicht gewählt!</h3>
                <p>Wenn du einen neuen Key eingibst wird dieser Key ungültig!</p>
            </div>
            <div class="btnGroup">
                <a class="style-link" href="index.php?logout=1">&#60; Neuen Key eingeben</a>
                <a class="style-link" href="voting.php">Wählen fortsetzen &#62;</a>
            </div>
        <?php } ?>
    </div>
</main>
<?php include "php/footer.inc.php"; ?>
</body>
</html>