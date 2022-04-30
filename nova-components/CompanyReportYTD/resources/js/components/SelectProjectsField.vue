<template>
    <loading-view :loading="loading">
        <info-label-field
            :class="{'hidden': !is_view}"
            :field="project"
            :label="project.name"
            :value="getSelectedProjectsNames()"
        />

        <vue-multi-select
            id="project_select_field"
            v-model="selectedProjects"
            :class="{'hidden': !!is_view}"
            :field="project"
            :get-option-description="parseOptionLabel"
            :get-option-value="parseOptionValue"
            :name="project.attribute"

            :option-key="optionKey"
            :option-label="optionLabel"
            :options="project.options"
            :placeholder="project.placeholder"
            :required="project.required"
            :value="selectedProjects"
            @input="handleProjectChange"
        ></vue-multi-select>

        <info-label-field
            :field="project"
            :label="__('Date')"
            :value="reportDateRange"
        />

    </loading-view>
</template>

<script>
import { HandlesValidationErrors } from 'laravel-nova'
import VueMultiSelect from "./VueMultiSelect";
import InfoLabelField from "./InfoLabelField";

export default {
    components: {
        VueMultiSelect,
        InfoLabelField
    },
    mixins: [ HandlesValidationErrors ],
    props: {
        value: {
            default: 0,
        },
        is_view: {
            type: Boolean,
            required: false,
            nullable: true,
            default: false,
        },
        multiple: {
            type: Boolean,
            required: false,
            nullable: true,
            default: false,
        },
        selectedIds: {
            type: Array,
            required: true,
        },
    },
    provide() {
        return {
            selectedProjectIds: this.selectedProjectIds,
        };
    },
    data() {
        let optionLabel = 'label',
            optionKey = 'value',
            optionSelectAll = {
            [ optionLabel ]: Nova.translate( "All" ),
            [ optionKey ]: '*',
        };

        return {
            loading: true,
            project: {
                name: Nova.translate( "Projects" ),
                singularLabel: Nova.translate( "Projects" ),
                placeholder: Nova.translate( "Pick projects" ),
                value: '*',
                attribute: "projects",
                helpText: "",
                validationKey: "projects",
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
                options: [
                    optionSelectAll,
                ],

            },
            selectedProject: optionSelectAll,
            selectedProjects: [
                optionSelectAll,
            ],
            optionLabel,
            optionKey,
            optionSelectAll,
        }
    },
    async created() {
        this.getProjects()
        // .then( (x) => Nova.$emit( 'project-changed', {selected: this.value || 0, projects: this.projects} ) )
    },
    mounted() {
        this.registerChangeListener()
        this.$once( 'hook:beforeDestroy', () => {
            this.removeChangeListener()
        } )
    },
    methods: {
        getProjects({ then = ()=>{} } = {}) {
            this.loading = true
            this.projects = []

            return Nova.request()
                       .get( `/nova-vendor/report-components/projects`, {params: {}} )
                       .then( (res) => res.data )
                       .then( (res) => {
                           const {data} = res;
                           this.projects = data
                           return data
                       } )
                       .then((...res) => {
                           return typeof then === 'function' ? then(...res) : res.length && res[0]
                       })
                       .catch( (error) => {
                           console.error( error.response.status, error )
                       } )
                       .finally( () => {
                           this.loading = false
                       } );
        },
        selectProject(value) {
            value = typeof value === 'object' ? value : {
                [ this.optionKey ]: value,
                [ this.optionLabel ]: value,
            }

            if( !this.selectedProjects.length || !this.selectedProjects.some( (x) => x && x[ this.optionKey ] === value[ this.optionKey ] ) ) {
                this.selectedProjects.push( value )
            }
            this.handleProjectChange([value])
            return true
        },
        getSelectedProjectsNames() {
            let $selectedProjects = Array.from( this.selectedProjects )
            let $_options = Array.from( this.project.options )
            let $options = $_options.filter(x =>this.parseOptionValue(x)!=='*')

            if( $selectedProjects.length === $options.length ) {
                return this.optionSelectAll[ this.optionLabel ] || Nova.translate( "All" )
            }

            return this.parseOptionsLabel( $selectedProjects ).join(', ')
        },
        parseOptionsValue(n) {
            return n.map( (x) => this.parseOptionValue( x ) )
        },
        parseOptionsLabel(n) {
            return n.map( (x) => this.parseOptionLabel( x ) )
        },
        handleProjectChange(v) {
            let selectAll = v.length && v.some( x => typeof x === 'object' && this.parseOptionValue( x ) === '*')
            let valid = v.length && v.every( x => typeof x !== 'object' )
            let selected = this.parseOptionsValue( selectAll ? this.project.options : v ).filter(x =>x!=='*')

            this.$emit( 'update:selectedIds', selected )
        },
        registerChangeListener() {
            // Nova.$on( 'project-change', this.handleProjectChange )
        },

        removeChangeListener() {
            // Nova.$off( 'project-change' )
        },

        parseOptionLabel(option) {
            if( this.optionKey && this.optionLabel ) {
                return option[ this.optionLabel ];
            }
            if( this.optionLabel ) {
                return option[ this.optionLabel ];
            }
            if( this.optionKey ) {
                return option[ this.optionKey ];
            }
            return option;
        },

        parseOptionValue(option) {
            if( this.optionKey ) {
                return option[ this.optionKey ];
            }

            if( this.optionLabel ) {
                return option[ this.optionLabel ];
            }

            return option;
        },

        getOptionSelectAll() {
            return this.optionSelectAll;
        }
    },
    computed: {
        reportDateRange() {
            return Nova.config.defaultDateRange || '-'
        },
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
                v = [this.getOptionSelectAll(), ...v]

                this.project.options = v
            },
        },
        selectedProjectId: {
            get() {
                return this.selectedProjectIds.length ? this.selectedProjectIds[ 0 ] : this.project.value
                // return this.project.value
            },
            set(value) {
                this.project.value = value
            },
        },
        selectedProjectIds: {
            get() {
                let ids = []
                this.selectedProjects.forEach( (x) => ids.push( this.parseOptionValue( x ) ) )

                return ids
            },
            set(value) {
                value = typeof value === 'object' ? value : {
                    [ this.optionKey ]: value,
                    [ this.optionLabel ]: value,
                }
                if( !this.selectedProjects.some( (x) => x && x[ this.optionKey ] === value ) ) {
                    this.selectedProjects.push( value )
                }
            },
        },
    },
    watch: {
        is_view(n, o) {
            let project_select_field = document.querySelector( '#project_select_field select' );
            if( project_select_field ) {
                // project_select_field.value=null
                project_select_field.dispatchEvent && project_select_field.dispatchEvent( new Event( 'change' ) )
                project_select_field.change && project_select_field.change()
            }
        },
        selectedProjects(v, o) {
            let selectAll = v.length && v.some( x => typeof x === 'object' && this.parseOptionValue( x ) === '*' || x === '*')
            let isOption = (v)=>v.length && v.every( x => typeof x === 'object' )

            v = isOption(v) ? this.parseOptionsValue(v) : v
            o = isOption(o) ? this.parseOptionsValue(o) : o

            if( selectAll ) {
                // v = this.parseOptionsValue( this.project.options ).filter(x =>x!=='*')
                v = this.project.options.filter(x =>this.parseOptionValue(x)!=='*')

                return (this.selectedProjects = v)
            }
        },
    },
}
</script>

<style>
/* Scoped Styles */
</style>
