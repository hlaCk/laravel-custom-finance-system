<template>
    <loading-view :loading="loading">
        <form-select-field :field="project" :value="selectedProjectId"/>

    </loading-view>
</template>

<script>
import { HandlesValidationErrors } from 'laravel-nova'

export default {
    mixins: [ HandlesValidationErrors ],
    props: {
        value: {
            default: 0,
        },
    },
    provide() {
        return {
            selectedProjectId: this.selectedProjectId,
        };
    },
    data: () => (
        {
            loading: true,
            project: {
                name: Nova.translate( "Project" ),
                singularLabel: Nova.translate( "Project" ),
                value: null,
                attribute: "project",
                helpText: "",
                validationKey: "project",
                required: false,
                stacked: false,
                showHelpText: false,
                nullable: true,
                readonly: false,
                reverse: false,
                searchable: false,
                prefixComponent: true,
                withSubtitles: false,
                textAlign: "left",
                debounce: 100,
                options: [],
            },
            selectedProject: {},
        }
    ),
    async created() {
        this.getProjects()
            .then( (x) => Nova.$emit( 'project-changed', {selected: this.value, projects: this.projects} ) )
    },
    mounted() {
        this.registerChangeListener()
        this.$once( 'hook:beforeDestroy', () => {
            this.removeChangeListener()
        } )
    },
    methods: {
        getProjects() {
            this.projects = []

            return Nova.request()
                       .get( `/nova-vendor/year-to-date/projects`, {params: {}} )
                       .then( (res) => res.data )
                       .then( (res) => {
                           const {data} = res;
                           this.projects = data
                           return data
                       } )
                       .catch( (error) => {
                           console.error( error.response.status, error )
                       } )
                       .finally( () => {
                           this.loading = false
                       } );
        },
        handleProjectChange(v) {
            this.selectedProjectId = v
            this.$emit( 'set-changed', {selected: v, projects: this.projects} )
            Nova.$emit( 'project-changed', {selected: v, projects: this.projects} )
        },
        registerChangeListener() {
            Nova.$on( 'project-change', this.handleProjectChange )
        },

        removeChangeListener() {
            Nova.$off( 'project-change' )
        },
    },
    computed: {
        projects: {
            get() {
                let $projects = {};
                for( let option in Array.from( this.project.options ) ) {
                    let _option = this.project.options[ option ]
                    $projects[ _option.value ] = _option.label
                }
                return $projects
            },
            set(v) {
                this.project.options = v
            },
        },
        selectedProjectId: {
            get() {
                return this.project.value
            },
            set(value) {
                this.project.value = value
            },
        },
    },
}
</script>

<style>
/* Scoped Styles */
</style>
