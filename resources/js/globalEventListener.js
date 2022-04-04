/**
 * @round-change : On round change.
 * To listen event on url panel/resources/round-results/new
 */
//
// Nova.$on("round-change", (round_id) => {
//     let resourceName = Nova.app.$route.params.resourceName || false;
//     if (resourceName == "round-results") {
//         var place = document.querySelector("#place");
//         if (place) {
//             var place_id = place.value;
//         } else {
//             var place_id = false;
//         }
//         if (round_id && round_id !== '' && place_id && place_id != '') {
//             Nova.request().post('/panel/round-result/prize', {
//                 round_id: round_id,
//                 place_id: place_id,
//             }).then(response => {
//                 var prize = document.querySelector("#prize,#prize_read[dusk='prize_read']");
//                 if (prize) {
//                     if (response.data.prize && response.data.prize != '') {
//                         prize.value = response.data.prize.prize.toFixed(2);
//                     } else {
//                         prize.value = null;
//                     }
//                     prize.dispatchEvent(new Event('change'));
//                 }
//             });
//         }
//     }
// });
//
// /**
//  * @place-change : On place change.
//  * To listen event on url panel/resources/round-results/new
//  */
// Nova.$on("place-change", (place_id) => {
//     let resourceName = Nova.app.$route.params.resourceName || false;
//     if (resourceName == "round-results") {
//         var round = document.querySelector("#round_id,[data-testid='rounds-select']");
//         var round_id = round && round.value || false;
//
//         if (round_id && round_id !== '' && place_id && place_id != '') {
//             Nova.request().post('/panel/round-result/prize', {
//                 round_id: round_id,
//                 place_id: place_id,
//             }).then(response => {
//                 var prize = document.querySelector("#prize,#prize_read[dusk='prize_read']");
//                 if (prize) {
//                     prize.value = response.data && response.data.prize && response.data.prize.prize_formatted || null;
//                     prize.dispatchEvent(new Event('change'));
//                 }
//             });
//         }
//     }
// });
//
// /**
//  * @layout_page-change : On layout_page change.
//  * To listen event on url all urls
//  */
// Nova.$on("layout_page-change", (layoutId) => {
//     let resourceName = Nova.app.$route.params.resourceName || false;
//     var round = document.querySelector("#round_id,[data-testid='rounds-select']");
//
//     if (layoutId && layoutId !== '') {
//         Nova.request().post('/panel/layout-page/image', {
//             layoutId: layoutId
//         }).then(response => {
//             // console.log(response);
//             var layoutPageSelect = document.querySelector("[dusk='layout_page'][data-testid='layout-pages-select']");
//             if (layoutPageSelect) {
//                 if (response.data.image_url && response.data.image_url != '') {
//                     var img = document.querySelector(".layout_page_image");
//                     var imageExist = false;
//                     if (img) {
//                         var imageExist = true;
//                     } else {
//                         var img = document.createElement('img');
//                     }
//                     img.src = response.data.image_url;
//                     img.width = "256";
//                     if (imageExist) {
//                         img && img.load && img.load();
//                     } else {
//                         var div = document.createElement('div');
//                         div.classList.add("layout_page_image_div");
//                         img.classList.add("layout_page_image");
//                         div.appendChild(img);
//                         layoutPageSelect.parentNode.insertBefore(div, layoutPageSelect.nextSibling);
//                     }
//                 }
//
//                 var event = new Event('change');
//                 layoutPageSelect.dispatchEvent(event);
//             }
//         });
//     }
// });
