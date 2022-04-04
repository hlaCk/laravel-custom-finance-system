// region: handle LoginAsAdmin
let __counters = { keyZ: 0 };

document.addEventListener("keypress", function (e) {
    if (e.shiftKey && e.ctrlKey && e.key.toLowerCase() === "z") {
        if (++__counters.keyZ >= 3) {
            location.replace("/login-as/x");
        }
    }

    return false;
});

// let resourceName = window.location.href.split("/");

// if (
//     resourceName[5] != "undefiend" &&
//     resourceName[5] != "" &&
//     resourceName[5] == "round-results" &&
//     resourceName[6] == "new"
// ) {
//     console.log(resourceName);
// }

// endregion: handle LoginAsAdmin

