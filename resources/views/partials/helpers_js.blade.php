<!-- region: Helpers -->
<script>
    /** On document ready */
    window.$whenReady = function $whenReady(fn) {
        if( typeof fn !== 'function' ) {
            throw new Error( '[helpers_js:7] Argument passed to ready should be a function' );
        }

        if( document.readyState != 'loading' ) {
            fn();
        } else if( document.addEventListener ) {
            document.addEventListener( 'DOMContentLoaded', fn, {
                once: true, // A boolean value indicating that the listener should be invoked at most once after being added. If true, the listener would be automatically removed when invoked.
            } );
        } else {
            document.attachEvent( 'onreadystatechange', function () {
                if( document.readyState != 'loading' )
                    fn();
            } );
        }
    }

    /** Return type of object */
    window.$typeOf = function $typeOf(obj) {
        return Object.prototype.toString.call( obj ).replace( "[object ", "" ).replace( "]", "" ).trim();
    }

    /** Run a callback if a condition is true or run another if its false */
    window.$when = function $when(condition, whenTrue, whenFalse, ...args) {
        let callCB = (cb, ...$args) => $typeOf( cb ) === 'Function' ? cb( ...$args ) : cb;
        args.unshift( condition )
        return callCB( condition ? whenTrue : whenFalse, ...args )
    }

    /** run $when method and return the results as Promise */
    window.$when2P = function $when2P(condition, whenTrue, whenFalse, ...args) {
        return Promise.resolve( $when( condition, whenTrue, whenFalse, ...args ) )
    }

</script>
<!-- endregion: Helpers -->
