import config from "../../config";

let $_debug = config.debug_mode && config.field_types.contractor
let lastUrl = [ '', '', '' ]
let reloadContractorsTimeOutId = 0
export default {
    created() {
        lastUrl = [ '', '', '' ]
        reloadContractorsTimeOutId = 0
    },
    mounted() {
        console.log(this.field.attribute,this.field.value,this.value)
        this.registerChangeListener()
        this.$once( 'hook:beforeDestroy', () => {
            this.removeChangeListener()
        } )
    },
    methods: {
        registerChangeListener() {
            this.$on( 'contractor_id-change', e=>{
                this.handleChange(e)
                console.error('FieldContractor: registerChangeListener: contractor_id-change', e, this.value)
            })
            this.$on( 'contractor_id-changed', e=>{
                this.handleChange(e)
                console.error('FieldContractor: registerChangeListener: contractor_id-changed', e, this.value)
            })
            this.$on( 'contractor_id-value', e=>{
                this.handleChange(e)
                console.error('FieldContractor: registerChangeListener: contractor_id-value', e, this.value)
            })
            this.$on( 'project-changed', this.reloadContractors)
            this.$on( 'entry_category-changed', this.reloadContractors )
        },
        removeChangeListener() {
            Nova.$off( 'project-changed' )
            Nova.$off( 'contractor_id-change' )
            Nova.$off( 'contractor_id-changed' )
            Nova.$off( 'contractor_id-value' )
            Nova.$off( 'entry_category-changed' )
        },

        forceReloadContractors() {
            lastUrl[ 2 ] = ''
            reloadContractorsTimeOutId && clearTimeout( reloadContractorsTimeOutId )
            reloadContractorsTimeOutId = 0
            // this.handleChange( null )

            return this.reloadContractors()
        },
        reloadContractors() {
            if( reloadContractorsTimeOutId ) {
                return false
            }

            reloadContractorsTimeOutId = setTimeout( () => {
                reloadContractorsTimeOutId = 0
                this.getContractors()
            }, 10 )
        },

        getContractors() {
            let $getProjectId = () => (
                this.selectedProject || this.getProjectId()
            )
            lastUrl[ 0 ] = `/nova-vendor/components/contractor-belongs-to-project/data`
            lastUrl[ 1 ] = {
                project_id: $getProjectId(),
            }
            let fullUrl = lastUrl[ 0 ] + '?'
            let paramCount = Object.values( lastUrl[ 1 ] )
                                   .filter( v => Number( v ) ).length

            Object.keys( lastUrl[ 1 ] )
                  .map( k => fullUrl = fullUrl + `${k}=${lastUrl[ 1 ][ k ]}&` )

            if( lastUrl[ 2 ] && lastUrl[ 2 ] === fullUrl ) {
                if( !paramCount ) {
                    setTimeout( () => {
                        this.getContractors()
                    }, 100 )
                }
                return Promise.resolve( this.field.options )
            }

            lastUrl[ 2 ] = fullUrl
            this.field.options = []
            this.loading = true

            return Nova.request()
                       .get( lastUrl[ 0 ], {
                           params: lastUrl[ 1 ],
                       } )
                       .then( (res) => res.data )
                       .then( (res) => {
                           const {data: {data, project_id}} = res;
                           if( Number( project_id ) !== Number( $getProjectId() ) ) {
                               this.forceReloadContractors()
                               return false
                           }

                           this.field.options = data
                           this.initEntryCategoryField()
                           return data
                       } )
                       .catch( (error) => {
                           console.error( error.response.status, error )
                       } )
                       .finally( () => {
                           this.loading = false
                       } );
        },

    },
    computed: {
        contractor_name() {
            return this.field.options[ this.value ]
        },
    },
    watch: {
        selectedProject(n, o) {
            if (n !== o) {
                console.error(' --------- ')
                console.error('FieldContractor: ', 'watch:selectedProject ',n, o, this.value)
                this.$set(this, "value", n !== null ? n : "")
                console.error('FieldContractor: ', 'watch:selectedProject ',this.value)
                this.handleChange(n)
                console.error('FieldContractor: ', 'watch:selectedProject ',this.value)

                console.error(' --------- ')
            }
        },
    },
}
