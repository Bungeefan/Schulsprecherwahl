"use strict";

const selects = $("form .choice select");
const maxPoints = selects.length;
const checkboxes = $("form .choice input[type=checkbox]");

const voteBtn = $("#voteBtn");
if (voteBtn) {
    voteBtn.prop("disabled", false);
}

const listener = (event) => {
    event.preventDefault();

    let invalid = false;
    if (selects && selects.length >= 1) {
        let votePoints = [];
        for (let value of selects.map((index, value) => parseInt(value.options[value.selectedIndex].text))) {
            if (value !== 0 && votePoints.includes(value)) {
                invalid = true;
                break;
            } else {
                votePoints.push(value);
            }
        }
        if (!invalid) {
            for (let i = 1; i <= maxPoints; i++) {
                if (!votePoints.includes(i)) {
                    invalid = true;
                    break;
                }
            }
        }
    } else if (checkboxes && checkboxes.length >= 1) {
        let checkedCheckboxes = checkboxes.filter((index, value) => value.checked).length;
        if (checkedCheckboxes !== 1) {
            invalid = true;
        }
    } else {
        console.log("There are no voting elements on the screen!");
        alert("Es ist ein JavaScript Fehler aufgetreten. Versuche die Seite neu zu laden!");
    }

    if (invalid) {
        $('#warningModal .modal-title').html("Ungültige Wahl!");
        $('#warningModal .modal-body').html("Du bist dabei ungültig zu wählen! Willst du fortfahren?");
        $('#warningModal').modal();
        $('#warningModal .modal-footer button').on('click', function (event) {
            $(this).closest('.modal').one('hidden.bs.modal', function () {
                if ($(event.target).hasClass("yes")) {
                    askOnExit()
                }
            });
        });

    } else {
        askOnExit();
    }
};

function askOnExit() {
    $('#warningModal .modal-title').html("");
    $('#warningModal .modal-body').html("Bist du sicher?");
    $('#warningModal').modal();
    $('#warningModal .modal-footer button').on('click', function (event) {
        $(this).closest('.modal').one('hide.bs.modal', function () {
            if ($(event.target).hasClass("yes")) {
                $("#voteForm").off("submit");
                $("#voteForm").submit();
            }
        });
    });
}

/*selects.each((index, selectElement) => $(selectElement).on("change", (event) => {
    console.log(event.target);
    selects.each((index, select) => {
        for (let i = 0; i < select.options.length; i++) {
            select.options[i].disabled = false;
        }
    });
    selects.each((index, select) => {
        if (parseInt(select.options[select.selectedIndex].text) !== 0) {
            selects/!*.filter((index, selectElement) => selectElement !== event.target)*!/.each((index, selectElement) => {
                selectElement.options[select.selectedIndex].disabled = true;
            });
        }
    });
}));*/

$("#voteForm").submit(listener);
