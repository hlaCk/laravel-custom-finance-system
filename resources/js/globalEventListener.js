let applyOnResources = [ 'expenses' ];
let _contractors = [];
let _element_contractor_name = 'contractor_id';
let _element_project_name = 'project';
let _element_entry_category_name = 'entry_category';

let preUpdateContractorElement = ()=>getNovaElement( _element_contractor_name, true, data => data.data ).then( e => e && e.setAttribute( 'disabled', true ) );
let updateContractorElement = async (id) => {
    let contractorElement = await getNovaElement( _element_contractor_name, true, data => data.data )
    let projectElement = await getNovaElement( _element_project_name, true, data => data.data )
    if( !contractorElement || !projectElement ) return;

    let contractorId = contractorElement.value
    let project_id = projectElement.value || "";

    if( contractorElement.hasAttribute( '_value' ) ) {
        contractorId = contractorElement.getAttribute( '_value' );
    } else {
        contractorElement.setAttribute( '_value', contractorId )
    }
    contractorElement.setAttribute( 'disabled', true )

    let updateConstructorsElement = async ($contractors, $selectElement) => {
        let validOptions = {}
        Array.from( $selectElement.options ).forEach( ($option, $index) => {
            if( !$option.value || Number( $option.value ) in $contractors ) {
                $option.style.display = "";
                validOptions[ $option.value || 0 ] = $index;
            } else {
                $option.style.display = "none";
            }
        } )

        $selectElement.value = Number( $selectElement.value ) in $contractors
                               ? $selectElement.value
                               : 0
        $selectElement.selectedIndex = validOptions[ $selectElement.value || 0 ] || 0
        $selectElement.setAttribute( '_options', $selectElement.options.length )
        return true
    };

    if( _contractors[ project_id ] ) {
        $promise = updateConstructorsElement( _contractors[ project_id ], contractorElement )
    } else {
        $promise = Nova.request().get( `/api/contractor/data` )
                       .then( response => {
                           let {data: data, project_id: $project_id} = response.data
                           _contractors = {};
                           Array.from( data ).forEach( ($model) => {
                               let _data = {}
                               let $_data = $model[ 'data' ]
                               Object.keys( $_data )
                                     .forEach( v => _data[ $_data[ v ][ 'value' ] ] = $_data[ v ][ 'label' ] )
                               _contractors[ $model[ 'project_id' ] ] = _data
                           } )

                           if( _contractors[ project_id ] ) {
                               return updateConstructorsElement( _contractors[ project_id ], contractorElement )
                           }

                           return false
                       } )
    }

    return $promise
        .then( (success) => {
            if( success ) {
                contractorElement.dispatchEvent( new Event( 'change' ) )
                Nova.$emit( `${_element_contractor_name}-change`, contractorElement.value )
            }
        } )
        .finally( () => {
            contractorElement.removeAttribute( 'disabled' )
        } );
};
let _callAfter = (timeout = 10) => callAfter( timeout, updateContractorElement, preUpdateContractorElement );

/**
 * @project-change : On entry_category change.
 * To listen event on url panel/resources/expenses/new or edit
 */
onNovaElementChange( _element_project_name, _callAfter(), applyOnResources )
onNovaElementChange( _element_entry_category_name, _callAfter(), applyOnResources )
onNovaLoaded( async () => {
    await waitForNovaElements( 100, {
        [ _element_contractor_name ]: () => Nova.$emit( `${_element_entry_category_name}-change`, false ),
    } )
}, applyOnResources )
