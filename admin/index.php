<?php
require_once __DIR__ . "/php/admin_start.inc.php";
global $title;
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <?php require_once "../php/imports.inc.php"; ?>
    <script src="admin/js/admin.js" defer></script>
    <link href="admin/css/admin.css" rel="stylesheet" type="text/css">
    <link href="admin/css/admin_custom.css" rel="stylesheet" type="text/css">
    <link href="admin/css/loader.css" rel="stylesheet" type="text/css">
</head>
<body>
<main>
    <section>
        <div class="tab-content container">
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
            <ul class="nav tab-list" id="tabList" role="tablist">
                <li class="tab-item">
                    <a class="tab-link button active" id="candidates-tab" data-toggle="tab" href="#candidates"
                       role="tab"
                       aria-controls="kandidaten" aria-selected="true">Kandidaten</a>
                </li>
                <li class="tab-item">
                    <a class="tab-link button" id="keys-tab" data-toggle="tab" href="#keys" role="tab"
                       aria-controls="keys" aria-selected="false">Keys</a>
                </li>
                <li class="tab-item">
                    <a class="tab-link button" id="results-tab" data-toggle="tab" href="#results" role="tab"
                       aria-controls="ergebnisse" aria-selected="false">Ergebnisse</a>
                </li>
                <li class="tab-item">
                    <a class="tab-link button" id="settings-tab" data-toggle="tab" href="#settings" role="tab"
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
                                <div class="property">
                                    <div class="candidate_picture_wrapper">
                                        <img alt="Kandidaten Bild" class="candidate_picture" id="candidatePicture"
                                             src="">
                                    </div>
                                </div>
                            </div>
                            <div class="properties">
                                <div class="property">
                                    <label for="candidateType">Wahl:</label>
                                    <select class="formSelect" id="candidateType"
                                            required></select>
                                </div>
                                <div class="property">
                                    <label for="candidateClass">Klasse:</label>
                                    <select class="formSelect" id="candidateClass"
                                            required></select>
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
                                <div class="property" style="flex-grow: 1">
                                    <label style="display: flex;flex-grow: 1;align-items: center;">Aktuell genutztes
                                        Bild:
                                        <input id="candidateImage"
                                               name="candidateImage" placeholder="-" readonly
                                               style="flex-grow: 1" type="text"/></label>
                                </div>
                                <div class="property">
                                    <button class="button" id="deleteCandidateImageBtn" type="submit">Bild
                                        löschen
                                    </button>
                                </div>
                            </div>
                            <div class="button-group">
                                <button class="button" id="saveCandidateBtn" formaction="admin/api/candidate/update.php"
                                        type="submit">Kandidat speichern
                                </button>
                                <button class="button" id="deleteCandidateBtn"
                                        formaction="admin/api/candidate/delete.php"
                                        type="submit">Kandidat löschen
                                </button>
                            </div>
                        </form>
                        <hr>
                        <div class="button-group">
                            <button class="button" id="resetCandidatesBtn"
                                    formaction="admin/api/reset.php?type=candidates"
                                    type="submit">Alle Kandidaten löschen
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div id="keys" class="tab-pane">
                <div class="properties">
                    <div class="property">
                        <label for="classList">Klasse:</label>
                        <select class="formSelect" id="classList"></select>
                    </div>
                </div>
                <div class="data">
                    <div class="leftCard">
                        <h3><span><span id="keysCounter">0</span> Keys</span></h3>
                        <h5><span><span id="unusedKeysCounter">0</span> davon unbenutzt</span></h5>
                        <select multiple id="keysList" size="20">
                        </select>
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
                                    <!--                                    <button class="button" id="deleteKeyVotesBtn" type="submit">Stimmen löschen-->
                                    <!--                                    </button>-->
                                </div>
                            </div>
                            <div class="button-group">
                                <button class="button" id="saveKeyBtn" formaction="admin/api/key/update.php"
                                        type="submit">
                                    Key speichern
                                </button>
                                <!--                                <button class="button" id="deleteKeyBtn" formaction="admin/api/key/delete.php"-->
                                <!--                                        type="submit">Key löschen-->
                                <!--                                </button>-->
                            </div>
                        </form>
                        <hr>
                        <div class="button-group">
                            <button class="button" id="resetKeysBtn" formaction="admin/api/reset.php?type=keys"
                                    type="submit">Alle Keys löschen
                            </button>
                            <a class="button" href="admin/api/key/download.php" download>Keys
                                exportieren</a>
                        </div>
                        <div class="button-group">
                            <button class="button" id="generateKeysBtn" formaction="admin/api/key/generate.php"
                                    type="submit">Neue Schlüssel generieren
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div id="results" class="tab-pane">
                <div class="properties">
                    <div class="property">
                        <label for="typeList">Wahl:</label>
                        <select class="formSelect" id="typeList"></select>
                    </div>
                    <div class="property" style="display: none;">
                        <label for="filterList">Filter:</label>
                        <select class="formSelect" id="filterList"></select>
                    </div>
                </div>
                <div class="data">
                    <div class="rightCard">
                        <div class="property">
                            <div id="resultsData"></div>
                        </div>
                        <div class="button-group">
                            <a class="button" href="admin/api/results/download.php" download>Ergebnisse
                                exportieren</a>
                            <button class="button" id="resetVotesBtn" formaction="admin/api/reset.php?type=votes"
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
                            <div class="button-group">
                                <button class="button" id="saveSettingsBtn" formaction="admin/api/settings/update.php"
                                        type="submit">Einstellungen speichern
                                </button>
                            </div>
                            <div class="button-group">
                                <button class="button" id="resetDatabaseBtn" formaction="admin/api/database/create.php"
                                        type="submit">Datenbank
                                    zurücksetzen/neu erstellen
                                </button>
                                <button class="button" id="repairDatabaseBtn" formaction="admin/api/database/repair.php"
                                        type="submit">Datenbank reparieren
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="button-group">
            <a class="button" href="index.php">&#60; Zurück zur Wahl</a>
            <button class="button" id="refreshBtn" type="button">Aktualisieren</button>
        </div>
    </section>
</main>
<?php require_once "../php/footer.inc.php"; ?>
</body>
</html>
