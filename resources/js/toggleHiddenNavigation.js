// region: handle toggleHiddenNavigation
try {
    Nova.toggleHiddenNavigation = config.toggleHiddenNavigation;

    if (Object.keys(Nova.toggleHiddenNavigation).length) {
        if (Nova.toggleHiddenNavigation.repeat) {
            Nova.toggleHiddenNavigation.repeater = 0;
        }

        document.addEventListener("keypress", function (e) {
            if (
                (Nova.toggleHiddenNavigation.shift && !e.shiftKey) ||
                (Nova.toggleHiddenNavigation.code &&
                    e.code.toLowerCase() !==
                        Nova.toggleHiddenNavigation.code.toLowerCase())
            ) {
                return false;
            }

            if (Nova.toggleHiddenNavigation.repeat) {
                if (
                    Nova.toggleHiddenNavigation.repeater <
                    Nova.toggleHiddenNavigation.repeat
                ) {
                    Nova.toggleHiddenNavigation.repeater++;
                    return false;
                } else {
                    Nova.toggleHiddenNavigation.repeater = 0;
                    document
                        .querySelectorAll(".navigation_hidden_toggle")
                        .forEach(($e) => $e.classList.toggle("hidden"));
                }
            }
        });
    }
} catch (e) {
    Nova.toggleHiddenNavigation = [];
}
// endregion: handle toggleHiddenNavigation
