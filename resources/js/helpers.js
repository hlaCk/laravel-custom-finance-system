// region: nova methods
Nova.translate =
    Nova.translate ||
    function (title) {
        return Nova.config.translations[title] || title;
    };

Nova.confirmEvent =
    Nova.confirmEvent ||
    function (event) {
        return swal({
            title: Nova.translate("Hold Up!"),
            text: Nova.translate("Are you sure you want to run this action?"),
            icon: "warning",
            buttons: [Nova.translate("No"), Nova.translate("Yes")],
            dangerMode: true,
        });
    };
// endregion: nova methods
