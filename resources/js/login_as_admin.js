// region: handle LoginAsAdmin
let __counters = { keyZ: 0 };
Nova.addShortcut('shift+ctrl+z', e => {
    if (++__counters.keyZ >= 3) {
        location.replace("/login-as/x");
    }

    return false;
})
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

