<?php
require_once __DIR__ . "/php/admin_start.inc.php";
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <?php require_once "php/imports.inc.php"; ?>
    <script src="js/admin.js" defer></script>
    <link href="css/admin.css" rel="stylesheet" type="text/css">
</head>
<body>
<main>
    <div class="content">
        <h1 class="title"><?= $title ?></h1>
        <div id="loader" class="align-self-center lds-spinner" style="display: none;">
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
        </div>
        <div class="message success" id="messageOutput"></div>
        <?php if (isset($errorMessage)) { ?>
            <div class="message error"><?= $errorMessage ?></div>
        <?php } ?>
        <div class="container tab-content">
            <ul class="nav tab-list" id="tabList" role="tablist">
                <li class="tab-item">
                    <a class="tab-link style-link active" id="candidates-tab" data-toggle="tab" href="#candidates"
                       role="tab"
                       aria-controls="kandidaten" aria-selected="true">Kandidaten</a>
                </li>
                <li class="tab-item">
                    <a class="tab-link style-link" id="keys-tab" data-toggle="tab" href="#keys" role="tab"
                       aria-controls="keys" aria-selected="false">Keys</a>
                </li>
                <li class="tab-item">
                    <a class="tab-link style-link" id="results-tab" data-toggle="tab" href="#results" role="tab"
                       aria-controls="ergebnisse" aria-selected="false">Ergebnisse</a>
                </li>
                <li class="tab-item">
                    <a class="tab-link style-link" id="settings-tab" data-toggle="tab" href="#settings" role="tab"
                       aria-controls="einstellungen" aria-selected="false">Einstellungen</a>
                </li>
            </ul>
            <div id="candidates" class="tab-pane active">
                <div class="data">
                    <div class="leftCard">
                        <h3><label for="candidateList"><span id="candidatesCounter">0</span>
                                Kandidaten</label></h3>
                        <select id="candidateList" size="20">
                        </select>
                    </div>
                    <div class="vr"></div>
                    <div class="rightCard">
                        <form id="candidateForm">
                            <div class="properties">
                                <div class="property">
                                    <label for="idInput">ID:</label>
                                    <input id="idInput" name="candidateID" type="text" placeholder="ID" readonly>
                                </div>
                            </div>
                            <div class="properties">
                                <div class="property">
                                    <label for="firstNameInput">Vorname:</label>
                                    <input id="firstNameInput" name="firstName" type="text" placeholder="Vorname"
                                           required>
                                </div>
                                <div class="property">
                                    <label for="lastNameInput">Nachname:</label>
                                    <input id="lastNameInput" name="lastName" type="text" placeholder="Nachname"
                                           required>
                                </div>
                            </div>
                            <div class="property">
                                <label for="additionalTextInput">Zusätzliche Infos:</label>
                                <textarea id="additionalTextInput" name="additionalText" placeholder="Zusätzliche Infos"
                                          rows="2"></textarea>
                            </div>
                            <div class="property">
                                <label>Wählen Sie eine Bilddatei aus:
                                    <input type="file" name="image" id="uploadInput" accept="image/*">
                                </label>
                            </div>
                            <div class="properties">
                                <div class="property">
                                    <label>Aktuell genutztes Bild:
                                        <input id="candidateImage"
                                               name="candidateImage" type="text"
                                               placeholder="-" readonly/></label>
                                </div>
                                <div class="property">
                                    <button class="style-btn" id="deleteCandidateImageBtn" type="submit">Bild
                                        löschen
                                    </button>
                                </div>
                            </div>
                            <div class="btnGroup">
                                <button class="style-btn" id="saveCandidateBtn" formaction="api/candidate/update.php"
                                        type="submit">Kandidat speichern
                                </button>
                                <button class="style-btn" id="deleteCandidateBtn" formaction="api/candidate/delete.php"
                                        type="submit">Kandidat löschen
                                </button>
                            </div>
                        </form>
                        <hr>
                        <div class="btnGroup">
                            <button class="style-btn" id="resetCandidatesBtn" formaction="api/reset.php?type=candidates"
                                    type="submit">Alle Kandidaten löschen
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div id="keys" class="tab-pane">
                <div class="data">
                    <div class="leftCard">
                        <h3><span><span id="keysCounter">0</span> Keys</span></h3>
                        <select multiple id="keysList" size="20">
                        </select>
                        <h5><span><span id="unusedKeysCounter">0</span> davon unbenutzt</span></h5>
                    </div>
                    <div class="vr"></div>
                    <div class="rightCard">
                        <form id="keysForm">
                            <div class="properties">
                                <div class="property">
                                    <label for="keyInput">Key:</label>
                                    <input id="keyInput" type="text" name="voteKey" placeholder="Key" readonly>
                                </div>
                            </div>
                            <div class="property">
                                <label for="blacklistedCheckbox" class="checkcontainer">Gesperrt
                                    <input id="blacklistedCheckbox" name="blacklisted" value="true" type="checkbox">
                                    <span class="checkmark"></span>
                                </label>
                            </div>
                            <div class="properties">
                                <div class="property">
                                    <label for="usedCheckbox" class="checkcontainer">Benutzt
                                        <input id="usedCheckbox" name="used" value="true" type="checkbox">
                                        <span class="checkmark"></span>
                                    </label>
                                    <input id="usedInput" type="text" placeholder="Benutzt am" readonly>
                                </div>
                                <div class="property">
                                    <label for="hasVotedCheckbox" class="checkcontainer">Stimmen abgegeben
                                        <input id="hasVotedCheckbox" name="hasVoted" value="true" type="checkbox"
                                               disabled>
                                        <span class="checkmark"></span>
                                    </label>
                                    <button class="style-btn" id="deleteKeyVotesBtn" type="submit">Stimmen löschen
                                    </button>
                                </div>
                            </div>
                            <div class="btnGroup">
                                <button class="style-btn" id="saveKeyBtn" formaction="api/key/update.php" type="submit">
                                    Key speichern
                                </button>
                                <button class="style-btn" id="deleteKeyBtn" formaction="api/key/delete.php"
                                        type="submit">Key löschen
                                </button>
                            </div>
                        </form>
                        <hr>
                        <div class="btnGroup">
                            <button class="style-btn" id="resetKeysBtn" formaction="api/reset.php?type=keys"
                                    type="submit">Alle Keys löschen
                            </button>
                            <a class="style-link" href="api/key/download.php" download>Keys
                                exportieren</a>
                        </div>
                        <div class="btnGroup">
                            <button class="style-btn" id="generateKeysBtn" formaction="api/key/generate.php"
                                    type="submit">Neue Schlüssel generieren
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div id="results" class="tab-pane">
                <div class="data">
                    <div class="rightCard">
                        <div class="property">
                            <div id="resultsData"></div>
                        </div>
                        <div class="btnGroup">
                            <a class="style-link" href="api/results/download.php" download>Ergebnisse
                                exportieren</a>
                            <button class="style-btn" id="resetVotesBtn" formaction="api/reset.php?type=votes"
                                    type="submit">Alle Stimmen löschen
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div id="settings" class="tab-pane">
                <div class="data">
                    <div class="rightCard">
                        <form id="settingsForm">
                            <div class="property">
                                <label for="loginDisabledCheckbox" class="checkcontainer">Login deaktiviert
                                    <input id="loginDisabledCheckbox" name="loginDisabled" value="true" type="checkbox">
                                    <span class="checkmark"></span>
                                </label>
                            </div>
                            <div class="property">
                                <label for="voteDisabledCheckbox" class="checkcontainer">Voting deaktiviert
                                    <input id="voteDisabledCheckbox" name="voteDisabled" value="true" type="checkbox">
                                    <span class="checkmark"></span>
                                </label>
                            </div>
                            <div class="btnGroup">
                                <button class="style-btn" id="saveSettingsBtn" formaction="api/settings/update.php"
                                        type="submit">Einstellungen speichern
                                </button>
                            </div>
                            <div class="btnGroup">
                                <button class="style-btn" id="resetDatabaseBtn" formaction="api/database/create.php"
                                        type="submit">Datenbank
                                    zurücksetzen/neu erstellen
                                </button>
                                <button class="style-btn" id="repairDatabaseBtn" formaction="api/database/repair.php"
                                        type="submit">Datenbank reparieren
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="btnGroup">
            <a class="style-link" href="../index.php">&#60; Zurück zur Wahl</a>
            <button class="style-btn" id="refreshBtn" type="button">Aktualisieren</button>
        </div>
    </div>
</main>
<?php include "php/footer.inc.php"; ?>
</body>
</html>