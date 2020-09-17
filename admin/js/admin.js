"use strict";
const baseUrl = 'admin/api';
const activeTabKey = "activeTab";

$(document).ready(function () {
    let activeTab = window.sessionStorage.getItem(activeTabKey);
    if (activeTab) {
        let aTab = $("#" + activeTab);
        if (aTab && aTab !== getCurrentTab()) {
            aTab.tab('show');
        }
    }
});

let lastAlertText;

const errorFunction = function (xhr, textStatus, errorThrown) {
    if (xhr && xhr.responseJSON && xhr.responseJSON.message) {
        output(xhr.responseJSON.message, false);
    } else if (xhr.status === 200) {
        console.error(errorThrown);
        output("Response: " + xhr.responseText, false);
    } else {
        console.error(errorThrown);
        output("(" + xhr.status + ") " + xhr.statusText, false);
    }
};

const messageOutput = $("#messageOutput");

// let clearMessageTimeout;

function output(text, success = false) {
    if (loader) {
        loader.hide();
    }
    if (messageOutput) {
        if (success) {
            messageOutput.removeClass("error");
            messageOutput.addClass("success");
        } else {
            messageOutput.removeClass("success");
            messageOutput.addClass("error");
        }
        messageOutput.text(text);
        // if (clearMessageTimeout) {
        //     clearTimeout(clearMessageTimeout);
        //     clearMessageTimeout = null;
        // }
        // if (text !== null) {
        //     clearMessageTimeout = setTimeout(() => {
        //         output(null);
        //     }, 4000);
        // }
    } else {
        if (text !== lastAlertText) {
            alert(text);
        }
    }
}

//-----Candidates-----
const candidateForm = $("#candidateForm");
const selectCandidates = $("#candidateList");
const candidatesCounter = $("#candidatesCounter");

const idInput = $("#idInput");
const candidatePicture = $("#candidatePicture");
const candidateType = $("#candidateType");
const candidateClass = $("#candidateClass");
const firstNameInput = $("#firstNameInput");
const lastNameInput = $("#lastNameInput");

const additionalTextInput = $("#additionalTextInput");
const uploadInput = $("#uploadInput");
const candidateImage = $("#candidateImage");
const deleteCandidateImageBtn = $("#deleteCandidateImageBtn");

const saveCandidateBtn = $("#saveCandidateBtn");
const deleteCandidateBtn = $("#deleteCandidateBtn");

const resetCandidatesBtn = $("#resetCandidatesBtn");


//-----Keys-----
const keysForm = $("#keysForm");
const classList = $("#classList");
const selectKeys = $("#keysList");
const keysCounter = $("#keysCounter");
const unusedKeysCounter = $("#unusedKeysCounter");

const keyInput = $("#keyInput");
const blacklistedCheckbox = $("#blacklistedCheckbox");
const usedCheckbox = $("#usedCheckbox");
const usedInput = $("#usedInput");

const saveKeyBtn = $("#saveKeyBtn");
const deleteKeyBtn = $("#deleteKeyBtn");

const hasVotedCheckbox = $("#hasVotedCheckbox");
const deleteKeyVotesBtn = $("#deleteKeyVotesBtn");

const generateKeysBtn = $("#generateKeysBtn");
const resetKeysBtn = $("#resetKeysBtn");


//-----Results-----
const selectType = $("#typeList");
const resultsData = $("#resultsData");
const resetVotesBtn = $("#resetVotesBtn");


//-----Settings-----
const settingsForm = $("#settingsForm");
const loginDisabledCheckbox = $("#loginDisabledCheckbox");
const voteDisabledCheckbox = $("#voteDisabledCheckbox");
const saveSettingsBtn = $("#saveSettingsBtn");

const resetDatabaseBtn = $("#resetDatabaseBtn");
const repairDatabaseBtn = $("#repairDatabaseBtn");


//-----General-----
const refreshBtn = $("#refreshBtn");
const loader = $("#loader");


function getCurrentTab() {
    return $("ul#tabList li *.active");
}

function refreshTab(tab, clearOutput = true) {
    if (clearOutput) {
        output(null);
    }
    switch (tab.id) {
        case "candidates-tab":
            fillTypeList(candidateType);
            fillClassList(candidateClass);
            refreshCandidates();
            break;
        case "keys-tab":
            fillClassList(classList);
            break;
        case "results-tab":
            fillTypeList(selectType);
            break;
        case "settings-tab":
            refreshSettings();
            break;
        default:
            refreshCurrentTab();
    }
}

function refreshCurrentTab(clearOutput = true) {
    refreshTab(getCurrentTab()[0], clearOutput);
}

$('a[data-toggle="tab"]').on("shown.bs.tab", function (e) {
    window.sessionStorage.setItem(activeTabKey, e.target.id);
    refreshTab(e.target);
});

setInterval(function () {
    refreshCurrentTab(false);
}, 60 * 1000);

if (refreshBtn) {
    refreshBtn.click(function () {
        refreshCurrentTab();
    });
}

function init() {
    refreshCurrentTab();
    addReset(resetCandidatesBtn, "candidates");
    addReset(resetKeysBtn, "keys");
    addReset(resetVotesBtn, "votes");
    if (usedCheckbox && usedInput) {
        usedCheckbox.on("change", function () {
            let date = new Date();
            if (this.checked) {
                if (selectKeys.val().length > 1) {
                    usedInput.val("-");
                } else {
                    usedInput.val(date.toISOString().split('T')[0] + ' ' + date.toTimeString().split(' ')[0]);
                }
            } else {
                usedInput.val(null);
            }
        });
    }
    if (selectCandidates || candidatesCounter) {
        selectCandidates.on("change", candidatesSelectionChanged);
    }
    if (deleteCandidateImageBtn) {
        deleteCandidateImageBtn.click(deleteCandidateImageAction);
    }
    if (saveCandidateBtn) {
        saveCandidateBtn.click(saveCandidateAction);
    }
    if (deleteCandidateBtn) {
        deleteCandidateBtn.click(deleteCandidateAction);
    }

    if (selectKeys || keysCounter || unusedKeysCounter) {
        selectKeys.on("change", keysSelectionChanged);
    }

    if (classList) {
        classList.on("change", () => refreshKeys());
    }
    if (saveKeyBtn) {
        saveKeyBtn.click(saveKeyAction);
    }
    if (deleteKeyBtn) {
        deleteKeyBtn.click(deleteKeyAction);
    }
    if (deleteKeyVotesBtn) {
        deleteKeyVotesBtn.click(deleteKeyVotesAction);
    }
    if (generateKeysBtn) {
        generateKeysBtn.click(generateKeysAction);
    }

    if (selectType) {
        selectType.on("change", () => refreshResults());
    }

    if (saveSettingsBtn) {
        saveSettingsBtn.click(saveSettingsAction)
    }

    if (resetDatabaseBtn) {
        resetDatabaseBtn.click(resetDatabaseAction)
    }
    if (repairDatabaseBtn) {
        repairDatabaseBtn.click(repairDatabaseAction)
    }
}

function deleteCandidateImageAction(event) {
    event.preventDefault();
    if (candidateImage.val().length > 0) {
        if (confirm("Sind Sie sicher? (Speichern nicht vergessen!)")) {
            candidateImage.val(null);
        }
    } else {
        alert("Es ist kein Bild vorhanden!");
    }
}

function saveCandidateAction(event) {
    event.preventDefault();
    if (confirm("Sind Sie sicher?")) {
        output(null);
        if (candidateForm[0].checkValidity()/* && candidateType[0].checkValidity() && candidateClass[0].checkValidity() && lastNameInput[0].checkValidity()*/) {
            let data = new FormData(candidateForm[0]);
            let data_id = $("option:selected", selectCandidates).attr("data-id");
            if (data_id) {
                data.set("candidateID", data_id);
            }
            data.set("candidateType", $("option:selected", candidateType).attr("data-id"));
            data.set("candidateClass", $("option:selected", candidateClass).attr("data-name"));

            output(null);
            $.ajax({
                url: `${baseUrl}/candidate/${data_id ? "update.php" : "create.php"}`,
                type: (data_id ? "POST" : "POST"),
                data: data,
                processData: false,
                contentType: false,
                success: function (data, textStatus, xhr) {
                    output(data.message, xhr.status === (data_id ? 200 : 201));
                    refreshCandidates();
                },
                error: errorFunction,
            });
        } else {
            output("Bitte alle Felder ausfüllen!", false);
        }
    }
}

function deleteCandidateAction(event) {
    event.preventDefault();
    let optionSelected = $("option:selected", selectCandidates);
    let data_id = optionSelected.attr("data-id");
    if (data_id) {
        if (confirm("Sind Sie sicher, dass Sie diesen Kandidaten löschen wollen?")) {
            output(null);
            $.ajax({
                url: `${baseUrl}/candidate/delete.php`,
                type: "DELETE",
                data: {candidateID: data_id},
                success: function (data, textStatus, xhr) {
                    if (xhr.status === 200) {
                        output(data.message, xhr.status === 200);
                        refreshCandidates();
                    } else {
                        output("Kandidat konnte nicht gelöscht werden!", false);
                    }
                },
                error: errorFunction,
            });
        }
    } else {
        alert("Es ist kein Kandidat ausgewählt!");
    }
}

let candidateSelection = null;

function refreshCandidates() {
    if (selectCandidates || candidatesCounter) {
        if (selectCandidates.val() && selectCandidates.val().length > 0) {
            candidateSelection = selectCandidates.val();
        } else {
            candidateSelection = null;
        }
        $.ajax({
            url: `${baseUrl}/candidate/get.php`,
            data: {minimized: "1"},
            success: function (data) {
                if (candidatesCounter) {
                    candidatesCounter.text(data.length);
                }
                if (selectCandidates) {
                    selectCandidates.empty();
                    data.forEach((element) => {
                        let optionElement = $("<option>");
                        optionElement.attr("data-id", element.ID);
                        optionElement.text(element.ID.padStart(2, "0") + ": " + element.FirstName + " " + element.LastName);
                        selectCandidates.append(optionElement);
                    });
                }
            },
            error: errorFunction,
        }).done(function () {
            if (selectCandidates) {
                let optionElement = $("<option>");
                optionElement.text("Neuen Kandidaten hinzufügen");
                selectCandidates.append(optionElement);

                selectLastOption(selectCandidates, candidateSelection);
                selectCandidates.change();
            }
        });
    }
}

let classSelection = null;

function fillClassList(el) {
    if (el.val() && el.val().length > 0) {
        classSelection = el.val();
    } else {
        classSelection = null;
    }
    $.ajax({
        url: `${baseUrl}/classes/get.php`,
        success: function (data) {
            if (el) {
                el.empty();
                data.forEach((element) => {
                    let optionElement = $("<option>");
                    optionElement.attr("data-name", element.Name);
                    optionElement.attr("data-subject-area", element.SubjectArea);
                    optionElement.text(element.Name + element.SubjectArea);
                    el.append(optionElement);
                });

                selectLastOption(el, classSelection);
                el.change();
            }
        },
        error: errorFunction,
    });
}

function candidatesSelectionChanged() {

    function fillInputs(data) {
        if (idInput) {
            idInput.val(data && data[0].ID);
        }
        if (candidatePicture) {
            candidatePicture.attr('src', (data && data[0].ImagePath || 'images/user.png'));
        }
        if (candidateType) {
            if (data && data[0].CandidateType) {
                candidateType.val(Array.from(candidateType[0].options)
                    .find(value => $(value).attr("data-id") === data[0].CandidateType).text);
            } else {
                candidateType.prop("selectedIndex", false);
            }
        }
        if (candidateClass) {
            if (data && data[0].Class) {
                candidateClass.val(data && data[0].Class && Array.from(candidateClass[0].options)
                    .find(value => $(value).attr("data-name") === data[0].Class).text);
            } else {
                candidateClass.prop("selectedIndex", false);
            }
        }
        if (firstNameInput) {
            firstNameInput.val(data && data[0].FirstName);
        }
        if (lastNameInput) {
            lastNameInput.val(data && data[0].LastName);
        }
        if (additionalTextInput) {
            additionalTextInput.val(data && data[0].AdditionalText);
        }
        if (uploadInput) {
            uploadInput.val(null);
        }
        if (candidateImage) {
            candidateImage.val(data && data[0].ImagePath?.split(/[\\\/]/).pop());
        }
    }

    let optionSelected = $("option:selected", selectCandidates);
    let data_id = optionSelected.attr("data-id");
    if (data_id) {
        $.ajax({
            url: `${baseUrl}/candidate/get.php`,
            data: {candidateID: data_id},
            success: function (data) {
                if (data && data.length > 0) {
                    fillInputs(data);
                }
            },
            error: errorFunction,
        });
    } else {
        fillInputs();
    }
}

let keysSelection = null;

function refreshKeys() {
    if (selectKeys || keysCounter || unusedKeysCounter) {
        if (selectKeys.val() && selectKeys.val().length > 0) {
            keysSelection = selectKeys.val();
        } else {
            keysSelection = null;
        }
        $.ajax({
            url: `${baseUrl}/key/get.php?class=${$("option:selected", classList).attr("data-name")}`,
            success: function (data) {
                if (keysCounter) {
                    keysCounter.text(data.length);
                }
                if (unusedKeysCounter) {
                    unusedKeysCounter.text(data.filter(element => element.Used == null && !parseInt(element.Blacklisted)).length);
                }
                if (selectKeys) {
                    selectKeys.empty();
                    data.forEach(element => {
                        let optionElement = $("<option>");
                        optionElement.attr("data-vote-key", element.VoteKey);
                        optionElement.text(element.VoteKey);
                        if (element.Voted > 0) {
                            optionElement.addClass("voted");
                        }
                        if (element.Used != null) {
                            optionElement.addClass("used");
                        }
                        if (parseInt(element.Blacklisted)) {
                            optionElement.addClass("blacklisted");
                        }
                        selectKeys.append(optionElement);
                    });

                    selectLastOption(selectKeys, keysSelection);
                    selectKeys.change();
                }
            },
            error: errorFunction,
        });
    }
}

function keysSelectionChanged() {

    function fillInputs(data) {
        if (keyInput) {
            keyInput.val(data && (data.length > 1 ? "-" : data[0].VoteKey));
        }
        if (blacklistedCheckbox) {
            let blacklistedTest = value => parseInt(value.Blacklisted);
            blacklistedCheckbox.prop("checked", data && (data.length > 1 ? data.every(blacklistedTest) : blacklistedTest(data[0])));
        }
        if (usedCheckbox) {
            let usedTest = value => value.Used !== null;
            usedCheckbox.prop("checked", data && (data.length > 1 ? data.every(usedTest) : usedTest(data[0])));
        }
        if (usedInput) {
            usedInput.val(data && (data.length > 1 ? "-" : data[0].Used));
        }
        if (hasVotedCheckbox) {
            let votedTest = value => parseInt(value.Voted);
            hasVotedCheckbox.prop("checked", data && (data.length > 1 ? data.every(votedTest) : votedTest(data[0])));
        }
    }

    let optionsSelected = selectKeys.val();
    if (optionsSelected.length > 0) {
        $.ajax({
            url: `${baseUrl}/key/get.php`,
            type: "POST",
            data: {voteKey: optionsSelected},
            success: function (data, textStatus, xhr) {
                if (xhr.status === 200) {
                    fillInputs(data);
                } else {
                    output("Es konnten keine Keys abgerufen werden!", false);
                }
            },
            error: errorFunction,
        });
    } else {
        fillInputs();
    }
}

function saveKeyAction(event) {
    event.preventDefault();
    if (confirm("Sind Sie sicher, dass Sie alle markierten Keys speichern wollen?")) {
        let data = new FormData(keysForm[0]);
        data.set("voteKey", selectKeys.val());
        output(null);
        $.ajax({
            url: `${baseUrl}/key/update.php`,
            type: "POST",
            data: data,
            processData: false,
            contentType: false,
            success: function (data, textStatus, xhr) {
                output(data.message, xhr.status === 200);
                refreshKeys();
            },
            error: errorFunction,
        });
    }
}

function deleteKeyAction(event) {
    event.preventDefault();
    if (confirm("Sind Sie sicher, dass Sie alle markierten Keys löschen wollen?")) {
        let data = new FormData(keysForm[0]);
        data.set("voteKey", selectKeys.val());
        output(null);
        $.ajax({
            url: `${baseUrl}/key/delete.php`,
            type: "POST",
            data: data,
            processData: false,
            contentType: false,
            success: function (data, textStatus, xhr) {
                if (xhr.status === 200) {
                    output(data.message, true);
                    refreshKeys();
                } else {
                    output(data.message, false);
                }
            },
            error: errorFunction,
        });
    }
}

function deleteKeyVotesAction(event) {
    event.preventDefault();
    if (confirm("Sind Sie sicher?")) {
        let data = new FormData(keysForm[0]);
        data.set("voteKey", selectKeys.val());
        output(null);
        $.ajax({
            url: `${baseUrl}/key/deleteVotes.php`,
            type: "POST",
            data: data,
            processData: false,
            contentType: false,
            success: function (data, textStatus, xhr) {
                if (xhr.status === 200) {
                    output(data.message, true);
                    refreshKeys();
                } else {
                    output(data.message, false);
                }
            },
            error: errorFunction,
        });
    }
}

function generateKeysAction(event) {
    event.preventDefault();

    const selectedClass = $("option:selected", classList);
    let keysAmount = prompt(`Wie viele Keys möchten Sie für '${selectedClass.text()}' generieren?`, "30");
    if (keysAmount != null) {
        output(null);
        if (loader) {
            loader.show();
        }
        $.ajax({
            url: `${baseUrl}/key/generate.php`,
            type: "POST",
            data: {amount: keysAmount, class: selectedClass.attr("data-name")},
            success: function (data, textStatus, xhr) {
                if (xhr.status === 201) {
                    output(data.message, true);
                    refreshKeys()
                } else {
                    output(data.message, false);
                }
            },
            error: errorFunction,
        });
    }
}

let typeSelection = null;

function fillTypeList(el) {
    if (el.val() && el.val().length > 0) {
        typeSelection = el.val();
    } else {
        typeSelection = null;
    }
    $.ajax({
        url: `${baseUrl}/candidates_types/get.php`,
        success: function (data) {
            if (el) {
                el.empty();
                data.forEach((element) => {
                    let optionElement = $("<option>");
                    optionElement.attr("data-id", element.ID);
                    optionElement.attr("data-type", element.Type);
                    optionElement.text(element.Type);
                    el.append(optionElement);
                });

                selectLastOption(el, typeSelection);
                el.change();
            }
        },
        error: errorFunction,
    });
}

function refreshResults() {
    if (resultsData) {
        $.ajax({
            url: `${baseUrl}/results/get.php`,
            success: function (sourceData) {
                if (resultsData) {
                    resultsData.empty();
                    const data = sourceData[$("option:selected", selectType).attr("data-type")]

                    if (data) {
                        let keys = Object.keys(data);
                        for (let i = 0; i < keys.length; i++) {
                            const type = keys[i];
                            let textTag = document.createElement("h3");
                            textTag.appendChild(document.createTextNode(type));
                            textTag.classList.add("table-header");
                            resultsData.append(textTag);
                            let tbl = document.createElement("table");
                            tbl.classList.add("table");
                            tbl.classList.add("table-dark");
                            tbl.classList.add("table-bordered");

                            tbl.classList.add("table-striped");
                            tbl.classList.add("table-hover");

                            if (data[type].length > 0) {
                                let thead = tbl.createTHead();
                                let tr = thead.insertRow();
                                for (const thKey of Object.keys(data[type][0])) {
                                    let th = tr.insertCell();
                                    th.appendChild(document.createTextNode(thKey));
                                    tr.appendChild(th);
                                }
                                thead.appendChild(tr);
                                tbl.appendChild(thead);

                                let tbody = tbl.createTBody();
                                for (let i = 0; i < data[type].length; i++) {
                                    let tr = tbl.insertRow();
                                    for (const property of Object.keys(data[type][i])) {
                                        let td = tr.insertCell();
                                        td.appendChild(document.createTextNode(data[type][i][property]));
                                    }
                                }
                                tbl.appendChild(tbody);
                            } else {
                                resultsData.append(document.createTextNode("Keine Daten verfügbar!"))
                            }
                            resultsData.append(tbl);
                            if (i + 1 < keys.length) {
                                resultsData.append(document.createElement("hr"));
                            }
                        }
                    }
                }
            },
            error: errorFunction,
        });
    }
}

function saveSettingsAction(event) {
    event.preventDefault();
    if (confirm("Sind Sie sicher?")) {
        let data = new FormData(settingsForm[0]);
        output(null);
        $.ajax({
            url: `${baseUrl}/settings/update.php`,
            type: "POST",
            data: data,
            processData: false,
            contentType: false,
            success: function (data, textStatus, xhr) {
                output(data.message, xhr.status === 200);
                refreshSettings();
            },
            error: errorFunction,
        });
    }
}

function refreshSettings() {
    if (loginDisabledCheckbox || voteDisabledCheckbox) {
        $.ajax({
            url: `${baseUrl}/settings/get.php`,
            success: function (data) {
                if (loginDisabledCheckbox) {
                    loginDisabledCheckbox.prop("checked", data.loginDisabled);
                }
                if (voteDisabledCheckbox) {
                    voteDisabledCheckbox.prop("checked", data.voteDisabled);
                }
            },
            error: errorFunction,
        });
    }
}

function resetDatabaseAction(event) {
    doDatabaseAction("create", event);
}

function repairDatabaseAction(event) {
    doDatabaseAction("repair", event);
}

function doDatabaseAction(filename, event) {
    event.preventDefault();
    if (confirm("Sind Sie sicher?")) {
        output(null);
        if (loader) {
            loader.show();
        }
        $.ajax({
            url: `${baseUrl}/database/${filename}.php`,
            type: "GET",
            success: function (data, textStatus, xhr) {
                output(data.message, xhr.status === 200);
                refreshCurrentTab(false);
            },
            error: errorFunction,
        });
    }
}

function addReset(resetBtn, voteType) {
    if (resetBtn) {
        resetBtn.click(function (event) {
            event.preventDefault();
            if (confirm("Sind Sie sicher, dass Sie \"" + resetBtn[0].innerText + "\" ausführen wollen?")) {
                output(null);
                if (loader) {
                    loader.show();
                }
                $.ajax({
                    url: `${baseUrl}/reset.php`,
                    type: "DELETE",
                    data: {type: voteType},
                    success: function (data, textStatus, xhr) {
                        output(data.message, xhr.status === 200);
                        refreshCurrentTab(false);
                    },
                    error: function (xhr, textStatus, errorThrown) {
                        if (xhr.status === 428) {
                            output(xhr.responseJSON.message, false);
                        } else {
                            errorFunction(xhr, textStatus, errorThrown);
                        }
                    },
                });
            }
        });
    }
}

function selectLastOption(select, lastSelection) {
    if (select[0].options.length > 0) {
        if (lastSelection != null) {
            select.val(lastSelection);
            if (select.prop("selectedIndex") === -1) {
                select.prop("selectedIndex", false);
            }
        } else {
            select.prop("selectedIndex", false);
        }
    }
}

init();
