// region: nova helpers
function when(expression, trueCallback, falseCallback) {
    return expression ? (
        trueCallback || Function()
    )() : (
               falseCallback || Function()
           )()
}

function getCurrentResourceUriKey() {
    return Nova.app.$route.params.resourceName || ""
}

function isCurrentResourceUriKey(uri) {
    if( Array.isArray( uri ) ) {
        return uri.includes( getCurrentResourceUriKey() );
    }

    return uri === getCurrentResourceUriKey()
}

function onNovaEvent(elementName, callback, applyOnResources = []) {
    Nova.$on( `${elementName}`, (id) => {
        if( applyOnResources.length ) {
            if( !applyOnResources.includes( getCurrentResourceUriKey() ) ) {
                return;
            }
        }
        callback( id );
    } );
}

function onNovaLoaded(callback, applyOnResources = []) {
    onNovaEvent( "resource-loaded", callback, applyOnResources );
}

function onNovaElementChange(elementName, callback, applyOnResources = []) {
    onNovaEvent( `${elementName}-change`, callback, applyOnResources );
}

function getNovaElement(elementName, as_promise = false, pipe = null) {
    pipe = pipe || (
        x => x
    );
    if( as_promise ) {
        return new Promise( (resolve, reject) => {
            setTimeout( () => resolve( pipe( {
                                                 data: getNovaElement( elementName, false ),
                                             } ) ), 10 )
        } )
    }

    return pipe( document.querySelector( `[dusk="${elementName}"]` ) )
}

function getNovaElements(elementName, as_promise = false, pipe = null) {
    pipe = pipe || (
        x => x
    );
    if( as_promise ) {
        return new Promise( (resolve, reject) => {
            setTimeout( () => resolve( pipe( {
                                                 data: getNovaElements( elementName, false ),
                                             } ) ), 10 )
        } )
    }

    return pipe( Array.from( document.querySelectorAll( `[dusk="${elementName}"]` ) ) )
}

function disableNovaElement(elementName = false, pipe = null) {
    let element = getNovaElement( elementName )
    if( element ) {
        element.setAttribute( 'disabled', true )
    }

    pipe = pipe || (
        x => x
    );
    let toggle = () => enableNovaElement( elementName, pipe )
    return {
        data: pipe( element ),
        toggle
    }
}

function enableNovaElement(elementName, pipe = null) {
    let element = getNovaElement( elementName )
    if( element ) {
        element.removeAttribute( 'disabled' )
    }

    pipe = pipe || (
        x => x
    );
    let toggle = () => disableNovaElement( elementName, pipe )
    return {
        data: pipe( element ),
        toggle
    }
}

async function waitForNovaElements(counter = 100, $elements = undefined) {
    counter = Number( counter ) || 0
    $elements = $elements || {}
    for( let name in $elements ) {
        if( await getNovaElement( name, true, data => data.data ) ) {
            let callback = $elements[ name ] || async( () => undefined );
            delete $elements[ name ];
            await callback();
        }
    }
    if( Object.keys( $elements ).length && counter ) {
        return new Promise( (resolve, reject) => {
            setTimeout( async () => resolve( await waitForNovaElements( counter - 1, $elements ) ), 100 )
        } )
    }

    return new Promise( (resolve, reject) => resolve( null ) );
}

// endregion: nova helpers

// region: helpers
function callAfter(timeout = 500, method = Function(), before = Function()) {
    before();
    return (...a) => setTimeout( () => method( ...a ), timeout )
}

// endregion: helpers

// region: nova methods
Nova.translate = Nova.translate || function (title) {
    return Nova.config.translations[ title ] || title;
};

Nova.confirmEvent = Nova.confirmEvent || function (event) {
    return swal( {
                     title: Nova.translate( "Hold Up!" ),
                     text: Nova.translate( "Are you sure you want to run this action?" ),
                     icon: "warning",
                     buttons: [ Nova.translate( "No" ), Nova.translate( "Yes" ) ],
                     dangerMode: true,
                 } );
};
// endregion: nova methods

