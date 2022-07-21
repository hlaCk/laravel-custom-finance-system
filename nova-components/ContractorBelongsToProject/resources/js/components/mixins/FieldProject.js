import config from "../../config";

let $_debug = config.debug_mode && config.field_types.project

export default {
    async created() {
        $_debug && console.warn( 'FieldProject: ', 'created' )
        this.initProjectField()
    },
    mounted() {
        $_debug && console.warn( 'FieldProject: ', 'mounted' )
        this.registerProjectListener()
        this.$once( 'hook:beforeDestroy', () => {
            this.removeProjectListener()
        } )
    },
    data: () => (
        {
            selectedProjectId: 0,
        }
    ),
    methods: {
        initProjectField() {
            let projects_interval = setInterval( () => {
                if( this.project_field ) {
                    if( this.getProjectId() ) {
                        projects_interval && clearInterval( projects_interval )
                        projects_interval = 0
                        this.$emit( 'project-changed', this.getProjectId() )
                        this.handleProjectChange(this.getProjectId())
                    }
                } else {
                    projects_interval && clearInterval( projects_interval )
                    projects_interval = 0
                    this.$emit( 'project-changed', undefined )
                    this.handleProjectChange(this.getProjectId())
                }
            }, 10 )

            setTimeout( () => {
                projects_interval && clearInterval( projects_interval )
                projects_interval = 0
            }, 10000 )
        },
        getProjectId(cb = undefined) {
            let
                project_field = this.project_field || 'project',
                element       = project_field && document.querySelector( `[dusk='${project_field}']` ),
                result        = element && element.value

            if( typeof cb != 'function' ) {
                cb = (x) => x
            }

            return cb( result )
        },
        handleProjectChange(v) {
            $_debug && console.warn( 'FieldProject: ', 'handleProjectChange: ', v )
            this.selectedProjectId = v
            this.forceReloadContractors()

            this.$emit( 'project-changed', v )
        },

        registerProjectListener() {
            $_debug && console.warn( 'FieldProject: ', 'registerProjectListener' )
            Nova.$on( 'project-change', (e)=>{
                this.handleProjectChange(e)

                // this.field.value = this.value
            } )
        },
        removeProjectListener() {
            $_debug && console.warn( 'FieldProject: ', 'removeProjectListener' )
            Nova.$off( 'project-change' )
        },
    },
    computed: {
        project_name() {
            return this.field.options[ this.selectedProject ]
        },
        project_field() {
            return this.field.project_field
        },
        selectedProject: {
            get() {
                return this.selectedProjectId || this.getProjectId() || this.value
            },
            set(value) {
                this.selectedProjectId = value
            }
        }
    },
    watch: {
        selectedProjectIda(n, o) {
            if (n !== o) {
                console.error('FieldProject: ', 'watch:selectedProjectId ', n, o, this.value)
                this.$set(this, "value", n !== null ? n : "")
                this.handleChange(n)

                console.error('FieldProject: ', 'watch:selectedProjectId ', this.value)
            }
        },
    },
}
