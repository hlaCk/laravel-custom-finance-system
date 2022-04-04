// region: handle dynamic triggers
if (config.$on) {
    (Array.from(config.$on) || []).forEach(function (attrs) {
        const [eventName, eventCallbackContext] = Array.from(attrs) || [
            null,
            null,
        ];
        let eventCallback = () => {};

        try {
            if (eventCallbackContext) {
                eventCallback = eval(eventCallbackContext);
            }

            eventName &&
                eventCallback &&
                typeof eventCallback === "function" &&
                Nova.$on(eventName, eventCallback);
        } catch (e) {
            console.error(e);
        }
    });
}
// endregion: handle dynamic triggers
