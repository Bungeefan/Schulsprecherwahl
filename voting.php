<?php
require_once "php/index_start.inc.php";
session_start();
$runoff = isRunoff();
$voteEnabled = !isVoteDisabled();

if (!isset($_SESSION['key']) || !checkKeyVotes($runoff, $_SESSION['key'])) {
    header("Location: index.php");
    die();
}

$statement = $database->getConnection()->query("SELECT * FROM `candidates` ORDER BY ID ASC");
$candidates = $statement->fetchAll(PDO::FETCH_ASSOC);

$maxPoints = min(6, count($candidates));

$invalidInput = false;
$canContinue = true;
$formWasSubmitted = $_SERVER['REQUEST_METHOD'] == 'POST';
$votePoints = array();
$preferenceVote = array();
$finishMessage = "Danke fürs Wählen!";
if ($formWasSubmitted) {
    if (checkKey($_SESSION['key'], false) && checkKeyVotes($runoff, $_SESSION['key'])) {
        if ($voteEnabled) {
            foreach ($candidates as $candidate) {
                if (!$runoff && isset($_POST['votePoints_' . $candidate['ID']])) {
                    $points = $_POST['votePoints_' . $candidate['ID']];
                    if ($points != null) {
                        if ($points < 0 || $points > $maxPoints) {
                            $invalidInput = true;
                            break;
                        } else {
                            $votePoints[$candidate['ID']] = $points;
                        }
                    }
                } else if ($runoff && isset($_POST['runoff_' . $candidate['ID']])) {
                    $runoffVote = $_POST['runoff_' . $candidate['ID']];
                    if ($runoffVote == "vote") {//isChecked
                        $preferenceVote[$candidate['ID']] = $runoffVote;
                    }
                }
            }
            if (!$runoff && count($candidates) != count($votePoints)) {
                $errorMessage = "Die Kandidaten wurden geändert, es wurden keine Stimmen aufgezeichnet!";
                $canContinue = false;
            }
            if ($invalidInput) {
                $errorMessage = "Ungültige Eingabe! Es wurden keine Stimmen aufgezeichnet!";
                $canContinue = false;
            }
            if ($canContinue) {
                if (!$runoff) {
                    $statement = $database->getConnection()->prepare("INSERT INTO `votes` (VoteKey, CandidateID, VoteCount) VALUES (:voteKey, :candidateID, :voteCount)");
                } else {
                    $statement = $database->getConnection()->prepare("INSERT INTO `votes_runoff` (VoteKey, CandidateID) VALUES (:voteKey, :candidateID)");
                }
                try {
                    if ($database->getConnection()->beginTransaction()) {
                        foreach ($candidates as $candidate) {
                            if ($runoff && !array_key_exists($candidate['ID'], $preferenceVote)) {
                                continue;
                            }
                            if (!$runoff) {
                                $statement->execute(array(":voteKey" => $_SESSION['key'], ":candidateID" => $candidate['ID'], ":voteCount" => $votePoints[$candidate['ID']]));
                            } else {
                                $statement->execute(array(":voteKey" => $_SESSION['key'], ":candidateID" => $candidate['ID']));
                            }
                        }
                        $database->getConnection()->commit();
                    } else {
                        $errorMessage = "Transaction begin failed!";
                        $canContinue = false;
                    }
                } catch (Exception $e) {
                    $database->getConnection()->rollBack();
                    $errorMessage = $e->getMessage();
                    $canContinue = false;
                }
            }
        } else {
            $canContinue = false;
            $errorMessage = "Die Wahl ist zurzeit deaktiviert! Bitte versuche es später erneut.";
        }
    } else {
        $finishMessage = "Bitte versuche es erneut mit einem anderen Key!";
    }
} else {
    $canContinue = false;
}

if (count($candidates) > 0 && $canContinue) {
    header("refresh:15;url=index.php");
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <link href="css/vote.css" rel="stylesheet" type="text/css">
    <script src="js/voting.js" defer></script>
    <?php require_once "php/imports.inc.php"; ?>
</head>
<body>
<main>
    <section>
        <h1 class="title"><?= $title ?></h1>
        <?php if (isset($errorMessage)) { ?>
            <div class="message error"><?= $errorMessage ?></div>
        <?php }
        if (count($candidates) > 0) {
            if (!$canContinue) {
                ?>
                <form class="big-form" id="voteForm" method="post">
                    <div class="voting">
                        <?php
                        foreach ($candidates as $candidate) {
                            ?>
                            <div class="choice">
                                <div class="candidate_picture_wrapper">
                                    <?php
                                    if (true) {
                                        ?>
                                        <img class="candidate_picture" alt="Kandidaten Bild"
                                             src="<?php if (isset($candidate['ImagePath']) && file_exists($intern_upload_folder . $candidate['ImagePath'])) {
                                                 echo $upload_folder . $candidate['ImagePath'];
                                             } else {
                                                 echo "images/user.png";
                                             } ?>"
                                        >
                                    <?php } else { ?>
                                        <div class="candidate_picture"></div>
                                    <?php } ?>
                                </div>
                                <h1 class="candidate_name"><?= htmlspecialchars($candidate['FirstName'] . " " . $candidate['LastName']) ?></h1>
                                <p class="candidate_description"><?= htmlspecialchars($candidate['AdditionalText']) ?></p>
                                <?php if ($runoff) { ?>
                                    <label class="runoff_check">
                                        <input type="checkbox" name="runoff_<?= $candidate['ID'] ?>" value="vote"
                                               class="runoff_checkbox"/>
                                        <svg viewBox="-5,-5,60,60" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M5 30 L 20 45 L 45 5"/>
                                            <!--                                            <path d="M 5 5 L 45 45"/>-->
                                            <!--                                            <path d="M 45 5 L 5 45"/>-->
                                        </svg>
                                    </label>
                                <?php } else { ?>
                                    <label class="points">Punkte:
                                        <select name="votePoints_<?= $candidate['ID'] ?>" class="votes">
                                            <?php for ($j = 0; $j <= $maxPoints; $j++) { ?>
                                                <option value="<?= $j ?>"
                                                        <?= $j == 0 ? " selected" : "" ?>><?= $j ?></option>
                                            <?php } ?>
                                        </select>
                                    </label>
                                <?php } ?>
                            </div>
                        <?php } ?>
                    </div>
                    <button class="button" disabled id="voteBtn" type="submit">Wählen!</button>
                </form>
                <?php
            } else {
                logout($_SESSION['key']);
                ?>
                <div class="text-center container">
                    <h2><?= $finishMessage ?></h2>
                </div>
                <a class="button big" href="index.php">&#60; Zurück zum Beginn!</a>
                <?php
            }
        } else {
            ?>
            <div class="text-center container">
                <h2>Keine Kandidaten gefunden!</h2>
            </div>
            <a class="button" href="index.php">&#60; Zurück zum Beginn!</a>
        <?php } ?>
    </section>
</main>
<?php include "php/footer.inc.php"; ?>
<!-- Modal -->
<div class="modal fade" id="warningModal" tabindex="-1" role="dialog" aria-labelledby="warningModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="warningModalLabel"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Nein</button>
                <button type="button" class="btn btn-success yes" data-dismiss="modal">Ja</button>
            </div>
        </div>
    </div>
</div>
</body>
</html>