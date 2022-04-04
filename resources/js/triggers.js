// region: triggers
// Nova.$on("resources-loaded", () => {
//     let resourceName = Nova.app.$route.params.resourceName || false;
//
//     //add heading on cups resource.viaRelationship
//
//     resourceName && Nova.$emit("resource-loaded-" + resourceName);
//     if (resourceName == "cups") {
//         let cups_counter = 0;
//         let updateCupsLink = setInterval(function () {
//             cups_counter++;
//             //get laser
//             let headers = document.getElementsByTagName("th");
//
//             let show_hide_class = document.getElementsByClassName("show_hide_class");
//             let cup_round_class = document.getElementsByClassName("cup_round_class");
//
//
//             if(show_hide_class.length > 0){
//                 let show_hide_class_index = show_hide_class[0].offsetParent.cellIndex;
//                 if (headers[show_hide_class_index] != "undefined") {
//                     headers[show_hide_class_index].innerHTML = Nova.translate("Visibility");
//                 }
//             }
//
//             if(cup_round_class.length > 0){
//                 let cup_round_class_index = cup_round_class[0].offsetParent.cellIndex;
//                 if (headers[cup_round_class_index] != "undefined") {
//                     headers[cup_round_class_index].innerHTML = Nova.translate("Rounds");
//                 }
//             }
//
//             if (cups_counter > 2) {
//                 clearInterval(updateCupsLink);
//             }
//         }, 500);
//     } else if (resourceName == "rounds") {
//         let rounds_counter = 0;
//         let updateRoundsLink = setInterval(function () {
//             rounds_counter++;
//             //get laser
//             let headers = document.getElementsByTagName("th");
//             let round_round_result_class = document.getElementsByClassName("round_round_result_class");
//             if(round_round_result_class.length > 0){
//                 let round_round_result_class_index = round_round_result_class[0].offsetParent.cellIndex;
//                 let round_actions_class_index = round_round_result_class_index +1;
//                 if (headers[round_round_result_class_index] != "undefined") {
//                     headers[round_round_result_class_index].innerHTML = Nova.translate("Round Results");
//                 }
//                 if (headers[round_actions_class_index] != "undefined") {
//                     headers[round_actions_class_index].innerHTML = Nova.translate("Round Actions");
//                 }
//             }
//
//             if (rounds_counter > 2) {
//                 clearInterval(updateRoundsLink);
//             }
//         }, 500);
//     }
//
//     const params = new URLSearchParams(window.location.search);
//     let viaResourceId = params.get("viaResourceId");
//     let viaResource = params.get("viaResource");
//     let viaRelationship = params.get("viaRelationship");
//
//     if (
//         typeof viaResourceId != "undefined" &&
//         resourceName == "rounds" &&
//         viaResourceId != null &&
//         viaResource != null &&
//         viaRelationship != null
//     ) {
//         let rounds_create_btn_counter = 0;
//         let updateRoundsCreateBtnLink = setInterval(function () {
//             rounds_create_btn_counter++;
//             let createButtons = document.querySelectorAll(
//                 "a[href='/panel/resources/rounds/new']"
//             );
//             if (createButtons.length > 0) {
//                 let i = 0;
//                 createButtons.forEach(function (createButton) {
//                     let url =
//                         "/panel/resources/rounds/new?viaResource=" +
//                         viaResource +
//                         "&viaResourceId=" +
//                         viaResourceId +
//                         "&viaRelationship=" +
//                         viaRelationship;
//                     let addClass =
//                         i == 0
//                             ? "btn btn-default btn-primary"
//                             : "btn btn-sm btn-outline inline-flex items-center focus:outline-none focus:shadow-outline active:outline-none active:shadow-outline";
//                     createButton.parentNode.classList.add("add_button" + i);
//                     let myDiv = document.querySelector(".add_button" + i);
//                     let button = document.createElement("a");
//                     let text = document.createTextNode(Nova.translate("Create Race"));
//                     button.appendChild(text);
//                     createButton.remove();
//                     button.setAttribute("href", url);
//                     button.setAttribute("class", addClass);
//                     myDiv.appendChild(button);
//
//                     if (createButton.classList.contains("inline-flex")) {
//                         clearInterval(updateRoundsCreateBtnLink);
//                     }
//
//                     i++;
//                 });
//             }
//             if (rounds_create_btn_counter > 15) {
//                 clearInterval(updateRoundsCreateBtnLink);
//             }
//         }, 500);
//     }
// });
// endregion: triggers
